<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\User;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::where('role', 'guru')->get();
        $teacherIds = $teachers->pluck('id')->toArray();

        $classes = [
            ['name' => 'X IPA 1', 'grade_level' => 'X', 'academic_year' => '2025/2026', 'homeroom_teacher_id' => $teacherIds[0] ?? null],
            ['name' => 'X IPA 2', 'grade_level' => 'X', 'academic_year' => '2025/2026', 'homeroom_teacher_id' => $teacherIds[1] ?? null],
            ['name' => 'XI IPS 1', 'grade_level' => 'XI', 'academic_year' => '2025/2026', 'homeroom_teacher_id' => $teacherIds[2] ?? null],
            ['name' => 'XI IPS 2', 'grade_level' => 'XI', 'academic_year' => '2025/2026', 'homeroom_teacher_id' => $teacherIds[3] ?? null],
            ['name' => 'XII IPA 1', 'grade_level' => 'XII', 'academic_year' => '2025/2026', 'homeroom_teacher_id' => $teacherIds[4] ?? null],
        ];

        foreach ($classes as $class) {
            $createdClass = Classroom::create($class);

            // Assign some students to each class
            $students = User::where('role', 'siswa')->inRandomOrder()->limit(10)->pluck('id');
            $createdClass->students()->attach($students);
        }
    }
}
