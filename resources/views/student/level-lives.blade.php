@extends('layouts.student')
@section('title', 'Lives - {{ $class->name ?? '' }}')
@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('student.subjects.index') }}"><i class="bi bi-book me-1"></i>Matières</a><span class="sep">/</span>
            <span style="color:#94A3B8;">{{ $class->name }}</span>
        </div>
        <h1><i class="bi bi-broadcast" style="color:#DC2626;"></i> Lives — {{ $class->name }}</h1>
        <div class="subtitle">Accédez aux sessions en direct pour cette classe</div>
    </div>
</div>

@if($lives->count() > 0)
<div class="row g-3">
    @foreach($lives as $live)
        @php
            $liveDate = \Carbon\Carbon::parse($live->live_date);
            $isLive = now()->gte($liveDate) && now()->lt($liveDate->copy()->addHours(2));
            $isUpcoming = now()->lt($liveDate);
            $status = $isLive ? '🔴 En direct' : ($isUpcoming ? '⏳ Bientôt' : '✅ Terminé');
            $badge = $isLive ? 'pr-badge-danger' : ($isUpcoming ? 'pr-badge-info' : 'pr-badge-warning');
        @endphp
        <div class="col-md-6 col-lg-4">
            <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.25rem;transition:all 0.2s ease;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 style="font-weight:600;color:#F1F5F9;margin:0;font-size:0.9rem;">{{ Str::limit($live->title, 35) }}</h5>
                    <span class="pr-badge {{ $badge }}" style="font-size:0.65rem;">{{ $status }}</span>
                </div>
                <p style="font-size:0.78rem;color:#64748B;margin-bottom:0.75rem;">{{ $live->description ?? 'Aucune description' }}</p>
                <div class="d-flex align-items-center justify-content-between pt-2" style="border-top:1px solid rgba(255,255,255,0.04);">
                    <small style="color:#475569;font-size:0.7rem;">{{ $isLive ? 'En cours' : 'Début le ' . $liveDate->format('d/m/Y H:i') }}</small>
                    @if($isLive || $isUpcoming)
                    <a href="{{ $live->stream_url }}" target="_blank" class="pr-btn {{ $isLive ? 'pr-btn-danger' : 'pr-btn-ghost' }} pr-btn-sm" style="font-size:0.7rem;">{{ $isLive ? 'Rejoindre' : 'Bientôt' }}</a>
                    @else
                    <span class="pr-badge pr-badge-warning" style="font-size:0.65rem;">Terminé</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-camera-video-off"></i></div><h5>Aucun live disponible</h5><p>Aucune session live n'est programmée pour cette classe.</p><a href="{{ route('student.subjects', [$level, $class]) }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-arrow-left me-1"></i> Voir les matières</a></div>
@endif

@endsection
