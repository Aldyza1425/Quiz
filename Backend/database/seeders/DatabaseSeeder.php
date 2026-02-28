<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
            ClassSeeder::class,
            SubjectSeeder::class,
            ScheduleSeeder::class,
            AttendanceSeeder::class,
            ScoreSeeder::class,
        ]);
    }
}
