# Implementation walkthrough

The source files contain the complete connected implementation. Examples below explain the key lines. Generator commands are for a fresh Laravel 10 skeleton; do not regenerate files over this completed project.

## 1. Migrations: create the tables

```powershell
php artisan make:migration create_courses_table
php artisan make:migration create_students_table
```

These generate timestamped files in `database/migrations/`. Copy the up/down definitions from `2026_01_01_000001_create_courses_table.php` and `2026_01_01_000002_create_students_table.php` into your generated files, keeping courses first. Do not keep duplicate table migrations.

`Schema::create` describes a table; `id()` creates its primary key; `timestamps()` adds created_at/updated_at, which Eloquent maintains. `up()` applies a migration and `down()` reverses it.

```php
$table->string('student_number', 30)->unique();
$table->string('email')->unique();
$table->foreignId('course_id')->constrained()->restrictOnDelete();
```

Unique indexes prevent duplicate numbers/emails even outside the form. course_id stores a course primary key. constrained() infers courses.id and prevents nonexistent course IDs. restrictOnDelete() stops deleting courses with students. Deleting a student leaves its course intact.

```powershell
php artisan migrate
php artisan migrate:status
```

**Test:** inspect courses and students in phpMyAdmin. Student fields include number, first/last names, email, year level, course_id, primary key, and timestamps. The skeleton's user/password/token tables are unrelated to this demo.

## 2. Models and relationships

```powershell
php artisan make:model Course
php artisan make:model Student
```

Use `app/Models/Course.php` and `app/Models/Student.php`. A **model** represents an entity. Eloquent ORM (Object-Relational Mapper) maps PHP objects to database rows, so ordinary CRUD does not need handwritten SQL. By convention Student maps to students; Course maps to courses.

```php
// Inside Course::students(): one course has many students.
return $this->hasMany(Student::class);
// Inside Student::course(): each student belongs to one course.
return $this->belongsTo(Course::class);
```

These lines belong in separate methods/classes as shown in the source files. Relationships define how to query related data; migrations create the actual columns/constraints. `$fillable` permits only listed fields for create/update mass assignment. It does not replace validation. `$casts` reads year_level as an integer.

**Test:** run `php artisan tinker`, enter `App\Models\Student::count();`, then `exit`. Eloquent should query the empty table without errors. Test related data after seeding.

## 3. Seeders and sample records

```powershell
php artisan make:seeder CourseSeeder
php artisan make:seeder StudentSeeder
```

Use `database/seeders/CourseSeeder.php`, `StudentSeeder.php`, and updated `DatabaseSeeder.php`. The default seeder calls:

```php
$this->call([CourseSeeder::class, StudentSeeder::class]);
```

Courses must exist before students reference them. `firstOrCreate` looks up an identifying value before inserting and leaves existing records alone. Data uses synthetic names and example.com addresses.

```powershell
php artisan db:seed
php artisan tinker
```

Inside Tinker:

```php
App\Models\Course::count(); // 3 on a clean demo database
App\Models\Student::count(); // 12
App\Models\Student::with('course')->first()->course->name;
App\Models\Course::where('code', 'BSIT')->first()->students->count(); // 4
exit
```

**Test:** a student exposes its course name; a course exposes a student collection.

## 4. Resource controller

```powershell
php artisan make:controller StudentController --resource --model=Student
```

Use `app/Http/Controllers/StudentController.php`. A **controller** coordinates a request: obtains data, validates input, calls a model, and chooses a response. A resource controller has seven conventional actions, which we connect next.

**Test:** `php -l app/Http/Controllers/StudentController.php` reports no syntax errors.

## 5. Routes

In **`routes/web.php`**, import the controller and register:

```php
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/students');
Route::resource('students', StudentController::class);
```

Routes are the address book: an HTTP method and URL map to an action.

| Method | URL | Action | Route name |
| --- | --- | --- | --- |
| GET | /students | index | students.index |
| GET | /students/create | create | students.create |
| POST | /students | store | students.store |
| GET | /students/{student} | show | students.show |
| GET | /students/{student}/edit | edit | students.edit |
| PUT/PATCH | /students/{student} | update | students.update |
| DELETE | /students/{student} | destroy | students.destroy |

The {student} parameter is a record ID. A typed `Student $student` argument activates **route model binding**: Laravel loads the record or returns HTTP 404 if it does not exist.

```powershell
php artisan route:list --path=students
```

**Test:** seven rows match this table. GET requests cannot save or delete a record.

## 6. Seven controller actions

Open **`app/Http/Controllers/StudentController.php`** and trace each method:

| Method | Key expression | Why it exists |
| --- | --- | --- |
| index | `Student::with('course')->orderBy('last_name')->paginate(10)` | Loads students/courses efficiently, ordered and paginated. Extra first-name/ID ordering keeps ties stable. |
| create | `Course::orderBy('code')->get()` | Loads dropdown choices and opens a blank form. |
| store | `Student::create($this->validatedData($request))` | Validates and inserts. |
| show | `$student->load('course')` | Loads related course for details. |
| edit | bound student plus course query | Opens a populated form. |
| update | `$student->update($this->validatedData($request, $student))` | Validates and updates this row. |
| destroy | `$student->delete()` | Deletes this row, leaving the course. |

`with('course')` is **eager loading**: it retrieves related courses together instead of doing one extra query per student. `compact('students')` passes a variable to the view. `view('students.index', ...)` resolves resources/views/students/index.blade.php. `redirect()->route(...)` sends the browser to a fresh GET request; `with('success', ...)` flashes a short-lived session message.

**Test:** after creating the views, open list/create/details/edit pages and follow the browser CRUD sequence in PRESENTER.md.

## 7. Blade and Bootstrap 5 views

Create these folders/files in VS Code, using the complete supplied source:

| File under resources/views/ | Purpose |
| --- | --- |
| layouts/app.blade.php | Shared HTML document, viewport, Bootstrap CSS, navigation, messages, content, footer. |
| students/index.blade.php | Responsive directory, courses, actions, pagination, empty state. |
| students/create.blade.php | Blank form posting to students.store. |
| students/edit.blade.php | Populated form posting with hidden PUT to students.update. |
| students/show.blade.php | Individual registration details and edit/delete actions. |
| students/_form.blade.php | Reusable fields, old input, errors, course/year choices, Save/Cancel. |
| students/_delete.blade.php | CSRF-protected DELETE form with confirmation. |

`@extends` selects a layout; `@section` fills its `@yield`; `@include` reuses a fragment. `@forelse` loops or displays an empty state. `{{ ... }}` escapes HTML. `route('students.edit', $student)` builds a URL from its name.

Bootstrap's container/row/col-md-6, form-control, table-responsive, btn, and alert classes supply responsive layout and consistent styling. `public/css/app.css` adds small visual refinements. Bootstrap 5.3.3 CSS and its MIT license are bundled in `public/vendor/bootstrap/`; copy that folder when rebuilding.

In **`app/Providers/AppServiceProvider.php`**, import `Illuminate\Pagination\Paginator` and call `Paginator::useBootstrapFive()` inside boot(). The provider runs during startup and selects matching pagination templates.

```powershell
php artisan view:cache
```

**Test:** templates compile; open /students/create and resize the browser to check labels, dropdowns, and controls.

## 8. Validation, CSRF, messages, and errors

The controller's private **validatedData()** method shares create/update rules. Text fields are required with limits matching their database columns. Email must be valid; year level is an integer 1–4; `exists:courses,id` rejects invented course IDs.

```php
$emailRule = Rule::unique('students', 'email');
if ($student) {
    $emailRule->ignore($student);
}
```

Update ignores only the current route-bound model so unchanged unique fields pass. Another student's email/number still fails. Never pass a user-submitted ID to ignore().

`$request->validate(...)` returns only validated fields. On failure it redirects back and flashes errors/input. The shared form uses `@error` for inline feedback and `old($field, $student?->{$field})` to prefer rejected input over the saved value. The null-safe ?-> operator works with PHP 8.1.

HTML required/type=email checks help users; server validation still applies when browser controls are bypassed. Laravel web middleware includes sessions, input trimming, CSRF, and conversion of empty strings to null. `@csrf` inserts the session token; missing/invalid tokens normally yield 419. `@method('PUT')` and `@method('DELETE')` express methods HTML forms cannot send directly.

The layout renders flashed success messages. Missing models use Laravel's 404 handling. Database failures use its exception handler and storage/logs/laravel.log; they must not be disguised as success. An empty courses table shows a warning and disables Save. Course management is outside this small example.

**Test:** submit duplicates/invalid values and check retained input/errors. Save valid data and check success. TESTING.md covers server validation and CSRF.

## 9. Laravel 10 compatibility

Routes live in routes/web.php; middleware is configured in app/Http/Kernel.php; providers include app/Providers/AppServiceProvider.php. Newer tutorials may configure middleware in bootstrap/app.php; do not replace Laravel 10's bootstrap with those examples. Resource routes, anonymous migrations, route binding, @selected, and @disabled work in Laravel 10. This project uses the `$casts` property rather than a newer casts() method example.
