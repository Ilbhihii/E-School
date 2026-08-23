@extends('layouts.admin')

@php
    $isEdit = isset($class) && $class && $class->exists;

    $selectedAdmissionMode = old(
        'admission_mode',
        $isEdit
            ? ($class->admission_mode ?? 'contact')
            : 'contact'
    );

    $formAction = $isEdit
        ? route(
            'admin.subjects.classes.update',
            [$subject, $level, $class]
        )
        : route(
            'admin.subjects.classes.store',
            [$subject, $level]
        );
@endphp

@section('title', $isEdit ? 'Modifier la classe' : 'Créer une classe')
@section('page_title', $isEdit ? 'Modifier la classe' : 'Créer une classe')
@section(
    'breadcrumb',
    $isEdit
        ? 'Matières → Niveaux → Classes → Modifier'
        : 'Matières → Niveaux → Classes → Créer'
)

@section('content')

<div class="class-form-page">
    <div class="class-form-shell">
        <div class="class-form-card">

            <div class="class-form-header">
                <div>
                    <h2>
                        {{
                            $isEdit
                                ? 'Modifier ' . $class->name
                                : 'Créer une classe'
                        }}
                    </h2>

                    <div class="class-form-path">
                        <span>{{ $subject->name }}</span>
                        <i class="bi bi-arrow-right"></i>
                        <span>{{ $level->name }}</span>
                    </div>
                </div>

                <a
                    href="{{ route('admin.subjects.classes', [$subject, $level]) }}"
                    class="class-back-btn"
                >
                    <i class="bi bi-arrow-left"></i>
                    Retour
                </a>
            </div>

            @if($errors->any())
                <div class="class-form-alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>
                        <strong>Vérifiez les informations saisies.</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form
                method="POST"
                action="{{ $formAction }}"
                class="class-form"
            >
                @csrf

                @if($isEdit)
                    @method('PATCH')
                @endif

                <div class="class-field">
                    <label for="name" class="class-label">
                        Nom de la classe
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $isEdit ? $class->name : '') }}"
                        class="class-input @error('name') is-invalid @enderror"
                        placeholder="Ex. Beginner (A1)"
                        maxlength="255"
                        required
                    >

                    @error('name')
                        <div class="class-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="class-field">
                    <label class="class-label">
                        Mode d’accès / inscription
                        <span>*</span>
                    </label>

                    <div class="admission-mode-grid">

                        <label
                            class="admission-mode-card {{
                                $selectedAdmissionMode === 'contact'
                                    ? 'is-active'
                                    : ''
                            }}"
                        >
                            <input
                                type="radio"
                                name="admission_mode"
                                value="contact"
                                {{
                                    $selectedAdmissionMode === 'contact'
                                        ? 'checked'
                                        : ''
                                }}
                            >

                            <span class="admission-mode-icon contact">
                                <i class="bi bi-headset"></i>
                            </span>

                            <span class="admission-mode-content">
                                <strong>Prise en contact</strong>
                                <small>
                                    Le visiteur est dirigé vers le formulaire
                                    de rendez-vous.
                                </small>
                            </span>

                            <span class="admission-check">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        </label>

                        <label
                            class="admission-mode-card {{
                                $selectedAdmissionMode === 'vocal_test'
                                    ? 'is-active'
                                    : ''
                            }}"
                        >
                            <input
                                type="radio"
                                name="admission_mode"
                                value="vocal_test"
                                {{
                                    $selectedAdmissionMode === 'vocal_test'
                                        ? 'checked'
                                        : ''
                                }}
                            >

                            <span class="admission-mode-icon vocal">
                                <i class="bi bi-mic-fill"></i>
                            </span>

                            <span class="admission-mode-content">
                                <strong>Test vocal</strong>
                                <small>
                                    Le visiteur passe le test vocal associé
                                    à cette classe.
                                </small>
                            </span>

                            <span class="admission-check">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        </label>
                    </div>

                    @error('admission_mode')
                        <div class="class-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="class-form-info">
                    <i class="bi bi-stars"></i>
                    <span>
                        {{
                            $isEdit
                                ? 'Après modification, les paramètres de cette classe seront immédiatement mis à jour.'
                                : 'La nouvelle classe sera automatiquement liée à la matière et au niveau sélectionnés.'
                        }}
                    </span>
                </div>

                <div class="class-form-actions">
                    <a
                        href="{{ route('admin.subjects.classes', [$subject, $level]) }}"
                        class="class-btn class-btn-secondary"
                    >
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="class-btn class-btn-primary"
                    >
                        <i class="bi bi-check-lg"></i>
                        {{ $isEdit ? 'Enregistrer' : 'Créer la classe' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.class-form-page {
    width: 100%;
    padding: 26px 16px 50px;
}

.class-form-shell {
    width: min(100%, 730px);
    margin: 0 auto;
}

.class-form-card {
    overflow: hidden;
    border: 1px solid rgba(148,163,184,0.16);
    border-radius: 22px;
    background: linear-gradient(
        145deg,
        rgba(17,27,47,0.98),
        rgba(11,19,34,0.99)
    );
    box-shadow: 0 24px 70px rgba(0,0,0,0.28);
}

.class-form-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 22px;
    padding: 25px 25px 19px;
}

.class-form-header h2 {
    margin: 0 0 7px;
    color: #fff;
    font-size: 1.28rem;
    font-weight: 800;
}

.class-form-path {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    color: rgba(255,255,255,0.48);
    font-size: 0.74rem;
}

.class-form-path i {
    font-size: 0.62rem;
}

.class-back-btn {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 14px;
    border: 1px solid rgba(148,163,184,0.16);
    border-radius: 11px;
    color: rgba(255,255,255,0.82);
    background: rgba(255,255,255,0.025);
    font-size: 0.73rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.class-back-btn:hover {
    color: #fff;
    border-color: rgba(96,165,250,0.35);
    background: rgba(59,130,246,0.09);
}

.class-form {
    padding: 0 25px 22px;
}

.class-field {
    margin-bottom: 19px;
}

.class-label {
    display: block;
    margin-bottom: 8px;
    color: rgba(255,255,255,0.9);
    font-size: 0.73rem;
    font-weight: 750;
}

.class-label > span {
    color: #F87171;
}

.class-input {
    width: 100%;
    min-height: 44px;
    padding: 0 13px;
    outline: none;
    border: 1px solid rgba(148,163,184,0.18);
    border-radius: 11px;
    color: rgba(255,255,255,0.94);
    background: rgba(15,23,42,0.76);
    font-size: 0.81rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.class-input:focus {
    border-color: rgba(99,102,241,0.9);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.10);
}

.class-input.is-invalid {
    border-color: rgba(248,113,113,0.8);
}

.class-error {
    margin-top: 6px;
    color: #FCA5A5;
    font-size: 0.69rem;
}

.admission-mode-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 11px;
}

.admission-mode-card {
    position: relative;
    min-height: 87px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    overflow: hidden;
    border: 1px solid rgba(148,163,184,0.16);
    border-radius: 14px;
    background: rgba(15,23,42,0.48);
    cursor: pointer;
    transition: all 0.2s ease;
}

.admission-mode-card:hover {
    transform: translateY(-2px);
    border-color: rgba(129,140,248,0.42);
    background: rgba(30,41,59,0.62);
}

.admission-mode-card.is-active {
    border-color: #6366F1;
    background: rgba(79,70,229,0.10);
    box-shadow:
        inset 0 0 0 1px rgba(99,102,241,0.12),
        0 8px 25px rgba(79,70,229,0.08);
}

.admission-mode-card input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.admission-mode-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    font-size: 1rem;
}

.admission-mode-icon.contact {
    color: #38BDF8;
    background: rgba(14,165,233,0.13);
}

.admission-mode-icon.vocal {
    color: #C084FC;
    background: rgba(168,85,247,0.13);
}

.admission-mode-content {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding-right: 19px;
}

.admission-mode-content strong {
    color: rgba(255,255,255,0.93);
    font-size: 0.77rem;
    font-weight: 800;
}

.admission-mode-content small {
    color: rgba(255,255,255,0.46);
    font-size: 0.65rem;
    line-height: 1.45;
}

.admission-check {
    position: absolute;
    top: 9px;
    right: 9px;
    width: 20px;
    height: 20px;
    display: none;
    place-items: center;
    border-radius: 50%;
    color: #fff;
    background: #6366F1;
    font-size: 0.65rem;
}

.admission-mode-card.is-active .admission-check {
    display: grid;
}

.class-form-info {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin: 5px 0 19px;
    padding: 12px 13px;
    border: 1px solid rgba(99,102,241,0.20);
    border-radius: 11px;
    color: rgba(255,255,255,0.67);
    background: rgba(79,70,229,0.08);
    font-size: 0.66rem;
    line-height: 1.5;
}

.class-form-info i {
    margin-top: 1px;
    color: #A5B4FC;
}

.class-form-alert {
    display: flex;
    gap: 11px;
    margin: 0 25px 18px;
    padding: 12px 14px;
    border: 1px solid rgba(248,113,113,0.22);
    border-radius: 12px;
    color: #FCA5A5;
    background: rgba(239,68,68,0.08);
    font-size: 0.7rem;
}

.class-form-alert strong {
    color: #FECACA;
}

.class-form-alert ul {
    margin: 5px 0 0;
    padding-left: 17px;
}

.class-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
}

.class-btn {
    min-height: 39px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 15px;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 750;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.class-btn-secondary {
    border: 1px solid rgba(148,163,184,0.18);
    color: rgba(255,255,255,0.78);
    background: rgba(255,255,255,0.025);
}

.class-btn-secondary:hover {
    color: #fff;
    background: rgba(255,255,255,0.055);
}

.class-btn-primary {
    border: 1px solid rgba(129,140,248,0.55);
    color: #fff;
    background: linear-gradient(135deg,#4F46E5,#6366F1);
    box-shadow: 0 10px 22px rgba(79,70,229,0.18);
}

.class-btn-primary:hover {
    color: #fff;
    filter: brightness(1.08);
    transform: translateY(-1px);
}

@media (max-width: 700px) {
    .class-form-page {
        padding: 14px 4px 35px;
    }

    .class-form-header {
        flex-direction: column;
        padding: 20px 18px 16px;
    }

    .class-back-btn {
        width: 100%;
    }

    .class-form {
        padding: 0 18px 18px;
    }

    .class-form-alert {
        margin-left: 18px;
        margin-right: 18px;
    }

    .admission-mode-grid {
        grid-template-columns: 1fr;
    }

    .class-form-actions {
        flex-direction: column-reverse;
    }

    .class-btn {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.admission-mode-card');

    const refreshCards = () => {
        cards.forEach(card => {
            const input = card.querySelector('input[type="radio"]');

            card.classList.toggle(
                'is-active',
                Boolean(input && input.checked)
            );
        });
    };

    cards.forEach(card => {
        card.addEventListener('click', () => {
            const input = card.querySelector('input[type="radio"]');

            if (input) {
                input.checked = true;
            }

            refreshCards();
        });
    });

    refreshCards();
});
</script>

@endsection
