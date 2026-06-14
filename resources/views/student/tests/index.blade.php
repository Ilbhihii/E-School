@extends('layouts.student')
@section('title', 'Tests disponibles')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-pencil-square" style="color:#7C3AED;"></i> Tests disponibles</h1>
        <div class="subtitle">Évaluez vos connaissances</div>
    </div>
</div>

@if($tests->count() > 0)
<div class="row g-3">
    @foreach($tests as $test)
    <div class="col-lg-4 col-md-6">
        <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.25rem;transition:all 0.2s ease;height:100%;display:flex;flex-direction:column;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;border-radius:10px;background:rgba(124,58,237,0.1);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#7C3AED;flex-shrink:0;"><i class="bi bi-pencil-fill"></i></div>
                <div>
                    <h5 style="font-weight:600;color:#F1F5F9;margin:0;font-size:0.9rem;">{{ $test->title }}</h5>
                    <small style="color:#64748B;">{{ $test->subject->name ?? 'Matière' }}</small>
                </div>
            </div>
            <div style="flex:1;"></div>
            <div class="d-flex align-items-center justify-content-between pt-3" style="border-top:1px solid rgba(255,255,255,0.04);">
                <div style="display:flex;align-items:center;gap:6px;">
                    <i class="bi bi-clock" style="color:#64748B;font-size:0.82rem;"></i>
                    <span style="font-size:0.78rem;color:#64748B;">{{ $test->duration }} min</span>
                </div>
                <a href="{{ route('student.tests.show', $test) }}" class="pr-btn pr-btn-primary pr-btn-sm"><i class="bi bi-play-fill me-1"></i> Passer le test</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-pencil"></i></div><h5>Aucun test disponible</h5><p>Les tests apparaîtront ici dès qu'ils seront créés par vos professeurs.</p></div>
@endif

@endsection
