@extends('layouts.admin')

@section('title', 'Modifier utilisateur')
@section('page_title', 'Modifier')
@section('breadcrumb', 'Modifier la classe')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-person-gear" style="color:rgba(255,255,255,0.35);"></i> Modifier la classe de {{ $user->name }}</h4>
            </div>
            <div class="adm-card-body">
                <div class="adm-card mb-4" style="background:rgba(0,58,143,0.08);border-color:rgba(0,58,143,0.15);">
                    <div class="adm-card-body" style="padding:1.25rem;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="adm-form-label">Nom</label>
                                <div style="font-weight:600;color:rgba(255,255,255,0.85);">{{ $user->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="adm-form-label">Email</label>
                                <div style="color:var(--adm-text-secondary);">{{ $user->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="adm-form-label">ID</label>
                                <div style="font-family:monospace;color:var(--adm-text-muted);">#{{ $user->id }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="adm-form-label">Classe actuelle</label>
                                <div>
                                    @if($user->classRoom)
                                        <span class="adm-badge adm-badge-info">{{ $user->classRoom->name }}</span>
                                    @else
                                        <span class="adm-badge adm-badge-danger">Aucune</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe à assigner</label>
                        <select name="class_id" class="adm-form-select" required>
                            <option value="">Aucune classe</option>
                            @foreach($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" {{ old('class_id', $user->class_id) == $classRoom->id ? 'selected' : '' }}>
                                    {{ $classRoom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.users.without-class') }}" class="adm-btn adm-btn-ghost flex-fill text-center">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-success flex-fill">
                            <i class="bi bi-check-lg"></i> {{ $user->class_id ? 'Modifier' : 'Assigner Classe' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
