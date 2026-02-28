<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'password',
        'role',
        'nip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quizzesCreated()
    {
        return $this->hasMany(Quiz::class, 'created_by');
    }

    public function categoriesCreated()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function assignedQuizzes()
    {
        return $this->belongsToMany(Quiz::class, 'user_quiz_assignments')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function teachingClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'schedules', 'teacher_id', 'class_id')->distinct();
    }

    public function studentClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'class_user', 'user_id', 'class_id');
    }

    public function classroom(): BelongsToMany
    {
        return $this->studentClassrooms();
    }

    public function getClassroomAttribute()
    {
        return $this->studentClassrooms->first();
    }

    public function getClassroomIdAttribute()
    {
        return $this->studentClassrooms->first()?->id;
    }

    /**
     * Teacher relationships
     */
    public function teacherSchedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function homeroomClass(): HasOne
    {
        return $this->hasOne(Classroom::class, 'homeroom_teacher_id');
    }

    /**
     * Student relationships
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'student_id');
    }
}
