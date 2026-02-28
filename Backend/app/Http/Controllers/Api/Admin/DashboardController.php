<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_guru' => User::where('role', 'guru')->count(),
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_quiz' => Quiz::count(),
            'active_quiz' => Quiz::where('is_active', true)->count(),
            'total_attempts' => QuizAttempt::count(),
            'avg_score' => QuizAttempt::whereNotNull('score')->avg('score') ?? 0,
        ];

        $recent_activity = QuizAttempt::with(['user', 'quiz'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($attempt) {
                return [
                    'user' => $attempt->user->name,
                    'quiz' => $attempt->quiz->title,
                    'score' => $attempt->score,
                    'status' => $attempt->score >= 70 ? 'Passed' : 'Failed',
                    'time' => $attempt->created_at->diffForHumans(),
                ];
            });

        $top_quizzes = Quiz::withCount('attempts')
            ->orderBy('attempts_count', 'desc')
            ->take(3)
            ->get()
            ->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'participants' => $quiz->attempts_count,
                    'category' => $quiz->category?->name ?? 'Umum',
                ];
            });

        return response()->json([
            'stats' => $stats,
            'recent_activity' => $recent_activity,
            'top_quizzes' => $top_quizzes
        ]);
    }
}
