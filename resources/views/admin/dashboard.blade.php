@extends('layouts.admin')

@section('title', 'Tableau de bord Admin')
@section('page_title', 'Tableau de bord')
@section('breadcrumb', 'Vue d\'ensemble')

@section('content')

<!-- Stats Grid -->
<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <span class="stat-change up"><i class="bi bi-arrow-up"></i> +12%</span>
        </div>
        <div class="stat-value">{{ $classesCount ?? 0 }}</div>
        <div class="stat-label">Classes actives</div>
    </div>

    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
            <span class="stat-change up"><i class="bi bi-arrow-up"></i> +8%</span>
        </div>
        <div class="stat-value">{{ $coursesCount ?? 0 }}</div>
        <div class="stat-label">Cours publiés</div>
    </div>

    <div class="adm-stat purple">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-camera-video-fill"></i></div>
            <span class="stat-change up"><i class="bi bi-arrow-up"></i> +5%</span>
        </div>
        <div class="stat-value">{{ $livesCount ?? 0 }}</div>
        <div class="stat-label">Lives programmés</div>
    </div>

    <div class="adm-stat cyan">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <span class="stat-change up"><i class="bi bi-arrow-up"></i> +23%</span>
        </div>
        <div class="stat-value">{{ $usersCount ?? 0 }}</div>
        <div class="stat-label">Étudiants inscrits</div>
    </div>
</div>

<div class="row g-4">
    <!-- Students Table -->
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-people" style="color:rgba(255,255,255,0.35);"></i> Étudiants récents</h4>
                <div class="card-actions">
                    <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">
                        Voir tout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="adm-card-body p-0">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Pays</th>
                                <th>Ville</th>
                                <th>Inscrit le</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students ?? [] as $student)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div class="adm-avatar" style="background:linear-gradient(135deg,#003A8F,#2563EB);">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <span style="font-weight:500;">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td><span class="adm-badge adm-badge-info">{{ $student->country ?? '-' }}</span></td>
                                <td style="color:var(--adm-text-muted);">{{ $student->city ?? '-' }}</td>
                                <td style="color:var(--adm-text-muted);font-size:0.8rem;">
                                    {{ $student->created_at ? $student->created_at->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="adm-empty">
                                        <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                        <h5>Aucun étudiant</h5>
                                        <p>Les étudiants inscrits apparaîtront ici.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(isset($students) && method_exists($students, 'links'))
            <div class="adm-card-footer">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-lightning-fill" style="color:#FFD166;"></i> Actions rapides</h4>
            </div>
            <div class="adm-card-body">
                <div class="adm-action-grid">
                    <a href="{{ route('admin.courses.create') }}" class="adm-action-card">
                        <div class="action-icon" style="background:rgba(0,58,143,0.2);color:#60A5FA;">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <span class="action-title">Nouveau cours</span>
                    </a>
                    <a href="{{ route('admin.lives.create') }}" class="adm-action-card">
                        <div class="action-icon" style="background:rgba(217,4,41,0.2);color:#FCA5A5;">
                            <i class="bi bi-camera-video"></i>
                        </div>
                        <span class="action-title">Nouveau live</span>
                    </a>
                    <a href="{{ route('admin.assign.class') }}" class="adm-action-card">
                        <div class="action-icon" style="background:rgba(124,58,237,0.2);color:#A78BFA;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <span class="action-title">Assigner</span>
                    </a>
                    <a href="{{ route('admin.schedule.index') }}" class="adm-action-card">
                        <div class="action-icon" style="background:rgba(6,182,212,0.2);color:#67E8F9;">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <span class="action-title">Planning</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-activity" style="color:#4ADE80;"></i> Activité récente</h4>
            </div>
            <div class="adm-card-body p-0">
                <div style="padding:0 1.5rem;">
                    <div class="adm-activity">
                        <div class="adm-activity-item">
                            <div class="adm-activity-dot" style="background:#4ADE80;"></div>
                            <div class="adm-activity-content">
                                <p><strong>Nouveau cours</strong> ajouté par l'admin</p>
                                <div class="adm-activity-time">Il y a 2 heures</div>
                            </div>
                        </div>
                        <div class="adm-activity-item">
                            <div class="adm-activity-dot" style="background:#60A5FA;"></div>
                            <div class="adm-activity-content">
                                <p><strong>3 étudiants</strong> se sont inscrits</p>
                                <div class="adm-activity-time">Il y a 5 heures</div>
                            </div>
                        </div>
                        <div class="adm-activity-item">
                            <div class="adm-activity-dot" style="background:#FCD34D;"></div>
                            <div class="adm-activity-content">
                                <p><strong>Live programmé</strong> : Cours de mathématiques</p>
                                <div class="adm-activity-time">Il y a 1 jour</div>
                            </div>
                        </div>
                        <div class="adm-activity-item">
                            <div class="adm-activity-dot" style="background:#FCA5A5;"></div>
                            <div class="adm-activity-content">
                                <p><strong>Mise à jour</strong> des paramètres système</p>
                                <div class="adm-activity-time">Il y a 2 jours</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
