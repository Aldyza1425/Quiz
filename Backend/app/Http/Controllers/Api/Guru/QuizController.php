<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->quizzesCreated()->with(['category', 'subject'])->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'subject_id' => 'sometimes|nullable|exists:subjects,id',
            'classroom_id' => 'nullable|exists:classes,id',
            'duration_minutes' => 'nullable|integer',
            'duration_seconds' => 'sometimes|required|integer|min:0',
            'random_questions_count' => 'nullable|integer',
            'is_active' => 'boolean',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.points' => 'integer|min:0',
            'questions.*.media_type' => 'nullable|string',
            'questions.*.media_url' => 'nullable|string',
            'questions.*.media_start' => 'nullable|integer',
            'questions.*.media_end' => 'nullable|integer',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.is_correct' => 'required_with:questions.*.options|boolean',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            if (!isset($validated['duration_minutes']) && isset($validated['duration_seconds'])) {
                $validated['duration_minutes'] = ceil($validated['duration_seconds'] / 60);
            }

            $quiz = $request->user()->quizzesCreated()->create(collect($validated)->except('questions')->toArray());

            if (isset($validated['questions'])) {
                foreach ($validated['questions'] as $qIdx => $qData) {
                    $question = $quiz->questions()->create([
                        'question_text' => $qData['question_text'],
                        'type' => $qData['type'],
                        'points' => $qData['points'] ?? 1,
                        'media_type' => $qData['media_type'] ?? 'none',
                        'media_url' => $qData['media_url'] ?? null,
                        'media_start' => $qData['media_start'] ?? null,
                        'media_end' => $qData['media_end'] ?? null,
                        'order' => $qIdx,
                    ]);

                    if (isset($qData['options'])) {
                        foreach ($qData['options'] as $oIdx => $oData) {
                            $question->options()->create([
                                'option_text' => $oData['option_text'],
                                'is_correct' => $oData['is_correct'],
                                'order' => $oIdx,
                            ]);
                        }
                    }
                }
            }


            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'type' => 'success',
                'message' => 'Berhasil membuat quiz baru: ' . $quiz->title,
                'module' => 'quiz',
                'metadata' => ['quiz_id' => $quiz->id]
            ]);

            return response()->json([
                'message' => 'Quiz berhasil dibuat',
                'quiz' => $quiz->load('questions.options')
            ], 201);
        });
    }

    public function show(Quiz $quiz)
    {

        if ($quiz->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($quiz->load(['category', 'subject', 'questions.options']));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'subject_id' => 'sometimes|nullable|exists:subjects,id',
            'classroom_id' => 'sometimes|nullable|exists:classes,id',
            'duration_minutes' => 'nullable|integer',
            'duration_seconds' => 'sometimes|required|integer|min:0',
            'random_questions_count' => 'nullable|integer',
            'is_active' => 'boolean',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.points' => 'integer|min:0',
            'questions.*.media_type' => 'nullable|string',
            'questions.*.media_url' => 'nullable|string',
            'questions.*.media_start' => 'nullable|integer',
            'questions.*.media_end' => 'nullable|integer',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.is_correct' => 'required_with:questions.*.options|boolean',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $quiz) {
            if (!isset($validated['duration_minutes']) && isset($validated['duration_seconds'])) {
                $validated['duration_minutes'] = ceil($validated['duration_seconds'] / 60);
            }

            $quiz->update(collect($validated)->except('questions')->toArray());

            if (isset($validated['questions'])) {

                $quiz->questions()->delete();

                foreach ($validated['questions'] as $qIdx => $qData) {
                    $question = $quiz->questions()->create([
                        'question_text' => $qData['question_text'],
                        'type' => $qData['type'],
                        'points' => $qData['points'] ?? 1,
                        'media_type' => $qData['media_type'] ?? 'none',
                        'media_url' => $qData['media_url'] ?? null,
                        'media_start' => $qData['media_start'] ?? null,
                        'media_end' => $qData['media_end'] ?? null,
                        'order' => $qIdx,
                    ]);

                    if (isset($qData['options'])) {
                        foreach ($qData['options'] as $oIdx => $oData) {
                            $question->options()->create([
                                'option_text' => $oData['option_text'],
                                'is_correct' => $oData['is_correct'],
                                'order' => $oIdx,
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'message' => 'Quiz dan soal berhasil diperbarui',
                'quiz' => $quiz->load('questions.options')
            ]);
        });
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quiz->delete();

        return response()->json([
            'message' => 'Quiz berhasil dihapus'
        ]);
    }
}
