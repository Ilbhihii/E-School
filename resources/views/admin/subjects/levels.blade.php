@extends('layouts.admin')

@section('title', 'Parcours - ' . $subject->name)
@section('page_title', $subject->name)
@section('breadcrumb', 'Matières → Parcours → Niveaux')

@section('content')

@php
    $normalizedSubjectName = \App\Models\VocalTestPrompt::normalizePathName(
        $subject->name
    );

    $isArabic = $normalizedSubjectName === 'arabe';

    $levelDescriptions = [
        \App\Models\VocalTestPrompt::ARABIC_READING_WRITING =>
            'Apprendre à lire et à écrire en arabe.',
        \App\Models\VocalTestPrompt::ARABIC_COMMUNICATION =>
            'Apprendre à comprendre et à communiquer en arabe.',
        \App\Models\VocalTestPrompt::QURAN_LEARNING_TAJWID =>
            'Comprendre, appliquer les règles du tajwid et mémoriser.',
    ];

    $levelIcons = [
        \App\Models\VocalTestPrompt::ARABIC_READING_WRITING =>
            'bi-book-half',
        \App\Models\VocalTestPrompt::ARABIC_COMMUNICATION =>
            'bi-chat-dots-fill',
        \App\Models\VocalTestPrompt::QURAN_LEARNING_TAJWID =>
            'bi-moon-stars-fill',
    ];

    $levelGradients = [
        \App\Models\VocalTestPrompt::ARABIC_READING_WRITING =>
            'linear-gradient(135deg, #047857, #22C55E)',
        \App\Models\VocalTestPrompt::ARABIC_COMMUNICATION =>
            'linear-gradient(135deg, #D97706, #FBBF24)',
        \App\Models\VocalTestPrompt::QURAN_LEARNING_TAJWID =>
            'linear-gradient(135deg, #92400E, #D97706)',
    ];

    $classDesigns = [
        \App\Models\VocalTestPrompt::CLASS_BEGINNER => [
            'gradient' => 'linear-gradient(135deg, #047857, #22C55E)',
            'icon' => 'bi-journal-bookmark-fill',
            'description' => $isArabic
                ? 'Premiers apprentissages, sans test vocal.'
                : 'J’apprends les premières règles.',
        ],
        \App\Models\VocalTestPrompt::CLASS_INTERMEDIATE => [
            'gradient' => 'linear-gradient(135deg, #1D4ED8, #60A5FA)',
            'icon' => 'bi-book-half',
            'description' => $isArabic
                ? 'Je progresse et je consolide mes acquis.'
                : 'Je débute et j’applique le tajwid.',
        ],
        \App\Models\VocalTestPrompt::CLASS_ADVANCED => [
            'gradient' => 'linear-gradient(135deg, #6D28D9, #A78BFA)',
            'icon' => 'bi-stars',
            'description' => $isArabic
                ? 'Je maîtrise des compétences avancées.'
                : 'Tajwid, mémorisation et perfectionnement.',
        ],
    ];

    $columnCount = max(1, min(3, $levels->count()));
@endphp

<style>
.validated-paths-grid {
    --validated-columns: {{ $columnCount }};
    display: grid;
    grid-template-columns:
        repeat(var(--validated-columns), minmax(285px, 390px));
    justify-content: center;
    align-items: stretch;
    gap: 22px;
}

.validated-path-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.14);
    border-radius: 22px;
    background: rgba(15, 23, 42, 0.72);
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
}

.validated-path-header {
    padding: 1.15rem;
    color: #fff;
}

.validated-path-heading {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.validated-path-icon {
    width: 50px;
    height: 50px;
    flex: 0 0 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.18);
    font-size: 1.35rem;
}

.validated-path-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
}

.validated-path-subtitle {
    margin: 5px 0 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.78rem;
    line-height: 1.5;
}

.validated-path-body {
    display: grid;
    gap: 12px;
    padding: 14px;
}

.validated-class-card {
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.14);
    border-radius: 16px;
    background: rgba(15, 23, 42, 0.72);
}

.validated-class-main {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px;
    color: inherit;
    text-decoration: none;
    transition: background 0.2s ease, transform 0.2s ease;
}

.validated-class-main:hover {
    color: inherit;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.035);
}

.validated-class-icon {
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    color: rgba(255, 255, 255, 0.88);
    font-size: 1.15rem;
}

.validated-class-content {
    min-width: 0;
    flex: 1;
}

.validated-class-name {
    margin: 0 0 3px;
    color: rgba(255, 255, 255, 0.94);
    font-size: 0.94rem;
    font-weight: 800;
}

.validated-class-description,
.validated-class-meta {
    display: block;
    color: var(--adm-text-muted);
    font-size: 0.74rem;
    line-height: 1.45;
}

.validated-class-status {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 0 13px 12px;
}

.validated-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
}

.validated-status-badge.no-test {
    color: #86EFAC;
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.22);
}

.validated-status-badge.test {
    color: #C4B5FD;
    background: rgba(124, 58, 237, 0.13);
    border: 1px solid rgba(167, 139, 250, 0.23);
}

.structure-info {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 22px;
    padding: 14px 16px;
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 15px;
    background: rgba(37, 99, 235, 0.08);
    color: #BFDBFE;
}

.structure-info i {
    margin-top: 2px;
    color: #60A5FA;
    font-size: 1.15rem;
}

@media (max-width: 980px) {
    .validated-paths-grid {
        grid-template-columns: repeat(2, minmax(270px, 380px));
    }
}

@media (max-width: 680px) {
    .validated-paths-grid {
        grid-template-columns: minmax(0, 430px);
    }
}
</style>

<div class="adm-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:0.8rem;color:var(--adm-text-muted);">
            <a
                href="{{ route('admin.subjects.index') }}"
                style="color:var(--adm-text-muted);text-decoration:none;"
            >
                <i class="bi bi-book me-1"></i>Matières
            </a>
            <span>/</span>
            <span style="color:rgba(255,255,255,0.6);font-weight:500;">
                {{ $subject->name }}
            </span>
        </div>

        <h1>
            <i
                class="bi bi-diagram-3-fill me-2"
                style="color:var(--adm-primary);"
            ></i>
            Parcours — {{ $subject->name }}
        </h1>

        <div class="subtitle">
            Structure validée : parcours, niveaux et accès aux cours
        </div>
    </div>

    <div class="page-actions">
        <a
            href="{{ route('admin.subjects.index') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Retour aux matières
        </a>
    </div>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div class="adm-alert adm-alert-info mb-4">
        {{ session('info') }}
    </div>
@endif

<div class="structure-info">
    <i class="bi bi-shield-check"></i>
    <div>
        <strong>Structure pédagogique protégée</strong>
        <div style="margin-top:3px;font-size:0.82rem;line-height:1.55;">
            Les anciens parcours et les doublons ne sont plus affichés.
            Les niveaux fixes sont Débutant, Intermédiaire et Avancé.
        </div>
    </div>
</div>

@if($levels->isEmpty())
    <div class="adm-card">
        <div class="adm-empty" style="padding:4rem 2rem;">
            <div class="adm-empty-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            <h5>Aucun parcours valide trouvé</h5>
            <p>
                Exécutez la migration et les seeders de la structure validée.
            </p>
        </div>
    </div>
@else
    <div class="validated-paths-grid">
        @foreach($levels as $level)
            @php
                $gradient = $levelGradients[$level->name]
                    ?? 'linear-gradient(135deg, #334155, #64748B)';

                $icon = $levelIcons[$level->name]
                    ?? 'bi-layers-fill';

                $description = $levelDescriptions[$level->name]
                    ?? $level->description
                    ?? 'Parcours pédagogique';

                $classes = $level->classes;
            @endphp

            <article class="validated-path-card">
                <header
                    class="validated-path-header"
                    style="background:{{ $gradient }};"
                >
                    <div class="validated-path-heading">
                        <div class="validated-path-icon">
                            <i class="bi {{ $icon }}"></i>
                        </div>

                        <div>
                            <h2 class="validated-path-title">
                                {{ $level->name }}
                            </h2>
                            <p class="validated-path-subtitle">
                                {{ $description }}
                            </p>
                        </div>
                    </div>
                </header>

                <div class="validated-path-body">
                    @forelse($classes as $class)
                        @php
                            $design = $classDesigns[$class->name]
                                ?? [
                                    'gradient' =>
                                        'linear-gradient(135deg, #334155, #64748B)',
                                    'icon' => 'bi-mortarboard-fill',
                                    'description' => 'Niveau pédagogique',
                                ];

                            $courseCount = $class->courses()
                                ->where('subject_id', $subject->id)
                                ->count();

                            $requiresTest = !(
                                $isArabic
                                && $class->name ===
                                    \App\Models\VocalTestPrompt::CLASS_BEGINNER
                            );
                        @endphp

                        <section class="validated-class-card">
                            <a
                                href="{{ route('admin.subjects.courses', [$subject, $level, $class]) }}"
                                class="validated-class-main"
                                title="Voir les cours de {{ $class->name }}"
                            >
                                <div
                                    class="validated-class-icon"
                                    style="background:{{ $design['gradient'] }};"
                                >
                                    <i class="bi {{ $design['icon'] }}"></i>
                                </div>

                                <div class="validated-class-content">
                                    <h3 class="validated-class-name">
                                        {{ $class->name }}
                                    </h3>

                                    <span class="validated-class-description">
                                        {{ $design['description'] }}
                                    </span>

                                    <span class="validated-class-meta">
                                        <i class="bi bi-play-circle me-1"></i>
                                        {{ $courseCount }} cours
                                    </span>
                                </div>

                                <i
                                    class="bi bi-chevron-right"
                                    style="color:rgba(255,255,255,0.24);"
                                ></i>
                            </a>

                            <div class="validated-class-status">
                                @if($requiresTest)
                                    <span class="validated-status-badge test">
                                        <i class="bi bi-mic-fill"></i>
                                        Test vocal
                                    </span>
                                @else
                                    <span class="validated-status-badge no-test">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Sans test vocal
                                    </span>
                                @endif

                                <a
                                    href="{{ route('admin.classes.edit', $class) }}"
                                    class="adm-btn adm-btn-warning adm-btn-sm"
                                    title="Modifier {{ $class->name }}"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Modifier
                                </a>
                            </div>
                        </section>
                    @empty
                        <div class="adm-empty" style="padding:2rem 1rem;">
                            <div
                                class="adm-empty-icon"
                                style="font-size:2rem;"
                            >
                                <i class="bi bi-building"></i>
                            </div>
                            <h5 style="font-size:1rem;">
                                Niveaux introuvables
                            </h5>
                            <p style="font-size:0.85rem;">
                                Relancez le ClassSeeder pour recréer
                                Débutant, Intermédiaire et Avancé.
                            </p>
                        </div>
                    @endforelse
                </div>
            </article>
        @endforeach
    </div>
@endif

@endsection