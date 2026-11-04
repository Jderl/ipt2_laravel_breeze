<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    // Eloquent maps Student to students; the default primary key is id.
    // Only these fields may be assigned together by create() or update().
    protected $fillable = [
        'student_number', 'first_name', 'last_name', 'email', 'year_level', 'course_id',
    ];

    // Read year_level as a PHP integer when working with a retrieved model.
    protected $casts = ['year_level' => 'integer'];

    // Eloquent uses course_id to find the student's course.
    // Example: $student->course->name reads the name from the related courses row.
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
