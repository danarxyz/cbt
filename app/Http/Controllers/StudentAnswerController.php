<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAnswer;
use App\Models\CourseQuestion;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Cari pertanyaan berikutnya yang belum dijawab oleh pengguna.
     *
     * @param Course $course
     * @param CourseQuestion $currentQuestion
     * @param User $user
     * @return CourseQuestion|null
     */
    private function getNextQuestion(Course $course, CourseQuestion $currentQuestion, $user)
    {
        return CourseQuestion::where('course_id', $course->id)
            ->where('id', '>', $currentQuestion->id)
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('course_question_id')
                      ->from('student_answers')
                      ->where('user_id', $user->id);
            })
            ->orderBy('id', 'ASC')
            ->first();
    }

    /**
     * Menyimpan jawaban pengguna.
     *
     * @param Request $request
     * @param Course $course
     * @param CourseQuestion $question
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request, Course $course, CourseQuestion $question)
    {
        $validated = $request->validate([
            'answer_id' => 'required|exists:course_answers,id',
        ]);

        DB::beginTransaction();
        try {
            // Temukan jawaban yang dipilih
            $selectedAnswer = CourseAnswer::find($validated['answer_id']);

            // Validasi konsistensi jawaban dengan pertanyaan
            if($selectedAnswer->course_question_id != $question->id){
                throw ValidationException::withMessages([
                    'system_error' => ['System Error: Jawaban tidak tersedia pada pertanyaan!'],
                ]);
            }

            // Cek apakah pengguna sudah menjawab pertanyaan ini sebelumnya
            $existingAnswer = StudentAnswer::where('user_id', Auth::id())
                ->where('course_question_id', $question->id)
                ->first();

            if($existingAnswer){
                throw ValidationException::withMessages([
                    'system_error' => ['System Error: Kamu telah menjawab pertanyaan ini sebelumnya!'],
                ]);
            }

            // Tentukan nilai jawaban ('correct' atau 'wrong')
            $answerValue = $selectedAnswer->is_correct ? 'correct' : 'wrong';

            // Simpan jawaban pengguna
            StudentAnswer::create([
                'user_id' => Auth::id(),
                'course_question_id' => $question->id,
                'answer_id' => $selectedAnswer->id,
                'answer' => $answerValue,
            ]);

            DB::commit();

            // Dapatkan pertanyaan berikutnya yang belum dijawab
            $nextQuestion = $this->getNextQuestion($course, $question, Auth::user());

            if ($nextQuestion) {
                return redirect()->route('dashboard.learning.course', [
                    'course' => $course->id,
                    'question' => $nextQuestion->id
                ]);
            }

            // Jika tidak ada pertanyaan lagi, arahkan ke halaman selesai belajar
            return redirect()->route('dashboard.learning.finished.course', ['course' => $course->id]);

        } catch(\Exception $e){
            DB::rollBack();
            throw ValidationException::withMessages([
                'system_error' => ['System Error: ' . $e->getMessage()],
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAnswer $studentAnswer)
    {
        //
    }
}
