<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // migrate runs up() to create the parent table before students references it.
        Schema::create('courses', function (Blueprint $table) {
            // The primary key is the value stored in students.course_id.
            $table->id();
            // Prevent duplicate course codes such as two separate BSIT entries.
            $table->string('code', 20)->unique();
            $table->string('name');
            // Eloquent automatically maintains created_at and updated_at.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Rollback reverses the schema change; student references must be removed first.
        Schema::dropIfExists('courses');
    }
};
