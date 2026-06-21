@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.levels') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-layers me-1"></i>Niveaux</a>
                    <span>/</span>
                    <a href="{{ route('prof.levels.classes', $level) }}" style="color:var(--muted);text-decoration:none;">{{ $level->name }}</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $class->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">📡 Lives — {{ $class->name }}</span></h1>
                <p class="admin-header-subtitle">Consultez les sessions en direct programmées</p>
            </div>
            
        </div>

        @if($lives->isEmpty())                    <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-camera-video-off"></i>
                    <h3>Aucun live</h3>
                    <p>Aucune session live n'a été planifiée pour cette classe.</p>
                </div>
            </div>
        @else
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><i class="bi bi-camera-video-fill"></i> Lives pour {{ $class->name }}</h3>
                </div>
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Lien</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lives as $live)
                            <tr>
                                <td><span style="font-weight:600;">{{ $live->title }}</span></td>
                                <td><span class="adm-badge adm-badge-gray">{{ \Carbon\Carbon::parse($live->live_date)->format('d/m/Y') }}</span></td>
                                <td>{{ $live->start_time }} - {{ $live->end_time }}</td>
                                <td><a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-ghost"><i class="bi bi-box-arrow-up-right"></i></a></td>
                                <td>
                                    @if($live->stream_url)
                                    <a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-primary"><i class="bi bi-box-arrow-up-right me-1"></i> Rejoindre</a>
                                    @else
                                    <span class="adm-badge adm-badge-gray">Lien à venir</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="st-mt-3">
            <a href="{{ route('prof.subjects', [$level, $class]) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left me-1"></i> Retour aux matières
            </a>
        </div>

    </div>
</div>
@endsection
