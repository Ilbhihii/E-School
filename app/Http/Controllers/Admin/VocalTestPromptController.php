<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VocalTestPromptController extends Controller
{
    public function index()
    {
        $prompts = VocalTestPrompt::with(['subject', 'level', 'classRoom'])
            ->orderBy('subject_id')
            ->orderBy('level_id')
            ->orderBy('class_id')
            ->paginate(20);

        return view('admin.vocal-tests.prompts.index', compact('prompts'));
    }

    public function create()
    {
        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])->orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.vocal-tests.prompts.create', compact('subjects', 'levels', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'                 => 'required|exists:subjects,id',
            'level_id'                   => 'required|exists:levels,id',
            'class_id'                   => 'required|exists:class_rooms,id',
            'title'                      => 'required|string|max:255',
            'instructions'               => 'nullable|string',
            'reading_text'               => 'required|string',
            'test_mode'                  => 'required|in:reading,tajwid,hifd',
            'preparation_seconds'        => 'nullable|integer|min:0|max:300',
            'maximum_duration'           => 'nullable|integer|min:15|max:600',
            'hide_text_during_recording' => 'boolean',
            'is_active'                  => 'boolean',
        ], [
            'subject_id.required'   => 'La matière est obligatoire.',
            'level_id.required'     => 'Le niveau est obligatoire.',
            'class_id.required'     => 'La classe est obligatoire.',
            'title.required'        => 'Le titre est obligatoire.',
            'reading_text.required' => 'Le texte à lire est obligatoire.',
            'test_mode.required'    => 'Le mode de test est obligatoire.',
        ]);

        // Vérifier la cohérence matière / niveau / classe
        $this->validateCoherence(
            $validated['subject_id'],
            $validated['level_id'],
            $validated['class_id']
        );

        $validated['maximum_duration'] = $validated['maximum_duration'] ?? 120;
        $validated['preparation_seconds'] = $validated['preparation_seconds'] ?? 0;
        $validated['hide_text_during_recording'] = $request->boolean('hide_text_during_recording', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        VocalTestPrompt::updateOrCreate(
            [
                'subject_id' => $validated['subject_id'],
                'level_id'   => $validated['level_id'],
                'class_id'   => $validated['class_id'],
            ],
            $validated
        );

        return redirect()->route('admin.vocal-tests.prompts.index')
            ->with('success', 'Test vocal créé avec succès.');
    }

    public function edit(VocalTestPrompt $prompt)
    {
        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])->orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.vocal-tests.prompts.edit', compact('prompt', 'subjects', 'levels', 'classes'));
    }

    public function update(Request $request, VocalTestPrompt $prompt)
    {
        $validated = $request->validate([
            'subject_id'                 => [
                'required',
                'exists:subjects,id',
            ],
            'level_id'                   => [
                'required',
                'exists:levels,id',
            ],
            'class_id'                   => [
                'required',
                'exists:class_rooms,id',
                Rule::unique('vocal_test_prompts')->where(function ($query) use ($request, $prompt) {
                    $query->where('subject_id', $request->subject_id)
                          ->where('level_id', $request->level_id);
                })->ignore($prompt->id),
            ],
            'title'                      => 'required|string|max:255',
            'instructions'               => 'nullable|string',
            'reading_text'               => 'required|string',
            'test_mode'                  => 'required|in:reading,tajwid,hifd',
            'preparation_seconds'        => 'nullable|integer|min:0|max:300',
            'maximum_duration'           => 'nullable|integer|min:15|max:600',
            'hide_text_during_recording' => 'boolean',
            'is_active'                  => 'boolean',
        ]);

        // Vérifier la cohérence matière / niveau / classe
        $this->validateCoherence(
            $validated['subject_id'],
            $validated['level_id'],
            $validated['class_id']
        );

        $validated['maximum_duration'] = $validated['maximum_duration'] ?? 120;
        $validated['preparation_seconds'] = $validated['preparation_seconds'] ?? 0;
        $validated['hide_text_during_recording'] = $request->boolean('hide_text_during_recording', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $prompt->update($validated);

        return redirect()->route('admin.vocal-tests.prompts.index')
            ->with('success', 'Test vocal mis à jour avec succès.');
    }

    public function destroy(VocalTestPrompt $prompt)
    {
        $prompt->delete();

        return redirect()->route('admin.vocal-tests.prompts.index')
            ->with('success', 'Test vocal supprimé avec succès.');
    }

    /**
     * Vérifier que le niveau appartient à la matière et que la classe appartient au niveau
     */
    private function validateCoherence(int $subjectId, int $levelId, int $classId): void
    {
        $subject = Subject::findOrFail($subjectId);
        $level   = Level::findOrFail($levelId);
        $class   = ClassRoom::findOrFail($classId);

        abort_unless(
            (int) $level->subject_id === (int) $subject->id,
            422,
            'Ce niveau n\'appartient pas à la matière sélectionnée.'
        );

        abort_unless(
            (int) $class->level_id === (int) $level->id,
            422,
            'Cette classe n\'appartient pas au niveau sélectionné.'
        );
    }
}
