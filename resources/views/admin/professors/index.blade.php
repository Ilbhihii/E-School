@extends('layouts.admin')

@section('title', 'Comptes professeurs')
@section('page_title', 'Professeurs')
@section('breadcrumb', 'Gestion des comptes professeurs')

@section('content')
<section class="professor-page-header">
    <div>
        <span class="admin-section-kicker">
            <i class="bi bi-mortarboard-fill"></i>
            Équipe pédagogique
        </span>
        <h1>Comptes professeurs</h1>
        <p>Créez les comptes enseignants et suivez l’activation de leurs accès sécurisés.</p>
    </div>

    <a href="{{ route('admin.professors.create') }}" class="professor-create-button">
        <i class="bi bi-person-plus-fill"></i>
        Nouveau professeur
    </a>
</section>

<div class="adm-stats-grid">
    <article class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-person-video3"></i></div>
        </div>
        <div class="stat-value">{{ $totalProfessors }}</div>
        <div class="stat-label">Professeurs enregistrés</div>
    </article>

    <article class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
        </div>
        <div class="stat-value">{{ $activeProfessors }}</div>
        <div class="stat-label">Comptes actifs</div>
    </article>

    <article class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-key-fill"></i></div>
        </div>
        <div class="stat-value">{{ $pendingPasswordChange }}</div>
        <div class="stat-label">Première connexion en attente</div>
    </article>
</div>

<section class="adm-card professor-directory-card">
    <header class="adm-card-header">
        <div>
            <h4><i class="bi bi-people-fill"></i> Annuaire des professeurs</h4>
            <small class="admin-card-subtitle">{{ $professors->total() }} compte(s) professeur</small>
        </div>

        <label class="admin-table-search" aria-label="Rechercher un professeur">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Nom ou adresse e-mail…">
        </label>
    </header>

    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Professeur</th>
                        <th>Adresse e-mail</th>
                        <th>Accès</th>
                        <th>Dernier envoi</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($professors as $professor)
                        <tr>
                            <td>
                                <div class="admin-person-cell">
                                    <span class="adm-avatar professor-avatar">
                                        {{ mb_strtoupper(mb_substr($professor->name, 0, 1)) }}
                                    </span>
                                    <span class="admin-person-copy">
                                        <strong>{{ $professor->name }}</strong>
                                        <small>Professeur · ID {{ $professor->id }}</small>
                                    </span>
                                </div>
                            </td>

                            <td>
                                <a href="mailto:{{ $professor->email }}" class="admin-email-link">
                                    <i class="bi bi-envelope"></i>
                                    {{ $professor->email }}
                                </a>
                            </td>

                            <td>
                                @if($professor->must_change_password)
                                    <span class="professor-status pending">
                                        <i class="bi bi-hourglass-split"></i>
                                        Première connexion
                                    </span>
                                @else
                                    <span class="professor-status ready">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Accès configuré
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($professor->temporary_password_sent_at)
                                    <span class="admin-date-cell">
                                        <i class="bi bi-send-check"></i>
                                        {{ \Carbon\Carbon::parse($professor->temporary_password_sent_at)->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span class="admin-muted-value">Jamais envoyé</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <form
                                    method="POST"
                                    action="{{ route('admin.professors.resend', $professor) }}"
                                    onsubmit="return confirm('Générer un nouveau mot de passe et l’envoyer à {{ addslashes($professor->email) }} ?')"
                                >
                                    @csrf
                                    <button type="submit" class="professor-resend-button">
                                        <i class="bi bi-envelope-arrow-up-fill"></i>
                                        Renvoyer les accès
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="adm-empty">
                                    <div class="adm-empty-icon"><i class="bi bi-person-video3"></i></div>
                                    <h5>Aucun professeur enregistré</h5>
                                    <p>Créez le premier compte professeur pour commencer.</p>
                                    <a href="{{ route('admin.professors.create') }}" class="adm-btn adm-btn-primary mt-2">
                                        <i class="bi bi-person-plus-fill"></i>
                                        Créer un professeur
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($professors->hasPages())
        <footer class="adm-card-footer">
            {{ $professors->links() }}
        </footer>
    @endif
</section>
@endsection
