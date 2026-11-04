@extends('layouts.app')
@section('title', 'Edit student')
@section('content')
<div class="record-form mx-auto">
    <a href="{{ route('students.index') }}" class="text-decoration-none">&larr; Student directory</a>
    <p class="eyebrow mt-4 mb-2">Update record · {{ $student->student_number }}</p>
    <h1 class="h2 fw-bold">Edit student</h1>
    <p class="text-secondary mb-4">Update student information, then save your changes.</p>
    <div class="card"><div class="card-body p-4">
        {{-- UPDATE: include the current student's ID in the URL so Laravel binds the correct row. --}}
        <form action="{{ route('students.update', $student) }}" method="POST">
            {{-- HTML has no PUT form method; Laravel reads this hidden field. --}}
            @method('PUT')
            @include('students._form', ['submitLabel' => 'Save changes'])
        </form>
    </div></div>
</div>
@endsection
