@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')
@section('page_title', 'Utilisateurs')
@section('breadcrumb', 'Gestion des utilisateurs')

@php
    $usersCollection = method_exists($users, 'getCollection')
        ? $users->getCollection()
        : collect($users);

    $totalUsersCount = $totalUsers
        ?? (method_exists($users, 'total') ? $users->total() : $usersCollection->count());

    $activeUsersCount = $usersCollection->filter(fn ($user) => (bool) $user->is_active)->count();
    $inactiveUsersCount = $usersCollection->filter(fn ($user) => ! (bool) $user->is_active)->count();
    $recentUsersCollection = collect($recentUsers ?? []);
    $recentUsersCount = $recentUsersCollection->count();
@endphp

@push('styles')
<style>
    .users-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .users-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        overflow: hidden;
        padding: 28px;
        border: 1px solid var(--ap-border);
        border-radius: 22px;
        background:
            radial-gradient(circle at 90% 10%, rgba(99, 102, 241, 0.20), transparent 20rem),
            linear-gradient(135deg, rgba(18, 30, 51, 0.98), rgba(10, 18, 32, 0.95));
        box-shadow: var(--ap-shadow-soft);
    }

    .users-hero::after {
        position: absolute;
        right: -62px;
        bottom: -92px;
        width: 220px;
        height: 220px;
        content: "";
        border-radius: 50%;
        background: rgba(34, 211, 238, 0.08);
        filter: blur(2px);
    }

    .users-hero-copy {
        position: relative;
        z-index: 1;
        max-width: 720px;
    }

    .users-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #93c5fd;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .users-hero h2 {
        margin: 0 0 8px;
        color: #fff;
        font-family: "Poppins", sans-serif;
        font-size: clamp(1.45rem, 2.4vw, 2rem);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .users-hero p {
        max-width: 650px;
        margin: 0;
        color: var(--ap-muted);
        font-size: 0.92rem;
        line-height: 1.65;
    }

    .users-hero-total {
        position: relative;
        z-index: 1;
        display: grid;
        min-width: 132px;
        min-height: 112px;
        place-content: center;
        text-align: center;
        border: 1px solid rgba(96, 165, 250, 0.22);
        border-radius: 20px;
        background: rgba(37, 99, 235, 0.10);
        backdrop-filter: blur(12px);
    }

    .users-hero-total strong {
        color: #fff;
        font-size: 2rem;
        font-weight: 850;
        line-height: 1;
    }

    .users-hero-total span {
        margin-top: 8px;
        color: #93c5fd;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .users-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .users-stat {
        position: relative;
        display: flex;
        min-height: 118px;
        align-items: center;
        gap: 15px;
        overflow: hidden;
        padding: 18px;
        border: 1px solid var(--ap-border);
        border-radius: 18px;
        background: rgba(17, 27, 46, 0.82);
        box-shadow: var(--ap-shadow-soft);
        transition: transform 0.22s ease, border-color 0.22s ease;
    }

    .users-stat:hover {
        border-color: var(--ap-border-strong);
        transform: translateY(-3px);
    }

    .users-stat-icon {
        display: grid;
        width: 50px;
        height: 50px;
        flex: 0 0 50px;
        place-items: center;
        border-radius: 15px;
        font-size: 1.15rem;
    }

    .users-stat.blue .users-stat-icon {
        color: #93c5fd;
        background: rgba(59, 130, 246, 0.13);
    }

    .users-stat.green .users-stat-icon {
        color: #86efac;
        background: rgba(34, 197, 94, 0.13);
    }

    .users-stat.orange .users-stat-icon {
        color: #fcd34d;
        background: rgba(245, 158, 11, 0.13);
    }

    .users-stat.purple .users-stat-icon {
        color: #c4b5fd;
        background: rgba(167, 139, 250, 0.13);
    }

    .users-stat-copy {
        min-width: 0;
    }

    .users-stat-copy strong {
        display: block;
        margin-bottom: 5px;
        color: #fff;
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1;
    }

    .users-stat-copy span {
        color: var(--ap-muted);
        font-size: 0.79rem;
        font-weight: 600;
    }

    .users-panel {
        overflow: hidden;
        border: 1px solid var(--ap-border);
        border-radius: 22px;
        background: rgba(12, 21, 37, 0.94);
        box-shadow: var(--ap-shadow);
    }

    .users-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 22px 24px;
        border-bottom: 1px solid var(--ap-border);
    }

    .users-panel-title {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 13px;
    }

    .users-panel-title-icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        color: #bfdbfe;
        border-radius: 13px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.24), rgba(124, 58, 237, 0.18));
    }

    .users-panel-title h3 {
        margin: 0 0 3px;
        color: #fff;
        font-size: 1rem;
        font-weight: 750;
    }

    .users-panel-title p {
        margin: 0;
        color: var(--ap-subtle);
        font-size: 0.75rem;
    }

    .users-visible-count {
        flex: 0 0 auto;
        padding: 8px 12px;
        color: #bfdbfe;
        font-size: 0.72rem;
        font-weight: 750;
        border: 1px solid rgba(96, 165, 250, 0.18);
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.09);
    }

    .users-toolbar {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 180px 180px auto;
        gap: 12px;
        padding: 18px 24px;
        border-bottom: 1px solid var(--ap-border);
        background: rgba(255, 255, 255, 0.015);
    }

    .users-search {
        position: relative;
    }

    .users-search i {
        position: absolute;
        top: 50%;
        left: 14px;
        color: var(--ap-subtle);
        transform: translateY(-50%);
        pointer-events: none;
    }

    .users-control {
        width: 100%;
        min-height: 44px;
        color: var(--ap-text);
        border: 1px solid var(--ap-border);
        border-radius: 12px;
        outline: none;
        background: rgba(255, 255, 255, 0.035);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    input.users-control {
        padding: 0 14px 0 42px;
    }

    select.users-control {
        padding: 0 38px 0 13px;
    }

    .users-control:focus {
        border-color: rgba(96, 165, 250, 0.48);
        background: rgba(255, 255, 255, 0.055);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.09);
    }

    .users-reset {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 15px;
        color: var(--ap-muted);
        cursor: pointer;
        border: 1px solid var(--ap-border);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.035);
        transition: 0.2s ease;
    }

    .users-reset:hover {
        color: #fff;
        border-color: rgba(148, 163, 184, 0.26);
        background: rgba(255, 255, 255, 0.065);
    }

    .users-table-wrap {
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .users-table thead th {
        padding: 13px 17px;
        color: #7f8da3;
        font-size: 0.66rem;
        font-weight: 800;
        letter-spacing: 0.09em;
        text-align: left;
        text-transform: uppercase;
        border-bottom: 1px solid var(--ap-border);
        background: rgba(255, 255, 255, 0.02);
        white-space: nowrap;
    }

    .users-table tbody tr {
        transition: background 0.2s ease;
    }

    .users-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.025);
    }

    .users-table td {
        padding: 15px 17px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(148, 163, 184, 0.09);
    }

    .users-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .users-person {
        display: flex;
        min-width: 210px;
        align-items: center;
        gap: 12px;
    }

    .users-avatar {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 850;
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 13px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        box-shadow: 0 9px 22px rgba(37, 99, 235, 0.22);
    }

    .users-person-copy {
        min-width: 0;
    }

    .users-person-copy strong {
        display: block;
        max-width: 210px;
        overflow: hidden;
        color: #f8fafc;
        font-size: 0.84rem;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .users-person-copy small {
        display: block;
        max-width: 230px;
        margin-top: 3px;
        overflow: hidden;
        color: var(--ap-subtle);
        font-size: 0.69rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .users-pill {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        font-size: 0.68rem;
        font-weight: 750;
        border: 1px solid transparent;
        border-radius: 999px;
        white-space: nowrap;
    }

    .users-role-admin {
        color: #c4b5fd;
        border-color: rgba(167, 139, 250, 0.20);
        background: rgba(124, 58, 237, 0.10);
    }

    .users-role-prof {
        color: #fcd34d;
        border-color: rgba(245, 158, 11, 0.20);
        background: rgba(245, 158, 11, 0.09);
    }

    .users-role-student {
        color: #7dd3fc;
        border-color: rgba(14, 165, 233, 0.20);
        background: rgba(14, 165, 233, 0.09);
    }

    .users-status-active {
        color: #86efac;
        border-color: rgba(34, 197, 94, 0.20);
        background: rgba(34, 197, 94, 0.09);
    }

    .users-status-inactive {
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.20);
        background: rgba(239, 68, 68, 0.09);
    }

    .users-test {
        min-width: 118px;
    }

    .users-test-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        color: var(--ap-muted);
        font-size: 0.7rem;
    }

    .users-test-top strong {
        color: #dbeafe;
        font-size: 0.72rem;
    }

    .users-test-track {
        height: 5px;
        margin-top: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.12);
    }

    .users-test-bar {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    }

    .users-date {
        display: flex;
        flex-direction: column;
        gap: 3px;
        color: var(--ap-muted);
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .users-date small {
        color: var(--ap-subtle);
        font-size: 0.65rem;
    }

    .users-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .users-action {
        display: inline-grid;
        width: 35px;
        height: 35px;
        place-items: center;
        color: #aeb8c8;
        cursor: pointer;
        border: 1px solid var(--ap-border);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.035);
        transition: 0.2s ease;
    }

    .users-action:hover {
        color: #fff;
        border-color: rgba(96, 165, 250, 0.30);
        background: rgba(37, 99, 235, 0.14);
        transform: translateY(-2px);
    }

    .users-action.success {
        color: #86efac;
        border-color: rgba(34, 197, 94, 0.18);
        background: rgba(34, 197, 94, 0.07);
    }

    .users-action.warning {
        color: #fcd34d;
        border-color: rgba(245, 158, 11, 0.18);
        background: rgba(245, 158, 11, 0.07);
    }

    .users-action.danger {
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.18);
        background: rgba(239, 68, 68, 0.07);
    }

    .users-action.danger:hover {
        border-color: rgba(239, 68, 68, 0.34);
        background: rgba(239, 68, 68, 0.14);
    }

    .users-empty {
        padding: 54px 20px !important;
        text-align: center;
    }

    .users-empty-icon {
        display: grid;
        width: 64px;
        height: 64px;
        margin: 0 auto 14px;
        place-items: center;
        color: #93c5fd;
        font-size: 1.45rem;
        border-radius: 18px;
        background: rgba(59, 130, 246, 0.10);
    }

    .users-empty h4 {
        margin: 0 0 6px;
        color: #fff;
        font-size: 1rem;
    }

    .users-empty p {
        margin: 0;
        color: var(--ap-subtle);
        font-size: 0.8rem;
    }

    .users-filter-empty {
        display: none;
        padding: 48px 20px;
        text-align: center;
        border-top: 1px solid var(--ap-border);
    }

    .users-filter-empty.is-visible {
        display: block;
    }

    .users-filter-empty i {
        display: block;
        margin-bottom: 10px;
        color: #64748b;
        font-size: 2rem;
    }

    .users-filter-empty strong {
        display: block;
        margin-bottom: 5px;
        color: #e2e8f0;
    }

    .users-filter-empty span {
        color: var(--ap-subtle);
        font-size: 0.78rem;
    }

    .users-panel-footer {
        padding: 15px 22px;
        border-top: 1px solid var(--ap-border);
        background: rgba(255, 255, 255, 0.012);
    }

    .users-recent {
        padding: 22px;
        border: 1px solid var(--ap-border);
        border-radius: 22px;
        background: rgba(12, 21, 37, 0.88);
        box-shadow: var(--ap-shadow-soft);
    }

    .users-recent-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 17px;
    }

    .users-recent-head h3 {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        color: #fff;
        font-size: 0.97rem;
    }

    .users-recent-head span {
        color: var(--ap-subtle);
        font-size: 0.72rem;
    }

    .users-recent-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .users-recent-card {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
        padding: 13px;
        border: 1px solid var(--ap-border);
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.025);
        transition: 0.2s ease;
    }

    .users-recent-card:hover {
        border-color: rgba(96, 165, 250, 0.24);
        background: rgba(255, 255, 255, 0.045);
        transform: translateY(-2px);
    }

    .users-recent-avatar {
        display: grid;
        width: 39px;
        height: 39px;
        flex: 0 0 39px;
        place-items: center;
        color: #fff;
        font-size: 0.78rem;
        font-weight: 800;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c3aed, #2563eb);
    }

    .users-recent-copy {
        min-width: 0;
        flex: 1;
    }

    .users-recent-copy strong,
    .users-recent-copy small {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .users-recent-copy strong {
        color: #e2e8f0;
        font-size: 0.77rem;
    }

    .users-recent-copy small {
        margin-top: 3px;
        color: var(--ap-subtle);
        font-size: 0.65rem;
    }

    html.light-mode .users-hero,
    html.light-mode .users-panel,
    html.light-mode .users-recent,
    html.light-mode .users-stat {
        border-color: rgba(15, 23, 42, 0.10);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.08);
    }

    html.light-mode .users-hero {
        background:
            radial-gradient(circle at 90% 10%, rgba(99, 102, 241, 0.12), transparent 20rem),
            linear-gradient(135deg, #ffffff, #f7f9fc);
    }

    html.light-mode .users-hero h2,
    html.light-mode .users-panel-title h3,
    html.light-mode .users-stat-copy strong,
    html.light-mode .users-person-copy strong,
    html.light-mode .users-empty h4,
    html.light-mode .users-recent-head h3,
    html.light-mode .users-recent-copy strong {
        color: #0f172a;
    }

    html.light-mode .users-control,
    html.light-mode .users-reset,
    html.light-mode .users-recent-card,
    html.light-mode .users-action {
        border-color: rgba(15, 23, 42, 0.10);
        background: rgba(15, 23, 42, 0.025);
    }

    html.light-mode .users-control {
        color: #0f172a;
    }

    html.light-mode .users-table thead th {
        background: #f8fafc;
    }

    html.light-mode .users-table td {
        border-bottom-color: rgba(15, 23, 42, 0.07);
    }

    html.light-mode .users-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.035);
    }

    @media (max-width: 1200px) {
        .users-stats,
        .users-recent-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .users-toolbar {
            grid-template-columns: minmax(230px, 1fr) 160px 160px;
        }

        .users-reset {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 760px) {
        .users-page {
            gap: 18px;
        }

        .users-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 22px;
        }

        .users-hero-total {
            min-width: 100%;
            min-height: 84px;
        }

        .users-stats,
        .users-recent-grid {
            grid-template-columns: 1fr;
        }

        .users-stat {
            min-height: 96px;
        }

        .users-panel-head {
            align-items: flex-start;
            flex-direction: column;
            padding: 18px;
        }

        .users-toolbar {
            grid-template-columns: 1fr;
            padding: 15px 18px;
        }

        .users-reset {
            grid-column: auto;
        }

        .users-table-wrap {
            overflow: visible;
            padding: 13px;
        }

        .users-table,
        .users-table tbody,
        .users-table tr,
        .users-table td {
            display: block;
            width: 100%;
            min-width: 0;
        }

        .users-table thead {
            display: none;
        }

        .users-table tbody {
            display: grid;
            gap: 12px;
        }

        .users-table tbody tr {
            display: grid;
            gap: 13px;
            padding: 16px;
            border: 1px solid var(--ap-border);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.022);
        }

        .users-table td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 0;
            border: 0;
        }

        .users-table td::before {
            flex: 0 0 auto;
            color: var(--ap-subtle);
            content: attr(data-label);
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .users-table td:first-child {
            justify-content: flex-start;
            padding-bottom: 13px;
            border-bottom: 1px solid var(--ap-border);
        }

        .users-table td:first-child::before,
        .users-table td:last-child::before {
            display: none;
        }

        .users-person {
            min-width: 0;
        }

        .users-person-copy strong,
        .users-person-copy small {
            max-width: 210px;
        }

        .users-actions {
            width: 100%;
            justify-content: flex-start;
            padding-top: 12px;
            border-top: 1px solid var(--ap-border);
        }

        .users-action {
            flex: 1;
            width: auto;
        }

        .users-recent {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="users-page">
    <section class="users-hero">
        <div class="users-hero-copy">
            <span class="users-kicker"><i class="bi bi-shield-check"></i> Gestion des comptes</span>
            <h2>Gérez les utilisateurs simplement</h2>
            <p>Consultez les comptes inscrits, contrôlez leur statut et accédez rapidement aux résultats des tests.</p>
        </div>

        <div class="users-hero-total" aria-label="Nombre total d'utilisateurs">
            <strong>{{ $totalUsersCount }}</strong>
            <span>Utilisateurs</span>
        </div>
    </section>

    <section class="users-stats" aria-label="Statistiques des utilisateurs">
        <article class="users-stat blue">
            <span class="users-stat-icon"><i class="bi bi-people-fill"></i></span>
            <div class="users-stat-copy">
                <strong>{{ $totalUsersCount }}</strong>
                <span>Total des comptes</span>
            </div>
        </article>

        <article class="users-stat green">
            <span class="users-stat-icon"><i class="bi bi-person-check-fill"></i></span>
            <div class="users-stat-copy">
                <strong>{{ $activeUsersCount }}</strong>
                <span>Actifs sur cette page</span>
            </div>
        </article>

        <article class="users-stat orange">
            <span class="users-stat-icon"><i class="bi bi-person-x-fill"></i></span>
            <div class="users-stat-copy">
                <strong>{{ $inactiveUsersCount }}</strong>
                <span>Inactifs sur cette page</span>
            </div>
        </article>

        <article class="users-stat purple">
            <span class="users-stat-icon"><i class="bi bi-person-plus-fill"></i></span>
            <div class="users-stat-copy">
                <strong>{{ $recentUsersCount }}</strong>
                <span>Inscriptions récentes</span>
            </div>
        </article>
    </section>

    <section class="users-panel">
        <header class="users-panel-head">
            <div class="users-panel-title">
                <span class="users-panel-title-icon"><i class="bi bi-people"></i></span>
                <div>
                    <h3>Liste des utilisateurs</h3>
                    <p>Recherchez, filtrez et gérez chaque compte depuis un seul espace.</p>
                </div>
            </div>

            <span class="users-visible-count"><span id="usersVisibleCount">{{ $usersCollection->count() }}</span> affiché(s)</span>
        </header>

        <div class="users-toolbar">
            <label class="users-search" for="usersSearch">
                <i class="bi bi-search"></i>
                <input id="usersSearch" class="users-control" type="search" placeholder="Rechercher par nom ou e-mail..." autocomplete="off">
            </label>

            <select id="usersRoleFilter" class="users-control" aria-label="Filtrer par rôle">
                <option value="">Tous les rôles</option>
                <option value="admin">Administrateur</option>
                <option value="prof">Professeur</option>
                <option value="student">Étudiant</option>
            </select>

            <select id="usersStatusFilter" class="users-control" aria-label="Filtrer par statut">
                <option value="">Tous les statuts</option>
                <option value="active">Actif</option>
                <option value="inactive">Inactif</option>
            </select>

            <button id="usersResetFilters" class="users-reset" type="button">
                <i class="bi bi-arrow-counterclockwise"></i>
                Réinitialiser
            </button>
        </div>

        <div class="users-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Meilleur test</th>
                        <th>Inscription</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse($users as $user)
                        @php
                            $role = $user->role ?? 'student';
                            $normalizedRole = in_array($role, ['admin', 'prof'], true) ? $role : 'student';
                            $roleLabel = $normalizedRole === 'admin'
                                ? 'Administrateur'
                                : ($normalizedRole === 'prof' ? 'Professeur' : 'Étudiant');
                            $roleIcon = $normalizedRole === 'admin'
                                ? 'bi-shield-lock-fill'
                                : ($normalizedRole === 'prof' ? 'bi-person-workspace' : 'bi-mortarboard-fill');
                            $bestResult = (float) ($user->results()->max('percentage') ?? 0);
                            $resultWidth = max(0, min(100, round($bestResult)));
                        @endphp

                        <tr class="users-row"
                            data-search="{{ mb_strtolower($user->name . ' ' . $user->email) }}"
                            data-role="{{ $normalizedRole }}"
                            data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                            <td data-label="Utilisateur">
                                <div class="users-person">
                                    <span class="users-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                    <div class="users-person-copy">
                                        <strong>{{ $user->name }}</strong>
                                        <small>{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Rôle">
                                <span class="users-pill users-role-{{ $normalizedRole }}">
                                    <i class="bi {{ $roleIcon }}"></i>
                                    {{ $roleLabel }}
                                </span>
                            </td>

                            <td data-label="Statut">
                                @if($user->is_active)
                                    <span class="users-pill users-status-active">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Actif
                                    </span>
                                @else
                                    <span class="users-pill users-status-inactive">
                                        <i class="bi bi-x-circle-fill"></i>
                                        Inactif
                                    </span>
                                @endif
                            </td>

                            <td data-label="Meilleur test">
                                <div class="users-test">
                                    <div class="users-test-top">
                                        <span>{{ $bestResult > 0 ? 'Résultat' : 'Non passé' }}</span>
                                        <strong>{{ $bestResult > 0 ? round($bestResult) . '%' : '—' }}</strong>
                                    </div>
                                    <div class="users-test-track" aria-hidden="true">
                                        <div class="users-test-bar" style="width: {{ $resultWidth }}%;"></div>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Inscription">
                                <span class="users-date">
                                    {{ optional($user->created_at)->format('d/m/Y') ?? '—' }}
                                    <small>{{ optional($user->created_at)->diffForHumans() ?? '' }}</small>
                                </span>
                            </td>

                            <td data-label="Actions">
                                <div class="users-actions">
                                    <a href="{{ route('admin.users.test-results', $user->id) }}"
                                       class="users-action"
                                       title="Voir les résultats"
                                       aria-label="Voir les résultats de {{ $user->name }}">
                                        <i class="bi bi-bar-chart-line-fill"></i>
                                    </a>

                                    @if(!$user->is_active)
                                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button class="users-action success" type="submit" title="Activer le compte" aria-label="Activer {{ $user->name }}">
                                                <i class="bi bi-person-check-fill"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button class="users-action" type="submit" title="Renvoyer l’e-mail d’activation" aria-label="Renvoyer l'e-mail d'activation à {{ $user->name }}">
                                                <i class="bi bi-envelope-arrow-up-fill"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.deactivate', $user->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Désactiver le compte de {{ addslashes($user->name) }} ?')">
                                            @csrf
                                            @method('PUT')
                                            <button class="users-action warning" type="submit" title="Désactiver le compte" aria-label="Désactiver {{ $user->name }}">
                                                <i class="bi bi-person-dash-fill"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer définitivement le compte de {{ addslashes($user->name) }} ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="users-action danger" type="submit" title="Supprimer définitivement" aria-label="Supprimer {{ $user->name }}">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="users-empty">
                                <span class="users-empty-icon"><i class="bi bi-people"></i></span>
                                <h4>Aucun utilisateur trouvé</h4>
                                <p>Les comptes inscrits apparaîtront automatiquement dans cette liste.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="usersFilterEmpty" class="users-filter-empty">
            <i class="bi bi-search"></i>
            <strong>Aucun résultat</strong>
            <span>Modifiez la recherche ou les filtres sélectionnés.</span>
        </div>

        @if(method_exists($users, 'links'))
            <footer class="users-panel-footer">
                {{ $users->links() }}
            </footer>
        @endif
    </section>

    @if($recentUsersCollection->isNotEmpty())
        <section class="users-recent">
            <header class="users-recent-head">
                <h3><i class="bi bi-clock-history"></i> Inscriptions récentes</h3>
                <span>{{ $recentUsersCount }} nouveau(x) compte(s)</span>
            </header>

            <div class="users-recent-grid">
                @foreach($recentUsersCollection as $recentUser)
                    <article class="users-recent-card">
                        <span class="users-recent-avatar">{{ mb_strtoupper(mb_substr($recentUser->name, 0, 1)) }}</span>
                        <div class="users-recent-copy">
                            <strong>{{ $recentUser->name }}</strong>
                            <small>{{ optional($recentUser->created_at)->diffForHumans() ?? 'Récemment' }}</small>
                        </div>
                        <span class="users-pill users-status-active">Nouveau</span>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('usersSearch');
        const roleFilter = document.getElementById('usersRoleFilter');
        const statusFilter = document.getElementById('usersStatusFilter');
        const resetButton = document.getElementById('usersResetFilters');
        const rows = Array.from(document.querySelectorAll('.users-row'));
        const visibleCount = document.getElementById('usersVisibleCount');
        const emptyState = document.getElementById('usersFilterEmpty');

        if (!searchInput || !roleFilter || !statusFilter || !resetButton) {
            return;
        }

        const normalize = (value) => (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();

        function applyFilters() {
            const query = normalize(searchInput.value);
            const role = roleFilter.value;
            const status = statusFilter.value;
            let count = 0;

            rows.forEach(function (row) {
                const searchable = normalize(row.dataset.search);
                const matchesSearch = !query || searchable.includes(query);
                const matchesRole = !role || row.dataset.role === role;
                const matchesStatus = !status || row.dataset.status === status;
                const visible = matchesSearch && matchesRole && matchesStatus;

                row.hidden = !visible;

                if (visible) {
                    count += 1;
                }
            });

            if (visibleCount) {
                visibleCount.textContent = count;
            }

            if (emptyState) {
                emptyState.classList.toggle('is-visible', rows.length > 0 && count === 0);
            }
        }

        searchInput.addEventListener('input', applyFilters);
        roleFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        resetButton.addEventListener('click', function () {
            searchInput.value = '';
            roleFilter.value = '';
            statusFilter.value = '';
            applyFilters();
            searchInput.focus();
        });
    });
</script>
@endpush
