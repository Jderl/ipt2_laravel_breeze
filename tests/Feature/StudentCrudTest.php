<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        $course = Course::firstOrCreate(['code' => 'BSIT'], ['name' => 'Information Technology']);

        return array_merge([
            'student_number' => 'DEMO-0001', 'first_name' => 'Maria',
            'last_name' => 'Dela Cruz', 'email' => 'maria@example.com',
            'year_level' => 2, 'course_id' => $course->id,
        ], $overrides);
    }

    public function test_complete_crud_workflow_and_relationships(): void
    {
        $data = $this->payload();
        $this->get(route('students.create'))->assertOk()->assertSee('BSIT');
        $this->post(route('students.store'), $data)
            ->assertRedirect(route('students.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('students', $data);
        $student = Student::firstOrFail();
        $this->assertSame('BSIT', $student->course->code);
        $this->assertTrue($student->course->students->contains($student));
        $this->get(route('students.index'))->assertOk()->assertSee('DEMO-0001')->assertSee('BSIT');
        $this->get(route('students.show', $student))->assertOk()->assertSee('Maria Dela Cruz');
        $this->get(route('students.edit', $student))->assertOk()->assertSee('maria@example.com');

        $otherCourse = Course::create(['code' => 'BSCS', 'name' => 'Computer Science']);
        $updated = array_merge($data, ['year_level' => 3, 'course_id' => $otherCourse->id]);
        $this->put(route('students.update', $student), $updated)
            ->assertRedirect(route('students.show', $student))->assertSessionHas('success');
        $this->assertDatabaseHas('students', $updated);
        $this->get(route('students.show', $student))->assertOk()->assertSee('Year 3')->assertSee('BSCS');
        $this->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->get(route('students.show', $student))->assertNotFound();
        $this->assertDatabaseHas('courses', ['id' => $otherCourse->id]);
    }

    public function test_empty_and_malformed_input_is_rejected_with_old_input(): void
    {
        $this->from(route('students.create'))->post(route('students.store'), [])
            ->assertRedirect(route('students.create'))
            ->assertSessionHasErrors(['student_number', 'first_name', 'last_name', 'email', 'year_level', 'course_id']);

        $bad = $this->payload(['email' => 'invalid', 'year_level' => 5, 'course_id' => 999999]);
        $this->from(route('students.create'))->post(route('students.store'), $bad)
            ->assertSessionHasErrors(['email', 'year_level', 'course_id'])
            ->assertSessionHasInput('first_name', 'Maria');
        $this->get(route('students.create'))->assertOk()->assertSee('Please correct')->assertSee('is-invalid');
        $this->assertDatabaseCount('students', 0);
    }

    public function test_duplicate_numbers_and_emails_are_rejected_on_create_and_update(): void
    {
        $data = $this->payload();
        Student::create($data);
        $this->post(route('students.store'), $data)
            ->assertSessionHasErrors(['student_number', 'email']);

        $second = Student::create($this->payload(['student_number' => 'DEMO-0002', 'email' => 'second@example.com']));
        $this->put(route('students.update', $second), $data)
            ->assertSessionHasErrors(['student_number', 'email']);
        $this->assertSame('second@example.com', $second->fresh()->email);
        $this->assertDatabaseCount('students', 2);
    }

    public function test_missing_students_return_404_for_all_bound_actions(): void
    {
        $this->get('/students/999999')->assertNotFound();
        $this->get('/students/999999/edit')->assertNotFound();
        $this->put('/students/999999', $this->payload())->assertNotFound();
        $this->delete('/students/999999')->assertNotFound();
    }

    public function test_directory_pagination_and_repeatable_seeders(): void
    {
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('courses', 3);
        $this->assertDatabaseCount('students', 12);
        $this->get('/students')->assertOk()->assertSee('12 students')
            ->assertViewHas('students', fn ($students) => $students->count() === 10);
        $this->get('/students?page=2')->assertOk()
            ->assertViewHas('students', fn ($students) => $students->count() === 2);
    }

    public function test_empty_states_and_safe_escaped_output(): void
    {
        $this->get('/students')->assertOk()->assertSee('No students yet');
        $this->get('/students/create')->assertOk()->assertSee('No courses are available');
        $student = Student::create($this->payload(['first_name' => '<script>alert(1)</script>']));
        $this->get(route('students.show', $student))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
