@extends('layouts.admin')

@section('title', 'Ajouter une classe')
@section('page_title', 'Nouvelle classe')
@section('breadcrumb', 'Ajouter une classe')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-plus-circle" style="color:rgba(255,255,255,0.35);"></i> Ajouter une classe</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.classes.store') }}">
                    @csrf
                    <div class="adm-form-group">
                        <label class="adm-form-label">Nom de la classe</label>
                        <input type="text" name="name" class="adm-form-control" placeholder="Ex: Groupe A" required>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Niveau <span style="color:var(--adm-danger);">*</span></label>
                        <select name="level_id" class="adm-form-select" required>
                            <option value="">Choisir un niveau</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id', $selectedLevelId ?? '') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary w-100">
                        <i class="bi bi-plus-lg"></i> Ajouter la classe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
