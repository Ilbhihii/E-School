@extends('layouts.prof')

@section('title', 'Historique des Absences')
@section('page_title', 'Historique Absences')
@section('breadcrumb', 'Suivi des présences')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-clock-history me-2" style="color:var(--adm-primary);"></i> Historique des absences</h1>
        <div class="subtitle">Suivi et modification des présences étudiants</div>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="adm-card">
    <div class="adm-card-body">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div class="adm-form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                <label class="adm-form-label">Filtrer par classe</label>
                <select name="class_id" class="adm-form-select">
                    <option value="">-- Toutes classes --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-funnel me-1"></i> Filtrer</button>
            <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost"><i class="bi bi-x me-1"></i> Reset</a>
        </form>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-list-check" style="color:rgba(255,255,255,0.35);"></i> Liste des absences</h4>
    </div>
    <div class="adm-card-body p-0">
        @if($absences->count() > 0)
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Date</th>
                        <th>Classe</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absences as $absence)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#6366F1,#8B5CF6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($absence->user?->name ?? 'E', 0, 1)) }}
                                </div>
                                <span style="font-weight:500;">{{ $absence->user?->name ?? 'Inconnu' }}</span>
                            </div>
                        </td>
                        <td style="color:var(--adm-text-muted);font-size:0.85rem;">{{ $absence->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="adm-badge adm-badge-info">{{ $absence->user->classRoom?->name ?? 'Non assigné' }}</span></td>
                        <td style="text-align:center;">
                            @if($absence->present)
                                <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle me-1"></i> Présent</span>
                            @else
                                <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle me-1"></i> Absent</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('prof.absences.update', $absence->id) }}" style="display:flex;gap:4px;justify-content:center;">
                                @csrf @method('PUT')
                                <select name="present" class="adm-form-select" style="width:100px;font-size:0.8rem;padding:6px 10px;" onchange="this.form.submit()">
                                    <option value="1" {{ $absence->present ? 'selected' : '' }}>Présent</option>
                                    <option value="0" {{ !$absence->present ? 'selected' : '' }}>Absent</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(isset($absences))
        <div class="adm-card-footer">
            {{ $absences->appends(request()->query())->links() }}
        </div>
        @endif
        @else
        <div class="adm-empty" style="padding:3rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-calendar-check"></i></div>
            <h5>🎉 Parfait !</h5>
            <p>Aucune absence enregistrée pour le moment.</p>
        </div>
        @endif
    </div>
</div>

@endsection
