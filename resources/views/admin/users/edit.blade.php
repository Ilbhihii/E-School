@extends('layouts.admin')

@section('title', 'Modifier l’étudiant')
@section('page_title', 'Modifier l’étudiant')
@section('breadcrumb', 'Utilisateurs → Modification étudiant')

@section('content')
<style>
    .student-edit-page {
        --se-bg: #080d17;
        --se-panel: #0d1422;
        --se-panel-2: #111a2b;
        --se-input: #09111f;
        --se-border: rgba(148, 163, 184, .15);
        --se-border-hover: rgba(148, 163, 184, .28);
        --se-text: #f8fafc;
        --se-muted: #8fa0b8;
        --se-blue: #3b82f6;
        --se-violet: #7c3aed;
        --se-green: #22c55e;
        --se-red: #ef4444;
        --se-orange: #f59e0b;
        max-width: 1120px;
        margin: 0 auto;
        padding: 16px 0 40px;
        color: var(--se-text);
    }

    .student-edit-page *,
    .student-edit-page *::before,
    .student-edit-page *::after {
        box-sizing: border-box;
    }

    .se-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 28px 30px;
        margin-bottom: 22px;
        border: 1px solid rgba(96, 165, 250, .18);
        border-radius: 22px;
        background:
            radial-gradient(circle at 88% 10%, rgba(124,58,237,.22), transparent 34%),
            linear-gradient(135deg, #0b1120, #111827 58%, #171b2f);
        box-shadow: 0 20px 45px rgba(0,0,0,.25);
    }

    .se-hero::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        right: -60px;
        bottom: -90px;
        border-radius: 50%;
        background: rgba(59,130,246,.10);
        pointer-events: none;
    }

    .se-hero-main {
        min-width: 0;
        position: relative;
        z-index: 1;
    }

    .se-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(59,130,246,.09);
        border: 1px solid rgba(96,165,250,.18);
        color: #93c5fd;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .se-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(25px, 4vw, 36px);
        font-weight: 850;
        letter-spacing: -.03em;
    }

    .se-hero p {
        margin: 8px 0 0;
        color: #96a4ba;
        font-size: 13.5px;
        line-height: 1.65;
    }

    .se-avatar {
        position: relative;
        z-index: 1;
        flex: 0 0 66px;
        width: 66px;
        height: 66px;
        display: grid;
        place-items: center;
        border-radius: 18px;
        color: #fff;
        font-size: 28px;
        font-weight: 900;
        background: linear-gradient(135deg, #2563eb, #6d28d9);
        box-shadow: 0 12px 30px rgba(37,99,235,.23);
    }

    .se-alert {
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 13px;
        font-size: 13px;
        line-height: 1.6;
    }

    .se-alert-success {
        color: #bbf7d0;
        background: rgba(34,197,94,.08);
        border: 1px solid rgba(34,197,94,.22);
    }

    .se-alert-danger {
        color: #fecaca;
        background: rgba(239,68,68,.08);
        border: 1px solid rgba(239,68,68,.22);
    }

    .se-card {
        margin-bottom: 22px;
        overflow: hidden;
        border-radius: 20px;
        border: 1px solid var(--se-border);
        background: linear-gradient(180deg, rgba(17,26,43,.98), rgba(13,20,34,.98));
        box-shadow: 0 14px 36px rgba(0,0,0,.22);
    }

    .se-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 24px;
        border-bottom: 1px solid rgba(148,163,184,.09);
    }

    .se-card-title {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .se-card-icon {
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        color: #60a5fa;
        background: rgba(59,130,246,.09);
        border: 1px solid rgba(96,165,250,.15);
    }

    .se-card-title h2 {
        margin: 0;
        color: #f8fafc;
        font-size: 15px;
        font-weight: 800;
    }

    .se-card-title p {
        margin: 3px 0 0;
        color: #73839b;
        font-size: 11.5px;
    }

    .se-card-body {
        padding: 24px;
    }

    .se-label {
        display: block;
        margin-bottom: 7px;
        color: #cbd5e1;
        font-size: 12px;
        font-weight: 750;
    }

    .se-required {
        color: #f87171;
    }

    .se-input-wrap {
        position: relative;
    }

    .se-input-wrap > i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
        transition: .2s ease;
    }

    .se-input {
        width: 100%;
        height: 48px;
        padding: 0 14px 0 42px;
        border: 1px solid var(--se-border);
        border-radius: 12px;
        outline: none;
        background: var(--se-input);
        color: #f8fafc;
        font-size: 13px;
        transition: .2s ease;
    }

    .se-input::placeholder {
        color: #526078;
    }

    .se-input:hover {
        border-color: var(--se-border-hover);
    }

    .se-input:focus {
        border-color: var(--se-blue);
        background: #0b1424;
        box-shadow: 0 0 0 4px rgba(59,130,246,.09);
    }

    .se-input-wrap:focus-within > i {
        color: #60a5fa;
    }

    .se-field-error {
        margin-top: 6px;
        color: #f87171;
        font-size: 11.5px;
        font-weight: 650;
    }

    .se-status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 20px;
    }

    .se-status-box {
        min-width: 0;
        padding: 16px;
        border: 1px solid var(--se-border);
        border-radius: 14px;
        background: rgba(255,255,255,.022);
    }

    .se-status-box-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .se-status-label {
        color: #aebbd0;
        font-size: 12px;
        font-weight: 750;
    }

    .se-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .se-pill.green {
        color: #bbf7d0;
        background: rgba(34,197,94,.09);
        border: 1px solid rgba(34,197,94,.20);
    }

    .se-pill.red {
        color: #fecaca;
        background: rgba(239,68,68,.08);
        border: 1px solid rgba(239,68,68,.20);
    }

    .se-pill.orange {
        color: #fde68a;
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.20);
    }

    .se-status-detail {
        margin-top: 10px;
        color: #73839b;
        font-size: 11.5px;
        line-height: 1.55;
    }

    .se-switch-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 20px;
        padding: 15px 16px;
        border: 1px solid var(--se-border);
        border-radius: 14px;
        background: rgba(255,255,255,.022);
    }

    .se-switch-copy strong {
        display: block;
        color: #e5e7eb;
        font-size: 12.5px;
    }

    .se-switch-copy small {
        display: block;
        margin-top: 3px;
        color: #718096;
        font-size: 11px;
    }

    .se-switch {
        position: relative;
        width: 50px;
        height: 27px;
        flex: 0 0 50px;
    }

    .se-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .se-slider {
        position: absolute;
        inset: 0;
        cursor: pointer;
        border-radius: 999px;
        background: #273244;
        border: 1px solid rgba(148,163,184,.18);
        transition: .2s ease;
    }

    .se-slider::before {
        content: "";
        position: absolute;
        width: 19px;
        height: 19px;
        left: 3px;
        top: 3px;
        border-radius: 50%;
        background: #94a3b8;
        transition: .2s ease;
    }

    .se-switch input:checked + .se-slider {
        background: rgba(34,197,94,.20);
        border-color: rgba(34,197,94,.35);
    }

    .se-switch input:checked + .se-slider::before {
        transform: translateX(23px);
        background: #4ade80;
    }

    .se-actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 22px;
        padding-top: 20px;
        border-top: 1px solid rgba(148,163,184,.09);
    }

    .se-btn {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border: 1px solid transparent;
        border-radius: 11px;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s ease;
    }

    .se-btn-ghost {
        color: #aebbd0;
        background: #0a111e;
        border-color: var(--se-border);
    }

    .se-btn-ghost:hover {
        color: #fff;
        border-color: var(--se-border-hover);
        background: #101827;
    }

    .se-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #4f46e5 60%, #6d28d9);
        box-shadow: 0 10px 24px rgba(37,99,235,.18);
    }

    .se-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(37,99,235,.24);
    }

    .se-btn-success {
        color: #dcfce7;
        border-color: rgba(34,197,94,.20);
        background: rgba(34,197,94,.10);
    }

    .se-btn-danger {
        color: #fecaca;
        border-color: rgba(239,68,68,.18);
        background: rgba(239,68,68,.07);
    }

    .se-assignment {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid rgba(148,163,184,.08);
    }

    .se-assignment:last-child {
        border-bottom: 0;
    }

    .se-path {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .se-path-tag {
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 8px;
        color: #cbd5e1;
        background: rgba(148,163,184,.07);
        border: 1px solid rgba(148,163,184,.10);
        font-size: 11px;
        font-weight: 700;
    }

    .se-path-tag.blue {
        color: #bfdbfe;
        background: rgba(59,130,246,.08);
        border-color: rgba(59,130,246,.16);
    }

    .se-path-tag.orange {
        color: #fde68a;
        background: rgba(245,158,11,.07);
        border-color: rgba(245,158,11,.16);
    }

    .se-path-chevron {
        color: #475569;
        font-size: 10px;
    }

    .se-assignment-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        flex: 0 0 auto;
    }

    .se-empty {
        padding: 20px;
        text-align: center;
        border: 1px dashed rgba(148,163,184,.16);
        border-radius: 13px;
        color: #718096;
        font-size: 12.5px;
    }

    .se-assignment-form {
        padding-top: 4px;
    }

    @media (max-width: 767px) {
        .se-hero {
            padding: 23px 19px;
            border-radius: 18px;
        }

        .se-avatar {
            display: none;
        }

        .se-card {
            border-radius: 17px;
        }

        .se-card-head,
        .se-card-body {
            padding-left: 18px;
            padding-right: 18px;
        }

        .se-status-grid {
            grid-template-columns: 1fr;
        }

        .se-actions {
            flex-direction: column-reverse;
        }

        .se-btn {
            width: 100%;
        }

        .se-assignment {
            align-items: flex-start;
            flex-direction: column;
        }

        .se-assignment-actions {
            width: 100%;
        }

        .se-assignment-actions .se-btn {
            width: auto;
            flex: 1 1 auto;
        }
    }
</style>

<div class="student-edit-page">

    <section class="se-hero">
        <div class="se-hero-main">
            <span class="se-eyebrow">
                <i class="bi bi-pencil-square"></i>
                Modification étudiant
            </span>

            <h1>{{ $user->name }}</h1>

            <p>
                Modifiez les informations du compte, consultez son statut de paiement
                et gérez ses affectations pédagogiques depuis la même page.
            </p>
        </div>

        <div class="se-avatar" aria-hidden="true">
            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </div>
    </section>

    @if(session('success'))
        <div class="se-alert se-alert-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="se-alert" style="color:#bfdbfe;background:rgba(59,130,246,.07);border:1px solid rgba(59,130,246,.18);">
            <i class="bi bi-info-circle-fill me-1"></i>
            {{ session('info') }}
        </div>
    @endif

    @if($errors->any())
        <div class="se-alert se-alert-danger">
            <strong>Veuillez corriger les informations suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="se-card">
        <header class="se-card-head">
            <div class="se-card-title">
                <span class="se-card-icon">
                    <i class="bi bi-person-vcard-fill"></i>
                </span>
                <div>
                    <h2>Informations du compte</h2>
                    <p>Nom, e-mail, localisation et accès à la plateforme.</p>
                </div>
            </div>
        </header>

        <div class="se-card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_profile_update" value="1">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="se-label" for="name">
                            <i class="bi bi-person"></i>Nom complet <span class="se-required">*</span>
                        </label>
                        <div class="se-input-wrap">
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="se-input"
                                maxlength="255"
                                required
                            >
                        </div>
                        @error('name')
                            <div class="se-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="se-label" for="email">
                            <i class="bi bi-envelope"></i>Adresse e-mail <span class="se-required">*</span>
                        </label>
                        <div class="se-input-wrap">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="se-input"
                                maxlength="255"
                                required
                            >
                        </div>
                        @error('email')
                            <div class="se-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="se-label" for="country">
                            <i class="bi bi-globe2"></i>Pays
                        </label>
                        <div class="se-input-wrap">
                            <input
                                id="country"
                                type="text"
                                name="country"
                                value="{{ old('country', $user->country) }}"
                                class="se-input"
                                maxlength="120"
                                placeholder="Ex. Maroc"
                            >
                        </div>
                        @error('country')
                            <div class="se-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="se-label" for="city">
                            <i class="bi bi-geo-alt"></i>Ville
                        </label>
                        <div class="se-input-wrap">
                            <input
                                id="city"
                                type="text"
                                name="city"
                                value="{{ old('city', $user->city) }}"
                                class="se-input"
                                maxlength="120"
                                placeholder="Ex. Rabat"
                            >
                        </div>
                        @error('city')
                            <div class="se-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="se-status-grid">
                    <div class="se-status-box">
                        <div class="se-status-box-top">
                            <span class="se-status-label">État du compte</span>

                            @if($user->is_active)
                                <span class="se-pill green">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Actif
                                </span>
                            @else
                                <span class="se-pill red">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Inactif
                                </span>
                            @endif
                        </div>

                        <div class="se-status-detail">
                            L’état du compte contrôle l’accès de l’étudiant à son espace.
                        </div>
                    </div>

                    <div class="se-status-box">
                        <div class="se-status-box-top">
                            <span class="se-status-label">Paiement actuel</span>

                            @if($currentPayment)
                                <span class="se-pill green">
                                    <i class="bi bi-credit-card-fill"></i>
                                    {{ $currentPayment->plan_label }}
                                </span>
                            @elseif($user->is_paid)
                                <span class="se-pill orange">
                                    <i class="bi bi-check-circle"></i>
                                    Ancien statut payé
                                </span>
                            @else
                                <span class="se-pill red">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                    Non payé
                                </span>
                            @endif
                        </div>

                        <div class="se-status-detail">
                            @if($currentPayment)
                                Valable jusqu’au
                                <strong style="color:#cbd5e1;">
                                    {{ optional($currentPayment->expires_at)->format('d/m/Y') }}
                                </strong>.
                            @elseif($lastPayment)
                                Dernier paiement enregistré :
                                {{ optional($lastPayment->paid_at)->format('d/m/Y') ?? '—' }}.
                            @else
                                Aucun paiement récent enregistré.
                            @endif
                        </div>

                        <a
                            href="{{ route('admin.student-payments.create', ['student' => $user->id]) }}"
                            class="se-btn se-btn-ghost mt-3"
                        >
                            <i class="bi bi-credit-card-2-front"></i>
                            Gérer les paiements
                        </a>
                    </div>
                </div>

                <div class="se-switch-line">
                    <div class="se-switch-copy">
                        <strong>Autoriser l’accès au compte</strong>
                        <small>Décochez pour désactiver temporairement l’étudiant.</small>
                    </div>

                    <input type="hidden" name="is_active" value="0">

                    <label class="se-switch" aria-label="Statut du compte">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                        >
                        <span class="se-slider"></span>
                    </label>
                </div>

                <div class="se-actions">
                    <a href="{{ route('admin.users.index') }}" class="se-btn se-btn-ghost">
                        <i class="bi bi-arrow-left"></i>
                        Retour aux utilisateurs
                    </a>

                    <button type="submit" class="se-btn se-btn-primary">
                        <i class="bi bi-check2-circle"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="se-card">
        <header class="se-card-head">
            <div class="se-card-title">
                <span class="se-card-icon">
                    <i class="bi bi-diagram-3-fill"></i>
                </span>
                <div>
                    <h2>Affectations pédagogiques</h2>
                    <p>Matière → Niveau → Classe → Créneau.</p>
                </div>
            </div>

            <span class="se-pill {{ $assignments->count() ? 'green' : 'orange' }}">
                {{ $assignments->count() }} affectation(s)
            </span>
        </header>

        <div class="se-card-body">
            @forelse($assignments as $assignment)
                <div class="se-assignment">
                    <div class="se-path">
                        <span class="se-path-tag blue">
                            {{ $assignment->subject_name ?? 'Matière' }}
                        </span>

                        <i class="bi bi-chevron-right se-path-chevron"></i>

                        <span class="se-path-tag">
                            {{ $assignment->level_name ?? 'Niveau' }}
                        </span>

                        <i class="bi bi-chevron-right se-path-chevron"></i>

                        <span class="se-path-tag blue">
                            {{ $assignment->class_name ?? 'Classe' }}
                        </span>

                        <i class="bi bi-chevron-right se-path-chevron"></i>

                        <span class="se-path-tag orange">
                            {{ $assignment->slot_code ?? '—' }}
                        </span>
                    </div>

                    <div class="se-assignment-actions">
                        <a
                            href="{{ route('admin.users.edit', ['user' => $user, 'assignment_id' => $assignment->pivot_id]) }}"
                            class="se-btn se-btn-ghost"
                        >
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>

                        <form
                            method="POST"
                            action="{{ route('admin.assign.class.destroy', $assignment->pivot_id) }}"
                            onsubmit="return confirm('Supprimer cette affectation ?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="se-btn se-btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="se-empty">
                    <i class="bi bi-diagram-3 d-block mb-2" style="font-size:22px;"></i>
                    Aucune affectation structurelle pour cet étudiant.
                </div>
            @endforelse
        </div>
    </section>

    @php
        $editingAssignment = $selectedAssignment !== null;
    @endphp

    <section class="se-card">
        <header class="se-card-head">
            <div class="se-card-title">
                <span class="se-card-icon">
                    <i class="bi {{ $editingAssignment ? 'bi-pencil-fill' : 'bi-plus-lg' }}"></i>
                </span>
                <div>
                    <h2>
                        {{ $editingAssignment ? 'Modifier une affectation' : 'Ajouter une affectation' }}
                    </h2>
                    <p>Sélectionnez la matière, le niveau, la classe et le créneau.</p>
                </div>
            </div>
        </header>

        <div class="se-card-body se-assignment-form">
            <form
                method="POST"
                action="{{
                    $editingAssignment
                        ? route('admin.assign.class.update', $selectedAssignment->pivot_id)
                        : route('admin.assign.class.store')
                }}"
            >
                @csrf

                @if($editingAssignment)
                    @method('PATCH')
                @endif

                <input type="hidden" name="user_id" value="{{ $user->id }}">

                @include(
                    'components.pedagogical-path-edit',
                    [
                        'hierarchy' => $assignmentHierarchy,
                        'prefix' => 'studentAssignmentEdit',
                        'selectedSubject' => old(
                            'subject_id',
                            $selectedAssignment ? $selectedAssignment->subject_id : null
                        ),
                        'selectedLevel' => old(
                            'level_id',
                            $selectedAssignment ? $selectedAssignment->level_id : null
                        ),
                        'selectedClass' => old(
                            'class_id',
                            $selectedAssignment ? $selectedAssignment->class_id : null
                        ),
                        'selectedSlot' => old(
                            'class_slot_id',
                            $selectedAssignment ? $selectedAssignment->class_slot_id : null
                        ),
                    ]
                )

                <div class="se-actions">
                    @if($editingAssignment)
                        <a
                            href="{{ route('admin.users.edit', $user) }}"
                            class="se-btn se-btn-ghost"
                        >
                            <i class="bi bi-x-lg"></i>
                            Annuler la modification
                        </a>
                    @else
                        <a
                            href="{{ route('admin.assign.class') }}"
                            class="se-btn se-btn-ghost"
                        >
                            <i class="bi bi-people"></i>
                            Toutes les affectations
                        </a>
                    @endif

                    <button type="submit" class="se-btn se-btn-success">
                        <i class="bi bi-check-lg"></i>
                        {{
                            $editingAssignment
                                ? 'Enregistrer l’affectation'
                                : 'Ajouter l’affectation'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </section>

</div>
@endsection
