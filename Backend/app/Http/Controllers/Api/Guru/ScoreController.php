<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $classroom = Classroom::with('students')->findOrFail($request->class_id);

        $scores = collect();
        if ($request->subject_id) {
            $scores = Score::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->get();
        }

        $students = $classroom->students->map(function ($student) use ($scores) {
            $studentScores = $scores->where('student_id', $student->id);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nip' => $student->nip,
                'scores' => [
                    'tugas' => (float) ($studentScores->where('type', 'Tugas')->first()?->score ?? 0),
                    'harian' => (float) ($studentScores->where('type', 'Harian')->first()?->score ?? 0),
                    'proyek' => (float) ($studentScores->where('type', 'Sumatif')->first()?->score ?? 0),
                    'pts' => (float) ($studentScores->where('type', 'PTS')->first()?->score ?? 0),
                    'pas' => (float) ($studentScores->where('type', 'PAS')->first()?->score ?? 0),
                ]
            ];
        });

        return response()->json([
            'class' => $classroom->name,
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:users,id',
            'scores.*.tugas' => 'nullable|numeric|min:0|max:100',
            'scores.*.harian' => 'nullable|numeric|min:0|max:100',
            'scores.*.proyek' => 'nullable|numeric|min:0|max:100',
            'scores.*.pts' => 'nullable|numeric|min:0|max:100',
            'scores.*.pas' => 'nullable|numeric|min:0|max:100',
        ]);

        $teacherId = $request->user()->id;

        DB::transaction(function () use ($validated, $teacherId) {
            foreach ($validated['scores'] as $data) {
                $scoreTypes = [
                    'Tugas' => $data['tugas'],
                    'Harian' => $data['harian'],
                    'Sumatif' => $data['proyek'],
                    'PTS' => $data['pts'],
                    'PAS' => $data['pas'],
                ];

                foreach ($scoreTypes as $type => $value) {
                    if ($value !== null) {
                        Score::updateOrCreate(
                            [
                                'student_id' => $data['student_id'],
                                'class_id' => $validated['class_id'],
                                'subject_id' => $validated['subject_id'],
                                'type' => $type,
                            ],
                            [
                                'score' => $value,
                                'created_by' => $teacherId,
                            ]
                        );
                    }
                }
            }
        });

        return response()->json(['message' => 'Nilai berhasil disimpan']);
    }
}
