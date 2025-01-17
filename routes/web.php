<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseQuestionController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\StudentAnswerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::resource('courses', CourseController::class)->middleware('role:teacher');
        
        Route::get('/course/question/create/{course}', [CourseQuestionController::class, 'create'])
        ->middleware('role:teacher')
        ->name('course.create.question');
        
        Route::resource('course_question', CourseQuestionController::class)
        ->middleware('role:teacher');
        
        Route::post('/course/question/save/{course}', [CourseQuestionController::class, 'store'])
        ->middleware('role:teacher')
        ->name('course.create.question.store');
        
        Route::get('/course/student/show/{course}', [CourseStudentController::class, 'index'])
        ->middleware('role:teacher')
        ->name('course.course_student.index');
        
        Route::get('/course/student/create/{course}', [CourseStudentController::class, 'create'])
        ->middleware('role:teacher')
        ->name('course.course_student.create');
        
        Route::post('/course/student/save/{course}', [CourseStudentController::class, 'store'])
        ->middleware('role:teacher')
        ->name('course.course_student.store');

        Route::get('/learning/finished/{course}', [LearningController::class, 'learning_finished'])
        ->middleware('role:student')
        ->name('learning.finished.course');
        
        Route::get('/learning/rapport/{course}', [LearningController::class, 'learning_rapport'])
        ->middleware('role:student')
        ->name('learning.rapport.course');
        
        //menampilkan beberapa kelas yang diberikan oleh guru
        
        Route::get('/learning', [LearningController::class, 'index'])
        ->middleware('role:student')
        ->name('learning.index');

        Route::get('/courses/{course}/questions/{question}', [LearningController::class, 'learning'])
        ->middleware('role:student')
        ->name('learning.course');
        
        Route::post('/courses/{course}/questions/{question}/answer', [StudentAnswerController::class, 'store'])
        ->middleware('role:student')
        ->name('learning.course.answer.store');
        
    });
});

    

require __DIR__.'/auth.php';
