@extends('layouts.admin')

@section('title', 'Maintenance')
@section('page_title', 'Maintenance')
@section('breadcrumb', 'Gestion de la maintenance')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-tools me-2"
                style="color:var(--adm-accent);"
            ></i>
            Maintenance de la plateforme
        </h1>

        <div class="subtitle">
            Programmez une maintenance et informez automatiquement
            les utilisateurs.
        </div>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="adm-alert adm-alert-danger mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
    </div>
@endif

<div class="adm-card maintenance-admin-card">
    <div class="adm-card-header">
        <div>
            <h4>
                <i class="bi bi-calendar-event"></i>
                Programmer une maintenance
            </h4>

            <p class="maintenance-admin-subtitle">
                L'annonce peut être visible avant le début de l'intervention.
            </p>
        </div>

        @if($maintenance->isBlockingNow())
            <span class="maintenance-state maintenance-state-danger">
                <i class="bi bi-circle-fill"></i>
                Maintenance active
            </span>
        @elseif($maintenance->announcement_enabled)
            <span class="maintenance-state maintenance-state-warning">
                <i class="bi bi-clock-fill"></i>
                Maintenance programmée
            </span>
        @else
            <span class="maintenance-state maintenance-state-success">
                <i class="bi bi-check-circle-fill"></i>
                Site opérationnel
            </span>
        @endif
    </div>

    <div class="adm-card-body">
        <form
            method="POST"
            action="{{ route('admin.maintenance.update') }}"
        >
            @csrf
            @method('PUT')

            <div class="adm-form-group">
                <label class="adm-form-label">
                    Message affiché aux utilisateurs
                </label>

                <textarea
                    name="message"
                    rows="4"
                    class="adm-form-control"
                    required
                >{{ old('message', $maintenance->message) }}</textarea>
            </div>

            <div class="maintenance-date-grid">
                <div class="adm-form-group">
                    <label class="adm-form-label">
                        Début de la maintenance
                    </label>

                    <input
                        type="datetime-local"
                        name="start_at"
                        class="adm-form-control"
                        required
                        value="{{
                            old(
                                'start_at',
                                optional($maintenance->start_at)
                                    ->format('Y-m-d\TH:i')
                            )
                        }}"
                    >
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label">
                        Fin prévue
                    </label>

                    <input
                        type="datetime-local"
                        name="end_at"
                        class="adm-form-control"
                        required
                        value="{{
                            old(
                                'end_at',
                                optional($maintenance->end_at)
                                    ->format('Y-m-d\TH:i')
                            )
                        }}"
                    >
                </div>
            </div>

            <label class="maintenance-toggle">
                <input
                    type="checkbox"
                    name="announcement_enabled"
                    value="1"
                    {{
                        old(
                            'announcement_enabled',
                            $maintenance->announcement_enabled
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <span>
                    <strong>
                        Afficher l'annonce de maintenance
                    </strong>

                    <small>
                        Un bandeau sera visible sur la page d'accueil
                        jusqu'à la fin prévue.
                    </small>
                </span>
            </label>

            <label class="maintenance-toggle">
                <input
                    type="checkbox"
                    name="blocking_enabled"
                    value="1"
                    {{
                        old(
                            'blocking_enabled',
                            $maintenance->blocking_enabled
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <span>
                    <strong>
                        Bloquer automatiquement la plateforme
                    </strong>

                    <small>
                        Pendant l'intervalle programmé, les utilisateurs
                        voient la page de maintenance. Les administrateurs
                        conservent l'accès.
                    </small>
                </span>
            </label>

            @if($maintenance->start_at && $maintenance->end_at)
                <div class="maintenance-preview">
                    <i class="bi bi-info-circle"></i>

                    <span>
                        Durée actuellement programmée :
                        <strong>
                            {{ $maintenance->duration_minutes }} minute(s)
                        </strong>
                        — retour prévu le
                        <strong>
                            {{ $maintenance->end_at->format('d/m/Y à H:i') }}
                        </strong>.
                    </span>
                </div>
            @endif

            <button
                type="submit"
                class="adm-btn adm-btn-accent w-100"
                style="padding:13px;margin-top:18px;"
            >
                <i class="bi bi-check-circle"></i>
                Enregistrer la maintenance
            </button>
        </form>
    </div>
</div>

<style>
.maintenance-admin-card {
    max-width: 920px;
    margin: 0 auto;
}

.maintenance-admin-subtitle {
    margin: 3px 0 0;
    color: var(--adm-text-muted);
    font-size: .66rem;
}

.maintenance-date-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.maintenance-toggle {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 13px;
    padding: 14px;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 13px;
    background: rgba(255,255,255,.025);
    cursor: pointer;
}

.maintenance-toggle input {
    margin-top: 4px;
}

.maintenance-toggle strong,
.maintenance-toggle small {
    display: block;
}

.maintenance-toggle strong {
    color: var(--adm-text);
    font-size: .72rem;
}

.maintenance-toggle small {
    margin-top: 3px;
    color: var(--adm-text-muted);
    font-size: .61rem;
    line-height: 1.5;
}

.maintenance-state {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 10px;
    border-radius: 999px;
    font-size: .61rem;
    font-weight: 800;
}

.maintenance-state i {
    font-size: .48rem;
}

.maintenance-state-success {
    color: #86EFAC;
    background: rgba(34,197,94,.08);
    border: 1px solid rgba(34,197,94,.16);
}

.maintenance-state-warning {
    color: #FDE68A;
    background: rgba(245,158,11,.08);
    border: 1px solid rgba(245,158,11,.16);
}

.maintenance-state-danger {
    color: #FDA4AF;
    background: rgba(244,63,94,.08);
    border: 1px solid rgba(244,63,94,.16);
}

.maintenance-preview {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-top: 14px;
    padding: 12px;
    color: #BFDBFE;
    border: 1px solid rgba(59,130,246,.14);
    border-radius: 12px;
    background: rgba(59,130,246,.05);
    font-size: .63rem;
    line-height: 1.55;
}

@media (max-width: 767.98px) {
    .maintenance-date-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection
