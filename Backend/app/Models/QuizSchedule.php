<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSchedule extends Model
{
    protected $fillable = [
        'quiz_id',
        'classroom_id',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
