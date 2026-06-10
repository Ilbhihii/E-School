@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- BACK LINK -->
        <div class="adm-mb-2">
            <a href="{{ route('admin.devoirs.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">
                <i class="bi bi-arrow-left"></i> Retour aux devoirs
            </a>
        </div>

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">✏️ Modifier le devoir</span></h1>
                <p class="admin-header-subtitle">Mettre à jour les informations du devoir</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.devoirs.update', $devoir) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label">Titre *</label>
                            <input type="text" name="title" required value="{{ old('title', $devoir->title) }}" class="adm-form-input">
                            @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Classe</label>
                            <select name="class_room_id" class="adm-form-select">
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $devoir->class_room_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Cours</label>
                            <select name="course_id" class="adm-form-select">
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" {{ $devoir->course_id == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label">Date limite</label>
                            <input type="date" name="due_date" value="{{ old('due_date', $devoir->due_date) }}" class="adm-form-input">
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-textarea">{{ old('description', $devoir->description) }}</textarea>
                    </div>

                    @if($devoir->file)
                    <div class="adm-alert adm-alert-success">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Fichier actuel: <a href="{{ Storage::url($devoir->file) }}" target="_blank" style="color:#166534;font-weight:600;">Voir le PDF</a>
                    </div>
                    @endif

                    <div class="adm-form-file adm-mb-2">
                        <label class="adm-form-label">Nouveau fichier (remplace l'ancien)</label>
                        <input type="file" name="file" accept=".pdf" class="w-100">
                    </div>

                    <div class="adm-flex adm-gap-2 adm-mt-3">
                        <a href="{{ route('admin.devoirs.index') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary" style="flex:1;">
                            <i class="bi bi-save-fill"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
