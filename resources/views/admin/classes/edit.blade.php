@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:600px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">✏️ Modifier la classe</span></h1>
                <p class="admin-header-subtitle">Mettez à jour les informations de la classe</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.classes.update',$class->id) }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Nom de la classe</label>
                        <input type="text" name="name" value="{{ $class->name }}" class="adm-form-input" placeholder="Ex: Terminale A" required>
                        @error('name') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-flex adm-gap-2">
                        <a href="{{ route('admin.classes.index') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
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
