@extends('layouts.admin')

@section('title', 'Mon Profil')
@section('page_title', 'Profil')
@section('breadcrumb', 'Mon profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card-body" style="text-align:center;padding:2.5rem;">
                <div class="adm-avatar adm-avatar-lg" style="width:100px;height:100px;font-size:2rem;margin:0 auto 1.25rem;background:var(--adm-gradient-primary);border-radius:50%;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <h3 style="font-weight:700;color:rgba(255,255,255,0.9);margin-bottom:0.25rem;">{{ auth()->user()->name }}</h3>
                <span class="adm-badge adm-badge-primary" style="margin-bottom:1rem;">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>

        <div class="adm-card mt-4">
            <div class="adm-card-header">
                <h4><i class="bi bi-info-circle" style="color:rgba(255,255,255,0.35);"></i> Informations personnelles</h4>
            </div>
            <div class="adm-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="adm-form-label">Nom complet</label>
                        <div style="font-weight:600;color:rgba(255,255,255,0.85);">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="adm-form-label">Email</label>
                        <div style="font-weight:500;color:rgba(255,255,255,0.75);">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="adm-form-label">Rôle</label>
                        <div><span class="adm-badge adm-badge-primary">{{ ucfirst(auth()->user()->role) }}</span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="adm-form-label">Membre depuis</label>
                        <div style="color:var(--adm-text-muted);">{{ auth()->user()->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('admin.settings') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-gear"></i> Modifier le profil
            </a>
            <a href="{{ route('admin.dashboard') }}" class="adm-btn adm-btn-ghost ms-2">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
