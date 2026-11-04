<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // DatabaseSeeder runs CourseSeeder first, so these course IDs already exist.
        // A fixed order keeps the sample course assignments predictable between runs.
        $courses = Course::orderBy('code')->get();
        // Synthetic records let students practice CRUD without using personal data.
        $names = [
            ['Ana', 'Cruz'], ['Miguel', 'Santos'], ['Bea', 'Reyes'],
            ['Carlo', 'Garcia'], ['Diana', 'Lopez'], ['Enzo', 'Ramos'],
            ['Faith', 'Torres'], ['Gabriel', 'Flores'], ['Hannah', 'Mendoza'],
            ['Ivan', 'Castro'], ['Julia', 'Rivera'], ['Leo', 'Aquino'],
        ];

        // $index starts at zero; destructuring gives each name its own variable.
        foreach ($names as $index => [$firstName, $lastName]) {
            // %04d pads the number with zeros: 1 becomes 0001, giving 2026-0001.
            // The first array identifies an existing student by student number.
            // The second supplies values only when inserting a new record;
            // rerunning this seeder leaves an existing student's edited values intact.
            Student::firstOrCreate(['student_number' => sprintf('2026-%04d', $index + 1)], [
                'first_name' => $firstName,
                'last_name' => $lastName,
                // Build a lowercase demonstration address from the sample name.
                'email' => strtolower($firstName.'.'.$lastName).'@example.com',
                // Modulo (%) cycles the sample year levels through 1, 2, 3, and 4.
                'year_level' => ($index % 4) + 1,
                // Cycle through available courses and store the selected course's ID.
                // The collection position chooses a course; its database ID is the foreign key.
                'course_id' => $courses[$index % $courses->count()]->id,
            ]);
        }
    }
}
