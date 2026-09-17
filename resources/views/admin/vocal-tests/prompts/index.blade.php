@extends('layouts.admin')

@section('title', 'Tests')
@section('page_title', 'Tests')
@section('breadcrumb', 'Évaluations → Tests')

@section('content')

@php
    $flexibleCount = isset($flexibleTests) ? $flexibleTests->count() : 0;
    $legacyCount = isset($prompts) ? $prompts->count() : 0;
    $totalTests = $flexibleCount + $legacyCount;

    $responseLabels = [
        'vocal' => 'Vocal',
        'written' => 'Écrit',
        'qcm' => 'QCM',
    ];

    $sourceLabels = [
        'text' => 'Texte',
        'files' => 'Fichiers',
        'mixed' => 'Texte + fichiers',
    ];
@endphp

<div class="adm-page-header">
    <div>
        <h1>
            <i class="bi bi-ui-checks-grid me-2" style="color:var(--adm-primary);"></i>
            Tests
        </h1>
        <div class="subtitle">
            Créez et gérez depuis un seul endroit les tests vocaux, écrits et QCM.
        </div>
    </div>

    <div class="page-actions">
        <a
            href="{{ route('admin.flexible-tests.create') }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Nouveau test
        </a>

        <a
            href="{{ route('admin.vocal-tests.submissions.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-list-check me-1"></i>
            Voir les soumissions vocales
        </a>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="adm-card mb-4">
    <div class="adm-card-header">
        <h4>
            <i class="bi bi-clipboard2-check" style="color:rgba(255,255,255,0.35);"></i>
            Tests disponibles
        </h4>

        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">
                {{ $totalTests }} test(s)
            </span>
        </div>
    </div>

    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Matière</th>
                        <th>Niveau</th>
                        <th>Classe</th>
                        <th>Type</th>
                        <th>Support</th>
                        <th>Statut</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($flexibleTests as $test)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div
                                        class="adm-avatar"
                                        style="
                                            background:
                                                {{ $test->response_type === 'vocal'
                                                    ? 'linear-gradient(135deg,#7C3AED,#2563EB)'
                                                    : ($test->response_type === 'qcm'
                                                        ? 'linear-gradient(135deg,#059669,#0EA5E9)'
                                                        : 'linear-gradient(135deg,#F59E0B,#EA580C)') }};
                                            width:36px;
                                            height:36px;
                                            font-size:0.9rem;
                                        "
                                    >
                                        @if($test->response_type === 'vocal')
                                            <i class="bi bi-mic"></i>
                                        @elseif($test->response_type === 'qcm')
                                            <i class="bi bi-ui-checks-grid"></i>
                                        @else
                                            <i class="bi bi-pencil-square"></i>
                                        @endif
                                    </div>

                                    <div>
                                        <strong style="font-size:0.9rem;">
                                            {{ $test->title }}
                                        </strong>

                                        @if(filled($test->instructions))
                                            <br>
                                            <small
                                                style="
                                                    color:var(--adm-text-muted);
                                                    font-size:0.75rem;
                                                "
                                            >
                                                {{ Str::limit($test->instructions, 70) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="adm-badge adm-badge-primary">
                                    {{ $test->subject?->name ?? '-' }}
                                </span>
                            </td>

                            <td style="color:var(--adm-text-muted);font-size:0.85rem;">
                                {{ $test->level?->name ?? '-' }}
                            </td>

                            <td style="color:var(--adm-text-muted);font-size:0.85rem;">
                                {{ $test->classRoom?->name ?? '-' }}
                            </td>

                            <td>
                                @if($test->response_type === 'vocal')
                                    <span class="adm-badge" style="background:rgba(99,102,241,.15);color:#C4B5FD;">
                                        Vocal
                                    </span>
                                @elseif($test->response_type === 'qcm')
                                    <span class="adm-badge" style="background:rgba(16,185,129,.15);color:#6EE7B7;">
                                        QCM
                                    </span>
                                @else
                                    <span class="adm-badge" style="background:rgba(245,158,11,.15);color:#FCD34D;">
                                        Écrit
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="adm-badge adm-badge-info">
                                    {{ $sourceLabels[$test->source_type] ?? $test->source_type }}
                                </span>

                                @if(is_array($test->source_files) && count($test->source_files))
                                    <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:4px;">
                                        @foreach(array_slice($test->source_files, 0, 2) as $index => $file)
                                            <a
                                                href="{{ route('admin.flexible-tests.file', [$test, $index]) }}"
                                                class="adm-badge"
                                                style="
                                                    text-decoration:none;
                                                    background:rgba(148,163,184,.1);
                                                    color:#CBD5E1;
                                                "
                                            >
                                                <i class="bi bi-paperclip"></i>
                                                {{ Str::limit($file['name'] ?? 'Fichier', 18) }}
                                            </a>
                                        @endforeach

                                        @if(count($test->source_files) > 2)
                                            <span style="color:var(--adm-text-muted);font-size:.72rem;">
                                                +{{ count($test->source_files) - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($test->is_active)
                                    <span class="adm-badge adm-badge-success">Actif</span>
                                @else
                                    <span class="adm-badge adm-badge-danger">Inactif</span>
                                @endif
                            </td>

                            <td style="text-align:right;">
                                @if($test->response_type === 'vocal')
                                    <span
                                        class="adm-badge"
                                        style="background:rgba(59,130,246,.10);color:#93C5FD;"
                                        title="Ce test est synchronisé avec le moteur vocal étudiant."
                                    >
                                        <i class="bi bi-link-45deg"></i>
                                        Lié au vocal
                                    </span>
                                @else
                                    <span style="color:var(--adm-text-muted);font-size:.75rem;">
                                        Nouveau moteur
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @foreach($prompts as $prompt)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div
                                        class="adm-avatar"
                                        style="
                                            background:linear-gradient(135deg,#475569,#334155);
                                            width:36px;
                                            height:36px;
                                            font-size:0.9rem;
                                        "
                                    >
                                        <i class="bi bi-mic"></i>
                                    </div>

                                    <div>
                                        <strong style="font-size:0.9rem;">
                                            {{ $prompt->title }}
                                        </strong>
                                        <br>
                                        <small style="color:var(--adm-text-muted);font-size:0.75rem;">
                                            Ancien test vocal
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="adm-badge adm-badge-primary">
                                    {{ $prompt->subject?->name ?? '-' }}
                                </span>
                            </td>

                            <td style="color:var(--adm-text-muted);font-size:0.85rem;">
                                {{ $prompt->level?->name ?? '-' }}
                            </td>

                            <td style="color:var(--adm-text-muted);font-size:0.85rem;">
                                {{ $prompt->classRoom?->name ?? '-' }}
                            </td>

                            <td>
                                <span class="adm-badge" style="background:rgba(99,102,241,.15);color:#C4B5FD;">
                                    Vocal
                                </span>
                            </td>

                            <td>
                                <span class="adm-badge adm-badge-info">Texte</span>
                            </td>

                            <td>
                                @if($prompt->is_active)
                                    <span class="adm-badge adm-badge-success">Actif</span>
                                @else
                                    <span class="adm-badge adm-badge-danger">Inactif</span>
                                @endif
                            </td>

                            <td style="text-align:right;">
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <a
                                        href="{{ route('admin.vocal-tests.prompts.edit', $prompt) }}"
                                        class="adm-btn adm-btn-warning adm-btn-sm"
                                        title="Modifier l'ancien test vocal"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.vocal-tests.prompts.destroy', $prompt) }}"
                                        style="display:inline;"
                                        onsubmit="return confirm('Supprimer cet ancien test vocal ?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="adm-btn adm-btn-danger adm-btn-sm"
                                            type="submit"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($totalTests === 0)
                        <tr>
                            <td colspan="8">
                                <div class="adm-empty">
                                    <div class="adm-empty-icon">
                                        <i class="bi bi-clipboard2-plus"></i>
                                    </div>

                                    <h5>Aucun test</h5>

                                    <p>
                                        Créez votre premier test vocal, écrit ou QCM.
                                    </p>

                                    <a
                                        href="{{ route('admin.flexible-tests.create') }}"
                                        class="adm-btn adm-btn-primary adm-btn-sm"
                                    >
                                        <i class="bi bi-plus-lg"></i>
                                        Nouveau test
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="adm-alert adm-alert-info">
    <strong>
        <i class="bi bi-info-circle me-1"></i>
        Une seule création désormais.
    </strong>
    Les nouveaux tests sont créés avec le bouton
    <strong>« Nouveau test »</strong>.
    Lorsqu'un test est de type Vocal, il est automatiquement relié
    au moteur de test vocal existant.
</div>

@endsection