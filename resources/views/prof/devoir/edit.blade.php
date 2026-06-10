@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:700px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">✏️ Modifier le devoir</span></h1>
                <p class="admin-header-subtitle">Formulaire d'édition</p>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('prof.devoir.update', $devoir) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">📝 Titre du devoir</label>
                        <input type="text" name="title" value="{{ $devoir->title }}" class="adm-form-input" placeholder="Entrez un titre descriptif...">
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">📄 Description détaillée</label>
                        <textarea name="description" rows="5" class="adm-form-textarea" placeholder="Décrivez les instructions du devoir...">{{ $devoir->description }}</textarea>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">🏫 Classe</label>
                        <select name="class_room_id" class="adm-form-select">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $class->id == $devoir->class_room_id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">⏰ Date limite</label>
                        <input type="date" name="due_date" value="{{ $devoir->due_date }}" class="adm-form-input">
                    </div>

                    <div class="adm-form-file adm-mb-2">
                        <label class="adm-form-label">📎 Nouveau fichier (optionnel)</label>
                        <input type="file" name="file" class="w-100" accept=".pdf,.doc,.docx">
                        <small style="color:var(--adm-text-secondary);">Le fichier sera remplacé par le nouveau. Taille max: 10Mo.</small>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg" style="width:100%;">
                        <i class="bi bi-save-fill"></i> Modifier le devoir
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
