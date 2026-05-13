@extends('layouts.admin')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-sky-100 p-6">

    <div class="w-full max-w-2xl bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 p-8">

        <!-- HEADER -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-indigo-600">Créer un Live</h1>
            <p class="text-gray-500 text-sm">Planifier un cours en direct</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 text-green-700 p-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.lives.store') }}" class="space-y-5">
            @csrf

            <!-- TITLE -->
            <div>
                <label class="text-sm font-medium text-gray-700">Titre du live</label>
                <input type="text" name="title"
                    value="{{ old('title') }}"
                    class="w-full mt-2 p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none"
                    placeholder="Ex: Révision Math"
                    required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- CLASS -->
            <div>
                <label class="text-sm font-medium text-gray-700">Classe</label>
                <select name="class_id"
                    class="w-full mt-2 p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none"
                    required>
                    <option value="">Choisir une classe</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- LINK -->
            <div>
                <label class="text-sm font-medium text-gray-700">Lien (YouTube / Zoom)</label>
                <input type="url" name="stream_url"
                    value="{{ old('stream_url') }}"
                    class="w-full mt-2 p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none"
                    placeholder="https://..."
                    required>
                @error('stream_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- DATE TIME -->
            <div>
                <label class="text-sm font-medium text-gray-700">Date & heure</label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">

                    <input type="date" name="live_date"
                        class="p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none"
                        required>

                    <input type="time" name="start_time"
                        class="p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none"
                        required>

                    <input type="time" name="end_time"
                        class="p-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none"
                        required>

                </div>
            </div>

            <!-- BUTTON -->
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition">
                🚀 Créer le Live
            </button>

        </form>

    </div>
</div>

@endsection
