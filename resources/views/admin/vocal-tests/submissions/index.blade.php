@extends('layouts.admin')

@section('title', 'Soumissions des tests')
@section('page_title', 'Soumissions des tests')
@section('breadcrumb', 'Tests vocaux → Soumissions')

@section('content')

<style>
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-badge.submitted { background: rgba(96,165,250,0.15); color: #93C5FD; }
.status-badge.under_review { background: rgba(251,191,36,0.15); color: #FCD34D; }
.status-badge.reviewed { background: rgba(148,163,184,0.15); color: #CBD5E1; }
.status-badge.accepted { background: rgba(34,197,94,0.15); color: #4ADE80; }
.status-badge.needs_improvement { background: rgba(239,68,68,0.15); color: #FCA5A5; }

.mode-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
}
.mode-badge.reading { background: rgba(99,102,241,0.12); color: #A5B4FC; }
.mode-badge.tajwid { background: rgba(16,185,129,0.12); color: #6EE7B7; }
.mode-badge.hifd { background: rgba(251,191,36,0.12); color: #FCD34D; }

.submission-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 7px;
    flex-wrap: wrap;
}

.submission-delete-form {
    display: inline-flex;
    margin: 0;
}

.submission-delete-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    min-height: 32px;
    padding: 7px 11px;
    border: 1px solid rgba(239, 68, 68, 0.34);
    border-radius: 9px;
    background: rgba(239, 68, 68, 0.12);
    color: #FCA5A5;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition:
        transform 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease,
        box-shadow 0.2s ease;
}

.submission-delete-btn:hover {
    color: #FFFFFF;
    background: rgba(220, 38, 38, 0.85);
    border-color: rgba(248, 113, 113, 0.9);
    box-shadow: 0 8px 20px rgba(127, 29, 29, 0.28);
    transform: translateY(-1px);
}

.submission-delete-btn:focus-visible {
    outline: 3px solid rgba(248, 113, 113, 0.28);
    outline-offset: 2px;
}

.prof-share-cell {
    min-width: 220px;
}
.prof-share-badges {
    display:flex;
    gap:5px;
    flex-wrap:wrap;
    margin-bottom:7px;
}
.prof-share-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 8px;
    border-radius:999px;
    background:rgba(14,165,233,.12);
    border:1px solid rgba(56,189,248,.2);
    color:#BAE6FD;
    font-size:.7rem;
    font-weight:700;
}
.prof-share-empty {
    color:var(--adm-text-muted);
    font-size:.74rem;
}
.prof-share-details summary {
    list-style:none;
    cursor:pointer;
    color:#C4B5FD;
    font-size:.75rem;
    font-weight:700;
}
.prof-share-details summary::-webkit-details-marker { display:none; }
.prof-share-panel {
    margin-top:8px;
    padding:10px;
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px;
    background:#0d1424;
    text-align:left;
}
.prof-share-checklist {
    display:grid;
    gap:6px;
    max-height:180px;
    overflow:auto;
    margin-bottom:8px;
    padding-right:4px;
}
.prof-share-check {
    display:flex;
    align-items:center;
    gap:8px;
    padding:7px 8px;
    border-radius:9px;
    background:rgba(255,255,255,.035);
    color:#E2E8F0;
    font-size:.74rem;
    cursor:pointer;
}
.prof-share-check input {
    accent-color:#38BDF8;
}

</style>

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-list-check me-2" style="color:var(--adm-primary);"></i> Soumissions des tests</h1>
        <div class="subtitle">Consultez et évaluez les tests vocaux, de complétion et d’observation</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.vocal-tests.prompts.index') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-file-text me-1"></i> Gérer les textes
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-mic" style="color:rgba(255,255,255,0.35);"></i> Réponses reçues</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $submissions->total() }} soumission(s)</span>
        </div>
    </div>
    <div class="adm-card-body">
        <!-- Filtres -->
        <form method="GET" action="{{ route('admin.vocal-tests.submissions.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:1.25rem;">
            <select name="status" class="adm-form-control" style="width:auto;min-width:140px;" onchange="this.form.submit()">
                <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Tous les statuts</option>
                @foreach(\App\Models\VocalTestSubmission::getStatuses() as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="test_mode" class="adm-form-control" style="width:auto;min-width:140px;" onchange="this.form.submit()">
                <option value="all" {{ request('test_mode') === 'all' || !request('test_mode') ? 'selected' : '' }}>Tous les modes</option>
                @foreach(\App\Models\VocalTestSubmission::getModes() as $val => $label)
                    <option value="{{ $val }}" {{ request('test_mode') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="prof_id" class="adm-form-control" style="width:auto;min-width:180px;" onchange="this.form.submit()">
                <option value="all" {{ request('prof_id') === 'all' || !request('prof_id') ? 'selected' : '' }}>Tous les professeurs</option>
                <option value="unassigned" {{ request('prof_id') === 'unassigned' ? 'selected' : '' }}>Non affectés</option>
                @foreach($professors as $professor)
                    <option value="{{ $professor->id }}" {{ (string) request('prof_id') === (string) $professor->id ? 'selected' : '' }}>
                        {{ $professor->name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('admin.vocal-tests.submissions.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">Réinitialiser</a>
        </form>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Matière</th>
                        <th>Niveau / Classe</th>
                        <th>Mode</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Note</th>
                        <th>Professeurs</th>
                        <th>Soumis le</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="adm-avatar" style="background:linear-gradient(135deg,#003A8F,#2563EB);width:34px;height:34px;font-size:0.8rem;">
                                    {{ strtoupper(substr($submission->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <span style="font-weight:500;font-size:0.85rem;">{{ $submission->user?->name ?? 'Utilisateur #'.$submission->user_id }}</span>
                            </div>
                        </td>
                        <td><span class="adm-badge adm-badge-primary">{{ $submission->subject?->name ?? '-' }}</span></td>
                        <td style="font-size:0.8rem;color:var(--adm-text-muted);">
                            {{ $submission->level?->name ?? '-' }}<br>
                            <small>{{ $submission->classRoom?->name ?? '-' }}</small>
                        </td>
                        <td>
                            @if($submission->isObservationSubmission())
                                <span class="mode-badge reading">
                                    <i class="bi bi-eye-fill"></i>
                                    Observation vocale
                                </span>
                            @elseif($submission->isCompletionSubmission())
                                <span class="mode-badge hifd">
                                    <i class="bi bi-puzzle-fill"></i>
                                    Complétion
                                </span>
                            @elseif($submission->test_mode)
                                <span class="mode-badge {{ $submission->test_mode }}">
                                    {{ \App\Models\VocalTestSubmission::getModes()[$submission->test_mode] ?? $submission->test_mode }}
                                </span>
                            @else
                                <span style="color:var(--adm-text-muted);font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td style="font-size:0.85rem;">
                            @if(
                                $submission->isObservationSubmission()
                                && $submission->duration_seconds
                            )
                                <span class="adm-badge adm-badge-info">
                                    Audio ·
                                    {{ $submission->duration_seconds }}s
                                </span>
                            @elseif($submission->isObservationSubmission())
                                <span class="adm-badge adm-badge-info">
                                    {{
                                        $submission->observationResponseMode()
                                            === 'image'
                                            ? 'Photo'
                                            : 'Texte'
                                    }}
                                </span>
                            @elseif($submission->duration_seconds)
                                <span class="adm-badge adm-badge-info">{{ $submission->duration_seconds }}s</span>
                            @else
                                <span style="color:var(--adm-text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $submission->status }}">
                                {{ \App\Models\VocalTestSubmission::getStatuses()[$submission->status] ?? $submission->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $displayScore = $submission->final_score ?? $submission->score;
                            @endphp
                            @if($displayScore !== null)
                                <span style="font-weight:700;font-size:0.9rem;color:{{ $displayScore >= 70 ? '#4ADE80' : ($displayScore >= 40 ? '#FCD34D' : '#FCA5A5') }};">
                                    {{ $displayScore }}/100
                                </span>
                            @else
                                <span style="color:var(--adm-text-muted);">—</span>
                            @endif
                        </td>
                        <td class="prof-share-cell">
                            <div class="prof-share-badges">
                                @forelse($submission->professors as $assignedProfessor)
                                    <span class="prof-share-badge">
                                        <i class="bi bi-person-check-fill"></i>
                                        {{ $assignedProfessor->name }}
                                    </span>
                                @empty
                                    <span class="prof-share-empty">Aucun professeur</span>
                                @endforelse
                            </div>

                            <details class="prof-share-details">
                                <summary>
                                    <i class="bi bi-person-plus-fill me-1"></i>
                                    Affecter / modifier
                                </summary>

                                <form
                                    method="POST"
                                    action="{{ route('admin.vocal-tests.submissions.professors', $submission) }}"
                                    class="prof-share-panel"
                                >
                                    @csrf
                                    <label class="adm-form-label" style="font-size:.72rem;">
                                        Choisissez un ou plusieurs professeurs
                                    </label>
                                    <div class="prof-share-checklist">
                                        @foreach($professors as $professor)
                                            <label class="prof-share-check">
                                                <input
                                                    type="checkbox"
                                                    name="prof_ids[]"
                                                    value="{{ $professor->id }}"
                                                    {{ $submission->professors->contains('id', $professor->id) ? 'checked' : '' }}
                                                >
                                                <span>
                                                    <strong>{{ $professor->name }}</strong><br>
                                                    <small>{{ $professor->email }}</small>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <small style="display:block;color:var(--adm-text-muted);margin-bottom:8px;">
                                        Laissez tout désélectionné pour retirer l’accès.
                                    </small>
                                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm w-100">
                                        <i class="bi bi-check2 me-1"></i>
                                        Enregistrer l’accès
                                    </button>
                                </form>
                            </details>
                        </td>
                        <td style="font-size:0.8rem;color:var(--adm-text-muted);">
                            {{ $submission->submitted_at?->format('d/m/Y H:i') ?? $submission->created_at->format('d/m/Y') }}
                        </td>
                        <td style="text-align:right;">
                            <div class="submission-actions">
                                <a
                                    href="{{ route('admin.vocal-tests.submissions.show', $submission) }}"
                                    class="adm-btn adm-btn-primary adm-btn-sm"
                                >
                                    <i class="bi bi-eye"></i>
                                    Voir
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.vocal-tests.submissions.destroy', $submission) }}"
                                    class="submission-delete-form"
                                    onsubmit="return confirm('Voulez-vous vraiment supprimer cette soumission ? Les fichiers associés seront également supprimés.');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="submission-delete-btn"
                                        title="Supprimer cette soumission"
                                        aria-label="Supprimer la soumission de {{ $submission->user?->name ?? 'cet élève' }}"
                                    >
                                        <i class="bi bi-trash3-fill"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-mic-mute"></i></div>
                                <h5>Aucune soumission</h5>
                                <p>Les réponses des étudiants apparaîtront ici.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($submissions->hasPages())
    <div class="adm-card-footer">
        {{ $submissions->links() }}
    </div>
    @endif
</div>

@endsection