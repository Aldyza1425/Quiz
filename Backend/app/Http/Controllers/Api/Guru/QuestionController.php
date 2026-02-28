<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        if ($quiz->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($quiz->questions()->with('options')->get());
    }

    public function show(Quiz $quiz, Question $question)
    {
        if ($quiz->created_by !== auth()->id() || $question->quiz_id !== $quiz->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($question->load('options'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        if ($quiz->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'explanation' => 'nullable|string',
            'order' => 'integer',
            'media_type' => 'nullable|string',
            'media_url' => 'nullable|string',
            'media_start' => 'nullable|integer',
            'media_end' => 'nullable|integer',
            'options' => 'required_if:type,multiple_choice,true_false|array',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($validated, $quiz) {
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'points' => $validated['points'] ?? 1,
                'explanation' => $validated['explanation'] ?? null,
                'media_type' => $validated['media_type'] ?? 'none',
                'media_url' => $validated['media_url'] ?? null,
                'media_start' => $validated['media_start'] ?? null,
                'media_end' => $validated['media_end'] ?? null,
                'order' => $validated['order'] ?? 0,
            ]);

            if (isset($validated['options'])) {
                foreach ($validated['options'] as $idx => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                        'order' => $idx,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Soal berhasil ditambahkan',
                'question' => $question->load('options')
            ], 201);
        });
    }

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        if ($quiz->created_by !== auth()->id() || $question->quiz_id !== $quiz->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'question_text' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:multiple_choice,true_false,short_answer',
            'points' => 'sometimes|integer|min:0',
            'explanation' => 'nullable|string',
            'order' => 'integer',
            'media_type' => 'nullable|string',
            'media_url' => 'nullable|string',
            'media_start' => 'nullable|integer',
            'media_end' => 'nullable|integer',
            'options' => 'sometimes|array',
            'options.*.id' => 'sometimes|exists:question_options,id',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($validated, $question) {
            $question->update($validated);

            if (isset($validated['options'])) {
                // Sederhana: hapus semua opsi lama dan buat baru untuk update
                // Atau bisa logic sinkronisasi yang lebih kompleks
                $question->options()->delete();
                foreach ($validated['options'] as $idx => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                        'order' => $idx,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Soal berhasil diperbarui',
                'question' => $question->load('options')
            ]);
        });
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        if ($quiz->created_by !== auth()->id() || $question->quiz_id !== $quiz->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();

        return response()->json([
            'message' => 'Soal berhasil dihapus'
        ]);
    }
}
