@extends('layouts.admin')

@section('title', 'Modifier absence')
@section('page_title', 'Modifier absence')
@section('breadcrumb', 'Modifier une absence')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-pencil" style="color:rgba(255,255,255,0.35);"></i> Modifier l'absence</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.absences.update', $absence->id) }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date</label>
                        <input type="date" name="date" value="{{ $absence->date }}" class="adm-form-control @error('date') error @enderror" required>
                        @error('date') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Statut de présence</label>
                        <select name="present" class="adm-form-select @error('present') error @enderror" required>
                            <option value="1" {{ $absence->present ? 'selected' : '' }}>✅ Présent</option>
                            <option value="0" {{ !$absence->present ? 'selected' : '' }}>❌ Absent</option>
                        </select>
                        @error('present') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-card" style="background:rgba(0,58,143,0.08);border-color:rgba(0,58,143,0.15);margin-bottom:1.25rem;">
                        <div class="adm-card-body" style="padding:1rem;font-size:0.85rem;color:var(--adm-text-secondary);">
                            <i class="bi bi-info-circle"></i> Vérifiez bien la date et le statut avant d'enregistrer.
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.absences.show', $absence->id) }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-x"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary flex-fill">
                            <i class="bi bi-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
