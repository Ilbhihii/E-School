@extends('layouts.prof')

@section('title', 'Voir le cours')
@section('page_title', 'Détail du cours')
@section('breadcrumb', 'Cours → Statut de validation')

@section('content')
@php
    $meta = match($course->approval_status) {
        'approved' => [
            'label' => 'Publié',
            'class' => 'adm-badge-success',
            'icon' => 'bi-check-circle-fill',
        ],
        'rejected' => [
            'label' => 'Refusé',
            'class' => 'adm-badge-danger',
            'icon' => 'bi-x-circle-fill',
        ],
        default => [
            'label' => 'En attente',
            'class' => 'adm-badge-warning',
            'icon' => 'bi-hourglass-split',
        ],
    };
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-book-half"></i>
            Proposition de cours
        </span>

        <h1 class="pp-page-title">
            {{ $course->title }}
        </h1>

        <p class="pp-page-description">
            {{
                $course->description
                ?: 'Aucune description.'
            }}
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="adm-badge {{ $meta['class'] }}">
            <i class="bi {{ $meta['icon'] }}"></i>
            {{ $meta['label'] }}
        </span>

        <a
            href="{{ route('prof.courses.edit', $course) }}"
            class="adm-btn adm-btn-warning"
        >
            <i class="bi bi-pencil"></i>
            Modifier
        </a>
    </div>
</section>

@if($course->isRejected() && $course->rejection_reason)
    <div class="adm-alert adm-alert-danger mb-4">
        <strong>Motif du refus :</strong>
        {{ $course->rejection_reason }}
    </div>
@endif

<section class="pp-panel mb-4">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-diagram-3-fill"></i>
                Parcours
            </h2>
        </div>
    </header>

    <div class="pp-panel-body">
        <div class="pps-path-line">
            <span class="pps-path-chip">
                {{ $course->subject?->name ?? 'Matière' }}
            </span>

            <i class="bi bi-chevron-right"></i>

            <span class="pps-path-chip">
                {{ $course->level?->name ?? 'Niveau' }}
            </span>

            <i class="bi bi-chevron-right"></i>

            <span class="pps-path-chip">
                {{ $course->classRoom?->name ?? 'Classe' }}
            </span>

            <i class="bi bi-chevron-right"></i>

            <span class="pps-slot-badge">
                {{ $course->slot_code ?? '—' }}
            </span>
        </div>
    </div>
</section>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-paperclip"></i>
                Ressources
            </h2>
        </div>
    </header>

    <div class="pp-panel-body">
        <div class="pps-inline-actions">
            @if(isset($resourceUrls['video']))
                <a
                    href="{{ $resourceUrls['video'] }}"
                    target="_blank"
                    class="adm-btn adm-btn-primary"
                >
                    <i class="bi bi-play-circle-fill"></i>
                    Voir la vidéo
                </a>
            @endif

            @if(isset($resourceUrls['pdf']))
                <a
                    href="{{ $resourceUrls['pdf'] }}"
                    target="_blank"
                    class="adm-btn adm-btn-danger"
                >
                    <i class="bi bi-file-pdf-fill"></i>
                    Voir le PDF
                </a>
            @endif

            @if(isset($resourceUrls['link']))
                <a
                    href="{{ $resourceUrls['link'] }}"
                    target="_blank"
                    class="adm-btn adm-btn-ghost"
                >
                    <i class="bi bi-link-45deg"></i>
                    Ouvrir le lien
                </a>
            @endif

            @if(empty($resourceUrls))
                <div class="pps-empty w-100">
                    Aucune ressource jointe.
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
