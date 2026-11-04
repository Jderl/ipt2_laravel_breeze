<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Student Records') | Campus Records</title>
    {{-- asset() builds URLs to files inside public/; local Bootstrap works without a CDN. --}}
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<a class="visually-hidden-focusable" href="#main">Skip to content</a>
<nav class="navbar navbar-dark bg-dark py-3" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('students.index') }}">Campus<span class="text-info"> / </span>Records</a>
        <span class="text-white-50 small">Student management · Classroom demo</span>
    </div>
</nav>
<main id="main" class="container py-4 py-md-5">
    {{-- Write actions flash this message before redirecting; the next page displays it. --}}
    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    {{-- Each CRUD page supplies its own content section while sharing this layout. --}}
    @yield('content')
</main>
<footer class="container pb-4 text-secondary small">Student Records Management System · Laravel 10 / Eloquent / Bootstrap 5</footer>
</body>
</html>
