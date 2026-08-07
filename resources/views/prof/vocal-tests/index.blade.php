@extends('layouts.prof')

@section('title', 'Tests de nouveaux étudiants')

@section('content')
<style>
.vt-status {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 9px;
    border-radius:999px;
    font-size:.72rem;
    font-weight:700;
    background:rgba(59,130,246,.12);
    color:#BFDBFE;
}
.vt-student {
    display:flex;
    align-items:center;
    gap:10px;
}
.vt-student-avatar {
    width:38px;
    height:38px;
    border-radius:12px;
    display:grid;
    place-items:center;
    background:linear-gradient(135deg,#0ea5e9,#2563eb);
    color:#fff;
    font-weight:800;
}
</style>

<div class="adm-page-header">
    <div>
        <h1>
            <i class="bi bi-mic-fill me-2" style="color:var(--adm-primary);"></i>
            Tests des nouveaux étudiants
        </h1>
        <div class="subtitle">
            Consultez uniquement les tests que l’administrateur vous a affectés.
        </div>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4>
            <i class="bi bi-person-check-fill" style="color:rgba(255,255,255,.35);"></i>
            Tests partagés avec moi
        </h4>
        <span style="color:var(--adm-text-muted);font-size:.8rem;">
            {{ $submissions->total() }} test(s)
        </span>
    </div>

    <div class="adm-card-body">
        <form
            method="GET"
            action="{{ route('prof.vocal-tests.index') }}"
            style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:1.2rem;"
        >
            <select
                name="status"
                class="adm-form-control"
                style="width:auto;min-width:160px;"
                onchange="this.form.submit()"
            >
                <option value="all">Tous les statuts</option>
                @foreach(\App\Models\VocalTestSubmission::getStatuses() as $value => $label)
                    <option
                        value="{{ $value }}"
                        @selected(request('status') === $value)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select
                name="test_mode"
                class="adm-form-control"
                style="width:auto;min-width:160px;"
                onchange="this.form.submit()"
            >
                <option value="all">Tous les modes</option>
                @foreach(\App\Models\VocalTestSubmission::getModes() as $value => $label)
                    <option
                        value="{{ $value }}"
                        @selected(request('test_mode') === $value)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <a
                href="{{ route('prof.vocal-tests.index') }}"
                class="adm-btn adm-btn-ghost adm-btn-sm"
            >
                Réinitialiser
            </a>
        </form>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Parcours</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td>
                                <div class="vt-student">
                                    <span class="vt-student-avatar">
                                        {{ mb_strtoupper(mb_substr($submission->user?->name ?? 'N', 0, 1)) }}
                                    </span>
                                    <div>
                                        <strong>
                                            {{ $submission->user?->name ?? 'Nouveau candidat' }}
                                        </strong>
                                        <small style="display:block;color:var(--adm-text-muted);">
                                            {{ $submission->user?->email ?? 'Compte non créé' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <strong>{{ $submission->subject?->name ?? '-' }}</strong>
                                <small style="display:block;color:var(--adm-text-muted);">
                                    {{ $submission->level?->name ?? '-' }}
                                    →
                                    {{ $submission->classRoom?->name ?? '-' }}
                                </small>
                            </td>

                            <td>
                                @if($submission->isObservationSubmission())
                                    Observation
                                @elseif($submission->isCompletionSubmission())
                                    Complétion
                                @else
                                    {{ \App\Models\VocalTestSubmission::getModes()[$submission->test_mode] ?? 'Test vocal' }}
                                @endif
                            </td>

                            <td>
                                <span class="vt-status">
                                    {{ \App\Models\VocalTestSubmission::getStatuses()[$submission->status] ?? $submission->status }}
                                </span>
                            </td>

                            <td>
                                @php($score = $submission->final_score ?? $submission->score)
                                {{ $score !== null ? $score . '/100' : '—' }}
                            </td>

                            <td style="color:var(--adm-text-muted);font-size:.8rem;">
                                {{ $submission->submitted_at?->format('d/m/Y H:i') ?? $submission->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td style="text-align:right;">
                                <a
                                    href="{{ route('prof.vocal-tests.show', $submission) }}"
                                    class="adm-btn adm-btn-primary adm-btn-sm"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Voir le test
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="adm-empty">
                                    <div class="adm-empty-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h5>Aucun test affecté</h5>
                                    <p>
                                        Lorsqu’un administrateur vous affectera un test,
                                        il apparaîtra automatiquement ici.
                                    </p>
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
