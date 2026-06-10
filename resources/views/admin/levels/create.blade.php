@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Ajouter un Niveau</span></h1>
                <p class="admin-header-subtitle">Créer un nouveau niveau d'enseignement</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form action="{{ route('admin.levels.store') }}" method="POST">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Nom du niveau</label>
                        <input type="text" name="name" class="adm-form-input" placeholder="Ex: 1ère année collège" required>
                        @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière</label>
                        <select name="subject_id" class="adm-form-select" required>
                            <option value="">Choisir matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-flex adm-gap-2">
                        <a href="{{ route('admin.levels.index') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="adm-btn adm-btn-success" style="flex:1;">
                            <i class="bi bi-check-circle"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
