{{-- Browsers submit POST; Laravel reads the hidden _method field as DELETE. --}}
{{-- Cancel stops submission in the browser. OK sends the protected request to destroy(). --}}
<form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Delete this student record? This cannot be undone.');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete {{ $student->first_name }} {{ $student->last_name }}">Delete</button>
</form>
