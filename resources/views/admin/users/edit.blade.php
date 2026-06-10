@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:700px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">✏️ Modifier la classe de {{ $user->name }}</span></h1>
                <p class="admin-header-subtitle">Assigner une classe à cet étudiant</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="adm-grid-2 adm-mb-3">
                        <div class="adm-detail-item">
                            <label>Nom</label>
                            <div class="value">{{ $user->name }}</div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Email</label>
                            <div class="value" style="font-family:monospace;">{{ $user->email }}</div>
                        </div>
                        <div class="adm-detail-item">
                            <label>ID</label>
                            <div class="value">#{{ $user->id }}</div>
                        </div>
                        <div class="adm-detail-item">
                            <label>Classe actuelle</label>
                            <div class="value">
                                <span class="adm-badge {{ $user->class_id ? 'adm-badge-success' : 'adm-badge-gray' }}">
                                    {{ $user->classRoom?->name ?? '❌ Aucune' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe à assigner</label>
                        <select name="class_id" class="adm-form-select" required>
                            <option value="">❌ Aucune classe</option>
                            @foreach($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" {{ old('class_id', $user->class_id) == $classRoom->id ? 'selected' : '' }}>
                                    {{ $classRoom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="adm-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="adm-flex adm-gap-2 adm-mt-3">
                        <a href="{{ route('admin.users.without-class') }}" class="adm-btn adm-btn-ghost" style="flex:1;text-align:center;">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="adm-btn adm-btn-success" style="flex:1;">
                            <i class="bi bi-check-lg"></i> {{ $user->class_id ? 'Modifier' : 'Assigner Classe' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
