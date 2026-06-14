@extends('layouts.admin')

@section('title', 'Modifier devoir')
@section('page_title', 'Modifier devoir')
@section('breadcrumb', 'Modifier un devoir')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-pencil" style="color:rgba(255,255,255,0.35);"></i> Modifier le devoir</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.devoirs.update', $devoir ?? '') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Titre</label>
                        <input type="text" name="title" value="{{ old('title', $devoir->title ?? '') }}" class="adm-form-control" placeholder="Titre du devoir" required>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Description</label>
                        <textarea name="description" rows="4" class="adm-form-control adm-form-textarea" placeholder="Description...">{{ old('description', $devoir->description ?? '') }}</textarea>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Date limite</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $devoir->due_date ?? '') }}" class="adm-form-control" required>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.devoirs.index') }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary flex-fill">
                            <i class="bi bi-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
