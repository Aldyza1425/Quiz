<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Score;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;

class ScoreSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classroom::with('students')->get();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();
        $types = ['Tugas', 'Harian', 'Sumatif', 'PTS', 'PAS'];

        foreach ($classes as $class) {
            foreach ($class->students as $student) {
                foreach ($subjects->random(3) as $subject) { // 3 random subjects per student
                    foreach ($types as $type) {
                        Score::create([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'type' => $type,
                            'score' => rand(70, 100),
                            'description' => "$type untuk $subject->name",
                            'period' => 'Semester 1',
                            'created_by' => $teachers->random()->id,
                        ]);
                    }
                }
            }
        }
    }
}
