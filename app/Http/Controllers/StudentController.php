<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * READ (list): GET /students loads a page of records for the directory.
     * Flow: route -> controller -> Student/Course models -> database -> Blade.
     */
    public function index(): View
    {
        // Eager loading avoids making an extra course query for each student.
        $students = Student::with('course')
            ->orderBy('last_name')->orderBy('first_name')->orderBy('id')
            ->paginate(10);

        // Pass the results to Blade as $students so it can build the table.
        return view('students.index', compact('students'));
    }

    /**
     * CREATE (form): GET /students/create displays the registration form.
     * Opening this page does not insert a student; store() handles that later.
     */
    public function create(): View
    {
        // The dropdown displays course names but submits the selected course ID.
        $courses = Course::orderBy('code')->get();

        return view('students.create', compact('courses'));
    }

    /**
     * CREATE (save): POST /students receives the submitted form fields.
     * Flow: form -> validation -> Eloquent INSERT -> redirect -> index().
     */
    public function store(Request $request): RedirectResponse
    {
        // If validation fails, Laravel returns to the form before create() runs.
        // Otherwise, Eloquent inserts only the validated, fillable student fields.
        Student::create($this->validatedData($request));

        // Redirect after POST so refreshing cannot resubmit the form.
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    /**
     * READ (details): GET /students/{student} displays one student's record.
     * The typed argument lets Laravel resolve the URL's ID to a Student model.
     */
    public function show(Student $student): View
    {
        // Binding has already found the student or returned a 404 before this method.
        // Load its course so the details view can display a name instead of an ID.
        $student->load('course');

        return view('students.show', compact('student'));
    }

    /**
     * UPDATE (form): GET /students/{student}/edit opens the existing record.
     * Pass both the student values and course choices to the edit form.
     */
    public function edit(Student $student): View
    {
        $courses = Course::orderBy('code')->get();

        return view('students.edit', compact('student', 'courses'));
    }

    /**
     * UPDATE (save): PUT/PATCH /students/{student} saves changes to that record.
     * Flow: edit form -> validation -> Eloquent UPDATE -> redirect -> show().
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Update the route-bound student's row; do not create a second student.
        // Passing this model to validation permits its own existing number/email.
        $student->update($this->validatedData($request, $student));

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    /**
     * DELETE: DELETE /students/{student} permanently removes this student.
     * Flow: confirmed form -> Eloquent DELETE -> redirect -> refreshed directory.
     */
    public function destroy(Student $student): RedirectResponse
    {
        // Only the student is deleted. The related course remains available.
        // This model does not use soft deletes, so the row cannot be restored here.
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    /**
     * Share the same data rules between CREATE and UPDATE.
     * The optional student is absent on create and supplied on update.
     */
    private function validatedData(Request $request, ?Student $student = null): array
    {
        $numberRule = Rule::unique('students', 'student_number');
        $emailRule = Rule::unique('students', 'email');

        // Ignore only this bound model on edit so unchanged unique fields pass.
        // Never pass a user-submitted ID to ignore().
        if ($student) {
            $numberRule->ignore($student);
            $emailRule->ignore($student);
        }

        // Failure automatically redirects back with errors and previous input.
        return $request->validate([
            'student_number' => ['required', 'string', 'max:30', $numberRule],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'year_level' => ['required', 'integer', 'between:1,4'],
            // Check the foreign key before trying to save the student record.
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);
    }
}
