<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // php artisan db:seed calls this entry point; migrate --seed calls it after migrations.
        // Parent records must exist before students can reference their IDs.
        $this->call([CourseSeeder::class, StudentSeeder::class]);
    }
}
