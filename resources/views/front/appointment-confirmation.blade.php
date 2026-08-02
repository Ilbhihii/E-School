@extends('layouts.front')

@section('title', 'Rendez-vous envoyé')

@section('content')

@php
    $submission =
        $vocalSubmission
        ?? $highSchoolSubmission;

    $isWritten =
        !empty($highSchoolSubmission);

    $isDirectInterview =
        !$submission
        && !empty($appointment->subject_id);

    $pathSubject =
        $submission?->subject
        ?? $appointment->subject;

    $pathLevel =
        $submission?->level
        ?? $appointment->level;

    $pathClass =
        $submission?->classRoom
        ?? $appointment->classRoom;
@endphp

<section class="confirmation-page">
    <div class="container">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <span class="confirmation-badge">
                Rendez-vous n°{{ $appointment->id }}
            </span>

            <h1>
                Votre demande a bien été envoyée
            </h1>

            <p class="confirmation-intro">
                @if($isDirectInterview)
                    Votre demande d’entretien pour le
                    Soutien Lycée a été transmise.
                    L’administration confirmera le créneau.
                @elseif($isWritten)
                    Les images de votre test écrit et votre
                    demande de rendez-vous sont disponibles
                    pour l’administration.
                @else
                    Votre récitation vocale et votre demande
                    de rendez-vous sont disponibles
                    pour l’administration.
                @endif
            </p>

            <div class="confirmation-details">
                <div>
                    <span>Matière</span>

                    <strong>
                        {{ $pathSubject?->name ?? '—' }}
                    </strong>
                </div>

                <div>
                    <span>Niveau</span>

                    <strong>
                        {{ $pathLevel?->name ?? '—' }}
                    </strong>
                </div>

                <div>
                    <span>
                        {{
                            $isDirectInterview
                                ? 'Spécialité'
                                : (
                                    $isWritten
                                        ? 'Matière du BAC'
                                        : 'Classe'
                                )
                        }}
                    </span>

                    <strong>
                        {{ $pathClass?->name ?? '—' }}
                    </strong>
                </div>

                @if($isDirectInterview)
                    <div>
                        <span>Mode d’entretien</span>

                        <strong>
                            {{
                                $appointment
                                    ->interview_method_label
                            }}
                        </strong>
                    </div>

                    <div>
                        <span>Date souhaitée</span>

                        <strong>
                            {{
                                $appointment
                                    ->preferred_date
                                    ?->format('d/m/Y')
                                ?? '—'
                            }}
                        </strong>
                    </div>

                    <div>
                        <span>Heure souhaitée</span>

                        <strong>
                            {{
                                $appointment
                                    ->preferred_time_label
                            }}
                        </strong>
                    </div>
                @endif

                @if($isWritten)
                    <div>
                        <span>Images envoyées</span>

                        <strong>
                            {{
                                count(
                                    $highSchoolSubmission
                                        ->images()
                                )
                            }}
                        </strong>
                    </div>
                @endif

                <div>
                    <span>Statut</span>

                    <strong class="pending-status">
                        En attente de confirmation
                    </strong>
                </div>
            </div>

            @if(
                $isDirectInterview
                && $appointment->notes
            )
                <div class="confirmation-note">
                    <i class="bi bi-chat-left-text"></i>

                    <div>
                        <span>Votre message</span>
                        <p>{{ $appointment->notes }}</p>
                    </div>
                </div>
            @endif

            <div class="confirmation-next">
                <i class="bi bi-info-circle-fill"></i>

                <p>
                    @if(
                        $isDirectInterview
                        && $appointment->interview_method
                            === 'whatsapp'
                    )
                        L’administration vous contactera
                        sur WhatsApp au
                        {{ $appointment->phone }}.
                    @elseif(
                        $isDirectInterview
                        && $appointment->interview_method
                            === 'phone_call'
                    )
                        L’administration vous appellera au
                        {{ $appointment->phone }}.
                    @elseif($isDirectInterview)
                        Le lien de l’appel vidéo vous sera
                        envoyé après confirmation.
                    @else
                        L’administration vous contactera pour
                        confirmer la date du rendez-vous.
                    @endif
                </p>
            </div>

            <div class="confirmation-actions">
                <a
                    href="{{ route('home') }}"
                    class="confirmation-home"
                >
                    <i class="bi bi-house"></i>
                    Retour à l’accueil
                </a>

                @if($isDirectInterview)
                    <a
                        href="{{
                            route(
                                'front.subject.levels',
                                $pathSubject
                            )
                        }}"
                        class="confirmation-back"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Retour au parcours
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
.confirmation-page {
    min-height: 100vh;
    padding: 6.5rem 0 4rem;
    background:
        radial-gradient(
            circle at 50% 12%,
            rgba(37,99,235,.17),
            transparent 33%
        ),
        linear-gradient(
            135deg,
            #07101F,
            #15102D
        );
}

.confirmation-card {
    max-width: 720px;
    margin: 0 auto;
    padding: 2rem;
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 27px;
    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.075),
            rgba(255,255,255,.035)
        );
    box-shadow:
        0 30px 75px rgba(0,0,0,.35);
    backdrop-filter: blur(20px);
    text-align: center;
}

.confirmation-icon {
    width: 76px;
    height: 76px;
    display: grid;
    place-items: center;
    margin: 0 auto 1.1rem;
    border: 1px solid rgba(34,197,94,.25);
    border-radius: 50%;
    color: #4ADE80;
    background: rgba(34,197,94,.13);
    font-size: 2rem;
}

.confirmation-badge {
    display: inline-flex;
    padding: 7px 12px;
    border-radius: 999px;
    color: #C4B5FD;
    background: rgba(124,58,237,.14);
    font-size: .68rem;
    font-weight: 800;
}

.confirmation-card h1 {
    margin: .9rem 0 .55rem;
    color: #fff;
    font-size: clamp(1.65rem,4vw,2.35rem);
    font-weight: 900;
}

.confirmation-intro {
    max-width: 550px;
    margin: 0 auto 1.35rem;
    color: rgba(255,255,255,.48);
    font-size: .75rem;
    line-height: 1.6;
}

.confirmation-details {
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.075);
    border-radius: 15px;
    background: rgba(255,255,255,.025);
    text-align: left;
}

.confirmation-details > div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 11px 13px;
    border-bottom: 1px solid rgba(255,255,255,.055);
}

.confirmation-details > div:last-child {
    border-bottom: 0;
}

.confirmation-details span {
    color: rgba(255,255,255,.38);
    font-size: .65rem;
}

.confirmation-details strong {
    color: rgba(255,255,255,.82);
    font-size: .7rem;
    text-align: right;
}

.pending-status {
    color: #FCD34D !important;
}

.confirmation-note,
.confirmation-next {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-top: 1rem;
    padding: 11px 12px;
    border-radius: 12px;
    text-align: left;
}

.confirmation-note {
    border: 1px solid rgba(96,165,250,.14);
    background: rgba(37,99,235,.07);
}

.confirmation-note i {
    color: #93C5FD;
}

.confirmation-note div {
    display: flex;
    flex-direction: column;
}

.confirmation-note span {
    color: #BFDBFE;
    font-size: .61rem;
    font-weight: 800;
}

.confirmation-note p {
    margin: 3px 0 0;
    color: rgba(255,255,255,.5);
    font-size: .65rem;
    line-height: 1.5;
}

.confirmation-next {
    border: 1px solid rgba(245,158,11,.15);
    color: #FCD34D;
    background: rgba(245,158,11,.07);
}

.confirmation-next p {
    margin: 0;
    color: rgba(255,255,255,.55);
    font-size: .65rem;
    line-height: 1.5;
}

.confirmation-actions {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 9px;
    margin-top: 1.2rem;
}

.confirmation-actions a {
    min-height: 43px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 15px;
    border-radius: 12px;
    font-size: .69rem;
    font-weight: 800;
    text-decoration: none;
}

.confirmation-home {
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
}

.confirmation-back {
    border: 1px solid rgba(255,255,255,.12);
    color: rgba(255,255,255,.7);
    background: rgba(255,255,255,.045);
}

.confirmation-actions a:hover {
    color: #fff;
}

@media (max-width:575px) {
    .confirmation-page {
        padding-top: 5.3rem;
    }

    .confirmation-card {
        padding: 1.2rem;
        border-radius: 20px;
    }

    .confirmation-details > div {
        align-items: flex-start;
        flex-direction: column;
        gap: 4px;
    }

    .confirmation-details strong {
        text-align: left;
    }
}
</style>

@endsection

{{-- Design global V12 : présentation uniquement, aucun contenu modifié. --}}
@push('scripts')
<link
    rel="stylesheet"
    href="{{ asset('css/front-design-v12.css?v=12.0') }}"
>
@endpush

