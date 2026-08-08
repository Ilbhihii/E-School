@extends('layouts.admin')

@section('title', 'Rendez-vous — Administration')
@section('page_title', 'Rendez-vous')
@section('breadcrumb', 'Demandes reçues')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>
            <i
                class="bi bi-calendar-check"
                style="color:#60A5FA;"
            ></i>

            Rendez-vous et entretiens
        </h1>

        <div class="subtitle">
            Toutes les demandes reçues : rendez-vous généraux,
            entretiens BAC, récitations vocales et tests écrits.
        </div>
    </div>

    <span class="appointment-total">
        {{ $appointments->count() }}
        demande(s)
    </span>
</div>

@if(session('success'))
    <div class="adm-alert adm-alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="adm-alert adm-alert-danger mb-3">
        {{ session('error') }}
    </div>
@endif

<div class="appointments-panel">
    <div class="appointments-toolbar">
        <div class="appointments-count">
            <i class="bi bi-inbox-fill"></i>
            Demandes reçues
        </div>

        <label class="appointments-search">
            <i class="bi bi-search"></i>

            <input
                type="search"
                id="appointmentsSearch"
                placeholder="Rechercher un étudiant, une ville..."
                autocomplete="off"
            >
        </label>
    </div>

    <div class="table-responsive">
        <table class="table adm-table">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Contact</th>
                    <th>Ville / Pays</th>
                    <th>Parcours</th>
                    <th>Entretien / Test</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="appointmentsBody">
                @forelse($appointments as $appointment)
                    @php
                        $vocal =
                            $appointment
                                ->vocalSubmission;

                        $written =
                            $appointment
                                ->highSchoolTestSubmission;

                        $submission =
                            $vocal ?? $written;

                        $isWritten =
                            (bool) $written;

                        $isDirectInterview =
                            !$submission
                            && !empty(
                                $appointment->subject_id
                            );

                        $isGeneralAppointment =
                            !$submission
                            && !$isDirectInterview;

                        $isAdmissionAppointment =
                            $appointment->type
                            === \App\Models\TestAppointment::TYPE_TEST;

                        $generalTypeIcons = [
                            'information' => 'bi-info-circle-fill',
                            'communication' => 'bi-chat-dots-fill',
                            'other' => 'bi-calendar2-check-fill',
                        ];

                        $generalTypeIcon =
                            $generalTypeIcons[$appointment->type]
                            ?? 'bi-calendar2-check-fill';

                        $pathSubject =
                            $submission?->subject
                            ?? $appointment->subject;

                        $pathLevel =
                            $submission?->level
                            ?? $appointment->level;

                        $pathClass =
                            $submission?->classRoom
                            ?? $appointment->classRoom;

                        $whatsAppNumber = preg_replace(
                            '/\D+/',
                            '',
                            (string) $appointment->phone
                        );

                        $paymentPlan =
                            $isAdmissionAppointment
                                ? $appointment->payment_plan_details
                                : null;

                        $canSendPayment =
                            $isAdmissionAppointment
                            && $appointment->canReceivePaymentInvitation();

                        $paymentUrl =
                            $canSendPayment
                                ? \Illuminate\Support\Facades\URL
                                    ::temporarySignedRoute(
                                        'appointment.payment',
                                        now()->addDays(7),
                                        [
                                            'appointment' =>
                                                $appointment->id,
                                        ]
                                    )
                                : null;

                        $searchValue = mb_strtolower(
                            implode(
                                ' ',
                                [
                                    $appointment->first_name,
                                    $appointment->last_name,
                                    $appointment->email,
                                    $appointment->phone,
                                    $appointment->city,
                                    $appointment->country,
                                    $pathSubject?->name,
                                    $pathLevel?->name,
                                    $pathClass?->name,
                                    $appointment
                                        ->interview_method_label,
                                ]
                            )
                        );
                    @endphp

                    <tr data-search="{{ $searchValue }}">
                        <td>
                            <div class="student-name">
                                <span class="student-avatar">
                                    {{
                                        mb_strtoupper(
                                            mb_substr(
                                                $appointment
                                                    ->first_name,
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </span>

                                <div>
                                    <strong>
                                        {{
                                            $appointment
                                                ->first_name
                                        }}
                                        {{
                                            $appointment
                                                ->last_name
                                        }}
                                    </strong>

                                    <small>
                                        {{ $appointment->email }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <a
                                href="tel:{{
                                    $appointment->phone
                                }}"
                                class="contact-link"
                            >
                                <i class="bi bi-telephone"></i>
                                {{ $appointment->phone }}
                            </a>
                        </td>

                        <td>
                            <strong>
                                {{
                                    $appointment->city
                                    ?: '—'
                                }}
                            </strong>

                            <small class="d-block">
                                {{
                                    $appointment->country
                                    ?: '—'
                                }}
                            </small>
                        </td>

                        <td style="min-width:180px;">
                            @if($isGeneralAppointment)
                                <span class="general-path-label">
                                    <i class="bi bi-globe2"></i>
                                    Rendez-vous général
                                </span>

                                <small class="d-block mt-1">
                                    Sans parcours pédagogique
                                </small>
                            @else
                                <strong>
                                    {{
                                        $pathSubject?->name
                                        ?? '—'
                                    }}
                                </strong>

                                <span class="path-pill">
                                    <i class="bi bi-diagram-3"></i>

                                    {{
                                        $pathLevel?->name
                                        ?? '—'
                                    }}
                                    ·
                                    {{
                                        $pathClass?->name
                                        ?? '—'
                                    }}
                                </span>
                            @endif
                        </td>

                        <td style="min-width:230px;">
                            @if($isDirectInterview)
                                <div class="direct-interview-block">
                                    <span
                                        class="answer-type-badge
                                            interview"
                                    >
                                        <i class="bi bi-headset"></i>
                                        Entretien BAC
                                    </span>

                                    <strong>
                                        {{
                                            $appointment
                                                ->interview_method_label
                                        }}
                                    </strong>

                                    <small>
                                        <i class="bi bi-calendar3"></i>

                                        {{
                                            $appointment
                                                ->preferred_date
                                                ?->format('d/m/Y')
                                            ?? 'Date non précisée'
                                        }}

                                        à

                                        {{
                                            $appointment
                                                ->preferred_time_label
                                        }}
                                    </small>

                                    @if($appointment->notes)
                                        <p
                                            title="{{
                                                $appointment->notes
                                            }}"
                                        >
                                            {{
                                                \Illuminate\Support\Str
                                                    ::limit(
                                                        $appointment
                                                            ->notes,
                                                        80
                                                    )
                                            }}
                                        </p>
                                    @endif

                                    <div
                                        class="direct-interview-actions"
                                    >
                                        @if(
                                            $appointment
                                                ->interview_method
                                            === 'whatsapp'
                                        )
                                            <a
                                                href="https://wa.me/{{
                                                    $whatsAppNumber
                                                }}"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                <i
                                                    class="bi bi-whatsapp"
                                                ></i>
                                                WhatsApp
                                            </a>
                                        @elseif(
                                            $appointment
                                                ->interview_method
                                            === 'phone_call'
                                        )
                                            <a
                                                href="tel:{{
                                                    $appointment->phone
                                                }}"
                                            >
                                                <i
                                                    class="bi bi-telephone"
                                                ></i>
                                                Appeler
                                            </a>
                                        @else
                                            <a
                                                href="mailto:{{
                                                    $appointment->email
                                                }}"
                                            >
                                                <i
                                                    class="bi bi-camera-video"
                                                ></i>
                                                Envoyer le lien
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @elseif($isWritten)
                                <div class="written-answer-block">
                                    <span class="answer-type-badge written">
                                        <i class="bi bi-images"></i>
                                        Test écrit
                                    </span>

                                    <a
                                        href="{{
                                            route(
                                                'admin.written-tests.show',
                                                $written
                                            )
                                        }}"
                                        class="written-review-link"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        Corriger
                                    </a>

                                    <div class="answer-thumbnails">
                                        @foreach(
                                            $written->images()
                                            as $imageIndex => $image
                                        )
                                            <a
                                                href="{{
                                                    route(
                                                        'high-school-test.image',
                                                        [
                                                            $written,
                                                            $imageIndex,
                                                        ]
                                                    )
                                                }}"
                                                target="_blank"
                                                rel="noopener"
                                                title="Ouvrir la réponse {{
                                                    $imageIndex + 1
                                                }}"
                                            >
                                                <img
                                                    src="{{
                                                        route(
                                                            'high-school-test.image',
                                                            [
                                                                $written,
                                                                $imageIndex,
                                                            ]
                                                        )
                                                    }}"
                                                    alt="Réponse {{
                                                        $imageIndex + 1
                                                    }}"
                                                >
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($vocal)
                                <a
                                    href="{{
                                        route(
                                            'admin.vocal-tests.submissions.index'
                                        )
                                    }}"
                                    class="vocal-answer-link"
                                >
                                    <i class="bi bi-mic-fill"></i>
                                    Voir la récitation
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @else
                                <div class="general-appointment-block">
                                    <span
                                        class="answer-type-badge general"
                                    >
                                        <i class="bi {{ $generalTypeIcon }}"></i>
                                        Demande visiteur
                                    </span>

                                    <strong>
                                        {{ $appointment->type_label }}
                                    </strong>

                                    <small>
                                        Demande envoyée depuis le formulaire
                                        public de rendez-vous.
                                    </small>

                                    <div class="general-appointment-actions">
                                        <a
                                            href="mailto:{{ $appointment->email }}"
                                        >
                                            <i class="bi bi-envelope"></i>
                                            E-mail
                                        </a>

                                        <a
                                            href="tel:{{ $appointment->phone }}"
                                        >
                                            <i class="bi bi-telephone"></i>
                                            Appeler
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </td>

                        <td>
                            <span
                                class="status-badge
                                    status-{{
                                        $appointment->status
                                    }}"
                            >
                                {{
                                    $appointment->status
                                    === 'pending'
                                        ? 'En attente'
                                        : (
                                            $appointment->status
                                            === 'confirmed'
                                                ? 'Confirmé'
                                                : 'Annulé'
                                        )
                                }}
                            </span>
                        </td>

                        <td>
                            <small>
                                {{
                                    $appointment
                                        ->created_at
                                        ->format('d/m/Y H:i')
                                }}
                            </small>
                        </td>

                        <td style="min-width:205px;">
                            <div class="appointment-actions">
                                <div class="appointment-actions-main">
                                    @if($appointment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="adm-action-btn adm-action-edit" title="Confirmer">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="adm-action-btn adm-action-danger" title="Annuler">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" onsubmit="return confirm('Supprimer ce rendez-vous et ses fichiers ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="adm-action-btn adm-action-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                @if($isAdmissionAppointment)
                                    <div class="payment-actions">
                                        <div class="payment-plan-label">
                                            <i class="bi bi-credit-card-2-front"></i>
                                            {{ $paymentPlan['name'] }}
                                            <strong>{{ $paymentPlan['amount_display'] }} {{ $paymentPlan['currency_symbol'] }}</strong>
                                        </div>

                                        <button
                                            type="button"
                                            class="payment-action-button payment-copy-button"
                                            data-payment-url="{{ $paymentUrl }}"
                                            {{ !$canSendPayment ? 'disabled' : '' }}
                                            title="{{ $canSendPayment ? 'Copier le lien de paiement' : 'Confirmez d’abord le rendez-vous' }}"
                                        >
                                            <i class="bi bi-link-45deg"></i>
                                            Copier le lien
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.appointments.payment-email', $appointment) }}"
                                            class="payment-email-form"
                                            onsubmit="return confirm('Envoyer le lien de paiement à {{ addslashes($appointment->email) }} ?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="payment-action-button payment-email-button"
                                                {{ !$canSendPayment ? 'disabled' : '' }}
                                                title="{{ $canSendPayment ? 'Envoyer l’e-mail de paiement' : 'Confirmez d’abord le rendez-vous' }}"
                                            >
                                                <i class="bi bi-envelope-arrow-up-fill"></i>
                                                Envoyer l’e-mail
                                            </button>
                                        </form>

                                        @if($appointment->payment_invited_at)
                                            <small class="payment-sent-status">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Envoyé le {{ $appointment->payment_invited_at->format('d/m/Y H:i') }}
                                                @if($appointment->payment_invitation_count > 1)
                                                    · {{ $appointment->payment_invitation_count }} envois
                                                @endif
                                            </small>
                                        @elseif(!$canSendPayment)
                                            <small class="payment-disabled-status">
                                                <i class="bi bi-lock-fill"></i>
                                                Confirmez le rendez-vous avant l’envoi.
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <div class="general-request-note">
                                        <i class="bi bi-info-circle"></i>
                                        Demande générale — aucun paiement associé.
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="adm-empty">
                                <div class="adm-empty-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>

                                <h5>Aucune demande reçue</h5>

                                <p>
                                    Les futurs entretiens et tests
                                    apparaîtront ici.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.appointment-total {
    padding: 8px 11px;
    border: 1px solid rgba(96,165,250,0.13);
    border-radius: 11px;
    color: #93C5FD;
    background: rgba(37,99,235,0.08);
    font-size: 0.7rem;
    font-weight: 750;
}

.appointments-panel {
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 17px;
    background: rgba(255,255,255,0.02);
}

.appointments-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.appointments-count {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,0.7);
    font-size: 0.76rem;
    font-weight: 700;
}

.appointments-search {
    position: relative;
    width: min(100%,310px);
}

.appointments-search i {
    position: absolute;
    top: 50%;
    left: 11px;
    color: rgba(255,255,255,0.25);
    transform: translateY(-50%);
}

.appointments-search input {
    width: 100%;
    height: 39px;
    padding: 7px 10px 7px 34px;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 11px;
    outline: none;
    color: #ffffff;
    background: rgba(255,255,255,0.035);
    font-size: 0.7rem;
}

.student-name {
    min-width: 165px;
    display: flex;
    align-items: center;
    gap: 9px;
}

.student-avatar {
    width: 37px;
    height: 37px;
    flex: 0 0 37px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: #ffffff;
    background:
        linear-gradient(135deg,#7C3AED,#2563EB);
    font-weight: 800;
}

.student-name > div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.student-name small,
td small {
    color: rgba(255,255,255,0.38);
    font-size: 0.62rem;
}

.contact-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,0.62);
    text-decoration: none;
    white-space: nowrap;
}

.path-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
    padding: 4px 7px;
    border-radius: 8px;
    color: rgba(255,255,255,0.48);
    background: rgba(255,255,255,0.04);
    font-size: 0.62rem;
}

.answer-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 7px;
    padding: 5px 8px;
    border-radius: 8px;
    font-size: 0.62rem;
    font-weight: 750;
}

.answer-type-badge.written {
    color: #7DD3FC;
    background: rgba(14,165,233,0.11);
}

.written-review-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 0 0 7px 6px;
    padding: 5px 8px;
    border-radius: 8px;
    color: #C4B5FD;
    background: rgba(124,58,237,0.1);
    font-size: 0.61rem;
    font-weight: 750;
    text-decoration: none;
}

.written-review-link:hover {
    color: #ffffff;
}

.answer-thumbnails {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.answer-thumbnails a {
    width: 42px;
    height: 42px;
    display: block;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
}

.answer-thumbnails img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vocal-answer-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 10px;
    border: 1px solid rgba(167,139,250,0.18);
    border-radius: 10px;
    color: #C4B5FD;
    background: rgba(124,58,237,0.1);
    font-size: 0.68rem;
    font-weight: 750;
    text-decoration: none;
}

/* Rendez-vous généraux envoyés par les visiteurs */
.general-path-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 8px;
    border: 1px solid rgba(96,165,250,.16);
    border-radius: 8px;
    color: #93C5FD;
    background: rgba(37,99,235,.08);
    font-size: .62rem;
    font-weight: 750;
}

.answer-type-badge.general {
    width: fit-content;
    color: #93C5FD;
    background: rgba(37,99,235,.11);
}

.general-appointment-block {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.general-appointment-block > strong {
    color: rgba(255,255,255,.88);
    font-size: .72rem;
}

.general-appointment-block > small {
    max-width: 215px;
    color: rgba(255,255,255,.42);
    font-size: .61rem;
    line-height: 1.45;
}

.general-appointment-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 3px;
}

.general-appointment-actions a {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;
    color: #CBD5E1;
    background: rgba(255,255,255,.04);
    font-size: .6rem;
    font-weight: 700;
    text-decoration: none;
}

.general-appointment-actions a:hover {
    color: #fff;
    border-color: rgba(96,165,250,.26);
    background: rgba(59,130,246,.09);
}

.general-request-note {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    padding-top: 8px;
    border-top: 1px solid rgba(255,255,255,.06);
    color: rgba(255,255,255,.38);
    font-size: .59rem;
    line-height: 1.4;
}

.general-request-note i {
    margin-top: 1px;
    color: #60A5FA;
}

.status-badge {
    display: inline-flex;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: 0.61rem;
    font-weight: 750;
}

.status-pending {
    color: #FBBF24;
    background: rgba(245,158,11,0.11);
}

.status-confirmed {
    color: #4ADE80;
    background: rgba(34,197,94,0.11);
}

.status-cancelled {
    color: #FCA5A5;
    background: rgba(239,68,68,0.11);
}

.appointment-actions {
    min-width: 190px;
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.appointment-actions-main {
    display: flex;
    align-items: center;
    gap: 5px;
}

.payment-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding-top: 8px;
    border-top: 1px solid rgba(255,255,255,0.06);
}

.payment-plan-label {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,0.48);
    font-size: 0.6rem;
}

.payment-plan-label i {
    color: #60A5FA;
}

.payment-plan-label strong {
    margin-left: auto;
    color: #FCD34D;
    font-size: 0.62rem;
}

.payment-action-button {
    width: 100%;
    min-height: 31px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 8px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 0.61rem;
    font-weight: 780;
    cursor: pointer;
    transition: transform 0.2s ease, filter 0.2s ease, opacity 0.2s ease;
}

.payment-action-button:not(:disabled):hover {
    transform: translateY(-1px);
    filter: brightness(1.08);
}

.payment-action-button:disabled {
    cursor: not-allowed;
    opacity: 0.38;
}

.payment-copy-button {
    border-color: rgba(14,165,233,0.2);
    color: #7DD3FC;
    background: rgba(14,165,233,0.09);
}

.payment-email-button {
    color: #ffffff;
    background: linear-gradient(135deg,#2563EB,#7C3AED);
}

.payment-email-form {
    margin: 0;
}

.payment-sent-status,
.payment-disabled-status {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    font-size: 0.56rem;
    line-height: 1.45;
}

.payment-sent-status {
    color: #86EFAC;
}

.payment-disabled-status {
    color: rgba(255,255,255,0.32);
}

.payment-copy-feedback {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 11px 14px;
    border: 1px solid rgba(34,197,94,0.23);
    border-radius: 11px;
    color: #DCFCE7;
    background: rgba(20,83,45,0.96);
    box-shadow: 0 16px 40px rgba(0,0,0,0.3);
    font-size: 0.7rem;
    font-weight: 750;
}

@media (max-width:760px) {
    .appointments-toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .appointments-search {
        width: 100%;
    }
}

.direct-interview-block {
    min-width: 205px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.answer-type-badge.interview {
    width: fit-content;
    color: #C4B5FD;
    background: rgba(124,58,237,.12);
}

.direct-interview-block > strong {
    color: rgba(255,255,255,.82);
    font-size: .72rem;
}

.direct-interview-block > small {
    color: rgba(255,255,255,.42);
    font-size: .62rem;
}

.direct-interview-block > p {
    max-width: 240px;
    margin: 0;
    color: rgba(255,255,255,.38);
    font-size: .59rem;
    line-height: 1.45;
}

.direct-interview-actions {
    display: flex;
    gap: 6px;
    margin-top: 2px;
}

.direct-interview-actions a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 9px;
    border: 1px solid rgba(96,165,250,.14);
    border-radius: 8px;
    color: #93C5FD;
    background: rgba(37,99,235,.07);
    font-size: .59rem;
    font-weight: 750;
    text-decoration: none;
}

.direct-interview-actions a:hover {
    color: #fff;
    background: rgba(37,99,235,.14);
}

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search =
        document.getElementById(
            'appointmentsSearch'
        );

    const rows =
        Array.from(
            document.querySelectorAll(
                '#appointmentsBody tr[data-search]'
            )
        );

    search?.addEventListener('input', () => {
        const term =
            search.value
                .trim()
                .toLocaleLowerCase();

        rows.forEach(row => {
            row.style.display =
                !term
                || row.dataset.search.includes(term)
                    ? ''
                    : 'none';
        });
    });

    const copyButtons = document.querySelectorAll(
        '.payment-copy-button'
    );

    const showCopyFeedback = message => {
        document.querySelector('.payment-copy-feedback')?.remove();

        const feedback = document.createElement('div');
        feedback.className = 'payment-copy-feedback';
        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i>' + message;
        document.body.appendChild(feedback);

        window.setTimeout(() => {
            feedback.remove();
        }, 2600);
    };

    const fallbackCopy = value => {
        const input = document.createElement('textarea');
        input.value = value;
        input.setAttribute('readonly', 'readonly');
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        const copied = document.execCommand('copy');
        input.remove();
        return copied;
    };

    copyButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const url = button.dataset.paymentUrl;

            if (!url) {
                return;
            }

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(url);
                } else if (!fallbackCopy(url)) {
                    throw new Error('Copie impossible');
                }

                showCopyFeedback('Lien de paiement copié.');
            } catch (error) {
                window.prompt('Copiez le lien de paiement :', url);
            }
        });
    });
});
</script>

@endsection
