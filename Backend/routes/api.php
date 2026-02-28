<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Api\Admin\UserController as AdminUser;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Api\Guru\QuizController as GuruQuiz;
use App\Http\Controllers\Api\Guru\QuestionController as GuruQuestion;
use App\Http\Controllers\Api\Guru\MediaController as GuruMedia;
use App\Http\Controllers\Api\Siswa\QuizController as SiswaQuiz;
use App\Http\Controllers\Api\Siswa\AttemptController as SiswaAttempt;
use App\Http\Controllers\Api\Siswa\ProfileController as SiswaProfile;
use App\Http\Controllers\Api\Guru\ScheduleController as GuruSchedule;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/public/classrooms', [\App\Http\Controllers\Api\Admin\ClassroomController::class, 'publicList']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index']);
        Route::post('/users/bulk-delete', [AdminUser::class, 'bulkDelete']);
        Route::apiResource('users', AdminUser::class);
        Route::apiResource('categories', AdminCategory::class);
        Route::apiResource('classrooms', \App\Http\Controllers\Api\Admin\ClassroomController::class);
    });

    // Guru Routes
    Route::prefix('guru')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Guru\DashboardController::class, 'index']);
        Route::post('/media/upload', [GuruMedia::class, 'upload']);
        Route::apiResource('quizzes', GuruQuiz::class);
        Route::apiResource('quizzes.questions', GuruQuestion::class);
        Route::get('/activity-logs', [\App\Http\Controllers\Api\Guru\ActivityLogController::class, 'index']);
        Route::apiResource('classrooms', \App\Http\Controllers\Api\Guru\ClassroomController::class);
        Route::get('attendances/rekap/{classId}', [\App\Http\Controllers\Api\Guru\AttendanceController::class, 'rekap']);
        Route::apiResource('attendances', \App\Http\Controllers\Api\Guru\AttendanceController::class);
        Route::apiResource('quiz-schedules', \App\Http\Controllers\Api\Guru\QuizScheduleController::class);
        Route::get('/subjects', [GuruSchedule::class, 'subjects']);
        Route::apiResource('teaching-schedules', GuruSchedule::class);
        Route::get('/scores', [\App\Http\Controllers\Api\Guru\ScoreController::class, 'index']);
        Route::post('/scores', [\App\Http\Controllers\Api\Guru\ScoreController::class, 'store']);
    });

    // Siswa Routes
    Route::prefix('siswa')->group(function () {
        Route::get('/quizzes', [SiswaQuiz::class, 'index']);
        Route::get('/quizzes/categories', [SiswaQuiz::class, 'categories']);
        Route::get('/quizzes/subjects', [SiswaQuiz::class, 'subjects']);
        Route::get('/quizzes/{quiz}', [SiswaQuiz::class, 'show']);

        Route::get('/attempts', [SiswaAttempt::class, 'index']);
        Route::post('/attempts/bulk-delete', [SiswaAttempt::class, 'bulkDestroy']);
        Route::post('/quizzes/{quiz}/attempt', [SiswaAttempt::class, 'store']);
        Route::post('/attempts/{attempt}/answer', [SiswaAttempt::class, 'answer']);
        Route::post('/attempts/{attempt}/finish', [SiswaAttempt::class, 'finish']);
        Route::get('/attempts/{attempt}', [SiswaAttempt::class, 'show']);

        // Profile Routes
        Route::get('/profile', [SiswaProfile::class, 'show']);
        Route::put('/profile', [SiswaProfile::class, 'update']);
        Route::post('/profile/avatar', [SiswaProfile::class, 'uploadAvatar']);
    });
});
