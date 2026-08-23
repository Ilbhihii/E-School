@extends('layouts.admin')

@section('title', 'Rendez-vous — Administration')
@section('page_title', 'Rendez-vous')
@section('breadcrumb', 'Demandes reçues')

@section('content')

@php
    $generalCount = $appointments->filter(function ($appointment) {
        $hasSubmission =
            (bool) $appointment->vocalSubmission
            || (bool) $appointment->highSchoolTestSubmission;

        $isDirectInterview =
            !$hasSubmission
            && !empty($appointment->subject_id);

        $isAdmission =
            $appointment->type
            === \App\Models\TestAppointment::TYPE_TEST;

        return !$isAdmission
            && !$hasSubmission
            && !$isDirectInterview;
    })->count();

    $admissionCount = $appointments->count() - $generalCount;
    $pendingCount = $appointments->where('status', 'pending')->count();
    $confirmedCount = $appointments->where('status', 'confirmed')->count();
@endphp

<div class="adm-page-header appointments-page-heading">
    <div>
        <h1>
            <i class="bi bi-calendar-check" style="color:#60A5FA;"></i>
            Rendez-vous et entretiens
        </h1>

        <div class="subtitle">
            Gérez dans une seule interface les rendez-vous visiteurs,
            les entretiens et les tests d’admission.
        </div>
    </div>

    <span class="appointment-total">
        <i class="bi bi-inbox-fill"></i>
        {{ $appointments->count() }} demande(s)
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

<div class="appointments-panel appointments-panel-redesign">
    <div class="appointments-toolbar appointments-toolbar-redesign">
        <div class="appointment-filters" role="group" aria-label="Filtrer les rendez-vous">
            <button
                type="button"
                class="appointment-filter is-active"
                data-filter="all"
            >
                <i class="bi bi-grid-fill"></i>
                Tous
                <span>{{ $appointments->count() }}</span>
            </button>

            <button
                type="button"
                class="appointment-filter"
                data-filter="general"
            >
                <i class="bi bi-person-lines-fill"></i>
                Visiteurs
                <span>{{ $generalCount }}</span>
            </button>

            <button
                type="button"
                class="appointment-filter"
                data-filter="admission"
            >
                <i class="bi bi-mortarboard-fill"></i>
                Tests / admissions
                <span>{{ $admissionCount }}</span>
            </button>

            <button
                type="button"
                class="appointment-filter"
                data-filter="pending"
            >
                <i class="bi bi-hourglass-split"></i>
                En attente
                <span>{{ $pendingCount }}</span>
            </button>

            <button
                type="button"
                class="appointment-filter"
                data-filter="confirmed"
            >
                <i class="bi bi-check-circle-fill"></i>
                Confirmés
                <span>{{ $confirmedCount }}</span>
            </button>
        </div>

        <label class="appointments-search appointments-search-redesign">
            <i class="bi bi-search"></i>

            <input
                type="search"
                id="appointmentsSearch"
                placeholder="Nom, e-mail, téléphone, ville..."
                autocomplete="off"
            >
        </label>
    </div>

    <div class="appointments-result-bar">
        <div>
            <i class="bi bi-inbox"></i>
            <strong id="appointmentsVisibleCount">
                {{ $appointments->count() }}
            </strong>
            demande(s) affichée(s)
        </div>

        <button
            type="button"
            class="appointments-reset"
            id="appointmentsReset"
            hidden
        >
            <i class="bi bi-arrow-counterclockwise"></i>
            Réinitialiser
        </button>
    </div>

    <div class="appointments-table-wrap">
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>Personne</th>
                    <th>Demande</th>
                    <th>Parcours</th>
                    <th>Offre choisie</th>
                    <th>Entretien</th>
                    <th>Contact</th>
                    <th>Créneau demandé</th>
                    <th>Statut</th>
                    <th>Reçue le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody id="appointmentsBody">
                @forelse($appointments as $appointment)
                    @php
                        $vocal = $appointment->vocalSubmission;
                        $written = $appointment->highSchoolTestSubmission;
                        $submission = $vocal ?? $written;

                        $isWritten = (bool) $written;

                        $isDirectInterview =
                            !$submission
                            && !empty($appointment->subject_id);

                        $isAdmissionAppointment =
                            $appointment->type
                            === \App\Models\TestAppointment::TYPE_TEST;

                        $isGeneralAppointment =
                            !$isAdmissionAppointment
                            && !$submission
                            && !$isDirectInterview;

                        $category =
                            $isGeneralAppointment
                                ? 'general'
                                : 'admission';

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

                        $admissionMode = strtolower(
                            trim(
                                (string) (
                                    $appointment->admission_mode
                                    ?: ($pathClass?->admission_mode ?? '')
                                )
                            )
                        );

                        if ($vocal) {
                            $admissionMode = 'vocal_test';
                        } elseif ($written) {
                            $admissionMode = 'test';
                        }

                        $admissionModeLabels = [
                            'contact' => 'Prise en contact',
                            'vocal_test' => 'Test vocal',
                            'test' => 'Test d’admission',
                        ];

                        $admissionModeLabel =
                            $admissionModeLabels[$admissionMode]
                            ?? 'Parcours sélectionné';

                        $admissionModeIcons = [
                            'contact' => 'bi-calendar2-check-fill',
                            'vocal_test' => 'bi-mic-fill',
                            'test' => 'bi-pencil-square',
                        ];

                        $admissionModeIcon =
                            $admissionModeIcons[$admissionMode]
                            ?? 'bi-diagram-3-fill';

                        $whatsAppNumber = preg_replace(
                            '/\D+/',
                            '',
                            (string) $appointment->phone
                        );

                        /*
                         * Ne jamais inventer une offre pour l'affichage.
                         * Ici on montre uniquement celle réellement enregistrée
                         * sur la demande avant sa confirmation.
                         */
                        $hasSelectedPaymentPlan =
                            trim((string) $appointment->payment_plan) !== '';

                        $paymentPlan =
                            $hasSelectedPaymentPlan
                                ? $appointment->payment_plan_details
                                : null;

                        $canSendPayment =
                            (bool) $paymentPlan
                            && $appointment->canReceivePaymentInvitation();

                        $paymentUrl =
                            $canSendPayment
                                ? \Illuminate\Support\Facades\URL::temporarySignedRoute(
                                    'appointment.payment',
                                    now()->addDays(7),
                                    ['appointment' => $appointment->id]
                                )
                                : null;

                        $statusLabels = [
                            'pending' => 'En attente',
                            'confirmed' => 'Confirmé',
                            'cancelled' => 'Annulé',
                        ];

                        $statusLabel =
                            $statusLabels[$appointment->status]
                            ?? ucfirst((string) $appointment->status);

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
                                    $appointment->type_label,
                                    $pathSubject?->name,
                                    $pathLevel?->name,
                                    $pathClass?->name,
                                    $appointment->interview_method_label,
                                    $admissionModeLabel,
                                    $paymentPlan['name'] ?? null,
                                    $paymentPlan['duration_label'] ?? null,
                                    $paymentPlan['amount_display'] ?? null,
                                    $appointment->preferred_date?->format('d/m/Y'),
                                    $appointment->preferred_time_label,
                                    $statusLabel,
                                ]
                            )
                        );

                        $initial = mb_strtoupper(
                            mb_substr(
                                trim((string) $appointment->first_name),
                                0,
                                1
                            )
                        ) ?: '?';
                    @endphp

                    <tr
                        data-search="{{ $searchValue }}"
                        data-category="{{ $category }}"
                        data-status="{{ $appointment->status }}"
                    >
                        {{-- PERSONNE --}}
                        <td class="appointment-person-cell">
                            <div class="appointment-person">
                                <span class="appointment-avatar">
                                    {{ $initial }}
                                </span>

                                <div class="appointment-person-copy">
                                    <strong>
                                        {{ $appointment->first_name }}
                                        {{ $appointment->last_name }}
                                    </strong>

                                    <a href="mailto:{{ $appointment->email }}">
                                        {{ $appointment->email }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        {{-- DEMANDE --}}
                        <td class="appointment-request-cell">
                            @if($isGeneralAppointment)
                                <div class="appointment-request">
                                    <span class="request-type request-type-general">
                                        <i class="bi {{ $generalTypeIcon }}"></i>
                                        Rendez-vous visiteur
                                    </span>

                                    <strong>
                                        {{ $appointment->type_label }}
                                    </strong>

                                    <small>
                                        Demande envoyée depuis le formulaire public.
                                    </small>
                                </div>
                            @elseif($isDirectInterview)
                                <div class="appointment-request">
                                    <span class="request-type {{ $admissionMode === 'contact' ? 'request-type-contact' : 'request-type-interview' }}">
                                        <i class="bi {{ $admissionModeIcon }}"></i>
                                        {{ $admissionModeLabel }}
                                    </span>

                                    <strong>
                                        {{ $appointment->type_label }}
                                    </strong>

                                    <small>
                                        Demande liée à l’offre / matière sélectionnée.
                                    </small>
                                </div>
                            @elseif($isWritten)
                                <div class="appointment-request">
                                    <span class="request-type request-type-written">
                                        <i class="bi bi-pencil-square"></i>
                                        Test écrit
                                    </span>

                                    <strong>Test d’admission</strong>

                                    <a
                                        href="{{ route('admin.written-tests.show', $written) }}"
                                        class="request-review-link"
                                    >
                                        Corriger le test
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            @elseif($vocal)
                                <div class="appointment-request">
                                    <span class="request-type request-type-vocal">
                                        <i class="bi bi-mic-fill"></i>
                                        Test vocal
                                    </span>

                                    <strong>Récitation vocale</strong>

                                    <a
                                        href="{{ route('admin.vocal-tests.submissions.index') }}"
                                        class="request-review-link request-review-link-vocal"
                                    >
                                        Voir la récitation
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            @else
                                <div class="appointment-request">
                                    <span class="request-type request-type-admission">
                                        <i class="bi bi-calendar2-check-fill"></i>
                                        Rendez-vous pour passer le test
                                    </span>

                                    <strong>
                                        {{ $appointment->type_label }}
                                    </strong>

                                    <small>
                                        Rendez-vous de test reçu.
                                        @if($appointment->preferred_date)
                                            ·
                                            {{
                                                ucfirst(
                                                    $appointment
                                                        ->preferred_date
                                                        ->locale('fr')
                                                        ->isoFormat(
                                                            'dddd D MMMM YYYY'
                                                        )
                                                )
                                            }}
                                        @endif

                                        @if($appointment->preferred_time)
                                            ·
                                            {{ $appointment->preferred_time_label }}
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </td>

                        {{-- PARCOURS --}}
                        <td class="appointment-path-cell">
                            @if($isGeneralAppointment)
                                <div class="appointment-empty-path">
                                    <span>
                                        <i class="bi bi-dash-circle"></i>
                                        Sans parcours
                                    </span>
                                    <small>Rendez-vous général</small>
                                </div>
                            @else
                                <div class="appointment-path">
                                    <strong>
                                        {{ $pathSubject?->name ?? 'Parcours non renseigné' }}
                                    </strong>

                                    @if($pathLevel || $pathClass)
                                        <span>
                                            <i class="bi bi-diagram-3"></i>
                                            {{ $pathLevel?->name ?? '—' }}
                                            <i class="bi bi-chevron-right"></i>
                                            {{ $pathClass?->name ?? '—' }}
                                        </span>

                                        @if($admissionMode)
                                            <span class="appointment-admission-mode appointment-admission-mode-{{ $admissionMode }}">
                                                <i class="bi {{ $admissionModeIcon }}"></i>
                                                {{ $admissionModeLabel }}
                                            </span>
                                        @endif
                                    @else
                                        <small>Aucun niveau / classe renseigné</small>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- OFFRE COMMERCIALE CHOISIE --}}
                        <td class="appointment-selected-plan-cell">
                            @if($paymentPlan)
                                <div class="appointment-selected-plan">
                                    <span class="appointment-selected-plan-name">
                                        <i class="bi bi-bag-check-fill"></i>
                                        {{ $paymentPlan['name'] ?? 'Offre' }}
                                    </span>

                                    @if(!empty($paymentPlan['is_family_pack']))
                                        <small class="appointment-family-badge">
                                            <i class="bi bi-people-fill"></i>
                                            Family Pack
                                            @if(!empty($paymentPlan['family_members']))
                                                · {{ $paymentPlan['family_members'] }} pers.
                                            @endif
                                        </small>
                                    @endif

                                    <strong>
                                        {{ $paymentPlan['amount_display'] ?? '—' }}
                                        {{ $paymentPlan['currency_symbol'] ?? '' }}
                                    </strong>

                                    <span>
                                        <i class="bi bi-calendar3"></i>
                                        {{ $paymentPlan['duration_label'] ?? ($paymentPlan['period'] ?? 'Durée non précisée') }}
                                    </span>

                                    @if($appointment->status === 'pending')
                                        <small class="selected-plan-before-confirmation">
                                            <i class="bi bi-eye-fill"></i>
                                            Choisie avant confirmation
                                        </small>
                                    @endif
                                </div>
                            @else
                                <div class="appointment-empty-plan">
                                    <span>
                                        <i class="bi bi-dash-circle"></i>
                                        Non renseignée
                                    </span>
                                    <small>Ancienne demande ou rendez-vous général</small>
                                </div>
                            @endif
                        </td>

                        {{-- ENTRETIEN --}}
                        <td class="appointment-interview-cell">
                            @if($appointment->interview_method)
                                <span class="interview-method-pill interview-method-{{ $appointment->interview_method }}">
                                    @if($appointment->interview_method === 'whatsapp')
                                        <i class="bi bi-whatsapp"></i>
                                    @elseif($appointment->interview_method === 'phone_call')
                                        <i class="bi bi-telephone-fill"></i>
                                    @else
                                        <i class="bi bi-camera-video-fill"></i>
                                    @endif

                                    {{ $appointment->interview_method_label }}
                                </span>

                                <div class="interview-quick-actions">
                                    @if($appointment->interview_method === 'whatsapp')
                                        <a href="https://wa.me/{{ $whatsAppNumber }}" target="_blank" rel="noopener">
                                            Contacter
                                        </a>
                                    @elseif($appointment->interview_method === 'phone_call')
                                        <a href="tel:{{ $appointment->phone }}">Appeler</a>
                                    @else
                                        <a href="mailto:{{ $appointment->email }}">Envoyer le lien</a>
                                    @endif
                                </div>
                            @elseif($vocal || $written)
                                <span class="interview-method-empty">
                                    <i class="bi bi-hourglass-split"></i>
                                    À définir après le test
                                </span>
                            @else
                                <span class="interview-method-empty">
                                    <i class="bi bi-dash-circle"></i>
                                    Non précisé
                                </span>
                            @endif
                        </td>

                        {{-- CONTACT --}}
                        <td class="appointment-contact-cell">
                            <div class="appointment-contact">
                                <a href="tel:{{ $appointment->phone }}">
                                    <i class="bi bi-telephone"></i>
                                    {{ $appointment->phone }}
                                </a>

                                <span>
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $appointment->city ?: '—' }}
                                    @if($appointment->country)
                                        · {{ $appointment->country }}
                                    @endif
                                </span>

                                <div class="appointment-contact-actions">
                                    <a
                                        href="mailto:{{ $appointment->email }}"
                                        title="Envoyer un e-mail"
                                        aria-label="Envoyer un e-mail"
                                    >
                                        <i class="bi bi-envelope"></i>
                                    </a>

                                    <a
                                        href="tel:{{ $appointment->phone }}"
                                        title="Appeler"
                                        aria-label="Appeler"
                                    >
                                        <i class="bi bi-telephone-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </td>

                        {{-- CRÉNEAU DEMANDÉ --}}
                        <td class="appointment-slot-cell">
                            @if($appointment->preferred_date || $appointment->preferred_time)
                                <strong>
                                    @if($appointment->preferred_date)
                                        {{ $appointment->preferred_date->format('d/m/Y') }}
                                    @else
                                        Date à confirmer
                                    @endif
                                </strong>

                                <span>
                                    <i class="bi bi-clock"></i>
                                    {{ $appointment->preferred_time_label }}
                                </span>
                            @else
                                <span class="appointment-slot-empty">
                                    <i class="bi bi-calendar2"></i>
                                    À confirmer
                                </span>
                            @endif

                            @if($appointment->notes)
                                <small title="{{ $appointment->notes }}">
                                    <i class="bi bi-chat-left-text"></i>
                                    Message joint
                                </small>
                            @endif
                        </td>

                        {{-- STATUT --}}
                        <td>
                            <span class="status-badge status-{{ $appointment->status }}">
                                @if($appointment->status === 'pending')
                                    <i class="bi bi-clock-fill"></i>
                                @elseif($appointment->status === 'confirmed')
                                    <i class="bi bi-check-circle-fill"></i>
                                @else
                                    <i class="bi bi-x-circle-fill"></i>
                                @endif

                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- DATE DE RÉCEPTION --}}
                        <td class="appointment-date-cell">
                            <strong>
                                {{ $appointment->created_at->format('d/m/Y') }}
                            </strong>

                            <span>
                                {{ $appointment->created_at->format('H:i') }}
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="appointment-actions-cell">
                            <div class="appointment-actions-compact">
                                <div class="appointment-primary-actions">
                                    @if($appointment->status === 'pending')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.appointments.confirm', $appointment) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="appointment-icon-btn appointment-confirm-btn"
                                                title="Confirmer"
                                                aria-label="Confirmer"
                                            >
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.appointments.cancel', $appointment) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="appointment-icon-btn appointment-cancel-btn"
                                                title="Annuler"
                                                aria-label="Annuler"
                                            >
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form
                                        method="POST"
                                        action="{{ route('admin.appointments.destroy', $appointment) }}"
                                        onsubmit="return confirm('Supprimer ce rendez-vous et ses fichiers ?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="appointment-icon-btn appointment-delete-btn"
                                            title="Supprimer"
                                            aria-label="Supprimer"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                @if($paymentPlan)
                                    <div class="appointment-payment-compact">
                                        <div class="payment-mini-plan">
                                            <i class="bi bi-credit-card-2-front"></i>
                                            <span>{{ $paymentPlan['name'] ?? 'Paiement' }}</span>
                                            <strong>
                                                {{ $paymentPlan['amount_display'] ?? '' }}
                                                {{ $paymentPlan['currency_symbol'] ?? '' }}
                                            </strong>
                                            <small>
                                                {{ $paymentPlan['duration_label'] ?? '' }}
                                            </small>
                                        </div>

                                        <div class="payment-mini-actions">
                                            <button
                                                type="button"
                                                class="appointment-payment-btn payment-copy-button"
                                                data-payment-url="{{ $paymentUrl }}"
                                                {{ !$canSendPayment ? 'disabled' : '' }}
                                                title="{{ $canSendPayment ? 'Copier le lien de paiement' : 'Confirmez d’abord le rendez-vous' }}"
                                            >
                                                <i class="bi bi-link-45deg"></i>
                                                Lien
                                            </button>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.appointments.payment-email', $appointment) }}"
                                                onsubmit="return confirm('Envoyer le lien de paiement à {{ addslashes($appointment->email) }} ?')"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="appointment-payment-btn appointment-payment-email"
                                                    {{ !$canSendPayment ? 'disabled' : '' }}
                                                    title="{{ $canSendPayment ? 'Envoyer l’e-mail de paiement' : 'Confirmez d’abord le rendez-vous' }}"
                                                >
                                                    <i class="bi bi-envelope-arrow-up"></i>
                                                    E-mail
                                                </button>
                                            </form>
                                        </div>

                                        @if($appointment->payment_invited_at)
                                            <small class="payment-mini-status payment-mini-status-sent">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Envoyé le
                                                {{ $appointment->payment_invited_at->format('d/m/Y H:i') }}
                                            </small>
                                        @elseif(!$canSendPayment)
                                            <small class="payment-mini-status">
                                                <i class="bi bi-lock-fill"></i>
                                                Disponible après confirmation
                                            </small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="appointments-empty-row">
                        <td colspan="10">
                            <div class="adm-empty">
                                <div class="adm-empty-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>

                                <h5>Aucune demande reçue</h5>

                                <p>
                                    Les futurs rendez-vous et tests
                                    apparaîtront ici.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse

                <tr
                    id="appointmentsNoResult"
                    class="appointments-empty-row"
                    hidden
                >
                    <td colspan="9">
                        <div class="appointments-no-result">
                            <i class="bi bi-search"></i>
                            <strong>Aucun résultat</strong>
                            <span>
                                Modifiez votre recherche ou choisissez un autre filtre.
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
/* =========================================================
   Rendez-vous — organisation compacte
   ========================================================= */

.appointments-page-heading {
    margin-bottom: 20px;
}

.appointment-total {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 12px;
    border: 1px solid rgba(96,165,250,.16);
    border-radius: 11px;
    color: #bfdbfe;
    background: rgba(37,99,235,.09);
    font-size: .7rem;
    font-weight: 800;
    white-space: nowrap;
}

.appointments-panel-redesign {
    overflow: hidden;
    border: 1px solid rgba(148,163,184,.13);
    border-radius: 18px;
    background:
        linear-gradient(
            180deg,
            rgba(20,31,51,.88),
            rgba(12,23,41,.92)
        );
    box-shadow: 0 18px 55px rgba(0,0,0,.12);
}

.appointments-toolbar-redesign {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 17px 18px;
    border-bottom: 1px solid rgba(148,163,184,.1);
}

.appointment-filters {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
}

.appointment-filter {
    display: inline-flex;
    min-height: 35px;
    align-items: center;
    gap: 7px;
    padding: 7px 10px;
    border: 1px solid rgba(148,163,184,.11);
    border-radius: 10px;
    color: #94a3b8;
    background: rgba(255,255,255,.025);
    font-size: .66rem;
    font-weight: 760;
    cursor: pointer;
    transition:
        color .18s ease,
        border-color .18s ease,
        background .18s ease,
        transform .18s ease;
}

.appointment-filter > span {
    min-width: 19px;
    padding: 2px 5px;
    border-radius: 999px;
    color: #cbd5e1;
    background: rgba(255,255,255,.07);
    text-align: center;
    font-size: .58rem;
}

.appointment-filter:hover {
    color: #e2e8f0;
    border-color: rgba(96,165,250,.2);
    background: rgba(59,130,246,.06);
}

.appointment-filter.is-active {
    color: #dbeafe;
    border-color: rgba(96,165,250,.34);
    background: rgba(37,99,235,.14);
    box-shadow: inset 0 0 0 1px rgba(59,130,246,.05);
}

.appointment-filter.is-active > span {
    color: #fff;
    background: rgba(59,130,246,.28);
}

.appointments-search-redesign {
    position: relative;
    width: min(100%, 305px);
    flex: 0 0 min(100%, 305px);
}

.appointments-search-redesign > i {
    position: absolute;
    top: 50%;
    left: 12px;
    z-index: 1;
    color: #64748b;
    transform: translateY(-50%);
}

.appointments-search-redesign input {
    width: 100%;
    height: 39px;
    padding: 7px 12px 7px 36px;
    border: 1px solid rgba(148,163,184,.11);
    border-radius: 11px;
    outline: none;
    color: #e2e8f0;
    background: rgba(2,6,23,.22);
    font-size: .66rem;
    transition:
        border-color .18s ease,
        box-shadow .18s ease;
}

.appointments-search-redesign input:focus {
    border-color: rgba(96,165,250,.34);
    box-shadow: 0 0 0 3px rgba(59,130,246,.08);
}

.appointments-search-redesign input::placeholder {
    color: #64748b;
}

.appointments-result-bar {
    display: flex;
    min-height: 40px;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 18px;
    color: #64748b;
    border-bottom: 1px solid rgba(148,163,184,.08);
    background: rgba(2,6,23,.14);
    font-size: .62rem;
}

.appointments-result-bar > div {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.appointments-result-bar strong {
    color: #cbd5e1;
}

.appointments-reset {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 7px;
    border: 0;
    color: #93c5fd;
    background: transparent;
    font-size: .6rem;
    font-weight: 750;
    cursor: pointer;
}

.appointments-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.appointments-table {
    width: 100%;
    min-width: 1480px;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
}

.appointments-table th {
    padding: 12px 14px;
    color: #718096;
    border-bottom: 1px solid rgba(148,163,184,.11);
    background: rgba(2,6,23,.24);
    font-size: .6rem;
    font-weight: 850;
    letter-spacing: .1em;
    text-align: left;
    text-transform: uppercase;
}

.appointments-table th:nth-child(1) { width: 11%; }
.appointments-table th:nth-child(2) { width: 11%; }
.appointments-table th:nth-child(3) { width: 11%; }
.appointments-table th:nth-child(4) { width: 11%; }
.appointments-table th:nth-child(5) { width: 10%; }
.appointments-table th:nth-child(6) { width: 10%; }
.appointments-table th:nth-child(7) { width: 9%; }
.appointments-table th:nth-child(8) { width: 8%; }
.appointments-table th:nth-child(9) { width: 8%; }
.appointments-table th:nth-child(10) { width: 11%; }

.appointments-table td {
    padding: 15px 14px;
    color: #cbd5e1;
    border-bottom: 1px solid rgba(148,163,184,.08);
    vertical-align: middle;
    font-size: .67rem;
}

.appointments-table tbody tr[data-search] {
    transition:
        background .18s ease,
        box-shadow .18s ease;
}

.appointments-table tbody tr[data-search]:hover {
    background: rgba(59,130,246,.035);
    box-shadow: inset 3px 0 0 rgba(96,165,250,.35);
}

.appointments-table tbody tr[data-search]:last-of-type td {
    border-bottom: 0;
}

.appointment-person {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 10px;
}

.appointment-avatar {
    display: grid;
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    place-items: center;
    color: #fff;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 11px;
    background: linear-gradient(135deg,#2563eb,#7c3aed);
    box-shadow: 0 7px 20px rgba(37,99,235,.17);
    font-size: .75rem;
    font-weight: 850;
}

.appointment-person-copy {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 3px;
}

.appointment-person-copy strong {
    overflow: hidden;
    color: #f1f5f9;
    font-size: .71rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.appointment-person-copy a {
    overflow: hidden;
    color: #64748b;
    font-size: .58rem;
    text-decoration: none;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.appointment-request {
    display: flex;
    min-width: 0;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.request-type {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 7px;
    border-radius: 8px;
    font-size: .57rem;
    font-weight: 800;
    white-space: nowrap;
}

.request-type-general {
    color: #93c5fd;
    background: rgba(37,99,235,.12);
}

.request-type-interview {
    color: #c4b5fd;
    background: rgba(124,58,237,.12);
}

.request-type-written {
    color: #7dd3fc;
    background: rgba(14,165,233,.11);
}

.request-type-vocal {
    color: #d8b4fe;
    background: rgba(147,51,234,.11);
}

.request-type-admission {
    color: #fde68a;
    background: rgba(245,158,11,.1);
}

.appointment-request > strong {
    color: #e2e8f0;
    font-size: .68rem;
}

.appointment-request > small {
    max-width: 220px;
    color: #64748b;
    font-size: .57rem;
    line-height: 1.4;
}

.request-review-link,
.request-inline-actions a {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 1px;
    padding: 5px 7px;
    color: #bae6fd;
    border: 1px solid rgba(14,165,233,.15);
    border-radius: 8px;
    background: rgba(14,165,233,.06);
    font-size: .57rem;
    font-weight: 760;
    text-decoration: none;
}

.request-review-link-vocal {
    color: #ddd6fe;
    border-color: rgba(139,92,246,.17);
    background: rgba(124,58,237,.07);
}

.request-inline-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.request-inline-actions a {
    color: #c4b5fd;
    border-color: rgba(139,92,246,.16);
    background: rgba(124,58,237,.06);
}

.appointment-path,
.appointment-empty-path {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.appointment-path > strong {
    color: #e2e8f0;
    font-size: .68rem;
}

.appointment-path > span,
.appointment-empty-path > span {
    display: inline-flex;
    width: fit-content;
    align-items: center;
    gap: 4px;
    padding: 4px 6px;
    color: #94a3b8;
    border-radius: 7px;
    background: rgba(255,255,255,.035);
    font-size: .56rem;
}

.appointment-path > span .bi-chevron-right {
    font-size: .48rem;
}

.appointment-path small,
.appointment-empty-path small {
    color: #64748b;
    font-size: .56rem;
}

.appointment-empty-path > span {
    color: #64748b;
}



.appointment-selected-plan,
.appointment-empty-plan {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.appointment-selected-plan-name {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #c4b5fd;
    font-size: .62rem;
    font-weight: 800;
}

.appointment-selected-plan > strong {
    color: #f8fafc;
    font-size: .74rem;
    font-weight: 900;
}

.appointment-selected-plan > span:not(.appointment-selected-plan-name) {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #94a3b8;
    font-size: .56rem;
}

.appointment-family-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 6px;
    border-radius: 999px;
    color: #fcd34d;
    background: rgba(245,158,11,.1);
    font-size: .52rem;
    font-weight: 800;
}

.selected-plan-before-confirmation {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #4ade80;
    font-size: .51rem;
    font-weight: 700;
}

.appointment-empty-plan > span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #64748b;
    font-size: .57rem;
}

.appointment-empty-plan > small {
    max-width: 130px;
    color: #475569;
    font-size: .5rem;
    line-height: 1.35;
}

.appointment-contact {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.appointment-contact > a,
.appointment-contact > span {
    display: inline-flex;
    min-width: 0;
    align-items: center;
    gap: 6px;
    color: #94a3b8;
    font-size: .59rem;
    text-decoration: none;
}

.appointment-contact > a {
    color: #cbd5e1;
}

.appointment-contact-actions {
    display: flex;
    gap: 5px;
    margin-top: 2px;
}

.appointment-contact-actions a {
    display: grid;
    width: 27px;
    height: 27px;
    place-items: center;
    color: #94a3b8;
    border: 1px solid rgba(148,163,184,.1);
    border-radius: 7px;
    background: rgba(255,255,255,.025);
    text-decoration: none;
    transition: .18s ease;
}

.appointment-contact-actions a:hover {
    color: #fff;
    border-color: rgba(96,165,250,.25);
    background: rgba(59,130,246,.08);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: .58rem;
    font-weight: 800;
    white-space: nowrap;
}

.status-pending {
    color: #fbbf24;
    background: rgba(245,158,11,.11);
}

.status-confirmed {
    color: #4ade80;
    background: rgba(34,197,94,.11);
}

.status-cancelled {
    color: #fca5a5;
    background: rgba(239,68,68,.11);
}

.appointment-date-cell {
    white-space: nowrap;
}

.appointment-date-cell strong,
.appointment-date-cell span {
    display: block;
}

.appointment-date-cell strong {
    color: #cbd5e1;
    font-size: .62rem;
    font-weight: 700;
}

.appointment-date-cell span {
    margin-top: 3px;
    color: #64748b;
    font-size: .57rem;
}

.appointment-actions-compact {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.appointment-primary-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
}

.appointment-primary-actions form,
.payment-mini-actions form {
    margin: 0;
}

.appointment-icon-btn {
    display: grid;
    width: 34px;
    height: 34px;
    place-items: center;
    border: 1px solid transparent;
    border-radius: 9px;
    font-size: .72rem;
    cursor: pointer;
    transition:
        transform .18s ease,
        filter .18s ease,
        border-color .18s ease;
}

.appointment-icon-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.08);
}

.appointment-confirm-btn {
    color: #86efac;
    border-color: rgba(34,197,94,.14);
    background: rgba(34,197,94,.09);
}

.appointment-cancel-btn {
    color: #fda4af;
    border-color: rgba(244,63,94,.14);
    background: rgba(244,63,94,.08);
}

.appointment-delete-btn {
    color: #fca5a5;
    border-color: rgba(239,68,68,.15);
    background: rgba(239,68,68,.09);
}

.appointment-payment-compact {
    display: flex;
    width: 100%;
    max-width: 175px;
    flex-direction: column;
    gap: 5px;
    padding-top: 7px;
    border-top: 1px solid rgba(148,163,184,.08);
}

.payment-mini-plan {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #64748b;
    font-size: .53rem;
}

.payment-mini-plan i {
    color: #60a5fa;
}

.payment-mini-plan small {
    color: #64748b;
    font-size: .5rem;
    white-space: nowrap;
}

.payment-mini-plan strong {
    margin-left: auto;
    color: #fcd34d;
    font-size: .55rem;
}

.payment-mini-actions {
    display: flex;
    justify-content: flex-end;
    gap: 5px;
}

.appointment-payment-btn {
    display: inline-flex;
    min-height: 28px;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 4px 7px;
    color: #7dd3fc;
    border: 1px solid rgba(14,165,233,.15);
    border-radius: 7px;
    background: rgba(14,165,233,.07);
    font-size: .52rem;
    font-weight: 750;
    cursor: pointer;
}

.appointment-payment-email {
    color: #c4b5fd;
    border-color: rgba(139,92,246,.16);
    background: rgba(124,58,237,.07);
}

.appointment-payment-btn:disabled {
    cursor: not-allowed;
    opacity: .34;
}

.payment-mini-status {
    display: inline-flex;
    justify-content: flex-end;
    gap: 4px;
    color: #64748b;
    font-size: .5rem;
    line-height: 1.35;
    text-align: right;
}

.payment-mini-status-sent {
    color: #86efac;
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
    color: #dcfce7;
    border: 1px solid rgba(34,197,94,.23);
    border-radius: 11px;
    background: rgba(20,83,45,.96);
    box-shadow: 0 16px 40px rgba(0,0,0,.3);
    font-size: .68rem;
    font-weight: 750;
}

.appointments-no-result {
    display: flex;
    min-height: 190px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    color: #64748b;
}

.appointments-no-result > i {
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    color: #93c5fd;
    border-radius: 12px;
    background: rgba(37,99,235,.08);
    font-size: 1rem;
}

.appointments-no-result > strong {
    color: #cbd5e1;
}

.appointments-no-result > span {
    font-size: .6rem;
}

@media (max-width: 1100px) {
    .appointments-toolbar-redesign {
        align-items: stretch;
        flex-direction: column;
    }

    .appointments-search-redesign {
        width: 100%;
        flex-basis: auto;
    }
}

@media (max-width: 760px) {
    .appointments-page-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .appointment-filters {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .appointment-filter {
        flex: 0 0 auto;
    }

    .appointments-result-bar {
        padding-inline: 12px;
    }
}

.appointment-admission-mode {
    margin-top: 2px;
    border: 1px solid rgba(96,165,250,.12);
}

.appointment-admission-mode-contact {
    color: #4ade80 !important;
    background: rgba(34,197,94,.08) !important;
}

.appointment-admission-mode-vocal_test {
    color: #c4b5fd !important;
    background: rgba(139,92,246,.08) !important;
}

.appointment-admission-mode-test {
    color: #fcd34d !important;
    background: rgba(245,158,11,.08) !important;
}

.request-type-contact {
    color: #4ade80;
    background: rgba(34,197,94,.1);
}

.appointment-interview-cell,
.appointment-slot-cell {
    vertical-align: middle;
}

.interview-method-pill,
.interview-method-empty,
.appointment-slot-empty {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 8px;
    border-radius: 9px;
    font-size: .58rem;
    font-weight: 750;
    line-height: 1.3;
}

.interview-method-pill {
    color: #dbeafe;
    background: rgba(59,130,246,.09);
}

.interview-method-whatsapp {
    color: #4ade80;
    background: rgba(34,197,94,.09);
}

.interview-method-phone_call {
    color: #93c5fd;
    background: rgba(59,130,246,.09);
}

.interview-method-video_call {
    color: #c4b5fd;
    background: rgba(139,92,246,.09);
}

.interview-method-empty,
.appointment-slot-empty {
    color: #64748b;
    background: rgba(100,116,139,.07);
}

.interview-quick-actions {
    margin-top: 5px;
}

.interview-quick-actions a {
    color: #93c5fd;
    font-size: .56rem;
    font-weight: 700;
    text-decoration: none;
}

.appointment-slot-cell strong,
.appointment-slot-cell > span,
.appointment-slot-cell small {
    display: flex;
    align-items: center;
    gap: 5px;
}

.appointment-slot-cell strong {
    color: #e2e8f0;
    font-size: .62rem;
}

.appointment-slot-cell > span:not(.appointment-slot-empty) {
    margin-top: 4px;
    color: #93c5fd;
    font-size: .58rem;
}

.appointment-slot-cell small {
    margin-top: 5px;
    color: #64748b;
    font-size: .54rem;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search =
        document.getElementById('appointmentsSearch');

    const filterButtons =
        Array.from(
            document.querySelectorAll('.appointment-filter')
        );

    const rows =
        Array.from(
            document.querySelectorAll(
                '#appointmentsBody tr[data-search]'
            )
        );

    const visibleCount =
        document.getElementById('appointmentsVisibleCount');

    const noResult =
        document.getElementById('appointmentsNoResult');

    const resetButton =
        document.getElementById('appointmentsReset');

    let activeFilter = 'all';

    const normalize = value =>
        (value || '')
            .trim()
            .toLocaleLowerCase();

    const rowMatchesFilter = row => {
        if (activeFilter === 'all') {
            return true;
        }

        if (
            activeFilter === 'general'
            || activeFilter === 'admission'
        ) {
            return row.dataset.category === activeFilter;
        }

        if (
            activeFilter === 'pending'
            || activeFilter === 'confirmed'
        ) {
            return row.dataset.status === activeFilter;
        }

        return true;
    };

    const applyFilters = () => {
        const term = normalize(search?.value);
        let count = 0;

        rows.forEach(row => {
            const matchesSearch =
                !term
                || normalize(row.dataset.search).includes(term);

            const visible =
                matchesSearch
                && rowMatchesFilter(row);

            row.hidden = !visible;

            if (visible) {
                count += 1;
            }
        });

        if (visibleCount) {
            visibleCount.textContent = count;
        }

        if (noResult) {
            noResult.hidden = count !== 0;
        }

        if (resetButton) {
            resetButton.hidden =
                activeFilter === 'all'
                && !term;
        }
    };

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(item => {
                item.classList.remove('is-active');
            });

            button.classList.add('is-active');
            activeFilter = button.dataset.filter || 'all';

            applyFilters();
        });
    });

    search?.addEventListener('input', applyFilters);

    resetButton?.addEventListener('click', () => {
        activeFilter = 'all';

        filterButtons.forEach(button => {
            button.classList.toggle(
                'is-active',
                button.dataset.filter === 'all'
            );
        });

        if (search) {
            search.value = '';
        }

        applyFilters();
    });

    const copyButtons =
        document.querySelectorAll('.payment-copy-button');

    const showCopyFeedback = message => {
        document
            .querySelector('.payment-copy-feedback')
            ?.remove();

        const feedback =
            document.createElement('div');

        feedback.className =
            'payment-copy-feedback';

        feedback.innerHTML =
            '<i class="bi bi-check-circle-fill"></i>'
            + message;

        document.body.appendChild(feedback);

        window.setTimeout(() => {
            feedback.remove();
        }, 2600);
    };

    const fallbackCopy = value => {
        const input =
            document.createElement('textarea');

        input.value = value;
        input.setAttribute('readonly', 'readonly');
        input.style.position = 'fixed';
        input.style.opacity = '0';

        document.body.appendChild(input);
        input.select();

        const copied =
            document.execCommand('copy');

        input.remove();

        return copied;
    };

    copyButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const url =
                button.dataset.paymentUrl;

            if (!url) {
                return;
            }

            try {
                if (
                    navigator.clipboard
                    && window.isSecureContext
                ) {
                    await navigator.clipboard.writeText(url);
                } else if (!fallbackCopy(url)) {
                    throw new Error('Copie impossible');
                }

                showCopyFeedback(
                    'Lien de paiement copié.'
                );
            } catch (error) {
                window.prompt(
                    'Copiez le lien de paiement :',
                    url
                );
            }
        });
    });

    applyFilters();
});
</script>

@endsection
