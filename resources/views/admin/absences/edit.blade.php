@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">✏️ Modifier l'absence</span></h1>
                <p class="admin-header-subtitle">Mettre à jour les informations de présence</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.absences.update', $absence->id) }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">📅 Date</label>
                        <input type="date" name="date" value="{{ $absence->date }}" class="adm-form-input" required>
                        @error('date') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">👤 Statut de présence</label>
                        <select name="present" class="adm-form-select">
                            <option value="1" {{ $absence->present ? 'selected' : '' }}>✅ Présent</option>
                            <option value="0" {{ !$absence->present ? 'selected' : '' }}>❌ Absent</option>
                        </select>
                        @error('present') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-alert adm-alert-info">
                        <i class="bi bi-info-circle-fill"></i> Vérifie bien la date et le statut avant d'enregistrer la modification.
                    </div>

                    <div class="adm-flex adm-gap-2">
                        <a href="{{ route('admin.absences.show', $absence->id) }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            ❌ Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary" style="flex:1;">
                            <i class="bi bi-save-fill"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
