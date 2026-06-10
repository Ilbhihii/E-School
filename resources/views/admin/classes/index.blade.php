@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📚 Gestion des Niveaux</span></h1>
                <p class="admin-header-subtitle">Créer et gérer les niveaux de la plateforme</p>
            </div>
            <a href="{{ route('admin.classes.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg"></i> Ajouter une classe
            </a>
        </div>

        <!-- TABLE CARD -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h3>Liste des classes</h3>
                    <p>Toutes les classes enregistrées</p>
                </div>
                <span class="adm-badge adm-badge-primary">{{ $classes->count() }} classes</span>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Nom de la classe</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                        <tr>
                            <td><span style="font-weight:600;"><i class="bi bi-mortarboard-fill" style="color:var(--adm-primary);"></i> {{ $class->name }}</span></td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('admin.classes.edit', $class->id) }}" class="adm-action-link adm-action-edit">
                                        <i class="bi bi-pencil-fill"></i> Modifier
                                    </a>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class->id) }}" onsubmit="return confirm('Supprimer cette classe ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;">
                                            <i class="bi bi-trash-fill"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="adm-empty">Aucune classe disponible</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
