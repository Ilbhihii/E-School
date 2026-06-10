<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\Subject;
use App\Models\Course;
use App\Services\QCMGenerator;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Models\StudentAnswer;

class TestController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('prof')) {
            $tests = Test::where('create_by', Auth::id())->with(['subject'])->withCount('questions')->get();
            return view('prof.tests.index', compact('tests'));
        }

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

    public function create()
    {
        $subjects = Subject::where('name', '!=', 'administration')->get()->unique('name');

        return view('prof.tests.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration' => 'required|integer|min:1',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:51200',
            'questions' => 'nullable|array|min:1',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.answers' => 'required_with:questions|array|min:2',
            'questions.*.answers.*.answer' => 'required_with:questions|string',
            'questions.*.answers.*.is_correct' => 'required_with:questions|boolean',
        ]);

        $test = Test::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'duration' => $request->duration,
            'create_by' => Auth::id(),
        ]);

        if ($request->hasFile('file')) {
            // File-based QCM generation
            $generator = app(QCMGenerator::class);
            $file = $request->file('file');
            $text = $generator->extractTextFromFile($file);

            if ($text) {
                $questions = $generator->generateFromText($text);
                if ($questions) {
                    $generator->saveToTest($questions, $test->id);
                    $test->update(['is_ai_generated' => true]);
                } else {
                    return back()->withErrors(['file' => 'Erreur génération AI QCM. Vérifiez OPENAI_API_KEY.']);
                }
            } else {
return back()->withErrors(['file' => 'Impossible d\'extraire le texte du fichier.']);
            }
        } else {
            // Manual questions
            foreach ($request->questions as $qData) {
                $question = Question::create([
                    'test_id' => $test->id,
                    'question' => $qData['question']
                ]);

                foreach ($qData['answers'] as $aData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer' => $aData['answer'],
                        'is_correct' => $aData['is_correct'] ?? false
                    ]);
                }
            }
        }

        return redirect()->route('prof.tests.index')->with('success', 'Test créé avec succès!');
    }

    private function extractTextFromFile($file)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $text = '';

            if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($file);
                $text = $pdf->getText();
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $phpWord = IOFactory::load($file);
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        }
                    }
                }
            }

            return trim($text);
        } catch (\Exception $e) {
            Log::error('File extraction error: ' . $e->getMessage());
            return null;
        }
    }

    private function convertToQCM($text)
    {
        $questions = [];
        $blocks = preg_split('/(\\d+\\.\\s*)/i', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        for ($i = 0; $i < count($blocks); $i += 2) {
            if (isset($blocks[$i + 1]) && trim($blocks[$i + 1]) !== '') {
                $questionText = trim($blocks[$i + 1]);

                // Look for answers in next block or parse A.B.C.D.
                $answerText = '';
                if (isset($blocks[$i + 2])) {
                    $answerText = $blocks[$i + 2];
                }

                preg_match_all('/([A-D])\\.\\s*(.+?)(?=\\n[A-D]\\.|$)/i', $answerText . $text, $matches, PREG_SET_ORDER);

                if (count($matches) >= 2) {
                    $answers = [];
                    foreach (array_slice($matches, 0, 4) as $match) {
                        $answers[] = [
                            'answer' => trim($match[2]),
                            'is_correct' => false
                        ];
                    }
                    // Set second answer (B) as correct by default, or random
                    if (!empty($answers)) {
                        $answers[1]['is_correct'] = true; // B is correct
                    }

                    $questions[] = [
                        'question' => $questionText,
                        'answers' => $answers
                    ];
                }
            }
        }

        return array_slice($questions, 0, 10); // Limit to 10 questions
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
                continue; // No answer selected, score 0 for this question
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

            // Auto test_passed disabled - now manual by admin only
            // if ($score >= 10) { 
            //     $user = auth()->user();
            //     $user->test_passed = true;
            //     $user->save();
            // }
        });

        return redirect()->route('student.waiting')
            ->with('success', "Test soumis avec succès! Score: {$score}/{$totalQuestions}");
    }

    public function profShow(Test $test)
    {
        if (!auth()->user()->hasRole('prof') || $test->create_by !== auth()->id()) {
            abort(403);
        }

        $test->load([
            'questions.answers',
            'results.user',
            'results.answers.answer'
        ]);

        return view('prof.tests.show', compact('test'));
    }

    public function studentResult($testId, $userId)
    {
        $test = Test::with('questions.answers')->findOrFail($testId);

        $result = Result::where('test_id', $testId)
            ->where('user_id', $userId)
            ->with('answers.answer', 'user')
            ->firstOrFail();

        return view('prof.tests.student_result', compact('test', 'result'));
    }


    public function edit(Test $test)
    {
        if (!auth()->user()->hasRole('prof') || $test->create_by !== auth()->id()) {
            abort(403);
        }

        $subjects = Subject::where('name', '!=', 'administration')->get()->unique('name');
        $test->load('questions.answers');
        return view('prof.tests.edit', compact('test', 'subjects'));
    }

    public function update(Request $request, Test $test)
    {
        if (!auth()->user()->hasRole('prof') || $test->create_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration' => 'required|integer|min:1',
        ]);

        $test->update($request->only('title', 'subject_id', 'duration'));

        return redirect()->route('prof.tests.index')->with('success', 'Test mis à jour!');
    }

    public function destroy(Test $test)
    {
        if (!auth()->user()->hasRole('prof') || $test->create_by !== auth()->id()) {
            abort(403);
        }

        $test->questions()->delete();
        $test->delete();

        return redirect()->route('prof.tests.index')->with('success', 'Test supprimé!');
    }

    public function generateAI(Request $request, Test $test)
    {
        if (!auth()->user()->hasRole('prof') || $test->create_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:51200',
        ]);

        $generator = app(QCMGenerator::class);
        $file = $request->file('file');
        $text = $generator->extractTextFromFile($file);

        if ($text) {
            $questions = $generator->generateFromText($text);
            if ($questions) {
                // Clear existing questions
                Question::where('test_id', $test->id)->delete();
                $generator->saveToTest($questions, $test->id);
                $test->update(['is_ai_generated' => true]);
                return back()->with('success', 'QCM généré par IA!');
            }
        }

        return back()->withErrors(['file' => 'Erreur génération AI. Vérifiez OPENAI_API_KEY.']);
    }
}
?>
