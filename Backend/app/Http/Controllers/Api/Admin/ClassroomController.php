<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function publicList()
    {
        return response()->json(Classroom::select('id', 'name', 'grade_level', 'academic_year')->latest()->get());
    }

    public function index()
    {
        return response()->json(Classroom::with(['homeroomTeacher'])
            ->withCount(['students', 'teachers'])
            ->latest()
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'grade_level' => $class->grade_level,
                    'academic_year' => $class->academic_year,
                    'homeroom_teacher_name' => $class->homeroomTeacher ? $class->homeroomTeacher->name : 'Belum Ada',
                    'students_count' => $class->students_count,
                    'teachers_count' => $class->teachers_count,
                ];
            }));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string',
            'academic_year' => 'required|string',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $classroom = Classroom::create($validated);
        $classroom->load('homeroomTeacher');
        $classroom->loadCount(['students', 'teachers']);

        $mappedClassroom = [
            'id' => $classroom->id,
            'name' => $classroom->name,
            'grade_level' => $classroom->grade_level,
            'academic_year' => $classroom->academic_year,
            'homeroom_teacher_name' => $classroom->homeroomTeacher ? $classroom->homeroomTeacher->name : 'Belum Ada',
            'students_count' => $classroom->students_count,
            'teachers_count' => $classroom->teachers_count,
        ];

        return response()->json([
            'message' => 'Kelas berhasil dibuat',
            'classroom' => $mappedClassroom
        ], 201);
    }

    public function show(Classroom $classroom)
    {
        $classroom->load(['homeroomTeacher', 'students', 'teachers']);
        $classroom->homeroom_teacher_name = $classroom->homeroomTeacher ? $classroom->homeroomTeacher->name : 'Belum Ditentukan';
        return response()->json($classroom);
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'grade_level' => 'sometimes|required|string',
            'academic_year' => 'sometimes|required|string',
            'homeroom_teacher_id' => 'sometimes|nullable|exists:users,id',
        ]);

        $classroom->update($validated);
        $classroom->load('homeroomTeacher');
        $classroom->loadCount(['students', 'teachers']);

        $mappedClassroom = [
            'id' => $classroom->id,
            'name' => $classroom->name,
            'grade_level' => $classroom->grade_level,
            'academic_year' => $classroom->academic_year,
            'homeroom_teacher_name' => $classroom->homeroomTeacher ? $classroom->homeroomTeacher->name : 'Belum Ada',
            'students_count' => $classroom->students_count,
            'teachers_count' => $classroom->teachers_count,
        ];

        return response()->json([
            'message' => 'Kelas berhasil diperbarui',
            'classroom' => $mappedClassroom
        ]);
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
