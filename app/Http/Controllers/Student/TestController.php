<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Result;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Answer;

class TestController extends Controller
{
    public function index()
    {
        if (auth()->user()->test_passed) {
            return redirect()->route('student.waiting');
        }

        $tests = Test::whereHas('subject', function($q) {
            $userClassId = auth()->user()->class_id;
            if ($userClassId) {
                $q->where('class_id', $userClassId);
            }
        })->withCount('questions')->get();
        
        return view('student.tests.index', compact('tests'));
    }

    public function show(Test $test)
    {
        $test->load('questions.answers');

        $hasResult = Result::where('user_id', Auth::id())->where('test_id', $test->id)->exists();
        if ($hasResult) {
            return redirect()->route('student.tests.index')->with('error', 'Test déjà passé.');
        }

        return view('student.tests.show', compact('test'));
    }

    public function submit(Request $request, Test $test)
    {
        $request->validate([
            'answers' => 'array',
        ]);

        $test->loadMissing('questions.answers');

        $score = 0;
        $totalQuestions = $test->questions->count();
        $studentAnswers = [];

        foreach ($test->questions as $question) {
            $submittedAnswerIds = array_filter((array) $request->input("answers.{$question->id}", []), fn($id) => is_numeric($id) && $id > 0);
            
            $studentAnswers[$question->id] = $submittedAnswerIds;
            
            if (empty($submittedAnswerIds)) {
                continue;
            }

            $hasCorrect = false;
            foreach ($submittedAnswerIds as $answerId) {
                $answer = Answer::find((int) $answerId);
                if ($answer && 
                    $answer->question_id === $question->id && 
                    $answer->is_correct) {
                    $hasCorrect = true;
                    break;
                }
            }

            if ($hasCorrect) {
                $score++;
            }
        }

        DB::transaction(function () use ($test, $score, $totalQuestions, $studentAnswers) {
            $result = Result::create([
                'user_id' => Auth::id(),
                'test_id' => $test->id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0,
                'answers' => json_encode($studentAnswers)
            ]);

            foreach ($studentAnswers as $questionId => $answerIds) {
                foreach ($answerIds as $answerId) {
                    StudentAnswer::create([
                        'result_id' => $result->id,
                        'question_id' => (int) $questionId,
                        'answer_id' => (int) $answerId
                    ]);
                }
            }
        });

        // ✅ marquer que test passé
        $user = auth()->user();
        $user->test_passed = true;
        $user->save();

        return redirect()->route('student.waiting')
            ->with('success', "Score: {$score}/{$total} — Test envoyé. En attente de validation par l'admin.");
    }

}

