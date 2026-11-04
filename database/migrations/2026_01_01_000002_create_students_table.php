<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration defines structure; seeders or the CRUD form insert the rows.
        Schema::create('students', function (Blueprint $table) {
            // Routes use this internal ID; student_number is the separate school identifier.
            $table->id();
            // Constraints also protect data written outside the HTML form.
            $table->string('student_number', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            // This column stores a nonnegative integer; controller validation limits it to 1-4.
            $table->unsignedTinyInteger('year_level');
            // Create courses first. A course with students cannot be deleted.
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            // Track when a student was registered and most recently updated.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Reversing this migration drops the table and its student records.
        Schema::dropIfExists('students');
    }
};
