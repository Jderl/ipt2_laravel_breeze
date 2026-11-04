{{-- CSRF protects write requests. old() restores input when validation fails. --}}
@csrf
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <p class="fw-semibold mb-1">Please correct the following fields.</p>
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
@if ($courses->isEmpty())
    <div class="alert alert-warning" role="alert">No courses are available. Ask the demo administrator to seed the courses before adding students.</div>
@endif
<p class="small text-secondary">All fields are required. Year level must be between 1 and 4.</p>
<div class="row g-3">
    {{-- Both CREATE and UPDATE reuse these inputs. old() keeps submitted values after errors;
         otherwise the bound student's values fill the edit form, or null leaves create blank. --}}
    @foreach (['student_number' => ['Student number', 'text', 30], 'first_name' => ['First name', 'text', 100], 'last_name' => ['Last name', 'text', 100], 'email' => ['Email address', 'email', 255]] as $field => [$label, $type, $limit])
        <div class="col-md-6">
            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
            <input type="{{ $type }}" id="{{ $field }}" name="{{ $field }}" maxlength="{{ $limit }}" required
                   class="form-control @error($field) is-invalid @enderror"
                   value="{{ old($field, $student?->{$field}) }}"
                   @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror>
            @error($field)<div id="{{ $field }}-error" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
    <div class="col-md-6">
        <label class="form-label" for="year_level">Year level</label>
        <select id="year_level" name="year_level" required class="form-select @error('year_level') is-invalid @enderror" @error('year_level') aria-invalid="true" aria-describedby="year_level-error" @enderror>
            <option value="">Select year level</option>
            @foreach (range(1, 4) as $year)
                <option value="{{ $year }}" @selected((string) old('year_level', $student?->year_level) === (string) $year)>Year {{ $year }}</option>
            @endforeach
        </select>
        @error('year_level')<div id="year_level-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="course_id">Course</label>
        {{-- Store only the course's primary key; the relationship retrieves its name for READ views. --}}
        <select id="course_id" name="course_id" required class="form-select @error('course_id') is-invalid @enderror" @error('course_id') aria-invalid="true" aria-describedby="course_id-error" @enderror>
            <option value="">Select course</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('course_id', $student?->course_id) === (string) $course->id)>{{ $course->code }} — {{ $course->name }}</option>
            @endforeach
        </select>
        @error('course_id')<div id="course_id-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="d-flex gap-2 mt-4 pt-3 border-top">
    <button class="btn btn-primary px-4" type="submit" @disabled($courses->isEmpty())>{{ $submitLabel }}</button>
    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
