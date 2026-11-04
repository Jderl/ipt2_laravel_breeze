# Setup: Laravel 10, PHP, and MySQL

Use PowerShell in VS Code on Windows. Commands run from the project root unless stated otherwise. The lesson PDF was not included with the attachment; this project follows the sequence in the pasted request.

## 1. Check tools

Install XAMPP, Composer, and VS Code. In XAMPP Control Panel start MySQL. Start Apache too for phpMyAdmin; Artisan's server does not need Apache.

```powershell
where.exe php
php -v
php -m
composer --version
```

Laravel 10 requires PHP 8.1+. PHP 8.1–8.3 is a practical fit for this pinned project. `where.exe php` shows which executable the terminal uses. On this machine it is Laragon's PHP 8.1, even though the database server comes from XAMPP. To choose XAMPP's PHP for a terminal session:

```powershell
$env:Path = 'C:\xampp\php;C:\xampp\mysql\bin;' + $env:Path
php -v
php -m
```

Check PDO, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, fileinfo, curl, and dom. Enable missing extensions in the CLI's `php.ini` (`php --ini` locates it), then reopen the terminal. Composer also checks extension requirements.

**Test:** PHP/Composer print versions and MySQL stays green in XAMPP.

## 2. Create a project OR use this completed project

For a fresh classroom rebuild, run from a parent folder with a new directory name:

```powershell
composer create-project laravel/laravel student-records "10.*" --prefer-dist
cd student-records
code .
php artisan --version
```

`create-project` downloads the skeleton and dependencies. `10.*` selects Laravel 10. `artisan` is Laravel's command-line tool. `code .` opens the folder. Do not run create-project inside this completed workspace.

**Advisory blocker:** Composer currently blocks Laravel 10 releases because of known security advisories. To reproduce this deliberately local teaching setup with recent Composer, use the one-command override:

```powershell
composer create-project laravel/laravel student-records "10.*" --prefer-dist --no-security-blocking
```

If the skeleton was already created before installation stopped, enter that directory and use `composer update --no-security-blocking` instead. This does not fix advisories. The lockfile supplied here pins Laravel 10.50.4. Keep this unauthenticated demo on localhost with synthetic records; upgrade to a supported Laravel version for deployment. No project/global advisory policy was disabled.

For **this completed project**, use:

```powershell
cd E:\laravel-ipt2-demo
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
# Only for a new installation without APP_KEY:
php artisan key:generate
php artisan --version
```

`composer install` reproduces locked versions in `vendor/`. `.env` holds local settings. `key:generate` creates the encryption/session key; avoid regenerating it during a running demonstration.

**Test:** Artisan reports Laravel Framework 10.x.

## 3. Create the database

Open `http://localhost/phpmyadmin`, choose **Databases → Create database**, name it `student_records_demo`, and choose `utf8mb4_unicode_ci`. Or use the database terminal:

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -p
```

At the password prompt, press Enter only if the local account has a blank password. At the SQL prompt:

```sql
CREATE DATABASE IF NOT EXISTS student_records_demo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES;
EXIT;
```

The database is a container; migrations will create its tables. XAMPP commonly ships MariaDB under its MySQL label; this project uses Laravel's MySQL driver for either server.

**Test:** the database appears in phpMyAdmin or SHOW DATABASES.

## 4. Configure the connection

Edit **`.env`**, keeping the generated APP_KEY already present:

```dotenv
APP_NAME="Campus Records"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_records_demo
DB_USERNAME=root
DB_PASSWORD=
```

Use your actual local credentials/port. A blank password is only a common local XAMPP default. `.env` is ignored by Git; `.env.example` is the shareable template. `config/database.php` already reads these variables.

```powershell
php artisan config:clear
php artisan migrate:status
```

Clearing config makes Laravel reread .env. Before first migration, "Migration table not found" is expected. Fix connection-refused, access-denied, or unknown-database errors first.

**Test:** there is no connection error; after the next step, migration status lists completed tables.

## 5. Implement and populate the application

Follow [IMPLEMENTATION.md](IMPLEMENTATION.md) in order: migrations → models/relationships → seeders → controller → routes → actions → Blade → validation/security. All code is already present in this project. To apply it:

```powershell
php artisan migrate --seed
php artisan migrate:status
php artisan route:list --path=students
```

`migrate` runs pending migrations in filename order. `--seed` calls DatabaseSeeder, which adds 3 courses before 12 students. `php artisan db:seed` can be repeated without duplicating unchanged sample records.

**Test:** migrations say Ran; seven routes appear; phpMyAdmin shows courses/students and sample records.

## 6. Start the application

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Keep the terminal open and visit `http://127.0.0.1:8000`. Root redirects to `/students`. Ctrl+C stops the server; MySQL must remain running.

Bootstrap 5.3.3 CSS and its MIT license are local in `public/vendor/bootstrap/`. No npm/Vite build, CDN, or Bootstrap JavaScript is needed. Delete uses the browser's built-in confirm function. Skeleton Vite assets are unused.

**Test:** a styled directory appears; page 2 has two students. Continue with [PRESENTER.md](PRESENTER.md) and [TESTING.md](TESTING.md).
