<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_question_id',
        'answer',
        'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(CourseQuestion::class, 'course_question_id');
    }
}
