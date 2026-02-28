<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;

        $schedules = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', $teacherId)
            ->get();

        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['teacher_id'] = $request->user()->id;

        $schedule = Schedule::create($validated);

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan',
            'schedule' => $schedule->load(['classroom', 'subject'])
        ], 201);
    }

    public function show(Request $request, Schedule $teaching_schedule)
    {
        if ($teaching_schedule->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($teaching_schedule->load(['classroom', 'subject']));
    }

    public function update(Request $request, Schedule $teaching_schedule)
    {
        if ($teaching_schedule->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'class_id' => 'sometimes|required|exists:classes,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'day_of_week' => 'sometimes|required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'sometimes|required',
            'end_time' => 'sometimes|required',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $teaching_schedule->update($validated);

        return response()->json([
            'message' => 'Jadwal berhasil diperbarui',
            'schedule' => $teaching_schedule->load(['classroom', 'subject'])
        ]);
    }

    public function destroy(Request $request, Schedule $teaching_schedule)
    {
        if ($teaching_schedule->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $teaching_schedule->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }

    public function subjects()
    {
        return response()->json(\App\Models\Subject::all());
    }
}
