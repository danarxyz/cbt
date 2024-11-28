<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseStudent extends Pivot
{
    use SoftDeletes;
    
    protected $table = 'course_students';
    protected $guarded = ['id'];
    public $incrementing = true;
}
