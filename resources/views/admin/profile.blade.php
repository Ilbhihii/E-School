@extends('layouts.admin')

@section('title', 'Mon Profil')
@section('page_title', 'Profil')
@section('breadcrumb', 'Mon profil')

@php
    $admin = auth()->user();
    $profilePhotoUrl = $admin->profile_photo
        ? asset('storage/' . ltrim($admin->profile_photo, '/'))
        : null;
    $profileInitial = strtoupper(mb_substr($admin->name ?? 'A', 0, 1));
@endphp

@push('styles')
<style>
    .admin-profile-shell {
        width: min(900px, 100%);
        margin: 0 auto;
    }

    .admin-profile-hero {
        position: relative;
        overflow: hidden;
        padding: 34px;
        border: 1px solid rgba(148, 163, 184, .14);
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(37,99,235,.15), rgba(124,58,237,.1)), rgba(15,23,42,.72);
    }

    .admin-profile-hero::after {
        position: absolute;
        top: -90px;
        right: -70px;
        width: 240px;
        height: 240px;
        content: '';
        border-radius: 50%;
        background: rgba(96,165,250,.1);
    }

    .admin-profile-main {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .admin-profile-photo {
        display: grid;
        width: 118px;
        height: 118px;
        flex: 0 0 118px;
        place-items: center;
        overflow: hidden;
        color: #fff;
        font-size: 2.35rem;
        font-weight: 800;
        border: 5px solid rgba(255,255,255,.1);
        border-radius: 50%;
        background: linear-gradient(135deg,#2563eb,#7c3aed);
        box-shadow: 0 20px 45px rgba(15,23,42,.32);
    }

    .admin-profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .admin-profile-copy h2 {
        margin: 0 0 6px;
        color: var(--adm-text,#f8fafc);
        font-size: clamp(1.5rem,3vw,2rem);
        font-weight: 800;
    }

    .admin-profile-copy p {
        margin: 0 0 12px;
        color: var(--adm-text-muted,#94a3b8);
    }

    .admin-profile-info {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 14px;
        margin-top: 22px;
    }

    .profile-info-item {
        padding: 17px;
        border: 1px solid rgba(148,163,184,.12);
        border-radius: 16px;
        background: rgba(255,255,255,.025);
    }

    .profile-info-item span {
        display: block;
        margin-bottom: 6px;
        color: var(--adm-text-muted,#94a3b8);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .profile-info-item strong {
        color: var(--adm-text,#f8fafc);
        font-size: .92rem;
        overflow-wrap: anywhere;
    }

    .admin-profile-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    html.light-mode .admin-profile-hero { background: linear-gradient(135deg,#eff6ff,#f5f3ff); }
    html.light-mode .profile-info-item { background:#f8fafc; }

    @media(max-width:620px) {
        .admin-profile-hero { padding: 25px 20px; }
        .admin-profile-main { flex-direction:column; text-align:center; }
        .admin-profile-info { grid-template-columns:1fr; }
        .admin-profile-actions .adm-btn { width:100%; justify-content:center; }
    }
</style>
@endpush

@section('content')
<div class="admin-profile-shell">
    <section class="admin-profile-hero">
        <div class="admin-profile-main">
            <div class="admin-profile-photo">
                @if ($profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}" alt="Photo de profil de {{ $admin->name }}">
                @else
                    <span>{{ $profileInitial }}</span>
                @endif
            </div>

            <div class="admin-profile-copy">
                <h2>{{ $admin->name }}</h2>
                <p>{{ $admin->email }}</p>
                <span class="adm-badge adm-badge-primary"><i class="bi bi-shield-check"></i> Administrateur</span>
            </div>
        </div>

        <div class="admin-profile-info">
            <div class="profile-info-item">
                <span>Nom complet</span>
                <strong>{{ $admin->name }}</strong>
            </div>
            <div class="profile-info-item">
                <span>Adresse e-mail</span>
                <strong>{{ $admin->email }}</strong>
            </div>
            <div class="profile-info-item">
                <span>Rôle</span>
                <strong>{{ ucfirst($admin->role) }}</strong>
            </div>
            <div class="profile-info-item">
                <span>Membre depuis</span>
                <strong>{{ optional($admin->created_at)->translatedFormat('d F Y') }}</strong>
            </div>
        </div>
    </section>

    <div class="admin-profile-actions">
        <a href="{{ route('admin.settings') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-camera"></i>
            Modifier le profil et la photo
        </a>
        <a href="{{ route('admin.dashboard') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-speedometer2"></i>
            Tableau de bord
        </a>
    </div>
</div>
@endsection
