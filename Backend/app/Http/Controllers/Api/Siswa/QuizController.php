<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Category;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $classIds = $user->studentClassrooms()->pluck('classes.id');

        $query = Quiz::where('is_active', true)
            ->where(function ($q) use ($classIds) {
                $q->whereIn('classroom_id', $classIds)
                    ->orWhereNull('classroom_id'); // Optional: show public quizzes too?
            })
            ->with(['category', 'subject', 'creator']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->latest()->get());
    }

    public function show(Quiz $quiz)
    {
        if (!$quiz->is_active) {
            return response()->json(['message' => 'Quiz tidak tersedia'], 404);
        }

        return response()->json($quiz->load(['category', 'subject', 'creator']));
    }

    public function categories()
    {
        return response()->json(Category::all());
    }

    public function subjects()
    {
        return response()->json(\App\Models\Subject::all());
    }
}
