<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classroom extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade_level',
        'academic_year',
        'homeroom_teacher_id',
    ];

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'schedules', 'class_id', 'teacher_id')->distinct();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'class_id');
    }
}
