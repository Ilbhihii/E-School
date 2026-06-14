@extends('layouts.student')

@section('title', 'Niveaux')

@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-layers" style="color:#4F46E5;"></i> Niveaux</h1>
        <div class="subtitle">Sélectionnez votre niveau pour accéder aux cours</div>
    </div>
</div>

@if($levels->count() > 0)
<div class="row g-3">
    @foreach($levels as $level)
        @php
            $name = Str::lower($level->name);
            $color = match(true) {
                str_contains($name, 'début') || str_contains($name, 'begin') => ['bg' => '#059669', 'rgb' => '5,150,105', 'icon' => 'seedling'],
                str_contains($name, 'pré') || str_contains($name, 'pre') => ['bg' => '#4F46E5', 'rgb' => '79,70,229', 'icon' => 'graph-up-arrow'],
                str_contains($name, 'inter') => ['bg' => '#D97706', 'rgb' => '217,119,6', 'icon' => 'stars'],
                str_contains($name, 'avan') => ['bg' => '#7C3AED', 'rgb' => '124,58,237', 'icon' => 'trophy'],
                default => ['bg' => '#0284C7', 'rgb' => '2,132,199', 'icon' => 'book']
            };
        @endphp
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('student.subjects', $level->id) }}" style="text-decoration:none;display:block;">
                <div style="background:#1E293B;border:1px solid rgba(255,255,255,0.04);border-radius:12px;padding:1.5rem 1.25rem;text-align:center;transition:all 0.2s ease;position:relative;overflow:hidden;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)';this.style.boxShadow='none'">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $color['bg'] }};"></div>
                    <div style="width:56px;height:56px;border-radius:50%;background:rgba({{ $color['rgb'] }},0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.3rem;color:{{ $color['bg'] }};">
                        <i class="bi bi-{{ $color['icon'] }}"></i>
                    </div>
                    <h4 style="font-weight:700;font-size:1rem;color:#F1F5F9;margin-bottom:0.25rem;">{{ $level->name }}</h4>
                    <p style="font-size:0.8rem;color:#64748B;margin:0;">{{ $level->classes_count ?? 0 }} classe{{ ($level->classes_count ?? 0) > 1 ? 's' : '' }}</p>
                </div>
            </a>
        </div>
    @endforeach
</div>
@else
<div class="pr-empty">
    <div class="pr-empty-icon"><i class="bi bi-layers"></i></div>
    <h5>Aucun niveau disponible</h5>
    <p>Les niveaux seront disponibles prochainement.</p>
</div>
@endif

@endsection
