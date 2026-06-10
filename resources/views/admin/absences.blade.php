@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📋 Gestion des absences</span></h1>
                <p class="admin-header-subtitle">Suivi des présences et absences des étudiants</p>
            </div>
            <a href="{{ route('admin.absences.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg"></i> Nouvelle absence
            </a>
        </div>

        <!-- FILTER -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <form method="GET" class="adm-flex adm-gap-2 adm-flex-wrap">
                    <select name="class_id" class="adm-form-select" style="width:auto;min-width:200px;">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <button class="adm-btn adm-btn-primary"><i class="bi bi-funnel-fill"></i> Filtrer</button>
                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#eff6ff,#eef2ff);">
                <h3><i class="bi bi-table"></i> Liste des absences</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Date</th>
                            <th>Classe</th>
                            <th style="text-align:center;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absence)
                        <tr>
                            <td>
                                <div class="adm-user-cell">
                                    <div class="adm-avatar adm-avatar-sm">{{ strtoupper(substr($absence->user->name ?? 'E', 0, 1)) }}</div>
                                    <div class="adm-user-cell-info">
                                        <div class="adm-user-cell-name">{{ $absence->user->name ?? 'Inconnu' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="adm-badge adm-badge-gray">{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</span></td>
                            <td><span class="adm-badge adm-badge-primary">{{ $absence->user->classRoom->name ?? '-' }}</span></td>
                            <td style="text-align:center;">
                                @if($absence->present)
                                    <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill"></i> Présent</span>
                                @else
                                    <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill"></i> Absent</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="adm-empty">Aucune absence trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($absences, 'links'))
        <div class="adm-pagination">{{ $absences->appends(request()->query())->links() }}</div>
        @endif

    </div>
</div>
@endsection
