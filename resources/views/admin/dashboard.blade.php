@extends('layouts.admin')

@section('title', 'Tableau de bord Admin')
@section('page_title', 'Tableau de bord')
@section('breadcrumb', 'Vue d\'ensemble')

@section('content')

<style>
.analysis-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.5rem;margin:1.5rem 0}.analysis-bars{display:flex;align-items:flex-end;gap:12px;height:180px;padding-top:18px}.analysis-bar-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;gap:8px}.analysis-bar{width:min(42px,75%);min-height:4px;border-radius:8px 8px 3px 3px;background:linear-gradient(180deg,#60A5FA,#2563EB);box-shadow:0 8px 20px rgba(37,99,235,.2)}.analysis-label{font-size:.68rem;color:var(--adm-text-muted);text-align:center}.analysis-value{font-size:.75rem;font-weight:700;color:#fff}.progress-row{margin-bottom:1rem}.progress-meta{display:flex;justify-content:space-between;gap:12px;margin-bottom:6px;font-size:.78rem}.progress-track{height:9px;border-radius:999px;background:rgba(255,255,255,.07);overflow:hidden}.progress-fill{height:100%;border-radius:inherit;background:linear-gradient(90deg,#7C3AED,#60A5FA)}.analysis-summary{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:18px}.summary-box{padding:12px;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.035);border-radius:12px;text-align:center}.summary-box strong{display:block;font-size:1.25rem;color:#fff}.summary-box span{font-size:.68rem;color:var(--adm-text-muted)}@media(max-width:900px){.analysis-grid{grid-template-columns:1fr}}@media(max-width:520px){.analysis-summary{grid-template-columns:1fr}.analysis-bars{gap:5px}}
</style>

<!-- Stats Grid -->
<div class="adm-stats-grid">
    <div class="adm-stat blue">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <span class="stat-change"><i class="bi bi-database-check"></i> Donnée réelle</span>
        </div>
        <div class="stat-value">{{ $classesCount ?? 0 }}</div>
        <div class="stat-label">Classes actives</div>
    </div>

    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
            <span class="stat-change">{{ $testResultsCount ?? 0 }} évaluations</span>
        </div>
        <div class="stat-value">{{ $coursesCount ?? 0 }}</div>
        <div class="stat-label">Cours publiés</div>
    </div>

    <div class="adm-stat purple">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-camera-video-fill"></i></div>
            <span class="stat-change">{{ $pendingAppointments ?? 0 }} RDV en attente</span>
        </div>
        <div class="stat-value">{{ $livesCount ?? 0 }}</div>
        <div class="stat-label">Lives programmés</div>
    </div>

    <div class="adm-stat cyan">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <span class="stat-change">{{ $assignmentRate ?? 0 }}% assignés</span>
        </div>
        <div class="stat-value">{{ $usersCount ?? 0 }}</div>
        <div class="stat-label">Étudiants inscrits</div>
    </div>
</div>

<div class="analysis-grid">
    <div class="adm-card">
        <div class="adm-card-header">
            <h4><i class="bi bi-bar-chart-line" style="color:#60A5FA;"></i> Inscriptions sur 6 mois</h4>
        </div>
        <div class="adm-card-body">
            <div class="analysis-bars">
                @foreach($registrationsByMonth as $month)
                    <div class="analysis-bar-item">
                        <span class="analysis-value">{{ $month['value'] }}</span>
                        <div class="analysis-bar" style="height:{{ max(4, round(($month['value'] / $maxMonthlyRegistrations) * 130)) }}px"></div>
                        <span class="analysis-label">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h4><i class="bi bi-pie-chart" style="color:#A78BFA;"></i> Analyse pédagogique</h4>
        </div>
        <div class="adm-card-body">
            @foreach($coursesBySubject as $item)
                <div class="progress-row">
                    <div class="progress-meta"><span>{{ $item['label'] }}</span><strong>{{ $item['value'] }} cours</strong></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ round(($item['value'] / $maxCoursesBySubject) * 100) }}%"></div></div>
                </div>
            @endforeach
            <div class="analysis-summary">
                <div class="summary-box"><strong>{{ $professorsCount }}</strong><span>Professeurs</span></div>
                <div class="summary-box"><strong>{{ $assignedStudentsCount }}/{{ $usersCount }}</strong><span>Étudiants assignés</span></div>
                <div class="summary-box"><strong>{{ $assignmentRate }}%</strong><span>Taux d’assignation</span></div>
            </div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header"><h4><i class="bi bi-calendar-check" style="color:#4ADE80;"></i> Rendez-vous</h4></div>
        <div class="adm-card-body">
            @php $appointmentTotal = max(1, array_sum($appointmentsByStatus)); @endphp
            @foreach(['pending' => ['En attente','#FCD34D'], 'confirmed' => ['Confirmés','#4ADE80'], 'cancelled' => ['Annulés','#F87171']] as $status => [$label, $color])
                <div class="progress-row">
                    <div class="progress-meta"><span>{{ $label }}</span><strong>{{ $appointmentsByStatus[$status] }}</strong></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ round(($appointmentsByStatus[$status] / $appointmentTotal) * 100) }}%;background:{{ $color }}"></div></div>
                </div>
            @endforeach
            <a href="{{ route('admin.appointments.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">Analyser les rendez-vous <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header"><h4><i class="bi bi-globe" style="color:#67E8F9;"></i> Principaux pays</h4></div>
        <div class="adm-card-body">
            @forelse($studentsByCountry as $country)
                <div class="progress-row">
                    <div class="progress-meta"><span>{{ $country->country }}</span><strong>{{ $country->total }} étudiant(s)</strong></div>
                    <div class="progress-track"><div class="progress-fill" style="width:{{ $usersCount ? round(($country->total / $usersCount) * 100) : 0 }}%;background:linear-gradient(90deg,#0891B2,#22D3EE)"></div></div>
                </div>
            @empty
                <div class="adm-empty"><p>Aucune donnée de pays disponible.</p></div>
            @endforelse
        </div>
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
                        @forelse($recentActivities as $activity)
                            @php
                                $activityColor = match($activity['type']) { 'student' => '#60A5FA', 'course' => '#4ADE80', default => '#FCD34D' };
                            @endphp
                            <div class="adm-activity-item">
                                <div class="adm-activity-dot" style="background:{{ $activityColor }};"></div>
                                <div class="adm-activity-content">
                                    <p><strong>{{ $activity['title'] }}</strong> : {{ $activity['description'] }}</p>
                                    <div class="adm-activity-time">{{ $activity['date']->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="adm-empty"><p>Aucune activité récente.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
