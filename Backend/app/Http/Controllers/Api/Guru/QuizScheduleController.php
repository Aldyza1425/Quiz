<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuizScheduleController extends Controller
{

    public function index(Request $request)
    {
        return response()->json(
            \App\Models\QuizSchedule::whereHas('quiz', function ($q) use ($request) {
                $q->where('created_by', $request->user()->id);
            })->with(['quiz', 'classroom'])->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'classroom_id' => 'required|exists:classes,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $schedule = \App\Models\QuizSchedule::create($validated);


        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'type' => 'warning', // warning for assignment/schedule
            'message' => 'Berhasil membuat jadwal quiz untuk kelas: ' . $schedule->classroom->name,
            'module' => 'schedule',
            'metadata' => ['schedule_id' => $schedule->id, 'quiz_id' => $schedule->quiz_id]
        ]);

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan',
            'schedule' => $schedule
        ], 201);
    }


    public function show(string $id)
    {

    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
