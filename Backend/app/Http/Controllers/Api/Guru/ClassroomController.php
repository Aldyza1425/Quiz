<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{

    public function index(Request $request)
    {
        $classrooms = \App\Models\Classroom::withCount('students')->get();

        return response()->json($classrooms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom = $request->user()->classrooms()->create($validated);


        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'type' => 'info',
            'message' => 'Berhasil membuat kelas baru: ' . $classroom->name,
            'module' => 'classroom',
            'metadata' => ['classroom_id' => $classroom->id]
        ]);

        return response()->json([
            'message' => 'Kelas berhasil dibuat',
            'classroom' => $classroom
        ], 201);
    }


    public function show(Request $request, string $id)
    {
        $user = $request->user();

        $isScheduled = \App\Models\Schedule::where('teacher_id', $user->id)
            ->where('class_id', $id)
            ->exists();

        $isHomeroom = \App\Models\Classroom::where('id', $id)
            ->where('homeroom_teacher_id', $user->id)
            ->exists();

        if (!$isScheduled && !$isHomeroom) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $classroom = \App\Models\Classroom::with('students')->findOrFail($id);
        return response()->json($classroom);
    }


    public function update(Request $request, string $id)
    {

    }


    public function destroy(string $id)
    {

    }
}
