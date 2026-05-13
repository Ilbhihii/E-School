@extends('layouts.admin')

@section('content')
<div class="container-fluid py-6 bg-gradient-to-br from-slate-50 to-indigo-50 min-h-screen">
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success shadow-lg rounded-2xl mb-6 p-4 border-0 bg-gradient-to-r from-emerald-50 to-teal-50">
            <i class="fas fa-check-circle text-emerald-500 me-2"></i>
            {{ session('success') }}
        </div>
        @endif

        <div class="card border-0 shadow-2xl rounded-3xl overflow-hidden bg-white/80 backdrop-blur-sm">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white p-8">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm">
                        <i class="fas fa-plus-circle text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl fw-bold mb-1">➕ Nouvelle absence</h2>
                        <p class="opacity-90 text-lg">Enregistrer une nouvelle présence ou absence</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-8">
                <form method="POST" action="{{ route('admin.absences.store') }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 mb-3">Classe <i class="fas fa-graduation-cap text-indigo-500"></i></label>
                            <select name="class_id" class="form-select border-2 border-slate-200 rounded-2xl py-3 px-4 focus:border-indigo-500 shadow-sm @error('class_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionner une classe --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id || old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 mb-3">Étudiant <i class="fas fa-user-graduate text-purple-500"></i></label>
                            <select name="user_id" class="form-select border-2 border-slate-200 rounded-2xl py-3 px-4 focus:border-indigo-500 shadow-sm @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionner un étudiant --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 mb-3">Date <i class="fas fa-calendar-day text-emerald-500"></i></label>
                            <input type="date" name="date" value="{{ old('date') }}" class="form-control border-2 border-slate-200 rounded-2xl py-3 px-4 focus:border-emerald-500 shadow-sm @error('date') is-invalid @enderror" required>
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 mb-3">Statut <i class="fas fa-check-circle text-success"></i></label>
                            <select name="present" class="form-select border-2 border-slate-200 rounded-2xl py-3 px-4 focus:border-success shadow-sm @error('present') is-invalid @enderror" required>
                                <option value="">-- Statut --</option>
                                <option value="1" {{ old('present') == '1' ? 'selected' : '' }}>Présent</option>
                                <option value="0" {{ old('present') == '0' ? 'selected' : '' }}>Absent</option>
                            </select>
                            @error('present') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-6">
                        <button type="submit" class="btn btn-success btn-lg px-8 py-3 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                            <i class="fas fa-save me-2"></i> Enregistrer l'absence
                        </button>
                        <a href="{{ route('admin.absences') . request()->get('class_id', '') ? '?class_id=' . request('class_id') : '' }}" class="btn btn-secondary btn-lg px-8 py-3">
                            <i class="fas fa-arrow-left me-2"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
