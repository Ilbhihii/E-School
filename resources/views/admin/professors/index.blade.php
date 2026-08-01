@extends('layouts.admin')

@section('title', 'Comptes professeurs')
@section('page_title', 'Professeurs')
@section('breadcrumb', 'Gestion des comptes professeurs')

@section('content')

<style>
.professor-page-header {
    margin-bottom: 1.4rem;
    padding: 1.35rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border: 1px solid rgba(99,102,241,.15);
    border-radius: 16px;
    background:
        linear-gradient(
            135deg,
            rgba(37,99,235,.12),
            rgba(124,58,237,.08)
        );
}

.professor-page-header h1 {
    margin: 0 0 5px;
    color: #f8fafc;
    font-size: 1.35rem;
    font-weight: 850;
}

.professor-page-header p {
    margin: 0;
    color: #64748b;
    font-size: .78rem;
}

.professor-create-button {
    min-height: 43px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 1.1rem;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563eb,
            #7c3aed
        );
    border-radius: 11px;
    font-size: .76rem;
    font-weight: 800;
    text-decoration: none;
    box-shadow:
        0 10px 25px rgba(79,70,229,.23);
}

.professor-create-button:hover {
    color: #fff;
    transform: translateY(-1px);
}

.professor-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: .63rem;
    font-weight: 750;
}

.professor-status.ready {
    color: #86efac;
    background: rgba(34,197,94,.1);
    border: 1px solid rgba(34,197,94,.15);
}

.professor-status.pending {
    color: #fde68a;
    background: rgba(245,158,11,.1);
    border: 1px solid rgba(245,158,11,.15);
}

.professor-resend-button {
    min-height: 32px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 9px;
    color: #c4b5fd;
    background: rgba(124,58,237,.1);
    border: 1px solid rgba(167,139,250,.18);
    border-radius: 8px;
    font-size: .64rem;
    font-weight: 750;
    cursor: pointer;
}

@media (max-width: 700px) {
    .professor-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .professor-create-button {
        width: 100%;
    }
}
</style>

<div class="professor-page-header">
    <div>
        <h1>
            <i class="bi bi-person-video3 me-2"></i>
            Comptes professeurs
        </h1>

        <p>
            Seul l’administrateur peut créer
            et envoyer les accès professeur.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.professors.create'
        ) }}"
        class="professor-create-button"
    >
        <i class="bi bi-person-plus-fill"></i>
        Nouveau professeur
    </a>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="adm-alert adm-alert-danger mb-3">
        {{ session('error') }}
    </div>
@endif

<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-person-video3"></i>
            </div>
        </div>
        <div class="stat-value">
            {{ $totalProfessors }}
        </div>
        <div class="stat-label">
            Total professeurs
        </div>
    </div>

    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>
        <div class="stat-value">
            {{ $activeProfessors }}
        </div>
        <div class="stat-label">
            Comptes actifs
        </div>
    </div>

    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon">
                <i class="bi bi-key-fill"></i>
            </div>
        </div>
        <div class="stat-value">
            {{ $pendingPasswordChange }}
        </div>
        <div class="stat-label">
            Mot de passe à modifier
        </div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4>
            <i class="bi bi-people"></i>
            Liste des professeurs
        </h4>
    </div>

    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Professeur</th>
                        <th>E-mail</th>
                        <th>Statut du mot de passe</th>
                        <th>Dernier envoi</th>
                        <th style="text-align:right;">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(
                        $professors as $professor
                    )
                        <tr>
                            <td>
                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                        gap:11px;
                                    "
                                >
                                    <div
                                        class="adm-avatar"
                                        style="
                                            background:
                                                linear-gradient(
                                                    135deg,
                                                    #2563eb,
                                                    #7c3aed
                                                );
                                        "
                                    >
                                        {{
                                            mb_strtoupper(
                                                mb_substr(
                                                    $professor->name,
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </div>

                                    <div>
                                        <div
                                            style="
                                                color:#f1f5f9;
                                                font-weight:750;
                                            "
                                        >
                                            {{ $professor->name }}
                                        </div>

                                        <small
                                            style="color:#64748b;"
                                        >
                                            ID {{ $professor->id }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $professor->email }}
                            </td>

                            <td>
                                @if(
                                    $professor
                                        ->must_change_password
                                )
                                    <span
                                        class="
                                            professor-status
                                            pending
                                        "
                                    >
                                        <i
                                            class="bi bi-hourglass-split"
                                        ></i>
                                        Première connexion
                                    </span>
                                @else
                                    <span
                                        class="
                                            professor-status
                                            ready
                                        "
                                    >
                                        <i
                                            class="bi bi-check-circle-fill"
                                        ></i>
                                        Mot de passe modifié
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if(
                                    $professor
                                        ->temporary_password_sent_at
                                )
                                    {{
                                        \Carbon\Carbon::parse(
                                            $professor
                                                ->temporary_password_sent_at
                                        )->format(
                                            'd/m/Y H:i'
                                        )
                                    }}
                                @else
                                    —
                                @endif
                            </td>

                            <td style="text-align:right;">
                                <form
                                    method="POST"
                                    action="{{
                                        route(
                                            'admin.professors.resend',
                                            $professor
                                        )
                                    }}"
                                    onsubmit="
                                        return confirm(
                                            'Générer un nouveau mot de passe et l’envoyer à {{ addslashes($professor->email) }} ?'
                                        )
                                    "
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="
                                            professor-resend-button
                                        "
                                    >
                                        <i
                                            class="bi bi-envelope-arrow-up-fill"
                                        ></i>
                                        Renvoyer les accès
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                style="
                                    padding:3rem;
                                    text-align:center;
                                    color:#64748b;
                                "
                            >
                                <i
                                    class="bi bi-person-video3"
                                    style="
                                        display:block;
                                        margin-bottom:.7rem;
                                        font-size:2rem;
                                    "
                                ></i>

                                Aucun compte professeur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($professors->hasPages())
    <div class="mt-3">
        {{ $professors->links() }}
    </div>
@endif

@endsection
