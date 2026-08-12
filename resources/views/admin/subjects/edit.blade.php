@extends('layouts.admin')

@section('title', 'Modifier la matière')
@section('page_title', 'Modifier la matière')
@section('breadcrumb', 'Matières → Modifier')

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
                <h1>Modifier {{ $subject->name }}</h1>
                <p>
                    Modifiez le nom, la description ou le statut.
                    Les niveaux, classes, créneaux et cours restent conservés.
                </p>
            </div>

            <a
                href="{{ route('admin.subjects.index') }}"
                class="adm-btn adm-btn-ghost"
            >
                <i class="bi bi-arrow-left"></i>
                Retour
            </a>
        </div>

        <form
            method="POST"
            action="{{ route('admin.subjects.update', $subject) }}"
        >
            @csrf
            @method('PATCH')

            <div class="entity-edit-grid">
                <div class="entity-edit-group">
                    <label class="entity-edit-label" for="name">
                        Matière *
                    </label>

                    <input
                        id="name"
                        name="name"
                        class="entity-edit-control"
                        value="{{ old('name', $subject->name) }}"
                        maxlength="120"
                        required
                    >

                    @error('name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="entity-edit-group">
                    <label class="entity-edit-label" for="status">
                        Statut *
                    </label>

                    @php
                        $currentStatus = old(
                            'status',
                            $subject->status ?: 'active'
                        );
                    @endphp

                    <select
                        id="status"
                        name="status"
                        class="entity-edit-control"
                        required
                    >
                        <option
                            value="active"
                            {{ $currentStatus === 'active' ? 'selected' : '' }}
                        >
                            Active
                        </option>

                        <option
                            value="coming_soon"
                            {{ $currentStatus === 'coming_soon' ? 'selected' : '' }}
                        >
                            Bientôt disponible
                        </option>

                        <option
                            value="inactive"
                            {{ $currentStatus === 'inactive' ? 'selected' : '' }}
                        >
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="entity-edit-group full">
                    <label class="entity-edit-label" for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="entity-edit-control"
                        maxlength="1000"
                    >{{ old('description', $subject->description) }}</textarea>
                </div>
            </div>

            <div class="entity-edit-actions">
                <a
                    href="{{ route('admin.subjects.index') }}"
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
