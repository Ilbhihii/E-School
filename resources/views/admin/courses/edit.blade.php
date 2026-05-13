@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 py-10">

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">

        <!-- TITLE -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">✏️ Modifier le cours</h1>
            <p class="text-gray-500">{{ $course->title }}</p>
        </div>

        <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data" class="space-y-6">
            @method('PUT')
            @csrf

            <!-- TITLE -->
            <div>
                <label class="font-medium text-gray-700">Titre</label>
                <input type="text" name="title" value="{{ old('title', $course->title) }}"
                       class="w-full mt-2 p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none @error('title') border-red-400 @enderror"
                       placeholder="Ex: Les équations du 2ème degré">
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- DESCRIPTION -->
            <div>
                <label class="font-medium text-gray-700">Description</label>
                <textarea name="description" rows="4"
                          class="w-full mt-2 p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none @error('description') border-red-400 @enderror"
                          placeholder="Description du cours...">{{ old('description', $course->description) }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- SELECTS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="font-medium text-gray-700">Classe</label>
                    <select name="class_id"
                            class="w-full mt-2 p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none @error('class_id') border-red-400 @enderror">
                        <option value="">-- Choisir --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $course->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-medium text-gray-700">Matière</label>
                    <select name="subject_id"
                            class="w-full mt-2 p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none @error('subject_id') border-red-400 @enderror">
                        <option value="">-- Choisir --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <!-- LINK -->
            <div>
                <label class="font-medium text-gray-700">Lien du cours (optionnel)</label>
                <input type="url" name="course_link" value="{{ old('course_link', $course->course_link) }}"
                       class="w-full mt-2 p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none"
                       placeholder="https://...">
            </div>

            <!-- CURRENT FILES DISPLAY -->
            @if($course->video)
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="font-medium text-blue-800">Vidéo actuelle:</p>
                <video src="{{ Storage::url($course->video) }}" controls class="w-full max-w-md mt-2 rounded"></video>
            </div>
            @endif

            @if($course->pdf)
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="font-medium text-green-800">PDF actuel:</p>
                <a href="{{ Storage::url($course->pdf) }}" target="_blank" class="text-green-600 hover:underline">Voir PDF</a>
            </div>
            @endif

            <!-- NEW FILES -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="font-medium text-gray-700">Nouvelle vidéo (remplace l'ancienne)</label>
                    <input type="file" name="video" accept="video/*"
                           class="w-full mt-2 p-2 border border-dashed rounded-lg">
                    @error('video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-medium text-gray-700">Nouveau PDF (remplace l'ancien)</label>
                    <input type="file" name="pdf" accept=".pdf"
                           class="w-full mt-2 p-2 border border-dashed rounded-lg">
                    @error('pdf') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('admin.courses.index') }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold text-center transition">
                    ← Annuler
                </a>
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold text-center transition">
                    💾 Mettre à jour
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

