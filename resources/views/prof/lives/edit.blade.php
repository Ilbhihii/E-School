@extends('layouts.prof')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-xl rounded-3xl p-8">
<h1 class="text-3xl font-bold text-gray-800 mb-8">📺 Modifier le Live</h1>

@if(session('success'))
    <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 p-4 rounded-xl mb-8 shadow-sm">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('prof.lives.update', $live) }}">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label for="title" class="block text-sm font-semibold text-gray-700 mb-3">Titre du live</label>
        <input type="text" name="title" id="title" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 ring-indigo-500 focus:border-indigo-500 focus:bg-white shadow-sm transition-all duration-300 placeholder-gray-400" placeholder="Titre" value="{{ old('title', $live->title) }}" required>
        @error('title')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-xl mt-2 font-medium text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-3">Classe</label>
        <select name="class_id" id="class_id" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 ring-indigo-500 focus:border-indigo-500 focus:bg-white shadow-sm transition-all duration-300" required>
            <option value="">Choisir la classe</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ old('class_id', $live->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
            @endforeach
        </select>
        @error('class_id')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-xl mt-2 font-medium text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="stream_url" class="block text-sm font-semibold text-gray-700 mb-3">Lien YouTube / Zoom</label>
        <input type="url" name="stream_url" id="stream_url" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 ring-indigo-500 focus:border-indigo-500 focus:bg-white shadow-sm transition-all duration-300 placeholder-gray-400" placeholder="Lien" value="{{ old('stream_url', $live->stream_url) }}" required>
        @error('stream_url')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-xl mt-2 font-medium text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Date du live</label>
        <input type="date" name="live_date" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 ring-indigo-500 focus:border-indigo-500 focus:bg-white shadow-sm transition-all duration-300" value="{{ old('live_date', $live->live_date) }}" required>
        @error('live_date')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-xl mt-2 font-medium text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Heure début</label>
        <input type="time" name="start_time" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 ring-indigo-500 focus:border-indigo-500 focus:bg-white shadow-sm transition-all duration-300" value="{{ old('start_time', $live->start_time) }}" required>
        @error('start_time')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-xl mt-2 font-medium text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Heure fin</label>
        <input type="time" name="end_time" class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 ring-indigo-500 focus:border-indigo-500 focus:bg-white shadow-sm transition-all duration-300" value="{{ old('end_time', $live->end_time) }}" required>
        @error('end_time')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-xl mt-2 font-medium text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="flex gap-4 mt-10">
        <a href="{{ route('prof.lives.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-4 rounded-2xl font-semibold shadow-md hover:shadow-lg transition-all duration-300 text-center">❌ Annuler</a>
        <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-center">✅ Modifier Live</button>
    </div>
</form>
</div>
@endsection
