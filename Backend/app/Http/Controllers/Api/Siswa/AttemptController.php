<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\AttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttemptController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->quizAttempts()->with(['quiz.subject', 'quiz.category'])->latest()->get()
        );
    }

    public function store(Request $request, Quiz $quiz)
    {
        if (!$quiz->is_active) {
            return response()->json(['message' => 'Quiz tidak tersedia'], 404);
        }

        // Cek jika ada attempt yang belum selesai
        $activeAttempt = $request->user()->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->first();

        if ($activeAttempt) {
            return response()->json([
                'message' => 'Lanjutkan pengerjaan quiz',
                'attempt' => $activeAttempt->load(['quiz.questions.options', 'answers'])
            ]);
        }

        $attempt = $request->user()->quizAttempts()->create([
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'total_questions' => $quiz->questions()->count(),
        ]);

        return response()->json([
            'message' => 'Quiz dimulai',
            'attempt' => $attempt->load(['quiz.questions.options', 'answers'])
        ], 201);
    }

    public function answer(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || $attempt->finished_at) {
            return response()->json(['message' => 'Unauthorized or finished'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'nullable|exists:question_options,id',
            'short_answer' => 'nullable|string',
        ]);

        $question = Question::findOrFail($validated['question_id']);

        // Logika sederhana penentuan benar/salah
        $isCorrect = false;
        if ($question->type === 'short_answer') {
            // Untuk short answer, anggap benar jika tidak kosong (user wants this for points)
            $isCorrect = !empty($validated['short_answer']);
        } else if (isset($validated['selected_option_id'])) {
            $option = $question->options()->find($validated['selected_option_id']);
            $isCorrect = $option ? $option->is_correct : false;
        }

        $answer = AttemptAnswer::updateOrCreate(
            ['quiz_attempt_id' => $attempt->id, 'question_id' => $question->id],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'short_answer' => $validated['short_answer'] ?? null,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0
            ]
        );

        return response()->json([
            'message' => 'Jawaban disimpan',
            'answer' => $answer
        ]);
    }

    public function finish(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || $attempt->finished_at) {
            return response()->json(['message' => 'Unauthorized or finished'], 403);
        }

        return DB::transaction(function () use ($attempt) {
            $totalPoints = $attempt->answers()->sum('points_earned');
            $maxPoints = $attempt->quiz->questions()->sum('points');
            $correctCount = $attempt->answers()->where('is_correct', true)->count();

            $score = $maxPoints > 0 ? ($totalPoints / $maxPoints) * 100 : 0;

            $attempt->update([
                'finished_at' => now(),
                'score' => $score,
                'correct_answers' => $correctCount,
            ]);

            return response()->json([
                'message' => 'Quiz selesai',
                'attempt' => $attempt->load('quiz')
            ]);
        });
    }

    public function show(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($attempt->load(['quiz.subject', 'quiz.category', 'quiz.questions.options', 'answers.question']));
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:quiz_attempts,id'
        ]);

        // Pastikan hanya menghapus milik sendiri
        $deletedCount = $request->user()->quizAttempts()
            ->whereIn('id', $validated['ids'])
            ->delete();

        return response()->json([
            'message' => "$deletedCount riwayat berhasil dihapus",
            'deleted_count' => $deletedCount
        ]);
    }
}
