@extends('layouts.admin')

@section('title', 'Parents & Responsables')
@section('page_title', 'Parents & Responsables')
@section('breadcrumb', 'Administration → Parents & Responsables')

@section('content')
@php
    $totalParents = $parents->count();
    $activeParents = $parents->filter(fn ($parent) => (bool) $parent->is_active)->count();
    $inactiveParents = $totalParents - $activeParents;
    $linkedChildren = $parents->sum(fn ($parent) => $parent->children_list->count());
    $withoutChildren = $parents->filter(fn ($parent) => $parent->children_list->isEmpty())->count();
@endphp

<style>
    .parents-page {
        --parents-purple: #8b5cf6;
        --parents-purple-2: #6d5dfc;
        --parents-blue: #38bdf8;
        --parents-green: #4ade80;
        --parents-orange: #fb923c;
        --parents-red: #f87171;
        --parents-muted: #94a3b8;
        --parents-card: rgba(15, 23, 42, .76);
        --parents-card-2: rgba(17, 24, 39, .90);
        --parents-border: rgba(148, 163, 184, .12);
        --parents-border-strong: rgba(139, 92, 246, .30);
        position: relative;
    }

    .parents-page * { box-sizing: border-box; }

    .parents-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 26px 28px;
        margin-bottom: 20px;
        border: 1px solid rgba(139, 92, 246, .20);
        border-radius: 22px;
        background:
            radial-gradient(circle at 8% 0%, rgba(139, 92, 246, .20), transparent 34%),
            radial-gradient(circle at 95% 85%, rgba(56, 189, 248, .10), transparent 30%),
            linear-gradient(135deg, rgba(15, 23, 42, .96), rgba(10, 18, 34, .82));
        box-shadow: 0 18px 55px rgba(0, 0, 0, .20);
    }

    .parents-hero::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        right: -90px;
        top: -120px;
        border: 1px solid rgba(255,255,255,.06);
        border-radius: 50%;
        box-shadow:
            0 0 0 30px rgba(255,255,255,.018),
            0 0 0 65px rgba(255,255,255,.012);
        pointer-events: none;
    }

    .parents-hero-content { position: relative; z-index: 1; min-width: 0; }

    .parents-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        color: #c4b5fd;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .10em;
        text-transform: uppercase;
    }

    .parents-hero h1 {
        margin: 0;
        color: #f8fafc;
        font-size: clamp(1.45rem, 2.5vw, 2.15rem);
        font-weight: 850;
        letter-spacing: -.035em;
    }

    .parents-hero p {
        max-width: 720px;
        margin: 8px 0 0;
        color: var(--parents-muted);
        font-size: .82rem;
        line-height: 1.65;
    }

    .parents-create-btn {
        position: relative;
        z-index: 1;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 17px;
        border: 1px solid rgba(196, 181, 253, .28);
        border-radius: 12px;
        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        box-shadow: 0 10px 28px rgba(79, 70, 229, .24);
        font-size: .76rem;
        font-weight: 800;
        text-decoration: none;
        transition: .2s ease;
    }

    .parents-create-btn:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 14px 35px rgba(79, 70, 229, .34);
    }

    .parents-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 18px;
    }

    .parents-stat {
        position: relative;
        overflow: hidden;
        min-height: 112px;
        padding: 17px;
        border: 1px solid var(--parents-border);
        border-radius: 18px;
        background: var(--parents-card);
        box-shadow: 0 10px 30px rgba(0,0,0,.12);
    }

    .parents-stat::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: var(--stat-color, var(--parents-purple));
        opacity: .85;
    }

    .parents-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .parents-stat-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border: 1px solid color-mix(in srgb, var(--stat-color) 28%, transparent);
        border-radius: 11px;
        color: var(--stat-color);
        background: color-mix(in srgb, var(--stat-color) 10%, transparent);
        font-size: 1rem;
    }

    .parents-stat-value {
        margin-top: 12px;
        color: #fff;
        font-size: 1.55rem;
        font-weight: 850;
        line-height: 1;
    }

    .parents-stat-label {
        margin-top: 5px;
        color: var(--parents-muted);
        font-size: .66rem;
        font-weight: 650;
    }

    .parents-toolbar {
        display: grid;
        grid-template-columns: minmax(240px, 1fr) auto auto;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding: 12px;
        border: 1px solid var(--parents-border);
        border-radius: 16px;
        background: rgba(15,23,42,.58);
    }

    .parents-search {
        position: relative;
    }

    .parents-search i {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }

    .parents-search input,
    .parents-sort,
    .parents-control {
        width: 100%;
        min-height: 42px;
        border: 1px solid rgba(148,163,184,.15);
        border-radius: 11px;
        outline: none;
        color: #f8fafc;
        background: rgba(2, 8, 23, .50);
        font: inherit;
        font-size: .72rem;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .parents-search input { padding: 9px 13px 9px 38px; }
    .parents-sort, .parents-control { padding: 9px 11px; }

    .parents-search input:focus,
    .parents-sort:focus,
    .parents-control:focus {
        border-color: rgba(139,92,246,.55);
        box-shadow: 0 0 0 3px rgba(139,92,246,.10);
    }

    .parents-filter-tabs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .parents-filter-btn {
        min-height: 34px;
        padding: 0 10px;
        border: 1px solid rgba(148,163,184,.12);
        border-radius: 9px;
        color: #94a3b8;
        background: rgba(255,255,255,.025);
        font-size: .63rem;
        font-weight: 750;
        cursor: pointer;
        transition: .18s ease;
    }

    .parents-filter-btn:hover,
    .parents-filter-btn.active {
        color: #ddd6fe;
        border-color: rgba(139,92,246,.30);
        background: rgba(124,58,237,.12);
    }

    .parents-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 18px 2px 11px;
    }

    .parents-section-header h3 {
        margin: 0;
        color: #f8fafc;
        font-size: .88rem;
        font-weight: 800;
    }

    .parents-result-count {
        color: #64748b;
        font-size: .64rem;
    }

    .parents-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .parent-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--parents-border);
        border-radius: 19px;
        background:
            linear-gradient(180deg, rgba(15,23,42,.86), rgba(8,15,29,.90));
        box-shadow: 0 12px 34px rgba(0,0,0,.14);
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .parent-card:hover {
        transform: translateY(-2px);
        border-color: rgba(139,92,246,.25);
        box-shadow: 0 17px 42px rgba(0,0,0,.20);
    }

    .parent-card::before {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        top: -90px;
        right: -70px;
        border-radius: 50%;
        background: rgba(124,58,237,.08);
        filter: blur(4px);
        pointer-events: none;
    }

    .parent-card-main {
        position: relative;
        z-index: 1;
        padding: 18px;
    }

    .parent-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .parent-identity {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .parent-avatar {
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(196,181,253,.20);
        border-radius: 14px;
        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        box-shadow: 0 8px 23px rgba(79,70,229,.20);
        font-size: .78rem;
        font-weight: 850;
    }

    .parent-name {
        overflow: hidden;
        margin: 0;
        color: #f8fafc;
        font-size: .83rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .parent-email {
        overflow: hidden;
        display: block;
        max-width: 300px;
        margin-top: 3px;
        color: #7f8da3;
        font-size: .62rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .parent-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: .56rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .parent-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 0 3px color-mix(in srgb, currentColor 15%, transparent);
    }

    .parent-status.active {
        color: #4ade80;
        background: rgba(34,197,94,.09);
    }

    .parent-status.inactive {
        color: #f87171;
        background: rgba(248,113,113,.09);
    }

    .parent-divider {
        height: 1px;
        margin: 15px 0;
        background: rgba(148,163,184,.08);
    }

    .parent-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .parent-meta-title {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #cbd5e1;
        font-size: .64rem;
        font-weight: 750;
    }

    .parent-meta-title i { color: #a78bfa; }

    .parent-children-count {
        color: #64748b;
        font-size: .57rem;
    }

    .parent-children {
        display: grid;
        gap: 7px;
    }

    .parent-child {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 9px 10px;
        border: 1px solid rgba(148,163,184,.09);
        border-radius: 11px;
        background: rgba(255,255,255,.025);
    }

    .parent-child-info {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .parent-child-icon {
        width: 29px;
        height: 29px;
        flex: 0 0 29px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        color: #7dd3fc;
        background: rgba(56,189,248,.08);
        font-size: .72rem;
    }

    .parent-child-name {
        display: block;
        overflow: hidden;
        color: #e2e8f0;
        font-size: .66rem;
        font-weight: 750;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .parent-child-relation {
        display: block;
        margin-top: 2px;
        color: #64748b;
        font-size: .54rem;
    }

    .parent-unlink-btn {
        width: 29px;
        height: 29px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(248,113,113,.15);
        border-radius: 8px;
        color: #fca5a5;
        background: rgba(248,113,113,.05);
        cursor: pointer;
        transition: .18s ease;
    }

    .parent-unlink-btn:hover {
        border-color: rgba(248,113,113,.28);
        background: rgba(248,113,113,.10);
    }

    .parent-no-child {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 12px;
        border: 1px dashed rgba(251,146,60,.18);
        border-radius: 11px;
        color: #a8b3c4;
        background: rgba(251,146,60,.03);
        font-size: .61rem;
    }

    .parent-no-child i { color: #fb923c; }

    .parent-card-actions {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 8px;
        margin-top: 14px;
    }

    .parent-action-btn {
        min-height: 37px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 11px;
        border: 1px solid rgba(148,163,184,.12);
        border-radius: 10px;
        color: #cbd5e1;
        background: rgba(255,255,255,.025);
        font-size: .62rem;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: .18s ease;
    }

    .parent-action-btn:hover {
        color: #ede9fe;
        border-color: rgba(139,92,246,.25);
        background: rgba(124,58,237,.09);
    }

    .parent-action-btn.danger {
        width: 38px;
        padding: 0;
        color: #fca5a5;
        border-color: rgba(248,113,113,.12);
    }

    .parent-action-btn.danger:hover {
        color: #fecaca;
        border-color: rgba(248,113,113,.28);
        background: rgba(248,113,113,.08);
    }

    .parent-manage-panel {
        margin: 0 18px 18px;
        border: 1px solid rgba(139,92,246,.14);
        border-radius: 13px;
        background: rgba(124,58,237,.035);
    }

    .parent-manage-panel summary {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 11px 12px;
        color: #c4b5fd;
        font-size: .62rem;
        font-weight: 800;
        cursor: pointer;
        list-style: none;
    }

    .parent-manage-panel summary::-webkit-details-marker { display: none; }

    .parent-manage-body {
        padding: 0 12px 12px;
    }

    .parent-link-form {
        display: grid;
        grid-template-columns: minmax(0,1.5fr) minmax(120px,.7fr);
        gap: 8px;
    }

    .parent-permissions {
        grid-column: 1 / -1;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 2px 0;
    }

    .parent-permission {
        position: relative;
    }

    .parent-permission input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .parent-permission span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 30px;
        padding: 0 8px;
        border: 1px solid rgba(148,163,184,.10);
        border-radius: 8px;
        color: #718096;
        background: rgba(255,255,255,.02);
        font-size: .55rem;
        font-weight: 700;
        cursor: pointer;
        transition: .15s ease;
    }

    .parent-permission input:checked + span {
        color: #c4b5fd;
        border-color: rgba(139,92,246,.22);
        background: rgba(124,58,237,.09);
    }

    .parent-link-submit { grid-column: 1 / -1; }

    .parents-empty {
        display: none;
        padding: 42px 20px;
        border: 1px dashed rgba(148,163,184,.14);
        border-radius: 18px;
        text-align: center;
        background: rgba(15,23,42,.45);
    }

    .parents-empty i {
        display: block;
        margin-bottom: 10px;
        color: #64748b;
        font-size: 1.7rem;
    }

    .parents-empty h4 {
        margin: 0 0 5px;
        color: #cbd5e1;
        font-size: .78rem;
    }

    .parents-empty p {
        margin: 0;
        color: #64748b;
        font-size: .62rem;
    }

    .parents-modal .modal-content {
        overflow: hidden;
        border: 1px solid rgba(139,92,246,.20);
        border-radius: 20px;
        color: #f8fafc;
        background:
            radial-gradient(circle at 0% 0%, rgba(139,92,246,.13), transparent 36%),
            #0b1424;
        box-shadow: 0 24px 70px rgba(0,0,0,.45);
    }

    .parents-modal .modal-header,
    .parents-modal .modal-footer {
        border-color: rgba(148,163,184,.09);
    }

    .parents-modal .modal-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .9rem;
        font-weight: 850;
    }

    .parents-modal .btn-close { filter: invert(1); opacity: .6; }

    .parent-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 12px;
    }

    .parent-form-field.full { grid-column: 1 / -1; }

    .parent-form-label {
        display: block;
        margin-bottom: 6px;
        color: #a8b3c4;
        font-size: .61rem;
        font-weight: 750;
    }

    .parent-form-label .required { color: #f87171; }

    .parent-student-picker {
        max-height: 220px;
        overflow: auto;
        display: grid;
        gap: 7px;
        padding: 4px;
    }

    .parent-student-option {
        position: relative;
    }

    .parent-student-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .parent-student-option label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        margin: 0;
        border: 1px solid rgba(148,163,184,.10);
        border-radius: 11px;
        background: rgba(255,255,255,.02);
        cursor: pointer;
        transition: .16s ease;
    }

    .parent-student-option input:checked + label {
        border-color: rgba(139,92,246,.35);
        background: rgba(124,58,237,.10);
        box-shadow: inset 0 0 0 1px rgba(139,92,246,.08);
    }

    .student-picker-avatar {
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        color: #7dd3fc;
        background: rgba(56,189,248,.08);
        font-size: .7rem;
        font-weight: 850;
    }

    .student-picker-text { min-width: 0; }
    .student-picker-text strong {
        display: block;
        overflow: hidden;
        color: #e2e8f0;
        font-size: .64rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .student-picker-text small {
        display: block;
        overflow: hidden;
        margin-top: 2px;
        color: #64748b;
        font-size: .53rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .parents-alert {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 11px 13px;
        margin-bottom: 14px;
        border-radius: 12px;
        font-size: .65rem;
    }

    .parents-alert.success {
        color: #bbf7d0;
        border: 1px solid rgba(74,222,128,.16);
        background: rgba(34,197,94,.07);
    }

    .parents-alert.danger {
        color: #fecaca;
        border: 1px solid rgba(248,113,113,.16);
        background: rgba(248,113,113,.07);
    }

    @media (max-width: 1200px) {
        .parents-stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
    }

    @media (max-width: 980px) {
        .parents-list { grid-template-columns: 1fr; }
        .parents-toolbar { grid-template-columns: 1fr; }
        .parents-sort { width: 100%; }
    }

    @media (max-width: 700px) {
        .parents-hero { align-items: flex-start; flex-direction: column; padding: 20px; }
        .parents-create-btn { width: 100%; }
        .parents-stats { grid-template-columns: 1fr 1fr; }
        .parent-form-grid { grid-template-columns: 1fr; }
        .parent-form-field.full { grid-column: auto; }
        .parent-link-form { grid-template-columns: 1fr; }
        .parent-permissions, .parent-link-submit { grid-column: auto; }
    }

    @media (max-width: 480px) {
        .parents-stats { grid-template-columns: 1fr; }
        .parent-card-head { flex-direction: column; }
        .parent-status { align-self: flex-start; }
    }
</style>

<div class="parents-page">
    @if(session('success'))
        <div class="parents-alert success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="parents-alert danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Une vérification est nécessaire.</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="parents-hero">
        <div class="parents-hero-content">
            <div class="parents-eyebrow">
                <i class="bi bi-people-fill"></i>
                Espace administration
            </div>
            <h1>Parents & responsables</h1>
            <p>
                Centralisez les comptes responsables, visualisez immédiatement les enfants associés
                et gérez leurs droits d’accès depuis une seule interface.
            </p>
        </div>

        <button
            type="button"
            class="parents-create-btn"
            data-bs-toggle="modal"
            data-bs-target="#createParentModal"
        >
            <i class="bi bi-person-plus-fill"></i>
            Ajouter un parent
        </button>
    </section>

    <section class="parents-stats">
        <article class="parents-stat" style="--stat-color:#a78bfa;">
            <div class="parents-stat-top">
                <span class="parents-stat-icon"><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="parents-stat-value">{{ $totalParents }}</div>
            <div class="parents-stat-label">Parents enregistrés</div>
        </article>

        <article class="parents-stat" style="--stat-color:#4ade80;">
            <div class="parents-stat-top">
                <span class="parents-stat-icon"><i class="bi bi-person-check-fill"></i></span>
            </div>
            <div class="parents-stat-value">{{ $activeParents }}</div>
            <div class="parents-stat-label">Comptes actifs</div>
        </article>

        <article class="parents-stat" style="--stat-color:#38bdf8;">
            <div class="parents-stat-top">
                <span class="parents-stat-icon"><i class="bi bi-mortarboard-fill"></i></span>
            </div>
            <div class="parents-stat-value">{{ $linkedChildren }}</div>
            <div class="parents-stat-label">Associations enfant-parent</div>
        </article>

        <article class="parents-stat" style="--stat-color:#fb923c;">
            <div class="parents-stat-top">
                <span class="parents-stat-icon"><i class="bi bi-person-exclamation"></i></span>
            </div>
            <div class="parents-stat-value">{{ $withoutChildren }}</div>
            <div class="parents-stat-label">Parents sans enfant associé</div>
        </article>
    </section>

    <section class="parents-toolbar">
        <div class="parents-search">
            <i class="bi bi-search"></i>
            <input
                type="search"
                id="parentsSearch"
                placeholder="Rechercher par nom, email ou enfant..."
                autocomplete="off"
            >
        </div>

        <div class="parents-filter-tabs" id="parentsFilters">
            <button class="parents-filter-btn active" type="button" data-filter="all">Tous</button>
            <button class="parents-filter-btn" type="button" data-filter="active">Actifs</button>
            <button class="parents-filter-btn" type="button" data-filter="inactive">Inactifs</button>
            <button class="parents-filter-btn" type="button" data-filter="no-child">Sans enfant</button>
        </div>

        <select class="parents-sort" id="parentsSort" aria-label="Trier les parents">
            <option value="name-asc">Nom A → Z</option>
            <option value="name-desc">Nom Z → A</option>
            <option value="children-desc">Plus d’enfants</option>
            <option value="recent">Plus récents</option>
        </select>
    </section>

    <div class="parents-section-header">
        <h3>Répertoire des responsables</h3>
        <span class="parents-result-count" id="parentsResultCount">
            {{ $totalParents }} résultat(s)
        </span>
    </div>

    <section class="parents-list" id="parentsList">
        @foreach($parents as $parent)
            @php
                $childrenNames = $parent->children_list->pluck('name')->implode(' ');
                $isActive = (bool) $parent->is_active;
                $initials = collect(preg_split('/\s+/', trim($parent->name)))
                    ->filter()
                    ->take(2)
                    ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                    ->implode('');
            @endphp

            <article
                class="parent-card"
                data-parent-card
                data-id="{{ $parent->id }}"
                data-name="{{ mb_strtolower($parent->name) }}"
                data-email="{{ mb_strtolower($parent->email) }}"
                data-children="{{ mb_strtolower($childrenNames) }}"
                data-status="{{ $isActive ? 'active' : 'inactive' }}"
                data-child-count="{{ $parent->children_list->count() }}"
            >
                <div class="parent-card-main">
                    <div class="parent-card-head">
                        <div class="parent-identity">
                            <div class="parent-avatar">{{ $initials ?: 'P' }}</div>
                            <div style="min-width:0;">
                                <h4 class="parent-name">{{ $parent->name }}</h4>
                                <span class="parent-email">{{ $parent->email }}</span>
                            </div>
                        </div>

                        <span class="parent-status {{ $isActive ? 'active' : 'inactive' }}">
                            {{ $isActive ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    <div class="parent-divider"></div>

                    <div class="parent-meta-row">
                        <div class="parent-meta-title">
                            <i class="bi bi-diagram-3-fill"></i>
                            Enfants associés
                        </div>
                        <div class="parent-children-count">
                            {{ $parent->children_list->count() }} enfant(s)
                        </div>
                    </div>

                    <div class="parent-children">
                        @forelse($parent->children_list as $child)
                            <div class="parent-child">
                                <div class="parent-child-info">
                                    <span class="parent-child-icon">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </span>
                                    <span style="min-width:0;">
                                        <span class="parent-child-name">{{ $child->name }}</span>
                                        <span class="parent-child-relation">
                                            {{ $child->parent_relationship ?: 'Responsable' }}
                                            @if($child->email)
                                                · {{ $child->email }}
                                            @endif
                                        </span>
                                    </span>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('admin.parents.children.destroy', [$parent, $child]) }}"
                                    onsubmit="return confirm('Retirer {{ addslashes($child->name) }} de ce parent ?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="parent-unlink-btn"
                                        title="Retirer l’association"
                                    >
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="parent-no-child">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                Aucun enfant n’est encore associé à ce responsable.
                            </div>
                        @endforelse
                    </div>

                    <div class="parent-card-actions">
                        <button
                            type="button"
                            class="parent-action-btn"
                            onclick="document.getElementById('manage-parent-{{ $parent->id }}').open = !document.getElementById('manage-parent-{{ $parent->id }}').open"
                        >
                            <i class="bi bi-person-plus"></i>
                            Associer un enfant
                        </button>

                        <form
                            method="POST"
                            action="{{ route('admin.parents.destroy', $parent) }}"
                            onsubmit="return confirm('Supprimer définitivement le compte Parent {{ addslashes($parent->name) }} ?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="parent-action-btn danger" title="Supprimer le parent">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <details class="parent-manage-panel" id="manage-parent-{{ $parent->id }}">
                    <summary>
                        <i class="bi bi-link-45deg"></i>
                        Nouvelle association pour {{ $parent->name }}
                    </summary>

                    <div class="parent-manage-body">
                        <form
                            method="POST"
                            action="{{ route('admin.parents.children.store', $parent) }}"
                            class="parent-link-form"
                        >
                            @csrf

                            <select class="parents-control" name="student_id" required>
                                <option value="">Sélectionner un étudiant</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} — {{ $student->email }}
                                    </option>
                                @endforeach
                            </select>

                            <select class="parents-control" name="relationship" required>
                                <option value="Père">Père</option>
                                <option value="Mère">Mère</option>
                                <option value="Tuteur">Tuteur</option>
                                <option value="Responsable">Responsable</option>
                            </select>

                            <div class="parent-permissions">
                                <label class="parent-permission">
                                    <input type="checkbox" name="can_view_schedule" value="1" checked>
                                    <span><i class="bi bi-calendar-week"></i> Planning</span>
                                </label>
                                <label class="parent-permission">
                                    <input type="checkbox" name="can_view_absences" value="1" checked>
                                    <span><i class="bi bi-person-x"></i> Absences</span>
                                </label>
                                <label class="parent-permission">
                                    <input type="checkbox" name="can_view_assignments" value="1" checked>
                                    <span><i class="bi bi-journal-check"></i> Devoirs</span>
                                </label>
                                <label class="parent-permission">
                                    <input type="checkbox" name="can_view_results" value="1" checked>
                                    <span><i class="bi bi-bar-chart-fill"></i> Résultats</span>
                                </label>
                            </div>

                            <button type="submit" class="parents-create-btn parent-link-submit">
                                <i class="bi bi-link-45deg"></i>
                                Associer cet enfant
                            </button>
                        </form>
                    </div>
                </details>
            </article>
        @endforeach
    </section>

    <section class="parents-empty" id="parentsEmpty">
        <i class="bi bi-search"></i>
        <h4>Aucun parent trouvé</h4>
        <p>Modifiez votre recherche ou le filtre sélectionné.</p>
    </section>
</div>

<div
    class="modal fade parents-modal"
    id="createParentModal"
    tabindex="-1"
    aria-labelledby="createParentModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.parents.store') }}" id="createParentForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="createParentModalLabel">
                        <i class="bi bi-person-plus-fill" style="color:#a78bfa;"></i>
                        Nouveau compte Parent
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="parent-form-grid">
                        <div class="parent-form-field">
                            <label class="parent-form-label" for="parent_name">
                                Nom complet <span class="required">*</span>
                            </label>
                            <input
                                id="parent_name"
                                class="parents-control"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Ex. Mohamed Amrani"
                                required
                            >
                        </div>

                        <div class="parent-form-field">
                            <label class="parent-form-label" for="parent_email">
                                Adresse e-mail <span class="required">*</span>
                            </label>
                            <input
                                id="parent_email"
                                class="parents-control"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="parent@email.com"
                                required
                            >
                        </div>

                        <div class="parent-form-field full">
                            <label class="parent-form-label" for="parent_relationship">
                                Lien avec l’enfant <span class="required">*</span>
                            </label>
                            <select id="parent_relationship" class="parents-control" name="relationship" required>
                                <option value="Père" @selected(old('relationship') === 'Père')>Père</option>
                                <option value="Mère" @selected(old('relationship') === 'Mère')>Mère</option>
                                <option value="Tuteur" @selected(old('relationship') === 'Tuteur')>Tuteur</option>
                                <option value="Responsable" @selected(old('relationship') === 'Responsable')>Responsable</option>
                            </select>
                        </div>

                        <div class="parent-form-field full">
                            <label class="parent-form-label" for="studentPickerSearch">
                                Enfant(s) associé(s) <span class="required">*</span>
                            </label>

                            <div class="parents-search mb-2">
                                <i class="bi bi-search"></i>
                                <input
                                    type="search"
                                    id="studentPickerSearch"
                                    placeholder="Rechercher un étudiant..."
                                    autocomplete="off"
                                >
                            </div>

                            <div class="parent-student-picker" id="parentStudentPicker">
                                @foreach($students as $student)
                                    @php
                                        $studentInitial = mb_strtoupper(mb_substr(trim($student->name), 0, 1));
                                        $isChecked = in_array((string) $student->id, array_map('strval', old('student_ids', [])), true);
                                    @endphp
                                    <div
                                        class="parent-student-option"
                                        data-student-option
                                        data-search="{{ mb_strtolower($student->name . ' ' . $student->email) }}"
                                    >
                                        <input
                                            type="checkbox"
                                            name="student_ids[]"
                                            value="{{ $student->id }}"
                                            id="parent_student_{{ $student->id }}"
                                            @checked($isChecked)
                                        >
                                        <label for="parent_student_{{ $student->id }}">
                                            <span class="student-picker-avatar">{{ $studentInitial ?: 'E' }}</span>
                                            <span class="student-picker-text">
                                                <strong>{{ $student->name }}</strong>
                                                <small>{{ $student->email }}</small>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <small style="display:block;margin-top:7px;color:#64748b;font-size:.55rem;">
                                Vous pouvez sélectionner un ou plusieurs enfants.
                            </small>
                        </div>

                        <div class="parent-form-field">
                            <label class="parent-form-label" for="parent_password">
                                Mot de passe <span class="required">*</span>
                            </label>
                            <input
                                id="parent_password"
                                class="parents-control"
                                type="password"
                                name="password"
                                placeholder="Minimum 8 caractères"
                                required
                            >
                        </div>

                        <div class="parent-form-field">
                            <label class="parent-form-label" for="parent_password_confirmation">
                                Confirmer le mot de passe <span class="required">*</span>
                            </label>
                            <input
                                id="parent_password_confirmation"
                                class="parents-control"
                                type="password"
                                name="password_confirmation"
                                placeholder="Répéter le mot de passe"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-3">
                    <button type="button" class="parent-action-btn" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="parents-create-btn">
                        <i class="bi bi-check-circle-fill"></i>
                        Créer le parent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('parentsList');
    const cards = Array.from(document.querySelectorAll('[data-parent-card]'));
    const search = document.getElementById('parentsSearch');
    const sort = document.getElementById('parentsSort');
    const empty = document.getElementById('parentsEmpty');
    const count = document.getElementById('parentsResultCount');
    const filterButtons = Array.from(document.querySelectorAll('[data-filter]'));

    let currentFilter = 'all';

    const normalize = value => (value || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    const cardMatchesFilter = card => {
        const status = card.dataset.status;
        const children = Number(card.dataset.childCount || 0);

        if (currentFilter === 'active') return status === 'active';
        if (currentFilter === 'inactive') return status === 'inactive';
        if (currentFilter === 'no-child') return children === 0;
        return true;
    };

    const applyFilters = () => {
        const query = normalize(search.value);
        let visible = 0;

        cards.forEach(card => {
            const haystack = normalize([
                card.dataset.name,
                card.dataset.email,
                card.dataset.children,
            ].join(' '));

            const show = haystack.includes(query) && cardMatchesFilter(card);
            card.style.display = show ? '' : 'none';

            if (show) visible++;
        });

        count.textContent = `${visible} résultat(s)`;
        empty.style.display = visible === 0 ? 'block' : 'none';
    };

    const applySort = () => {
        const mode = sort.value;

        const sorted = [...cards].sort((a, b) => {
            if (mode === 'name-desc') {
                return b.dataset.name.localeCompare(a.dataset.name, 'fr');
            }

            if (mode === 'children-desc') {
                return Number(b.dataset.childCount) - Number(a.dataset.childCount)
                    || a.dataset.name.localeCompare(b.dataset.name, 'fr');
            }

            if (mode === 'recent') {
                return Number(b.dataset.id) - Number(a.dataset.id);
            }

            return a.dataset.name.localeCompare(b.dataset.name, 'fr');
        });

        sorted.forEach(card => list.appendChild(card));
        applyFilters();
    };

    search.addEventListener('input', applyFilters);
    sort.addEventListener('change', applySort);

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            currentFilter = button.dataset.filter;
            applyFilters();
        });
    });

    const studentSearch = document.getElementById('studentPickerSearch');
    const studentOptions = Array.from(document.querySelectorAll('[data-student-option]'));

    if (studentSearch) {
        studentSearch.addEventListener('input', () => {
            const query = normalize(studentSearch.value);

            studentOptions.forEach(option => {
                option.style.display = normalize(option.dataset.search).includes(query)
                    ? ''
                    : 'none';
            });
        });
    }

    applySort();
});
</script>
@endsection