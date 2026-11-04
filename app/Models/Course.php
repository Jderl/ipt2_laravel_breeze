<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    // Eloquent maps Course to the courses table by convention.
    // Allow these attributes when a seeder creates a course using an array.
    protected $fillable = ['code', 'name'];

    // One course has many students, linked through students.course_id.
    // Example: $course->students retrieves the collection of enrolled students.
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
