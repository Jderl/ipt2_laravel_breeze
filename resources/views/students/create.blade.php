@extends('layouts.app')
@section('title', 'Add student')
@section('content')
<div class="record-form mx-auto">
    <a href="{{ route('students.index') }}" class="text-decoration-none">&larr; Student directory</a>
    <p class="eyebrow mt-4 mb-2">New record</p>
    <h1 class="h2 fw-bold">Add student</h1>
    <p class="text-secondary mb-4">Register a student and assign their course.</p>
    <div class="card"><div class="card-body p-4">
        {{-- CREATE: submitting sends POST /students to StudentController::store(). --}}
        <form action="{{ route('students.store') }}" method="POST">
            @include('students._form', ['student' => null, 'submitLabel' => 'Save student'])
        </form>
    </div></div>
</div>
@endsection
