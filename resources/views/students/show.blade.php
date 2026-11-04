@extends('layouts.app')
@section('title', 'Student details')
@section('content')
<div class="record-form mx-auto">
    <a href="{{ route('students.index') }}" class="text-decoration-none">&larr; Student directory</a>
    <p class="eyebrow mt-4 mb-2">Student profile</p>
    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
        <div><h1 class="h2 fw-bold">{{ $student->first_name }} {{ $student->last_name }}</h1><span class="student-number text-secondary">{{ $student->student_number }}</span></div>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">Edit student</a>
    </div>
    <div class="card"><div class="card-body p-4">
        <h2 class="h6 border-bottom pb-3 mb-4">Registration details</h2>
        {{-- READ: show() supplies one bound student and its related course for these details. --}}
        <dl class="row mb-0">
            <dt class="col-sm-4">Email address</dt><dd class="col-sm-8">{{ $student->email }}</dd>
            <dt class="col-sm-4">Course</dt><dd class="col-sm-8">{{ $student->course->code }} — {{ $student->course->name }}</dd>
            <dt class="col-sm-4">Year level</dt><dd class="col-sm-8">Year {{ $student->year_level }}</dd>
            <dt class="col-sm-4">Registered</dt><dd class="col-sm-8">{{ $student->created_at->format('M j, Y') }}</dd>
        </dl>
        <div class="border-top pt-3">@include('students._delete')</div>
    </div></div>
</div>
@endsection
