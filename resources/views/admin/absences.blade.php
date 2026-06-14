@extends('layouts.admin')

@section('title', 'Gestion des absences')
@section('page_title', 'Absences')
@section('breadcrumb', 'Gestion des absences')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Absences</h1>
        <div class="subtitle">Suivi des présences et absences des étudiants</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.absences.create') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvelle absence
        </a>
    </div>
</div>

<!-- Filter -->
<div class="adm-card mb-4">
    <div class="adm-card-body" style="padding:1.25rem;">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="adm-form-label">Filtrer par classe</label>
                <select name="class_id" class="adm-form-select">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="adm-btn adm-btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-calendar-x" style="color:rgba(255,255,255,0.35);"></i> Liste des absences</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $absences->count() }} absence(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
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
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="adm-avatar" style="background:var(--adm-gradient-primary);">
                                    {{ strtoupper(substr($absence->user->name ?? 'E', 0, 1)) }}
                                </div>
                                <span style="font-weight:500;">{{ $absence->user->name ?? 'Inconnu' }}</span>
                            </div>
                        </td>
                        <td style="color:var(--adm-text-secondary);">{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</td>
                        <td><span class="adm-badge adm-badge-info">{{ $absence->user->classRoom->name ?? '-' }}</span></td>
                        <td style="text-align:center;">
                            @if($absence->present)
                                <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill" style="font-size:0.6rem;"></i> Présent</span>
                            @else
                                <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill" style="font-size:0.6rem;"></i> Absent</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-calendar-check"></i></div>
                                <h5>Aucune absence</h5>
                                <p>Aucune absence trouvée pour les critères sélectionnés.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($absences, 'links'))
    <div class="adm-card-footer">
        {{ $absences->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
