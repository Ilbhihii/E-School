@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 py-10">

    <div class="max-w-4xl mx-auto">

        <!-- BACK -->
        <div class="mb-6">
            <a href="{{ route('admin.devoirs.index', ['course_id' => $course_id]) }}"
               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold">
                ← Retour
            </a>
        </div>

        <!-- COURSE INFO -->
        @if($course)
        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 mb-8">
            <h2 class="text-2xl font-bold text-indigo-900">{{ $course->title }}</h2>
            <p class="text-indigo-700">
                {{ $course->classRoom->name }} • {{ $course->subject->name }}
            </p>
        </div>
        @endif

        <!-- FORM CARD -->
        <div class="bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-white/50">

            <h2 class="text-3xl font-bold mb-8 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                ➕ Nouveau Devoir
            </h2>

            <form method="POST" action="{{ route('admin.devoirs.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- TITLE -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Titre *</label>
                        <input type="text" name="title" required
                               value="{{ old('title') }}"
                               class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition"
                               placeholder="Ex: Exercices sur les équations">
                        @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- CLASS -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Classe *</label>
                        <select name="class_room_id" required
                                class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition">
                            <option value="">Sélectionner</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- COURSE -->
                    @if($courses->count() > 0)
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Cours *</label>
                        <select name="course_id" required
                                class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition">
                            <option value="">Sélectionner un cours</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ old('course_id', $course_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- DATE -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Date limite *</label>
                        <input type="date" name="due_date" required
                               min="{{ now()->format('Y-m-d') }}"
                               value="{{ old('due_date') }}"
                               class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition">
                    </div>

                </div>

                <!-- DESCRIPTION -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition"
                              placeholder="Description du devoir...">{{ old('description') }}</textarea>
                </div>

                <!-- FILE -->
                <div class="p-4 border border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                    <label class="text-sm font-semibold text-gray-700">Fichier (PDF optionnel)</label>
                    <input type="file" name="file" accept=".pdf" class="w-full mt-2">
                </div>

                <!-- BUTTONS -->
                <div class="flex justify-end gap-3 pt-4">

                    <a href="{{ route('admin.devoirs.index', ['course_id' => $course_id]) }}"
                       class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition">
                        Annuler
                    </a>

                    <button type="submit"
                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg hover:scale-105 transition">
                        ➕ Créer Devoir
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection
