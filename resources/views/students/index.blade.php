@extends('layouts.app')
@section('title', 'Students')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="eyebrow mb-2">Academic administration</p>
        <h1 class="h2 fw-bold mb-2">Student records</h1>
        <p class="text-secondary mb-0">Manage student information and course assignments.</p>
    </div>
    <a href="{{ route('students.create') }}" class="btn btn-primary px-4">+ Add student</a>
</div>
<div class="card overflow-hidden">
    <div class="card-body border-bottom d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-0 fw-semibold">Student directory</h2>
        {{-- total() counts all matching records, not just the ten on the current page. --}}
        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $students->total() }} {{ Str::plural('student', $students->total()) }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <caption class="visually-hidden">Students with their assigned courses and record actions</caption>
            <thead><tr>
                <th scope="col">Student number</th><th scope="col">Name / Email</th>
                <th scope="col">Course</th><th scope="col">Year</th><th scope="col" class="text-end">Actions</th>
            </tr></thead>
            <tbody>
            {{-- READ: index() supplies this page of students with their courses already loaded. --}}
            @forelse ($students as $student)
                <tr>
                    <td class="student-number">{{ $student->student_number }}</td>
                    <td><div class="fw-semibold">{{ $student->last_name }}, {{ $student->first_name }}</div><div class="small text-secondary">{{ $student->email }}</div></td>
                    <td><span class="badge bg-secondary-subtle text-dark">{{ $student->course->code }}</span></td>
                    <td>{{ $student->year_level }}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            {{-- Named routes include this student's ID for details/edit model binding. --}}
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('students.show', $student) }}" aria-label="View {{ $student->first_name }} {{ $student->last_name }}">View</a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('students.edit', $student) }}" aria-label="Edit {{ $student->first_name }} {{ $student->last_name }}">Edit</a>
                            @include('students._delete')
                        </div>
                    </td>
                </tr>
            {{-- forelse reaches this branch when the current page has no student records. --}}
            @empty
                <tr><td colspan="5" class="text-center py-5"><h3 class="h5">No students yet</h3><p class="text-secondary">Add your first student to start the directory.</p><a class="btn btn-primary" href="{{ route('students.create') }}">Add student</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- links() builds navigation to other pages; the controller sets the page size. --}}
    @if ($students->hasPages())
        <div class="card-body border-top">{{ $students->links() }}</div>
    @endif
</div>
@endsection
