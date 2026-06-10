@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">➕ Ajouter une classe</span></h1>
                <p class="admin-header-subtitle">Créer une nouvelle classe pour organiser les cours</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.classes.store') }}">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Nom de la classe</label>
                        <input type="text" name="name" class="adm-form-input" placeholder="Ex: Terminale A" required>
                        @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg" style="width:100%;">
                        <i class="bi bi-plus-lg"></i> Ajouter la classe
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
