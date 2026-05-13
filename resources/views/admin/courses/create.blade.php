@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 py-10">

    <div class="max-w-4xl mx-auto">

        <!-- HEADER -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                📘 Créer un cours
            </h1>
            <p class="text-gray-500 mt-2">Ajoutez du contenu pédagogique facilement</p>
        </div>

        <!-- CARD -->
        <div class="bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-white/50">

            <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- TITLE -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Titre</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition"
                           placeholder="Ex: Les équations du 2ème degré">
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- DESCRIPTION -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition"
                              placeholder="Décrire le cours...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- SELECTS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- CLASS -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Classe</label>
                        <select name="class_id"
                                class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition">
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- SUBJECT -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Matière</label>
                        <select name="subject_id"
                                class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition">
                            <option value="">Sélectionner une matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                <!-- LINK -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Lien du cours (optionnel)</label>
                    <input type="url" name="course_link"
                           class="w-full mt-2 p-4 rounded-xl border bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-400 outline-none transition"
                           placeholder="https://...">
                </div>

                <!-- FILES -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- VIDEO -->
                    <div class="p-4 border border-dashed rounded-xl bg-indigo-50 hover:bg-indigo-100 transition">
                        <label class="text-sm font-semibold text-gray-700">Vidéo</label>
                        <input type="file" name="video" accept="video/*" class="w-full mt-2">
                        @error('video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- PDF -->
                    <div class="p-4 border border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                        <label class="text-sm font-semibold text-gray-700">PDF</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full mt-2">
                        @error('pdf') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="text-right pt-4">
                    <button type="submit"
                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg hover:scale-105 transition">
                        ➕ Créer le cours
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
