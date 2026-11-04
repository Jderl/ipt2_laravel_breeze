# Testing and troubleshooting

## Automated integration tests

The feature tests in `tests/Feature/StudentCrudTest.php` use Laravel's real HTTP kernel, Eloquent, and the MySQL driver. They cover create/read/edit/delete, course reassignment, relationships, unchanged unique fields on edit, duplicate rejection on create/update, invalid/empty fields, retained old input/errors, missing-record 404s, pagination, repeatable seeding, empty states, and escaped output.

Create a **separate disposable test database** in phpMyAdmin named `student_records_demo_test`, or open the MySQL terminal as described in SETUP.md and execute:

```sql
CREATE DATABASE IF NOT EXISTS student_records_demo_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

`phpunit.xml` forces DB_CONNECTION=mysql and DB_DATABASE=student_records_demo_test. The host/port/credentials come from your local .env. If you need different test credentials, copy .env.example to .env.testing, configure it, and generate its key with `php artisan key:generate --env=testing`. RefreshDatabase migrates/reset tables in the test database; never point this test configuration at a database with records you want to retain. The database name is intentionally separate from student_records_demo.

```powershell
php artisan config:clear
php artisan test
php vendor/bin/pint --test
php artisan view:cache
php artisan route:list --path=students
composer validate --no-check-publish
```

Config clearing prevents a cached demo connection from interfering with tests. Tests check behavior and persisted rows; Pint checks PHP formatting; view:cache compiles Blade; route:list verifies resource routing; Composer validation checks package metadata. Laravel normally disables CSRF in automated feature tests, so verify it through a real browser/server request as described below.

## Browser checklist

Use the exact sample entries and click sequence in PRESENTER.md.

- [ ] Open /students: 12 sample records, 10 on page 1 and 2 on page 2.
- [ ] Add Maria with DEMO-2026-001 and maria.demo@example.com: success appears and total becomes 13.
- [ ] Refresh: total stays 13; no second POST occurs.
- [ ] View Maria: all details and the correct course name appear.
- [ ] Edit Maria: current values are prefilled; change year/course; save and refresh to confirm persistence.
- [ ] Keep the same email/number during edit: save succeeds.
- [ ] Attempt another student's email/number on create and update: both fields show errors; stored data is unchanged.
- [ ] Empty required fields and invalid email: browser blocks submission; server tests independently reject them.
- [ ] Year outside 1–4 and nonexistent course ID: server rejects both.
- [ ] Failed submission retains entered values and selected dropdown choices.
- [ ] Success and error messages are visible and understandable.
- [ ] Delete → Cancel: record remains and no write request is sent.
- [ ] Delete → OK: success appears; refresh confirms removal and course remains.
- [ ] Open deleted/missing ID: HTTP 404.
- [ ] Narrow the viewport to about 390px: fields stack and the table scrolls horizontally.
- [ ] Tab through form controls: labels/focus work; no control requires a mouse.
- [ ] Disable network after loading local dependencies: Bootstrap styling still loads from this app.

To see **server** required/email errors in the browser, open DevTools on the Add student form and run `document.querySelector('main form').noValidate = true`. Submit the empty form, then invalid data. This only bypasses browser checks; the server must still reject the request. Reload afterward to restore normal browser behavior.

To check **CSRF**, on a fresh Add student page use DevTools to remove the hidden _token input, complete valid fields, and submit. Expect HTTP 419 and no new record. Reload to obtain a valid token. Do not disable the CSRF middleware to fix this expected result.

## Common errors

| Symptom | Likely cause | Fix |
| --- | --- | --- |
| php/composer not recognized | Missing executable or PATH | Install tool; reopen terminal; use where.exe php and composer --version. |
| Wrong PHP version/extensions | Terminal uses a different PHP than Apache | Check where.exe php and php --ini; select correct PHP path and enable required extensions. |
| Composer refuses Laravel 10 advisories | Legacy Laravel release affected by advisories | For this local demo only use the one-command override in SETUP.md; do not disable blocking globally. Use supported Laravel for deployment. |
| vendor/autoload.php missing | Dependencies not installed | Run composer install from the directory containing composer.json. |
| No application encryption key | APP_KEY empty or .env missing | Copy .env.example if needed; run php artisan key:generate, then config:clear. |
| Could not find driver | CLI pdo_mysql disabled | Enable extension=pdo_mysql in CLI php.ini; reopen terminal. |
| SQLSTATE 2002 connection refused | Server stopped or wrong host/port | Start MySQL in XAMPP; compare its port with DB_PORT; config:clear. |
| SQLSTATE 1045 access denied | Wrong credentials | Correct DB_USERNAME/DB_PASSWORD; verify with phpMyAdmin/MySQL client. |
| SQLSTATE 1049 unknown database | Database not created | Create student_records_demo; confirm DB_DATABASE spelling. |
| Table students/courses does not exist | Pending migrations or wrong database | config:clear; migrate:status; migrate --seed. |
| Foreign-key error | Students migration ran before courses, or invalid course ID | Preserve migration order; seed courses first; use real course choices. |
| Empty course dropdown | CourseSeeder not run | php artisan db:seed --class=CourseSeeder, then reload form. |
| MassAssignmentException | Missing permitted model fields | Compare Student::$fillable with validated fields; do not globally unguard models. |
| Duplicate-key SQL error | Write outside normal validation, conflicting seed data, or concurrent duplicate insert | Check number/email conflicts; preserve unique indexes; use validated form flow. |
| 419 Page Expired | Missing/stale token or session mismatch | Reload, ensure @csrf exists, use the same hostname consistently; check storage permissions. |
| 405 Method Not Allowed | Wrong form method/action or missing @method | Store is POST; edit/delete use POST plus hidden PUT/DELETE; compare route:list. |
| Route/view not found | Typo or wrong path | Compare route names and resources/views/students; route:clear and view:clear after corrections. |
| 404 for a student | Deleted/nonexistent ID | Return to the list and choose an existing record. |
| No Bootstrap styling | Missing local CSS file or wrong document root | Check /vendor/bootstrap/bootstrap.min.css; run artisan serve from root. Apache document root must be public/. |
| Port 8000 occupied | Another server is listening | Stop that server or use php artisan serve --port=8001 and visit the matching URL. |
| Test database missing | Test database not created | Create student_records_demo_test; keep it separate; config:clear and retry. |
| storage/cache permission error | PHP cannot write runtime files | Grant the local PHP process access to storage and bootstrap/cache. |

Read `storage/logs/laravel.log` for actual exception details. APP_DEBUG=true is for the local demo. Avoid `migrate:fresh` as a routine fix: it drops all tables on the selected connection. The supplied setup uses ordinary migrate and seed commands.

## Final verification before class

- [ ] PHP version/extensions and Composer requirements pass.
- [ ] MySQL is running and .env selects the demo database.
- [ ] APP_KEY exists; configuration cache is clear.
- [ ] All six supplied migrations show Ran; courses/students have sample data.
- [ ] Seven resource routes match the implementation table.
- [ ] Automated checks above pass using the disposable test database.
- [ ] Browser checklist passes, including delete cancellation and CSRF.
- [ ] Bootstrap CSS is local; classroom internet is unnecessary after setup.
- [ ] Presenter sample number/email are free, or the prior demo record has been removed.
- [ ] The server terminal stays open; PROCESS-FLOW.md and PRESENTER.md are ready.
