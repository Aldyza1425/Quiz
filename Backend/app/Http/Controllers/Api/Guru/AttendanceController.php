<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $classId = $request->query('class_id');
        $date = $request->query('date', now()->toDateString());

        $query = Attendance::where('created_by', $request->user()->id)
            ->with('student:id,name');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($date) {
            $query->where('date', $date);
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:Hadir,Izin,Sakit,Alpha',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $isScheduled = \App\Models\Schedule::where('teacher_id', $user->id)
            ->where('class_id', $validated['class_id'])
            ->exists();

        $isHomeroom = \App\Models\Classroom::where('id', $validated['class_id'])
            ->where('homeroom_teacher_id', $user->id)
            ->exists();

        if (!$isScheduled && !$isHomeroom) {
            return response()->json(['message' => 'Unauthorized Access to Classroom'], 403);
        }

        $classroom = \App\Models\Classroom::findOrFail($validated['class_id']);

        try {
            $date = \Carbon\Carbon::parse($validated['date'])->toDateString();

            foreach ($validated['attendances'] as $item) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'class_id' => $validated['class_id'],
                        'date' => $date,
                    ],
                    [
                        'status' => $item['status'],
                        'notes' => $item['notes'] ?? null,
                        'created_by' => $user->id,
                    ]
                );
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'success',
                'message' => "Berhasil menyimpan absensi kelas {$classroom->name} untuk tanggal {$date}",
                'module' => 'classroom',
                'metadata' => ['class_id' => $classroom->id, 'date' => $date]
            ]);

            return response()->json(['message' => 'Absensi berhasil disimpan']);
        } catch (\Exception $e) {
            \Log::error('Attendance store failed: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Gagal menyimpan absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rekap(Request $request, string $classId)
    {
        $validated = $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
        ]);

        $startDate = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $validated['end_date'] ?? now()->toDateString();

        $user = $request->user();

        // Check access
        $isScheduled = \App\Models\Schedule::where('teacher_id', $user->id)
            ->where('class_id', $classId)
            ->exists();

        $isHomeroom = \App\Models\Classroom::where('id', $classId)
            ->where('homeroom_teacher_id', $user->id)
            ->exists();

        if (!$isScheduled && !$isHomeroom) {
            return response()->json(['message' => 'Unauthorized Access to Classroom'], 403);
        }

        $classroom = \App\Models\Classroom::with([
            'students' => function ($q) {
                $q->select('users.id', 'users.name');
            }
        ])->findOrFail($classId);

        $attendanceData = Attendance::where('class_id', $classId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id');

        return response()->json([
            'classroom' => $classroom->name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'students' => $classroom->students->map(function ($student) use ($attendanceData) {
                $studentAttendance = $attendanceData->get($student->id, collect());
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'summary' => [
                        'Hadir' => $studentAttendance->where('status', 'Hadir')->count(),
                        'Izin' => $studentAttendance->where('status', 'Izin')->count(),
                        'Sakit' => $studentAttendance->where('status', 'Sakit')->count(),
                        'Alpha' => $studentAttendance->where('status', 'Alpha')->count(),
                    ],
                    'details' => $studentAttendance->map(function ($att) {
                        return [
                            'date' => $att->date->toDateString(),
                            'status' => $att->status
                        ];
                    })
                ];
            })
        ]);
    }
}
