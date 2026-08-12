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

@media (max-width: 700px) {
    .entity-edit-grid {
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
                <h1>Modifier { $class->name }</h1>
                <p>
                    { $subject->name } → { $level->name }
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
