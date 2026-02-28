<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Quiz;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Total Kelas (assigned to this teacher)
        $totalClasses = Schedule::where('teacher_id', $user->id)
            ->distinct('class_id')
            ->count('class_id');

        // 2. Total Siswa (in those classes)
        $totalStudents = DB::table('class_user')
            ->whereIn('class_id', function ($query) use ($user) {
                $query->select('class_id')
                    ->from('schedules')
                    ->where('teacher_id', $user->id);
            })
            ->distinct('user_id')
            ->count('user_id');

        // 3. Quiz Aktif
        $activeQuizzes = Quiz::where('created_by', $user->id)
            ->where('is_active', true)
            ->count();

        // 4. Jadwal Hari Ini
        $daysInIndonesian = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $today = $daysInIndonesian[Carbon::now()->format('l')];

        $todaySchedules = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', $user->id)
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        // 5. Aktivitas Terakhir
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_classes' => $totalClasses,
                'total_students' => $totalStudents,
                'active_quizzes' => $activeQuizzes,
                'today_schedules_count' => $todaySchedules->count(),
            ],
            'today_schedules' => $todaySchedules,
            'recent_activities' => $recentActivities
        ]);
    }
}
