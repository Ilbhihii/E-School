@extends('layouts.admin')

@section('title', 'Lives - ' . $class->name)
@section('page_title', $class->name)
@section('breadcrumb', 'Lives → ' . $subject->name . ' → ' . $level->name . ' → ' . $class->name)

@section('content')

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a href="{{ route('admin.lives.index') }}" style="color:var(--adm-text-muted);text-decoration:none;"><i class="bi bi-camera-video me-1"></i>Lives</a>
            <span>/</span>
            <a href="{{ route('admin.lives.subject-levels', $subject) }}" style="color:var(--adm-text-muted);text-decoration:none;">{{ $subject->name }}</a>
            <span>/</span>
            <a href="{{ route('admin.lives.subject-classes', [$subject, $level]) }}" style="color:var(--adm-text-muted);text-decoration:none;">{{ $level->name }}</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ $class->name }}</span>
        </div>
        <h1><i class="bi bi-camera-video me-2" style="color:var(--adm-danger);"></i> Lives — {{ $class->name }}</h1>
        <div class="subtitle">{{ $subject->name }} · {{ $level->name }} · {{ $class->name }}</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.lives.subject-classes', [$subject, $level]) }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Retour aux classes
        </a>
        <a href="{{ route('admin.lives.create') }}?class_id={{ $class->id }}" class="adm-btn adm-btn-danger">
            <i class="bi bi-plus-lg"></i> Nouveau live
        </a>
    </div>
</div>

<div class="adm-stats-grid">
    <div class="adm-stat red">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-camera-video-fill"></i></div>
        </div>
        <div class="stat-value">{{ $totalLives }}</div>
        <div class="stat-label">Total lives</div>
    </div>
    <div class="adm-stat orange">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-value">{{ $lives->filter(fn($l) => $l->live_date && now()->gte($l->live_date))->count() }}</div>
        <div class="stat-label">Passés</div>
    </div>
    <div class="adm-stat green">
        <div class="stat-top">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        </div>
        <div class="stat-value">{{ $lives->filter(fn($l) => $l->live_date && now()->lt($l->live_date))->count() }}</div>
        <div class="stat-label">À venir</div>
    </div>
</div>

@if($lives->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon"><i class="bi bi-camera-video"></i></div>
            <h5>Aucun live pour cette classe</h5>
            <p>Créez votre premier live pour cette classe.</p>
            <a href="{{ route('admin.lives.create') }}?class_id={{ $class->id }}" class="adm-btn adm-btn-danger mt-3">
                <i class="bi bi-plus-lg"></i> Créer un live
            </a>
        </div>
    </div>
@else
    <div class="adm-card">
        <div class="adm-card-header">
            <h4><i class="bi bi-collection" style="color:rgba(255,255,255,0.35);"></i> Lives — {{ $class->name }}</h4>
            <div class="card-actions">
                <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $lives->count() }} live(s)</span>
            </div>
        </div>
        <div class="adm-card-body p-0">
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Lien</th>
                            <th>Date</th>
                            <th>Horaire</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lives as $live)
                        <tr>
                            <td><span style="font-weight:500;">{{ $live->title }}</span></td>
                            <td>
                                @if($live->stream_url)
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <a href="{{ $live->stream_url }}" target="_blank" rel="noopener noreferrer" class="adm-btn adm-btn-ghost adm-btn-sm">
                                        <i class="bi bi-globe2"></i> Web
                                    </a>
                                    @if($live->teams_app_url)
                                    <a href="{{ $live->teams_app_url }}" class="adm-btn adm-btn-primary adm-btn-sm">
                                        <i class="bi bi-microsoft-teams"></i> Application Teams
                                    </a>
                                    @endif
                                </div>
                                @else
                                <span style="color:var(--adm-text-muted);font-size:0.75rem;">—</span>
                                @endif
                            </td>
                            <td style="color:var(--adm-text-muted);font-size:0.8rem;">
                                @if($live->live_date)
                                    {{ \Carbon\Carbon::parse($live->live_date)->format('d/m/Y') }}
                                @else
                                    {{ $live->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                            <td style="color:var(--adm-text-muted);font-size:0.8rem;">
                                @if($live->start_time)
                                    {{ $live->start_time }} @if($live->end_time) — {{ $live->end_time }} @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    @if($live->live_date)
                                    @php
                                        $liveDate = \Carbon\Carbon::parse($live->live_date);
                                        $startTime = $live->start_time ? $live->start_time : '00:00';
                                        $endTime = $live->end_time ? $live->end_time : date('H:i', strtotime($startTime . ' +1 hour'));
                                        $startDt = $liveDate->format('Y-m-d') . 'T' . $startTime . ':00Z';
                                        $endDt = $liveDate->format('Y-m-d') . 'T' . $endTime . ':00Z';
                                        $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
                                        $outlookUrl .= '&subject=' . urlencode($live->title);
                                        $outlookUrl .= '&startdt=' . $startDt;
                                        $outlookUrl .= '&enddt=' . $endDt;
                                        $outlookUrl .= '&body=' . urlencode(($live->description ?? 'Session en direct') . "\n\nLien : " . ($live->stream_url ?? ''));
                                        $outlookUrl .= '&location=' . urlencode($live->stream_url ?? '');
                                    @endphp
                                    <a href="{{ $outlookUrl }}" target="_blank" class="adm-btn adm-btn-sm" style="background:rgba(2,132,199,0.12);color:#38BDF8;border:1px solid rgba(2,132,199,0.15);"
                                       onmouseover="this.style.background='rgba(2,132,199,0.25)'" onmouseout="this.style.background='rgba(2,132,199,0.12)'">
                                        <i class="bi bi-calendar-plus"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.lives.edit', $live) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.lives.destroy',$live) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce live ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="adm-empty">
                                    <div class="adm-empty-icon"><i class="bi bi-camera-video"></i></div>
                                    <h5>Aucun live</h5>
                                    <p>Créez votre premier live pour cette classe.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Outlook Calendar List -->
    <div class="adm-card mt-4">
        <div class="adm-card-header">
            <h4><i class="bi bi-calendar-plus" style="color:#FCA5A5;"></i> Ajouter au calendrier Outlook</h4>
            <div class="card-actions">
                <span style="color:var(--adm-text-muted);font-size:0.78rem;"><i class="bi bi-info-circle me-1"></i> Cliquez sur Outlook pour chaque live</span>
            </div>
        </div>
        <div class="adm-card-body p-0">
            @forelse($lives as $live)
            @php
                $liveDate = $live->live_date ? \Carbon\Carbon::parse($live->live_date) : null;
                $startTime = $live->start_time ? $live->start_time : '00:00';
                $endTime = $live->end_time ? $live->end_time : date('H:i', strtotime($startTime . ' +1 hour'));
                $startDt = $liveDate ? $liveDate->format('Y-m-d') . 'T' . $startTime . ':00Z' : '';
                $endDt = $liveDate ? $liveDate->format('Y-m-d') . 'T' . $endTime . ':00Z' : '';
                $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
                $outlookUrl .= '&subject=' . urlencode($live->title);
                $outlookUrl .= '&startdt=' . $startDt;
                $outlookUrl .= '&enddt=' . $endDt;
                $outlookUrl .= '&body=' . urlencode(($live->description ?? 'Session en direct') . "\n\nLien : " . ($live->stream_url ?? ''));
                $outlookUrl .= '&location=' . urlencode($live->stream_url ?? '');
            @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.04);gap:12px;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#DC2626,#EF4444);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-camera-video-fill" style="font-size:0.85rem;color:white;"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-weight:500;font-size:0.85rem;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $live->title }}</div>
                        @if($liveDate)
                        <div style="color:var(--adm-text-muted);font-size:0.72rem;margin-top:2px;">
                            <i class="bi bi-calendar3 me-1"></i>{{ $liveDate->format('d/m/Y') }}
                            @if($live->start_time) <i class="bi bi-clock ms-2 me-1"></i>{{ $live->start_time }} @endif
                        </div>
                        @endif
                    </div>
                </div>
                @if($live->live_date)
                <a href="{{ $outlookUrl }}" target="_blank"
                   style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.5);font-size:0.78rem;text-decoration:none;transition:all 0.2s;white-space:nowrap;"
                   onmouseover="this.style.background='rgba(2,132,199,0.15)';this.style.borderColor='rgba(2,132,199,0.2)';this.style.color='#38BDF8'"
                   onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.borderColor='rgba(255,255,255,0.06)';this.style.color='rgba(255,255,255,0.5)'">
                    <i class="bi bi-calendar-plus"></i> Outlook
                </a>
                @endif
            </div>
            @empty
            <div class="adm-empty">
                <div class="adm-empty-icon"><i class="bi bi-calendar-plus"></i></div>
                <h5>Aucun live à ajouter</h5>
                <p>Les lives apparaîtront ici pour ajouter au calendrier Outlook.</p>
            </div>
            @endforelse
        </div>
    </div>
@endif

@endsection
