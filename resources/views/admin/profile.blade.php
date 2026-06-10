@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- HEADER -->
        <div class="admin-header" style="text-align:center;flex-direction:column;">
            <div style="width:120px;height:120px;background:linear-gradient(135deg,#003A8F,#0F3460);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 15px 40px rgba(0,58,143,0.3);">
                <i class="bi bi-person-circle" style="font-size:3.5rem;color:white;"></i>
            </div>
            <div>
                <h1 class="admin-header-title"><span class="gradient">Mon Profil Administrateur</span></h1>
                <span class="adm-badge adm-badge-primary" style="margin-top:0.5rem;"><i class="bi bi-shield-check-fill"></i> Admin Vérifié</span>
            </div>
        </div>

        <!-- INFOS -->
        <div class="adm-card">
            <div class="adm-card-body">
                <div class="adm-detail-row">
                    <div class="adm-detail-item">
                        <label>Nom complet</label>
                        <div class="value">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Email</label>
                        <div class="value" style="font-family:monospace;">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Rôle</label>
                        <div class="value"><span class="adm-badge adm-badge-primary">{{ ucfirst(auth()->user()->role) }}</span></div>
                    </div>
                    <div class="adm-detail-item">
                        <label>Inscrit le</label>
                        <div class="value">{{ auth()->user()->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="adm-flex adm-gap-2 adm-mt-3" style="justify-content:center;">
            <a href="{{ route('admin.settings') }}" class="adm-btn adm-btn-primary adm-btn-lg">
                <i class="bi bi-gear-fill"></i> Modifier Profil
            </a>
            <a href="{{ route('admin.dashboard') }}" class="adm-btn adm-btn-ghost adm-btn-lg">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
