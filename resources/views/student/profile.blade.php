@extends('layouts.student')
@section('title', 'Mon Profil')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-person-circle" style="color:#4F46E5;"></i> Mon Profil</h1>
        <div class="subtitle">Vos informations personnelles</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('student.settings') }}" class="pr-btn pr-btn-ghost pr-btn-sm"><i class="bi bi-gear me-1"></i> Paramètres</a>
    </div>
</div>

<!-- Hero -->
<div class="pr-card mb-4" style="background:linear-gradient(135deg,rgba(79,70,229,0.04),rgba(2,132,199,0.02));border-color:rgba(79,70,229,0.06);">
    <div class="pr-card-body" style="padding:1.5rem 1.5rem;">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div style="width:72px;height:72px;border-radius:50%;padding:3px;background:conic-gradient(#4F46E5,#0284C7,#7C3AED,#4F46E5);animation:spin 8s linear infinite;flex-shrink:0;">
                <div style="width:100%;height:100%;border-radius:50%;background:#0F172A;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.4rem;color:white;">{{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 2)) }}</div>
            </div>
            <div>
                <h3 style="font-weight:700;color:#F1F5F9;margin-bottom:0.15rem;">{{ auth()->user()->name }}</h3>
                <p style="color:#64748B;font-size:0.85rem;margin-bottom:0.5rem;">{{ auth()->user()->email }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="pr-badge pr-badge-info"><i class="bi bi-mortarboard-fill me-1"></i> Étudiant</span>
                    <span class="pr-badge pr-badge-primary"><i class="bi bi-calendar3 me-1"></i> Inscrit le {{ auth()->user()->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="pr-card">
            <div class="pr-card-header">
                <h4><i class="bi bi-info-circle" style="color:#64748B;"></i> Informations personnelles</h4>
            </div>
            <div class="pr-card-body">
                <div class="row g-3">
                    @foreach([
                        ['label' => 'Nom complet', 'value' => auth()->user()->name, 'icon' => 'person-fill'],
                        ['label' => 'Adresse Email', 'value' => auth()->user()->email, 'icon' => 'envelope-fill'],
                        ['label' => 'Rôle', 'value' => '<span class="pr-badge pr-badge-info"><i class="bi bi-mortarboard-fill me-1"></i> Étudiant</span>', 'icon' => 'person-badge'],
                        ['label' => 'Membre depuis', 'value' => auth()->user()->created_at->format('d F Y'), 'icon' => 'calendar3'],
                        ['label' => 'Statut', 'value' => '<span class="pr-badge pr-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Actif</span>', 'icon' => 'shield-check'],
                        ['label' => 'Dernière connexion', 'value' => auth()->user()->updated_at->format('d/m/Y H:i'), 'icon' => 'clock'],
                    ] as $info)
                    <div class="col-md-6">
                        <div style="padding:0.75rem;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.04em;color:#64748B;font-weight:600;margin-bottom:2px;">
                                <i class="bi bi-{{ $info['icon'] }} me-1" style="color:#4F46E5;"></i> {{ $info['label'] }}
                            </div>
                            <div style="font-weight:500;font-size:0.85rem;color:#F1F5F9;">{!! $info['value'] !!}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="pr-card">
            <div class="pr-card-header">
                <h4><i class="bi bi-bar-chart-fill" style="color:#059669;"></i> Aperçu</h4>
            </div>
            <div class="pr-card-body">
                @foreach([
                    ['icon' => 'book', 'label' => 'Cours', 'sub' => 'Consultés', 'value' => $coursesCount ?? 0, 'color' => '#4F46E5'],
                    ['icon' => 'upload', 'label' => 'Devoirs', 'sub' => 'Soumis', 'value' => $assignmentsSent ?? 0, 'color' => '#059669'],
                    ['icon' => 'trophy', 'label' => 'Moyenne', 'sub' => 'Générale', 'value' => number_format($average ?? 0, 1) . '/20', 'color' => '#7C3AED'],
                ] as $item)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;border-radius:8px;background:rgba({{ $item['color'] === '#4F46E5' ? '79,70,229' : ($item['color'] === '#059669' ? '5,150,105' : '124,58,237') }},0.04);border:1px solid rgba({{ $item['color'] === '#4F46E5' ? '79,70,229' : ($item['color'] === '#059669' ? '5,150,105' : '124,58,237') }},0.04);margin-bottom:0.5rem;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:8px;background:rgba({{ $item['color'] === '#4F46E5' ? '79,70,229' : ($item['color'] === '#059669' ? '5,150,105' : '124,58,237') }},0.1);display:flex;align-items:center;justify-content:center;font-size:0.9rem;color:{{ $item['color'] }};"><i class="bi bi-{{ $item['icon'] }}"></i></div>
                        <div><div style="font-weight:600;color:#F1F5F9;font-size:0.82rem;">{{ $item['label'] }}</div><div style="font-size:0.68rem;color:#64748B;">{{ $item['sub'] }}</div></div>
                    </div>
                    <span style="font-weight:700;font-size:1.2rem;color:{{ $item['color'] }};">{{ $item['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

@endsection
