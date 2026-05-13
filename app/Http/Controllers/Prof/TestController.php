<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Subject;
use App\Services\QCMGenerator;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Result;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Answer;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::where('create_by', auth()->id())
            ->with('subject')
            ->withCount('questions')
            ->get();

        return view('prof.tests.index', compact('tests'));
    }

    public function create()
    {
        $subjects = Subject::where('name', '!=', 'administration')->get()->unique('name');
        return view('prof.tests.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

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
            'create_by' => auth()->id(),
        ]);

        if ($request->hasFile('file')) {
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

    public function show(Test $test)
    {
        $this->authorizeTest($test);

        $test->load([
            'questions.answers',
            'results.user',
            'results.answers.answer'
        ]);

        return view('prof.tests.show', compact('test'));
    }

    public function edit(Test $test)
    {
        $this->authorizeTest($test);

        $subjects = Subject::where('name', '!=', 'administration')->get()->unique('name');
        $test->load('questions.answers');
        return view('prof.tests.edit', compact('test', 'subjects'));
    }

    public function update(Request $request, Test $test)
    {
        $this->authorizeTest($test);

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
        $this->authorizeTest($test);

        $test->questions()->delete();
        $test->delete();

        return redirect()->route('prof.tests.index')->with('success', 'Test supprimé!');
    }

    public function generateAI(Request $request, Test $test)
    {
        $this->authorizeTest($test);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:51200',
        ]);

        $generator = app(QCMGenerator::class);
        $file = $request->file('file');
        $text = $generator->extractTextFromFile($file);

        if ($text) {
            $questions = $generator->generateFromText($text);
            if ($questions) {
                Question::where('test_id', $test->id)->delete();
                $generator->saveToTest($questions, $test->id);
                $test->update(['is_ai_generated' => true]);
                return back()->with('success', 'QCM généré par IA!');
            }
        }

        return back()->withErrors(['file' => 'Erreur génération AI. Vérifiez OPENAI_API_KEY.']);
    }

    public function studentResult($testId, $userId)
    {
        $test = Test::with('questions.answers')->findOrFail($testId);
        $this->authorizeTest($test);

        $result = Result::where('test_id', $testId)
            ->where('user_id', $userId)
            ->with('answers.answer', 'user')
            ->firstOrFail();

        return view('prof.tests.student_result', compact('test', 'result'));
    }

    private function authorizeTest(Test $test)
    {
        if ($test->create_by !== auth()->id()) {
            abort(403);
        }
    }
}

