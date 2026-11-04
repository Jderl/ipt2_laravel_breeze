<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Each array key is a short code; its value is the full dropdown label.
        foreach ([
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSCS' => 'Bachelor of Science in Computer Science',
            'BSIS' => 'Bachelor of Science in Information Systems',
        ] as $code => $name) {
            // Look up by unique code before inserting. Existing courses are left unchanged.
            Course::firstOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
