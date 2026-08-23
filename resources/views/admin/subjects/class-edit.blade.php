@extends('layouts.admin')

@section('title', 'Modifier la classe')
@section('page_title', 'Modifier la classe')
@section('breadcrumb', 'Matières → Niveaux → Classes → Modifier')

@section('content')

<style>
.entity-edit-shell {
    width: min(100%, 760px);
    margin: 0 auto;
}

.entity-edit-card {
    border: 1px solid var(--adm-border, rgba(148,163,184,.15));
    border-radius: 20px;
    padding: 22px;
    background: var(--adm-card-bg, rgba(15,23,42,.78));
    box-shadow: 0 18px 44px rgba(2,6,23,.16);
}

.entity-edit-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}

.entity-edit-head h1 {
    margin: 0;
    color: var(--adm-text, #f8fafc);
    font-size: 1.35rem;
    font-weight: 850;
}

.entity-edit-head p {
    margin: 7px 0 0;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .8rem;
    line-height: 1.55;
}

.entity-edit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.entity-edit-group {
    min-width: 0;
}

.entity-edit-group.full {
    grid-column: 1 / -1;
}

.entity-edit-label {
    display: block;
    margin-bottom: 7px;
    color: var(--adm-text, #e2e8f0);
    font-size: .72rem;
    font-weight: 760;
}

.entity-edit-control {
    width: 100%;
    min-height: 44px;
    padding: 10px 12px;
    border: 1px solid var(--adm-border, rgba(148,163,184,.17));
    border-radius: 11px;
    outline: none;
    color: var(--adm-text, #f8fafc);
    background: rgba(15,23,42,.66);
    font: inherit;
    font-size: .8rem;
}

textarea.entity-edit-control {
    min-height: 100px;
    resize: vertical;
}

.entity-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.entity-edit-note {
    margin-top: 12px;
    padding: 11px 13px;
    border: 1px solid rgba(99,102,241,.16);
    border-radius: 12px;
    color: #cbd5e1;
    background: rgba(99,102,241,.06);
    font-size: .72rem;
    line-height: 1.55;
}

.admission-mode-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.admission-mode-option {
    position: relative;
    display: block;
    cursor: pointer;
}

.admission-mode-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.admission-mode-card {
    min-height: 92px;
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 14px;
    border: 1px solid var(--adm-border, rgba(148,163,184,.17));
    border-radius: 14px;
    background: rgba(15,23,42,.56);
    transition: border-color .2s ease, background .2s ease, transform .2s ease;
}

.admission-mode-option:hover .admission-mode-card {
    transform: translateY(-1px);
    border-color: rgba(99,102,241,.45);
}

.admission-mode-option input:checked + .admission-mode-card {
    border-color: #6366f1;
    background: rgba(99,102,241,.12);
    box-shadow: 0 0 0 2px rgba(99,102,241,.12);
}

.admission-mode-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.05rem;
}

.admission-mode-icon.contact {
    color: #38bdf8;
    background: rgba(56,189,248,.12);
}

.admission-mode-icon.vocal {
    color: #c4b5fd;
    background: rgba(167,139,250,.12);
}

.admission-mode-copy strong {
    display: block;
    margin-bottom: 4px;
    color: var(--adm-text, #f8fafc);
    font-size: .78rem;
}

.admission-mode-copy span {
    display: block;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .68rem;
    line-height: 1.45;
}

@media (max-width: 700px) {
    .entity-edit-grid,
    .admission-mode-grid {
        grid-template-columns: 1fr;
    }

    .entity-edit-group.full {
        grid-column: auto;
    }
}
</style>


<div class="entity-edit-shell">
    <div class="entity-edit-card">
        <div class="entity-edit-head">
            <div>
                <h1>Modifier {{ $class->name }}</h1>
                <p>
                    {{ $subject->name }} → {{ $level->name }}
                </p>
            </div>

            <a
                href="{{ route('admin.subjects.classes', [$subject, $level]) }}"
                class="adm-btn adm-btn-ghost"
            >
                <i class="bi bi-arrow-left"></i>
                Retour
            </a>
        </div>

        <form
            method="POST"
            action="{{ route('admin.subjects.classes.update', [$subject, $level, $class]) }}"
        >
            @csrf
            @method('PATCH')

            <div class="entity-edit-grid">
                <div class="entity-edit-group full">
                    <label class="entity-edit-label" for="name">
                        Nom de la classe *
                    </label>

                    <input
                        id="name"
                        name="name"
                        class="entity-edit-control"
                        value="{{ old('name', $class->name) }}"
                        maxlength="120"
                        required
                    >

                    @error('name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="entity-edit-group full">
                    <span class="entity-edit-label">
                        Mode d’accès / inscription *
                    </span>

                    @php
                        $selectedAdmissionMode = old(
                            'admission_mode',
                            $class->admission_mode ?: 'contact'
                        );
                    @endphp

                    <div class="admission-mode-grid">
                        <label class="admission-mode-option">
                            <input
                                type="radio"
                                name="admission_mode"
                                value="contact"
                                {{ $selectedAdmissionMode === 'contact' ? 'checked' : '' }}
                                required
                            >

                            <span class="admission-mode-card">
                                <span class="admission-mode-icon contact">
                                    <i class="bi bi-headset"></i>
                                </span>
                                <span class="admission-mode-copy">
                                    <strong>Prise en contact</strong>
                                    <span>Le visiteur est dirigé vers le formulaire de rendez-vous.</span>
                                </span>
                            </span>
                        </label>

                        <label class="admission-mode-option">
                            <input
                                type="radio"
                                name="admission_mode"
                                value="vocal_test"
                                {{ $selectedAdmissionMode === 'vocal_test' ? 'checked' : '' }}
                                required
                            >

                            <span class="admission-mode-card">
                                <span class="admission-mode-icon vocal">
                                    <i class="bi bi-mic-fill"></i>
                                </span>
                                <span class="admission-mode-copy">
                                    <strong>Test vocal</strong>
                                    <span>Le visiteur passe le test vocal associé à cette classe.</span>
                                </span>
                            </span>
                        </label>
                    </div>

                    @error('admission_mode')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="entity-edit-note">
                <i class="bi bi-magic me-1"></i>
                Après modification, les 4 créneaux actifs sont
                automatiquement resynchronisés.
                Débutant → D1-D4, Intermédiaire → I1-I4,
                Avancé → A1-A4, autre nom → G1-G4.
            </div>

            <div class="entity-edit-actions">
                <a
                    href="{{ route('admin.subjects.classes', [$subject, $level]) }}"
                    class="adm-btn adm-btn-ghost"
                >
                    Annuler
                </a>

                <button class="adm-btn adm-btn-primary" type="submit">
                    <i class="bi bi-check-lg"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
