@extends('layouts.student')
@section('title', 'Mes Absences')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-calendar-x" style="color:#DC2626;"></i> Mes Absences</h1>
        <div class="subtitle">Suivez votre assiduité</div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="pr-stat red">
            <div class="pr-stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="pr-stat-value">{{ $totalAbsences }}</div>
            <div class="pr-stat-label">Total absences</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pr-stat green">
            <div class="pr-stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="pr-stat-value">{{ $totalAbsences - ($justifiedCount ?? 0) }}</div>
            <div class="pr-stat-label">Non justifiées</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pr-stat blue">
            <div class="pr-stat-icon"><i class="bi bi-info-circle"></i></div>
            <div class="pr-stat-value">{{ $justifiedCount ?? 0 }}</div>
            <div class="pr-stat-label">Justifiées</div>
        </div>
    </div>
</div>

<div class="pr-card">
    <div class="pr-card-header">
        <h4><i class="bi bi-list-check" style="color:#64748B;"></i> Historique des absences</h4>
        <span class="pr-badge pr-badge-purple">{{ $absences->count() }} entrée{{ $absences->count() > 1 ? 's' : '' }}</span>
    </div>
    <div class="pr-card-body p-0">
        @if($absences->count() > 0)
        <div class="pr-table-wrap">
            <table class="pr-table">
                <thead>
                    <tr><th>Date</th><th>Statut</th><th style="text-align:center;">Justifiée</th></tr>
                </thead>
                <tbody>
                    @foreach($absences as $absence)
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#F1F5F9;">{{ \Carbon\Carbon::parse($absence->date)->format('d M Y') }}</div>
                            <small style="color:#475569;">{{ \Carbon\Carbon::parse($absence->date)->format('l') }}</small>
                        </td>
                        <td><span class="pr-badge pr-badge-danger"><i class="bi bi-x-circle-fill me-1"></i> Absent</span></td>
                        <td style="text-align:center;">
                            @if($absence->justified)
                                <span class="pr-badge pr-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Oui</span>
                            @else
                                <span class="pr-badge pr-badge-warning"><i class="bi bi-clock me-1"></i> Non</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="pr-empty">
            <div class="pr-empty-icon"><i class="bi bi-check-circle" style="color:#059669;"></i></div>
            <h5>Aucune absence enregistrée</h5>
            <p>Vous êtes toujours présent aux cours !</p>
        </div>
        @endif
    </div>
</div>

<div class="pr-card mt-3">
    <div class="pr-card-body">
        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0.75rem;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.08);border-radius:8px;">
            <div style="width:36px;height:36px;border-radius:8px;background:rgba(220,38,38,0.1);display:flex;align-items:center;justify-content:center;font-size:1rem;color:#DC2626;flex-shrink:0;"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div style="font-weight:600;color:#F1F5F9;font-size:0.85rem;margin-bottom:1px;">Situation de l'étudiant</div>
                <div style="font-size:0.78rem;color:#64748B;">{{ $situation ?? 'Absences: ' . $totalAbsences }}</div>
            </div>
        </div>
    </div>
</div>

@endsection
