@extends('layouts.admin')

@section('title', 'Nouvelle absence')
@section('page_title', 'Nouvelle absence')
@section('breadcrumb', 'Ajouter une absence')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('success'))
        <div class="adm-alert adm-alert-success">
            <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-plus-circle" style="color:rgba(255,255,255,0.35);"></i> Enregistrer une absence</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.absences.store') }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe</label>
                                <select name="class_id" class="adm-form-select @error('class_id') error @enderror" required>
                                    <option value="">-- Sélectionner une classe --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id || old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Étudiant</label>
                                <select name="user_id" class="adm-form-select @error('user_id') error @enderror" required>
                                    <option value="">-- Sélectionner un étudiant --</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Date</label>
                                <input type="date" name="date" value="{{ old('date') }}" class="adm-form-control @error('date') error @enderror" required>
                                @error('date') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Statut</label>
                                <select name="present" class="adm-form-select @error('present') error @enderror" required>
                                    <option value="">-- Statut --</option>
                                    <option value="1" {{ old('present') == '1' ? 'selected' : '' }}>Présent</option>
                                    <option value="0" {{ old('present') == '0' ? 'selected' : '' }}>Absent</option>
                                </select>
                                @error('present') <div class="adm-form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="adm-btn adm-btn-primary flex-fill">
                            <i class="bi bi-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('admin.absences') }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
