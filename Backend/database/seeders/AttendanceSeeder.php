<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classroom::with('students')->get();
        $teachers = User::where('role', 'guru')->get();

        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();

        foreach ($classes as $class) {
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                if ($currentDate->isWeekday()) {
                    foreach ($class->students as $student) {
                        $statusChance = rand(1, 100);
                        $status = 'Hadir';
                        if ($statusChance > 95)
                            $status = 'Alpha';
                        elseif ($statusChance > 90)
                            $status = 'Sakit';
                        elseif ($statusChance > 85)
                            $status = 'Izin';

                        Attendance::create([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'date' => $currentDate->format('Y-m-d'),
                            'status' => $status,
                            'notes' => $status !== 'Hadir' ? 'Alasan tertentu' : null,
                            'created_by' => $teachers->random()->id,
                        ]);
                    }
                }
                $currentDate->addDay();
            }
        }
    }
}
