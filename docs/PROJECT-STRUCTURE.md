# Project structure and responsibilities

This is the Laravel 10 skeleton plus the teaching application. Generated vendor dependencies and runtime cache contents are intentionally grouped rather than listed file by file.

```text
laravel-ipt2-demo/
├── app/
│   ├── Console/Kernel.php                 # Artisan scheduling/commands
│   ├── Exceptions/Handler.php             # Standard exception handling
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php             # Base controller
│   │   │   └── StudentController.php      # Seven CRUD actions + validation
│   │   ├── Kernel.php                     # Laravel 10 middleware registration
│   │   └── Middleware/                    # Sessions, CSRF, trimming, etc.
│   ├── Models/
│   │   ├── Course.php                     # hasMany students
│   │   ├── Student.php                    # belongsTo course; fillable/casts
│   │   └── User.php                       # Unused skeleton authentication model
│   └── Providers/
│       ├── AppServiceProvider.php         # Bootstrap 5 pagination
│       └── other skeleton providers      # Routing, auth, events, broadcasting
├── bootstrap/
│   ├── app.php                            # Laravel 10 application creation
│   └── cache/                             # Generated package/config caches
├── config/                                # Laravel settings, reading .env
│   ├── app.php
│   ├── database.php                       # MySQL driver configuration
│   └── other skeleton configuration       # Sessions, cache, logging, mail, etc.
├── database/
│   ├── factories/UserFactory.php          # Unused skeleton sample factory
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_reset_tokens_table.php
│   │   ├── 2019_08_19_000000_create_failed_jobs_table.php
│   │   ├── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   ├── 2026_01_01_000001_create_courses_table.php
│   │   └── 2026_01_01_000002_create_students_table.php
│   └── seeders/
│       ├── CourseSeeder.php               # Three sample courses
│       ├── StudentSeeder.php              # Twelve synthetic students
│       └── DatabaseSeeder.php             # Calls parent then child seeders
├── docs/
│   ├── SETUP.md
│   ├── IMPLEMENTATION.md
│   ├── PROCESS-FLOW.md
│   ├── PRESENTER.md
│   ├── TESTING.md
│   ├── PROJECT-STRUCTURE.md
│   └── VERIFICATION.md
├── public/                                # The only web document root
│   ├── index.php                          # Front controller into Laravel
│   ├── .htaccess                          # Apache rewrite rules
│   ├── css/app.css                        # Small custom styling
│   ├── vendor/bootstrap/
│   │   ├── bootstrap.min.css              # Local Bootstrap 5.3.3
│   │   └── LICENSE                        # Upstream MIT license
│   └── skeleton favicon/robots files
├── resources/
│   ├── css/app.css                         # Unused skeleton Vite entry
│   ├── js/                                # Unused skeleton Vite entries
│   └── views/
│       ├── welcome.blade.php              # Unused skeleton welcome page
│       ├── layouts/app.blade.php          # Shared page shell and messages
│       └── students/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           ├── show.blade.php
│           ├── _form.blade.php             # Shared create/edit inputs
│           └── _delete.blade.php           # Shared delete/confirmation form
├── routes/
│   ├── web.php                            # Root redirect + resource routes
│   ├── api.php                            # Unused skeleton API route
│   ├── console.php                        # Skeleton Artisan routes
│   └── channels.php                       # Skeleton broadcast routes
├── storage/                               # Writable logs, sessions, compiled views
├── tests/
│   ├── CreatesApplication.php             # Boots Laravel 10 for tests
│   ├── TestCase.php
│   ├── Feature/ExampleTest.php             # Root redirect
│   ├── Feature/StudentCrudTest.php         # Database-backed behavior tests
│   └── Unit/ExampleTest.php                # Skeleton unit example
├── vendor/                                # Composer-installed packages
├── .env                                   # Private local configuration
├── .env.example                           # Shareable demo configuration template
├── .gitignore                             # Excludes secrets/dependencies/runtime data
├── artisan                                # Laravel command-line entry
├── composer.json                          # PHP dependency requirements
├── composer.lock                          # Exact resolved versions
├── phpunit.xml                            # Test suites + separate MySQL test DB
├── package.json / vite.config.js           # Unused skeleton asset tooling
└── README.md                              # Entry point and quick-start links
```

For teaching, concentrate on routes/web.php, StudentController, Student/Course, the two new migrations, seeders, and resources/views/students. The rest is Laravel's supporting framework structure; it need not be explained all at once.
