<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\StudentAnswer;
use App\Models\Test;
use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TestController extends Controller
{
    private LearningPathService $paths;

    public function __construct(LearningPathService $paths)
    {
        $this->paths = $paths;
    }

    public function index()
    {
        if (auth()->user()->test_passed) {
            return redirect()->route('student.waiting');
        }

        $subjectIds = $this->paths->studentAssignmentRows(Auth::id())
            ->pluck('subject_id')->filter()->unique()->values();

        $tests = Test::query()
            ->whereIn('subject_id', $subjectIds)
            ->withCount('questions')
            ->get();

        return view('student.tests.index', compact('tests'));
    }

    public function show(Test $test)
    {
        $this->authorizeTest($test);
        $test->load('questions.answers');

        if (Result::where('user_id', Auth::id())->where('test_id', $test->id)->exists()) {
            return redirect()->route('student.tests.index')->with('error', 'Test déjà passé.');
        }

        return view('student.tests.show', compact('test'));
    }

    public function submit(Request $request, Test $test)
    {
        $this->authorizeTest($test);

        if (Result::where('user_id', Auth::id())->where('test_id', $test->id)->exists()) {
            return redirect()->route('student.tests.index')->with('error', 'Test déjà passé.');
        }

        $request->validate(['answers' => ['nullable', 'array']]);
        $test->loadMissing('questions.answers');

        $score = 0;
        $totalQuestions = $test->questions->count();
        $studentAnswers = [];

        foreach ($test->questions as $question) {
            $submitted = collect((array) $request->input("answers.{$question->id}", []))
                ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
                ->map(fn ($id) => (int) $id)
                ->unique()->sort()->values();

            $validIds = $question->answers->pluck('id')->map(fn ($id) => (int) $id);
            if ($submitted->diff($validIds)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'answers' => 'Une réponse envoyée ne correspond pas à la question.',
                ]);
            }

            $correct = $question->answers
                ->where('is_correct', true)
                ->pluck('id')->map(fn ($id) => (int) $id)
                ->sort()->values();

            $studentAnswers[$question->id] = $submitted->all();

            // Point uniquement si l'ensemble soumis est exactement l'ensemble attendu.
            if ($correct->isNotEmpty() && $submitted->all() === $correct->all()) {
                $score++;
            }
        }

        DB::transaction(function () use ($test, $score, $totalQuestions, $studentAnswers) {
            // Re-vérification transactionnelle pour empêcher les doubles soumissions.
            if (Result::where('user_id', Auth::id())->where('test_id', $test->id)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['test' => 'Test déjà passé.']);
            }

            $result = Result::create([
                'user_id' => Auth::id(),
                'test_id' => $test->id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0,
                'answers' => json_encode($studentAnswers),
            ]);

            foreach ($studentAnswers as $questionId => $answerIds) {
                foreach ($answerIds as $answerId) {
                    StudentAnswer::create([
                        'result_id' => $result->id,
                        'question_id' => (int) $questionId,
                        'answer_id' => (int) $answerId,
                    ]);
                }
            }

            auth()->user()->forceFill(['test_passed' => true])->save();
        });

        return redirect()->route('student.waiting')
            ->with('success', "Score: {$score}/{$totalQuestions} — Test envoyé. En attente de validation par l'admin.");
    }

    private function authorizeTest(Test $test): void
    {
        $subjectIds = $this->paths->studentAssignmentRows(Auth::id())
            ->pluck('subject_id')->map(fn ($id) => (int) $id);

        abort_unless($subjectIds->contains((int) $test->subject_id), 403);
    }
}
