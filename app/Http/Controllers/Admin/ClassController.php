<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::query()
            ->with(['level', 'subjects'])
            ->orderBy('level_id')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $levels = Level::query()
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        return view('admin.classes.create', compact('levels', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_rooms', 'name')
                    ->where(fn ($query) => $query->where('level_id', $request->level_id)),
            ],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['integer', 'exists:subjects,id'],
        ]);

        $level = Level::findOrFail($validated['level_id']);

        $classRoom = ClassRoom::create([
            'name' => trim($validated['name']),
            'level_id' => $level->id,
        ]);

        $subjectIds = collect();

        if (!empty($level->subject_id)) {
            $subjectIds->push((int) $level->subject_id);
        }

        if (!empty($validated['subject_id'])) {
            $subjectIds->push((int) $validated['subject_id']);
        }

        if (!empty($validated['subjects'])) {
            foreach ($validated['subjects'] as $subjectId) {
                $subjectIds->push((int) $subjectId);
            }
        }

        $subjectIds = $subjectIds->filter()->unique()->values();

        if ($subjectIds->isNotEmpty()) {
            $classRoom->subjects()->syncWithoutDetaching($subjectIds->all());
        }

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Classe créée avec succès.');
    }

    public function edit(ClassRoom $class)
    {
        $class->load(['level', 'subjects']);

        $levels = Level::query()
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        return view('admin.classes.edit', compact('class', 'levels', 'subjects'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_rooms', 'name')
                    ->ignore($class->id)
                    ->where(fn ($query) => $query->where('level_id', $request->level_id)),
            ],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['integer', 'exists:subjects,id'],
        ]);

        $level = Level::findOrFail($validated['level_id']);

        $class->update([
            'name' => trim($validated['name']),
            'level_id' => $level->id,
        ]);

        $subjectIds = collect();

        if (!empty($level->subject_id)) {
            $subjectIds->push((int) $level->subject_id);
        }

        if (!empty($validated['subject_id'])) {
            $subjectIds->push((int) $validated['subject_id']);
        }

        if (!empty($validated['subjects'])) {
            foreach ($validated['subjects'] as $subjectId) {
                $subjectIds->push((int) $subjectId);
            }
        }

        $subjectIds = $subjectIds->filter()->unique()->values();

        if ($subjectIds->isNotEmpty()) {
            $class->subjects()->syncWithoutDetaching($subjectIds->all());
        }

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Classe modifiée avec succès.');
    }

    public function destroy(ClassRoom $class)
    {
        $class->subjects()->detach();
        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Classe supprimée avec succès.');
    }
}
