@extends('layouts.admin')

@section('title', 'Étudiants sans classe')
@section('page_title', 'Sans classe')
@section('breadcrumb', 'Étudiants sans classe')

@section('content')
<div class="adm-page-header">
    <div>
        <h1>Étudiants sans classe ({{ $count ?? 0 }})</h1>
        <div class="subtitle">Ces étudiants n'ont pas de classe assignée et ne peuvent pas accéder aux lives et cours.</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

@if(($count ?? 0) === 0)
    <div class="adm-card">
        <div class="adm-card-body text-center" style="padding:3rem;">
            <div style="font-size:3rem;margin-bottom:1rem;opacity:0.3;"><i class="bi bi-check-circle"></i></div>
            <h5 style="color:rgba(255,255,255,0.6);font-weight:600;">🎉 Tous les étudiants ont une classe !</h5>
            <p style="color:var(--adm-text-muted);">Aucun étudiant n'est sans classe.</p>
        </div>
    </div>
@else
    <div class="adm-card">
        <div class="adm-card-header">
            <h4><i class="bi bi-people" style="color:rgba(255,255,255,0.35);"></i> Assigner des classes</h4>
        </div>
        <div class="adm-card-body p-0">
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Classe à assigner</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td style="font-family:monospace;color:var(--adm-text-muted);">#{{ $student->id }}</td>
                            <td>
                                <div style="font-weight:500;">{{ $student->name }}</div>
                                <div style="color:var(--adm-text-muted);font-size:0.75rem;">Inscrit: {{ $student->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td style="color:var(--adm-text-secondary);">{{ $student->email }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.update', $student->id) }}" style="display:flex;gap:8px;">
                                    @csrf @method('PUT')
                                    <select name="class_id" class="adm-form-select" style="width:auto;min-width:160px;padding:7px 12px;font-size:0.8rem;">
                                        <option value="">Aucune</option>
                                        @foreach($classRooms as $classRoom)
                                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="adm-btn adm-btn-success adm-btn-sm" type="submit">
                                        <i class="bi bi-check-lg"></i> Assigner
                                    </button>
                                </form>
                            </td>
                            <td style="text-align:center;">
                                <span class="adm-badge adm-badge-danger">🚨 URGENT</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
