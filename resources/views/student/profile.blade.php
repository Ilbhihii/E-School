@extends('layouts.student')

@section('title', 'Mon profil')
@section('page_title', 'Mon profil')
@section('breadcrumb', 'Profil')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/student-account-v8.css') }}"
    >
@endpush

@section('content')
@php
    $student = auth()->user();

    $studentInitial = strtoupper(
        mb_substr(trim($student->name ?? 'E'), 0, 1)
    );

    $studentPhotoUrl = $student->profile_photo
        ? asset(
            'storage/'
            . ltrim($student->profile_photo, '/')
        )
        : null;
@endphp

<div class="student-account-page">

    <section class="student-profile-hero">
        <div class="student-profile-photo-wrap">
            <span class="student-profile-photo">
                @if($studentPhotoUrl)
                    <img
                        src="{{ $studentPhotoUrl }}"
                        alt="Photo de {{ $student->name }}"
                        onerror="
                            this.hidden=true;
                            this.nextElementSibling.hidden=false;
                        "
                    >
                    <span hidden>{{ $studentInitial }}</span>
                @else
                    <span>{{ $studentInitial }}</span>
                @endif
            </span>

            <span class="student-profile-status-dot"></span>
        </div>

        <div class="student-profile-hero-copy">
            <span class="student-account-kicker">
                <i class="bi bi-mortarboard-fill"></i>
                Espace étudiant
            </span>

            <h2>{{ $student->name }}</h2>

            <p>{{ $student->email }}</p>

            <div class="student-profile-badges">
                <span>
                    <i class="bi bi-person-badge-fill"></i>
                    Étudiant
                </span>

                <span>
                    <i class="bi bi-calendar3"></i>
                    Membre depuis
                    {{ $student->created_at->format('d/m/Y') }}
                </span>

                <span class="success">
                    <i class="bi bi-check-circle-fill"></i>
                    Compte actif
                </span>
            </div>
        </div>

        <a
            href="{{ route('student.settings') }}"
            class="student-account-primary-button"
        >
            <i class="bi bi-pencil-square"></i>
            Modifier mon profil
        </a>
    </section>

    <section class="student-profile-metrics">
        <article>
            <span class="blue">
                <i class="bi bi-journal-bookmark-fill"></i>
            </span>

            <div>
                <small>Matières</small>
                <strong>{{ $subjectsCount ?? 0 }}</strong>
            </div>
        </article>

        <article>
            <span class="violet">
                <i class="bi bi-collection-play-fill"></i>
            </span>

            <div>
                <small>Cours disponibles</small>
                <strong>{{ $coursesCount ?? 0 }}</strong>
            </div>
        </article>

        <article>
            <span class="green">
                <i class="bi bi-send-check-fill"></i>
            </span>

            <div>
                <small>Devoirs envoyés</small>
                <strong>{{ $assignmentsSent ?? 0 }}</strong>
            </div>
        </article>

        <article>
            <span class="amber">
                <i class="bi bi-graph-up-arrow"></i>
            </span>

            <div>
                <small>Moyenne générale</small>
                <strong>
                    {{ number_format($average ?? 0, 1) }}/20
                </strong>
            </div>
        </article>
    </section>

    <div class="student-profile-grid">

        <section class="student-account-card">
            <header class="student-account-card-header">
                <div>
                    <span class="student-account-card-icon blue">
                        <i class="bi bi-person-vcard-fill"></i>
                    </span>

                    <div>
                        <h3>Informations du compte</h3>

                        <p>
                            Vos informations personnelles et
                            votre statut.
                        </p>
                    </div>
                </div>
            </header>

            <div class="student-profile-info-grid">
                <article>
                    <span>
                        <i class="bi bi-person-fill"></i>
                    </span>

                    <div>
                        <small>Nom complet</small>
                        <strong>{{ $student->name }}</strong>
                    </div>
                </article>

                <article>
                    <span>
                        <i class="bi bi-envelope-fill"></i>
                    </span>

                    <div>
                        <small>Adresse e-mail</small>
                        <strong>{{ $student->email }}</strong>
                    </div>
                </article>

                <article>
                    <span>
                        <i class="bi bi-shield-check"></i>
                    </span>

                    <div>
                        <small>Statut du compte</small>
                        <strong>
                            {{
                                $student->is_active
                                    ? 'Actif'
                                    : 'En attente'
                            }}
                        </strong>
                    </div>
                </article>

                <article>
                    <span>
                        <i class="bi bi-clock-history"></i>
                    </span>

                    <div>
                        <small>Dernière mise à jour</small>
                        <strong>
                            {{
                                $student->updated_at
                                    ->format('d/m/Y à H:i')
                            }}
                        </strong>
                    </div>
                </article>
            </div>
        </section>

        <aside class="student-account-card student-profile-security">
            <header class="student-account-card-header">
                <div>
                    <span class="student-account-card-icon green">
                        <i class="bi bi-shield-lock-fill"></i>
                    </span>

                    <div>
                        <h3>Sécurité</h3>

                        <p>
                            Protégez l’accès à votre espace.
                        </p>
                    </div>
                </div>
            </header>

            <div class="student-profile-security-list">
                <div>
                    <span class="success">
                        <i class="bi bi-check-lg"></i>
                    </span>

                    <div>
                        <strong>Compte protégé</strong>
                        <small>
                            Votre mot de passe peut être modifié
                            dans les paramètres.
                        </small>
                    </div>
                </div>

                <div>
                    <span class="blue">
                        <i class="bi bi-image-fill"></i>
                    </span>

                    <div>
                        <strong>Photo de profil</strong>
                        <small>
                            {{
                                $student->profile_photo
                                    ? 'Une photo est actuellement utilisée.'
                                    : 'Ajoutez une photo pour personnaliser votre espace.'
                            }}
                        </small>
                    </div>
                </div>
            </div>

            <a
                href="{{ route('student.settings') }}"
                class="student-account-secondary-button"
            >
                <i class="bi bi-gear-fill"></i>
                Ouvrir les paramètres
            </a>
        </aside>
    </div>

    <section class="student-account-card">
        <header class="student-account-card-header">
            <div>
                <span class="student-account-card-icon violet">
                    <i class="bi bi-diagram-3-fill"></i>
                </span>

                <div>
                    <h3>Mon parcours pédagogique</h3>

                    <p>
                        Matières, niveaux et classes qui vous sont
                        assignés.
                    </p>
                </div>
            </div>

            <span class="student-account-soft-badge">
                {{ $learningPaths->count() }}
                parcours
            </span>
        </header>

        @if($learningPaths->isNotEmpty())
            <div class="student-learning-path-grid">
                @foreach($learningPaths as $path)
                    <article>
                        <span class="student-learning-path-icon">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </span>

                        <div>
                            <small>Matière</small>
                            <strong>{{ $path['subject'] }}</strong>

                            <p>
                                <span>
                                    <i class="bi bi-layers-fill"></i>
                                    {{ $path['level'] }}
                                </span>

                                <i class="bi bi-chevron-right"></i>

                                <span>
                                    <i class="bi bi-building-fill"></i>
                                    {{ $path['class'] }}
                                </span>
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="student-account-empty">
                <span>
                    <i class="bi bi-diagram-3"></i>
                </span>

                <h4>Aucun parcours assigné</h4>

                <p>
                    Votre matière, votre niveau et votre classe
                    apparaîtront ici après l’affectation.
                </p>
            </div>
        @endif
    </section>
</div>
@endsection
