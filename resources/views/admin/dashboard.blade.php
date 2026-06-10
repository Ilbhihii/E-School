@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Dashboard Admin</span></h1>
                <p class="admin-header-subtitle">Gestion complète de la plateforme éducative</p>
            </div>
            <div class="adm-flex adm-gap-2">
                <span class="adm-badge adm-badge-info">
                    <i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card blue adm-fade-up">
                <div class="stat-card-icon blue"><i class="bi bi-mortarboard-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $classesCount }}</div>
                    <div class="stat-card-label">Classes</div>
                </div>
            </div>
            <div class="stat-card green adm-fade-up">
                <div class="stat-card-icon green"><i class="bi bi-book-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $coursesCount }}</div>
                    <div class="stat-card-label">Cours</div>
                </div>
            </div>
            <div class="stat-card red adm-fade-up">
                <div class="stat-card-icon red"><i class="bi bi-camera-video-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $livesCount }}</div>
                    <div class="stat-card-label">Lives</div>
                </div>
            </div>
            <div class="stat-card cyan adm-fade-up">
                <div class="stat-card-icon cyan"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-card-value">{{ $usersCount }}</div>
                    <div class="stat-card-label">Étudiants</div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h3>Liste des étudiants</h3>
                    <p>Derniers inscrits sur la plateforme</p>
                </div>
                <div class="adm-search-wrap" style="max-width:280px">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" placeholder="Rechercher..." id="studentSearch">
                </div>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Pays</th>
                            <th>Ville</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="adm-user-cell">
                                    <div class="adm-avatar">{{ strtoupper(substr($student->name,0,2)) }}</div>
                                    <div class="adm-user-cell-info">
                                        <div class="adm-user-cell-name">{{ $student->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="adm-badge adm-badge-gray">{{ $student->country ?? '-' }}</span></td>
                            <td>{{ $student->city ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="adm-empty">Aucun étudiant trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($students, 'links'))
            <div class="adm-card-footer">
                <div class="adm-pagination">{{ $students->links() }}</div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
