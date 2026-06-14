@extends('layouts.admin')

@section('title', 'Modifier la classe')
@section('page_title', 'Modifier classe')
@section('breadcrumb', 'Modifier la classe')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-pencil" style="color:rgba(255,255,255,0.35);"></i> Modifier la classe</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.classes.update',$class->id) }}">
                    @csrf @method('PUT')
                    <div class="adm-form-group">
                        <label class="adm-form-label">Nom de la classe</label>
                        <input type="text" name="name" value="{{ $class->name }}" class="adm-form-control" placeholder="Ex: Terminale A" required>
                    </div>
                    <button type="submit" class="adm-btn adm-btn-primary w-100">
                        <i class="bi bi-save"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
