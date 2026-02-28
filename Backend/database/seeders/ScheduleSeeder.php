<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classroom::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        foreach ($classes as $class) {
            foreach ($days as $day) {
                // Each class has 2 schedules per day for simplicity
                Schedule::create([
                    'teacher_id' => $teachers->random()->id,
                    'class_id' => $class->id,
                    'subject_id' => $subjects->random()->id,
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '09:30:00',
                    'room' => 'R.' . rand(101, 110),
                    'notes' => 'Pertemuan rutin',
                ]);

                Schedule::create([
                    'teacher_id' => $teachers->random()->id,
                    'class_id' => $class->id,
                    'subject_id' => $subjects->random()->id,
                    'day_of_week' => $day,
                    'start_time' => '10:00:00',
                    'end_time' => '11:30:00',
                    'room' => 'R.' . rand(101, 110),
                    'notes' => 'Praktikum jika diperlukan',
                ]);
            }
        }
    }
}
