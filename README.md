# Student Records CRUD — Laravel 10 classroom demo

A complete beginner-oriented MVC example using Laravel 10, Eloquent, MySQL/MariaDB, Blade, and Bootstrap 5. Includes student CRUD, Course → Students relationships, validation, CSRF protection, pagination, local styling, and 12 synthetic students.

## Run this project

Start MySQL in XAMPP. The local .env uses student_records_demo on 127.0.0.1:3306 with the local root account and a blank password. Adjust these values for your installation.

```powershell
cd E:\laravel-ipt2-demo
composer install
# On another machine, copy .env.example to .env and run php artisan key:generate.
# Create student_records_demo in phpMyAdmin before migrating.
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Open **http://127.0.0.1:8000/students**. No npm build or internet connection is required after dependency installation.

## Understand the flow: database → models → controller → views → routes

Use this order to study how the files fit together. During an actual browser request, the **route runs before the controller**; the database does not call a view or route directly.

| Layer | File in this project | Responsibility |
| --- | --- | --- |
| Database configuration | [.env.example](.env.example), [config/database.php](config/database.php) | `.env` supplies the connection settings that Laravel's database configuration reads. Put your local credentials in `.env`. |
| Database tables | [Courses migration](database/migrations/2026_01_01_000001_create_courses_table.php), [students migration](database/migrations/2026_01_01_000002_create_students_table.php) | Define columns, unique student numbers/emails, and the course foreign key. Create courses before students. |
| Sample data | [DatabaseSeeder](database/seeders/DatabaseSeeder.php), [CourseSeeder](database/seeders/CourseSeeder.php), [StudentSeeder](database/seeders/StudentSeeder.php) | Insert courses first, then students who reference those courses. |
| Eloquent models | [Course](app/Models/Course.php), [Student](app/Models/Student.php) | Represent rows as PHP objects. Course has many students; each student belongs to one course. |
| Controller | [StudentController](app/Http/Controllers/StudentController.php) | Read or validate input, use the models, and return a view or redirect. |
| Views | [Directory](resources/views/students/index.blade.php), [create](resources/views/students/create.blade.php), [details](resources/views/students/show.blade.php), [edit](resources/views/students/edit.blade.php) | Display records and forms. Shared [_form](resources/views/students/_form.blade.php) and [_delete](resources/views/students/_delete.blade.php) partials handle reusable markup. |
| Routes | [routes/web.php](routes/web.php) | Match each HTTP method/URL to the appropriate controller action. |

The database relationship is:

See the [ERD with database columns, keys, and cardinality](docs/ERD.md) for the diagram based on the actual database, including the supporting Laravel tables.

```text
courses.id (one course) ← students.course_id (many students)
```

For example, `$student->course->name` follows `course_id` to the related course. The view displays a course name while forms submit its ID.

### Actual request flow for each CRUD operation

```text
Browser → Route → Controller → Eloquent model → MySQL
Browser ← Rendered Blade view ← Controller ← Eloquent results
```

| Operation | What happens from click to response |
| --- | --- |
| **Create** | Add student → GET `/students/create` → `create()` loads courses → Blade form. Save → POST `/students` → `store()` validates → `Student::create()` inserts into MySQL → redirect → GET directory → Blade displays success. |
| **Read list** | Open directory → GET `/students` → `index()` → `Student::with('course')->paginate(10)` reads MySQL → controller passes results to `students/index.blade.php` → browser shows rows. The actual query also sorts by last name, first name, and ID. |
| **Read details** | View → GET `/students/{student}` → route binding retrieves the student → `show()` loads its course → `students/show.blade.php` displays details. Missing IDs return 404. |
| **Update** | Edit → GET `/students/{student}/edit` → `edit()` loads the student/course choices → populated form. Save → PUT `/students/{student}` → `update()` validates → `$student->update()` saves to MySQL → redirect → GET details shows saved values. |
| **Delete** | Delete → browser confirmation → Cancel sends nothing; OK submits DELETE `/students/{student}` → `destroy()` → `$student->delete()` removes the row → redirect → GET directory shows remaining records. The course remains. |

After create, update, or delete, the redirect starts a **new GET request** that reads the saved data and renders Blade. If validation fails, Laravel returns to the form with errors and previous input, without saving the invalid changes.

## Build the CRUD from scratch: commands in order

These commands are a **rebuild walkthrough for a new project folder**. This workspace already contains the implementation; use the quick start above to run it. Generator commands create scaffolding, so fill the generated files with the linked project implementations before moving to the next step.

### 1. Verify PHP and Composer, then create Laravel 10

Start MySQL in XAMPP. In a VS Code PowerShell terminal, run from the parent directory where you want the new project:

```powershell
where.exe php
php -v
php -m
composer --version
composer create-project laravel/laravel student-records "10.*" --prefer-dist
cd student-records
code .
php artisan --version
```

PHP must satisfy the project's PHP 8.1+ requirement and have `pdo_mysql` enabled. `create-project` installs the framework; `10.*` selects the required major version. Artisan is Laravel's command-line tool.

If Composer blocks this legacy version because of security advisories, the local-demo-only override is documented in [setup step 2](docs/SETUP.md#2-create-a-project-or-use-this-completed-project). It does not resolve those advisories.

**Check:** Artisan reports Laravel Framework 10.x.

### 2. Create the database and configure .env

Open the MySQL client using your local credentials:

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -p
```

Enter your password when prompted; press Enter only if the account has no password. Run the following inside the **MySQL client**, not PowerShell:

```sql
CREATE DATABASE IF NOT EXISTS student_records_demo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES;
EXIT;
```

Back in PowerShell, ensure `.env` exists:

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
```

Edit these values in **`.env`**, using your actual port and credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_records_demo
DB_USERNAME=root
DB_PASSWORD=
```

Generate a key only if the new installation's `APP_KEY` is blank, then clear cached configuration:

```powershell
php artisan key:generate
php artisan config:clear
```

**Check:** the database appears in phpMyAdmin. The migration command in the next step should connect without errors.

### 3. Generate and implement the database migrations

```powershell
php artisan make:migration create_courses_table
php artisan make:migration create_students_table
```

Edit the generated files in **`database/migrations/`** using the courses/students migrations linked above. Generated timestamps will differ from this project's filenames; keep courses before students and use one migration per table. Then apply the schema:

```powershell
php artisan migrate
php artisan migrate:status
```

**Check:** both tables exist and migrations show `Ran`. Courses contain code/name; students contain the student fields and `course_id`.

### 4. Generate and implement the Eloquent models

```powershell
php artisan make:model Course
php artisan make:model Student
```

Edit **`app/Models/Course.php`** and **`app/Models/Student.php`** using this project's definitions. Add `$fillable`, `Course::students()` with `hasMany()`, and `Student::course()` with `belongsTo()`. These relationships use `students.course_id`.

**Check:** run `php artisan tinker`, enter `App\Models\Student::count();`, then `exit`. On the new empty database, the result should be 0.

### 5. Generate seeders and add demonstration data

```powershell
php artisan make:seeder CourseSeeder
php artisan make:seeder StudentSeeder
```

Implement **`database/seeders/CourseSeeder.php`** and **`StudentSeeder.php`** from the linked source. Update the existing **`DatabaseSeeder.php`** to call them in order:

```php
$this->call([CourseSeeder::class, StudentSeeder::class]);
```

Then populate the tables:

```powershell
php artisan db:seed
php artisan tinker
```

Inside Tinker, check the records and relationship:

```php
App\Models\Course::count(); // 3
App\Models\Student::count(); // 12
App\Models\Student::with('course')->first()->course->name;
exit
```

`firstOrCreate()` makes the supplied seeders repeatable without duplicating unchanged sample records. For future setup on an empty database, `php artisan migrate --seed` applies migrations and runs these seeders together.

### 6. Generate the controller and implement all seven actions

```powershell
php artisan make:controller StudentController --resource --model=Student
```

Implement **`app/Http/Controllers/StudentController.php`** from this project's commented source: `index`, `create`, `store`, `show`, `edit`, `update`, and `destroy`. Include its shared `validatedData()` method for required fields, unique email/number, year range, and existing course ID. On update, unique rules ignore only the current bound student.

```powershell
php -l app/Http/Controllers/StudentController.php
```

**Check:** no PHP syntax errors. The generated controller alone does not implement CRUD; the method bodies provide the behavior.

### 7. Connect the routes to the controller

Edit **`routes/web.php`**:

```php
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/students');
Route::resource('students', StudentController::class);
```

Keep each import only once. The resource declaration registers these seven routes:

| HTTP method | URL | Controller action | Route name |
| --- | --- | --- | --- |
| GET | `/students` | `index()` | `students.index` |
| GET | `/students/create` | `create()` | `students.create` |
| POST | `/students` | `store()` | `students.store` |
| GET | `/students/{student}` | `show()` | `students.show` |
| GET | `/students/{student}/edit` | `edit()` | `students.edit` |
| PUT/PATCH | `/students/{student}` | `update()` | `students.update` |
| DELETE | `/students/{student}` | `destroy()` | `students.destroy` |

```powershell
php artisan route:list --path=students
```

**Check:** seven student routes appear. `{student}` is the ID Laravel resolves to `Student $student` through route model binding.

### 8. Create the Blade views and local styles

In the **new rebuild folder**, create the empty view and stylesheet files:

```powershell
New-Item -ItemType Directory -Force -Path resources/views/layouts, resources/views/students, public/css, public/vendor/bootstrap
New-Item -ItemType File -Path resources/views/layouts/app.blade.php
New-Item -ItemType File -Path resources/views/students/index.blade.php, resources/views/students/create.blade.php, resources/views/students/show.blade.php, resources/views/students/edit.blade.php, resources/views/students/_form.blade.php, resources/views/students/_delete.blade.php
New-Item -ItemType File -Path public/css/app.css
```

Fill them with the corresponding files from this project. Use [the shared layout](resources/views/layouts/app.blade.php) and [custom stylesheet](public/css/app.css) along with the linked student views. Download Bootstrap locally for the layout's CSS link:

```powershell
Invoke-WebRequest -Uri 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' -OutFile public/vendor/bootstrap/bootstrap.min.css
Invoke-WebRequest -Uri 'https://raw.githubusercontent.com/twbs/bootstrap/v5.3.3/LICENSE' -OutFile public/vendor/bootstrap/LICENSE
```

In **`app/Providers/AppServiceProvider.php`**, import `Illuminate\Pagination\Paginator` and add `Paginator::useBootstrapFive();` inside `boot()`, as shown in [this project's provider](app/Providers/AppServiceProvider.php).

Ensure forms include `@csrf`. Edit uses `@method('PUT')`; delete uses `@method('DELETE')` and a confirmation prompt. The shared form uses `old()` and `@error`; the layout displays the success message. No additional generator command is needed for those Blade directives.

```powershell
php artisan view:cache
```

**Check:** Blade compiles successfully. Once started below, the pages should show Bootstrap styling, course choices, and working action buttons.

### 9. Run and verify the complete CRUD

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Open **http://127.0.0.1:8000/students** and keep the terminal running. In another terminal, you can inspect records with `php artisan tinker` or check migrations/routes using the commands above.

1. **Create:** Add a student with a new number/email and an existing course; check success.
2. **Read:** Find the record in the directory and click View.
3. **Update:** Click Edit, change year/course, save, and refresh to verify persistence.
4. **Validate:** Attempt duplicate number/email; confirm errors and retained form input.
5. **Delete:** Try Cancel first, then confirm deletion; refresh and verify the record is gone.

See [the exact browser demonstration](docs/PRESENTER.md) for sample values and click-by-click instructions. The separate test database and commands for the supplied automated suite are documented in [testing](docs/TESTING.md); copy this project's tests and `phpunit.xml` as well if reproducing that suite in a fresh skeleton.

## Teaching materials

1. [Windows/XAMPP setup](docs/SETUP.md): exact commands, configuration, and checkpoints.
2. [Implementation walkthrough](docs/IMPLEMENTATION.md): file paths, code explanations, and teaching order.
3. [CRUD request flows](docs/PROCESS-FLOW.md): follow each operation through MVC and the database.
4. [Presenter scripts and browser demonstration](docs/PRESENTER.md).
5. [Testing checklist and common errors](docs/TESTING.md).
6. [Project structure and file responsibilities](docs/PROJECT-STRUCTURE.md).
7. [Database ERD](docs/ERD.md): students, courses, keys, and supporting Laravel tables.

See [verification results](docs/VERIFICATION.md) for the automated checks and real browser workflow completed on this machine.

The referenced Week 14 PDF was not supplied. These materials follow the detailed pasted request and do not claim to reproduce the PDF.

## Version and scope

The lockfile pins **Laravel 10.50.4**, with **PHP 8.1+** and **Bootstrap 5.3.3**. Laravel 10 has known security advisories; installation needed a one-command Composer advisory-blocker override. The blocker was not disabled in project/global configuration. This is an unauthenticated localhost teaching demo with synthetic data. Upgrade to a supported Laravel version before a real deployment.

## Checks

Create the separate disposable student_records_demo_test database first; see [testing instructions](docs/TESTING.md). The feature tests use the MySQL driver.

```powershell
php artisan config:clear
php artisan test
php vendor/bin/pint --test
```

The main teaching idea: the route chooses the controller, the controller validates and uses Eloquent, and Blade renders the result after the database operation. Start with [the request flow](docs/PROCESS-FLOW.md).
