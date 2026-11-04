# Verification performed — October 28, 2026

The completed project was verified locally using PHP 8.1.10, Laravel 10.50.4, XAMPP MariaDB 10.4.28 through Laravel's MySQL driver, and Bootstrap 5.3.3.

## Automated checks

- `php artisan test`: **8 passed, 75 assertions**, using the separate student_records_demo_test database.
- `php vendor/bin/pint --test`: passed for 58 PHP files.
- `php artisan view:cache`: all Blade templates compiled.
- `php artisan route:list --path=students`: seven resource routes registered.
- `php artisan migrate:status`: all six skeleton/application migrations ran.
- `composer validate --no-check-publish`: valid composer.json.
- `composer check-platform-reqs`: all installed package requirements satisfied.

## Real browser verification

The running localhost application was exercised in headless Microsoft Edge using a temporary Playwright harness, in addition to Laravel feature tests:

- Loaded the styled directory and navigated both pagination pages.
- Created a temporary student through the form and observed the success message.
- Opened details and refreshed to confirm persistence.
- Changed course/year while keeping number/email unchanged; refreshed to confirm saved changes.
- Submitted duplicate number/email and checked both inline errors and retained input.
- Cancelled deletion, refreshed, and confirmed the student remained.
- Confirmed deletion and checked that the previous details URL returned 404.
- Submitted a filled form without its CSRF token and received HTTP 419.
- Checked directory/form at 390px width: no document-wide horizontal overflow; the directory table scrolls inside its responsive container.
- Confirmed local Bootstrap styles loaded and no JavaScript page errors occurred.

Temporary browser records were removed. A final database check confirmed **12 demo students, 3 courses, and zero records from the rejected CSRF submission**. The browser harness and screenshots live in the ignored .browser-tools directory; they are not application dependencies. The reproducible database-backed test suite is in tests/Feature/StudentCrudTest.php.

The development server was left running at http://127.0.0.1:8000/students. Restart it using the command in README.md if the terminal session ends.

## Limits

The supplied lesson PDF was unavailable, so its exact contents could not be checked. Verification used XAMPP's MariaDB server, not a separate Oracle MySQL installation. Laravel 10 has security advisories and required a one-time Composer installation override; this demonstration should remain local with synthetic data.
