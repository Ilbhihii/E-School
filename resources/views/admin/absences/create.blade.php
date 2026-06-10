@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:700px">

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <!-- GRADIENT HEADER -->
        <div class="admin-gradient-header">
            <div class="adm-flex adm-gap-3">
                <div style="background:rgba(255,255,255,0.15);border-radius:16px;padding:0.75rem;">
                    <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
                </div>
                <div>
                    <h1>➕ Nouvelle absence</h1>
                    <p>Enregistrer une nouvelle présence ou absence</p>
                </div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.absences.store') }}">
                    @csrf
                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Classe <i class="bi bi-mortarboard-fill" style="color:var(--adm-primary);"></i></label>
                            <select name="class_id" class="adm-form-select @error('class_id') error @enderror" required>
                                <option value="">-- Sélectionner une classe --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id || old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Étudiant <i class="bi bi-person-graduate" style="color:var(--adm-purple);"></i></label>
                            <select name="user_id" class="adm-form-select @error('user_id') error @enderror" required>
                                <option value="">-- Sélectionner un étudiant --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Date <i class="bi bi-calendar-day" style="color:var(--adm-success);"></i></label>
                            <input type="date" name="date" value="{{ old('date') }}" class="adm-form-input @error('date') error @enderror" required>
                            @error('date') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
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
                    <div class="adm-flex adm-gap-2 adm-mt-3">
                        <a href="{{ route('admin.absences') . (request()->get('class_id', '') ? '?class_id=' . request('class_id') : '') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-success" style="flex:1;">
                            <i class="bi bi-save"></i> Enregistrer l'absence
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
