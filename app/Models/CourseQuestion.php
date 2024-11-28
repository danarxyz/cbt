<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseQuestion extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function answers()
    {
        return $this->hasMany(CourseAnswer::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
