<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'subject_id',
        'classroom_id',
        'created_by',
        'duration_minutes',
        'duration_seconds',
        'random_questions_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'classroom_id' => 'integer',
        'subject_id' => 'integer',
        'duration_minutes' => 'integer',
        'duration_seconds' => 'integer',
        'random_questions_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'user_quiz_assignments')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }
}
