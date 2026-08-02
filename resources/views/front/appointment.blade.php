@extends('layouts.front')

@section('title', 'Réserver un entretien')

@section('content')

@php
    $isDirectInterview =
        !empty($interviewSubject)
        && !empty($interviewLevel)
        && !empty($interviewClass);

    $isSubmittedTest =
        !empty($vocalSubmission)
        || !empty($highSchoolSubmission);
@endphp

<section class="interview-page">
    <div class="interview-glow interview-glow-one"></div>
    <div class="interview-glow interview-glow-two"></div>

    <div class="container position-relative">
        <div class="interview-layout">
            <aside class="interview-summary">
                <span class="interview-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                    TEST D’ADMISSION
                </span>

                <h1>
                    {{
                        $isDirectInterview
                            ? 'Entretien Soutien Lycée'
                            : (
                                $type === 'test'
                                    ? 'Rendez-vous de test'
                                    : 'Prendre rendez-vous'
                            )
                    }}
                </h1>

                <p>
                    @if($isDirectInterview)
                        Remplissez le formulaire, choisissez votre
                        mode d’entretien et proposez un créneau.
                        L’administration vous contactera pour confirmer.
                    @elseif($isSubmittedTest)
                        Votre test a été enregistré. Complétez vos
                        coordonnées pour demander un rendez-vous.
                    @else
                        Complétez vos coordonnées. L’administration
                        vous recontactera rapidement.
                    @endif
                </p>

                @if($isDirectInterview)
                    <div class="selected-path">
                        <span class="selected-path-icon">
                            <i class="bi bi-journal-check"></i>
                        </span>

                        <div>
                            <small>Parcours sélectionné</small>

                            <strong>
                                {{ $interviewSubject->name }}
                            </strong>

                            <span>
                                {{ $interviewLevel->name }}
                                ·
                                {{ $interviewClass->name }}
                            </span>
                        </div>
                    </div>

                    <div class="interview-steps">
                        <div>
                            <span>1</span>
                            Remplir le formulaire
                        </div>

                        <div>
                            <span>2</span>
                            Choisir le mode d’entretien
                        </div>

                        <div>
                            <span>3</span>
                            Recevoir la confirmation
                        </div>

                        <div>
                            <span>4</span>
                            Passer le test oral
                        </div>
                    </div>
                @endif
            </aside>

            <div class="interview-form-card">
                <div class="interview-form-head">
                    <span>
                        <i class="bi bi-calendar2-check"></i>
                    </span>

                    <div>
                        <h2>
                            Formulaire de rendez-vous
                        </h2>

                        <p>
                            Les champs marqués d’un astérisque
                            sont obligatoires.
                        </p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="interview-alert">
                        <i class="bi bi-exclamation-circle-fill"></i>

                        <div>
                            <strong>
                                Vérifiez les informations saisies.
                            </strong>

                            <span>
                                {{
                                    $errors->first()
                                }}
                            </span>
                        </div>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{
                        route('appointment.store')
                    }}{{
                        request()->query('from')
                            ? '?'
                                . http_build_query([
                                    'redirect' =>
                                        'student.waiting',
                                ])
                            : ''
                    }}"
                >
                    @csrf

                    @if(!empty($submissionToken))
                        <input
                            type="hidden"
                            name="submission_token"
                            value="{{ $submissionToken }}"
                        >
                    @endif

                    @if($vocalSubmission)
                        <input
                            type="hidden"
                            name="vocal_test_submission_id"
                            value="{{ $vocalSubmission->id }}"
                        >

                        <div class="submission-summary vocal">
                            <i class="bi bi-mic-fill"></i>

                            <div>
                                <strong>
                                    Récitation vocale enregistrée
                                </strong>

                                <span>
                                    {{ $vocalSubmission->subject->name }}
                                    ·
                                    {{ $vocalSubmission->level->name }}
                                    ·
                                    {{ $vocalSubmission->classRoom->name }}
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($highSchoolSubmission)
                        <input
                            type="hidden"
                            name="high_school_test_submission_id"
                            value="{{ $highSchoolSubmission->id }}"
                        >

                        <div class="submission-summary written">
                            <i class="bi bi-images"></i>

                            <div>
                                <strong>
                                    Test écrit importé
                                </strong>

                                <span>
                                    {{
                                        $highSchoolSubmission
                                            ->subject->name
                                    }}
                                    ·
                                    {{
                                        $highSchoolSubmission
                                            ->level->name
                                    }}
                                    ·
                                    {{
                                        $highSchoolSubmission
                                            ->classRoom->name
                                    }}
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($isDirectInterview)
                        <input
                            type="hidden"
                            name="interview_path"
                            value="1"
                        >

                        <input
                            type="hidden"
                            name="subject_id"
                            value="{{ $interviewSubject->id }}"
                        >

                        <input
                            type="hidden"
                            name="level_id"
                            value="{{ $interviewLevel->id }}"
                        >

                        <input
                            type="hidden"
                            name="class_id"
                            value="{{ $interviewClass->id }}"
                        >
                    @endif

                    @if($type === 'test')
                        <input
                            type="hidden"
                            name="type"
                            value="test"
                        >
                    @else
                        <div class="form-field full">
                            <label for="type">
                                Type de rendez-vous *
                            </label>

                            <select
                                id="type"
                                name="type"
                                required
                            >
                                <option value="">
                                    Choisissez un type
                                </option>

                                @foreach(
                                    \App\Models\TestAppointment
                                        ::getTypes()
                                    as $value => $label
                                )
                                    @if($value !== 'test')
                                        <option
                                            value="{{ $value }}"
                                            {{
                                                old('type')
                                                === $value
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ $label }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="first_name">
                                Prénom *
                            </label>

                            <input
                                id="first_name"
                                type="text"
                                name="first_name"
                                value="{{
                                    old(
                                        'first_name',
                                        $user?->name
                                            ? (
                                                explode(
                                                    ' ',
                                                    $user->name
                                                )[0]
                                                ?? ''
                                            )
                                            : ''
                                    )
                                }}"
                                required
                                autocomplete="given-name"
                                placeholder="Votre prénom"
                            >
                        </div>

                        <div class="form-field">
                            <label for="last_name">
                                Nom *
                            </label>

                            <input
                                id="last_name"
                                type="text"
                                name="last_name"
                                value="{{
                                    old(
                                        'last_name',
                                        $user?->name
                                            ? implode(
                                                ' ',
                                                array_slice(
                                                    explode(
                                                        ' ',
                                                        $user->name
                                                    ),
                                                    1
                                                )
                                            )
                                            : ''
                                    )
                                }}"
                                required
                                autocomplete="family-name"
                                placeholder="Votre nom"
                            >
                        </div>

                        <div class="form-field">
                            <label for="phone">
                                Téléphone / WhatsApp *
                            </label>

                            <input
                                id="phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                                placeholder="+212 6XX XX XX XX"
                            >
                        </div>

                        <div class="form-field">
                            <label for="email">
                                Adresse e-mail *
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{
                                    old(
                                        'email',
                                        $user?->email ?? ''
                                    )
                                }}"
                                required
                                autocomplete="email"
                                placeholder="exemple@email.com"
                            >
                        </div>

                        <div class="form-field">
                            <label for="city">
                                Ville *
                            </label>

                            <input
                                id="city"
                                type="text"
                                name="city"
                                value="{{
                                    old(
                                        'city',
                                        $user?->city ?? ''
                                    )
                                }}"
                                required
                                autocomplete="address-level2"
                                placeholder="Votre ville"
                            >
                        </div>

                        <div class="form-field">
                            <label for="country">
                                Pays *
                            </label>

                            <input
                                id="country"
                                type="text"
                                name="country"
                                value="{{
                                    old(
                                        'country',
                                        $user?->country
                                            ?? 'Maroc'
                                    )
                                }}"
                                required
                                autocomplete="country-name"
                                placeholder="Votre pays"
                            >
                        </div>
                    </div>

                    @if($isDirectInterview)
                        <div class="form-section-title">
                            <span>1</span>

                            <div>
                                <strong>
                                    Choisissez le mode d’entretien
                                </strong>

                                <small>
                                    Une seule option est nécessaire.
                                </small>
                            </div>
                        </div>

                        <div class="method-grid">
                            @foreach(
                                $interviewMethods
                                as $methodValue => $methodLabel
                            )
                                @php
                                    $methodIcon = match(
                                        $methodValue
                                    ) {
                                        'video_call' =>
                                            'bi-camera-video-fill',
                                        'phone_call' =>
                                            'bi-telephone-fill',
                                        'whatsapp' =>
                                            'bi-whatsapp',
                                        default =>
                                            'bi-chat-dots-fill',
                                    };
                                @endphp

                                <label class="method-option">
                                    <input
                                        type="radio"
                                        name="interview_method"
                                        value="{{ $methodValue }}"
                                        {{
                                            old(
                                                'interview_method'
                                            ) === $methodValue
                                                ? 'checked'
                                                : ''
                                        }}
                                        required
                                    >

                                    <span class="method-card">
                                        <i
                                            class="bi {{
                                                $methodIcon
                                            }}"
                                        ></i>

                                        <strong>
                                            {{ $methodLabel }}
                                        </strong>

                                        <small>
                                            @if(
                                                $methodValue
                                                === 'video_call'
                                            )
                                                Google Meet,
                                                Zoom ou WhatsApp vidéo
                                            @elseif(
                                                $methodValue
                                                === 'phone_call'
                                            )
                                                Appel direct sur
                                                votre numéro
                                            @else
                                                Échange ou appel
                                                via WhatsApp
                                            @endif
                                        </small>

                                        <b>
                                            <i class="bi bi-check-lg"></i>
                                        </b>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div class="form-section-title">
                            <span>2</span>

                            <div>
                                <strong>
                                    Proposez un créneau
                                </strong>

                                <small>
                                    L’administration confirmera
                                    la disponibilité.
                                </small>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-field">
                                <label for="preferred_date">
                                    Date souhaitée *
                                </label>

                                <input
                                    id="preferred_date"
                                    type="date"
                                    name="preferred_date"
                                    value="{{
                                        old('preferred_date')
                                    }}"
                                    min="{{
                                        now()->toDateString()
                                    }}"
                                    required
                                >
                            </div>

                            <div class="form-field">
                                <label for="preferred_time">
                                    Heure souhaitée *
                                </label>

                                <input
                                    id="preferred_time"
                                    type="time"
                                    name="preferred_time"
                                    value="{{
                                        old('preferred_time')
                                    }}"
                                    required
                                >
                            </div>

                            <div class="form-field full">
                                <label for="notes">
                                    Message complémentaire
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="4"
                                    maxlength="1000"
                                    placeholder="Disponibilités supplémentaires ou informations utiles..."
                                >{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    @endif

                    <button
                        type="submit"
                        class="interview-submit"
                    >
                        <i class="bi bi-calendar-check-fill"></i>

                        @if($isDirectInterview)
                            Envoyer ma demande d’entretien
                        @elseif($isSubmittedTest)
                            Envoyer mon test avec le rendez-vous
                        @else
                            Envoyer ma demande
                        @endif
                    </button>

                    <p class="form-security">
                        <i class="bi bi-shield-check"></i>

                        Vos informations sont utilisées uniquement
                        pour organiser votre rendez-vous.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.interview-page {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    padding: 6.5rem 0 4rem;
    background:
        radial-gradient(
            circle at 12% 15%,
            rgba(37,99,235,.16),
            transparent 31%
        ),
        radial-gradient(
            circle at 88% 75%,
            rgba(124,58,237,.16),
            transparent 34%
        ),
        linear-gradient(
            135deg,
            #07101F,
            #121129 55%,
            #0C1627
        );
}

.interview-glow {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(5px);
}

.interview-glow-one {
    top: 5%;
    right: 5%;
    width: 280px;
    height: 280px;
    background:
        radial-gradient(
            circle,
            rgba(96,165,250,.18),
            transparent 68%
        );
}

.interview-glow-two {
    bottom: 2%;
    left: 3%;
    width: 320px;
    height: 320px;
    background:
        radial-gradient(
            circle,
            rgba(168,85,247,.16),
            transparent 68%
        );
}

.interview-layout {
    display: grid;
    grid-template-columns:
        minmax(0,.82fr)
        minmax(0,1.35fr);
    gap: 22px;
    max-width: 1120px;
    margin: 0 auto;
    align-items: start;
}

.interview-summary,
.interview-form-card {
    border: 1px solid rgba(255,255,255,.085);
    border-radius: 25px;
    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.07),
            rgba(255,255,255,.035)
        );
    box-shadow:
        0 26px 70px rgba(0,0,0,.3);
    backdrop-filter: blur(18px);
}

.interview-summary {
    position: sticky;
    top: 6.5rem;
    padding: 1.55rem;
}

.interview-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    border-radius: 999px;
    color: #BFDBFE;
    background: rgba(37,99,235,.12);
    font-size: .65rem;
    font-weight: 850;
    letter-spacing: .06em;
}

.interview-summary h1 {
    margin: 1rem 0 .65rem;
    color: #fff;
    font-size: clamp(1.75rem,4vw,2.55rem);
    font-weight: 900;
    line-height: 1.08;
}

.interview-summary > p {
    margin: 0;
    color: rgba(255,255,255,.5);
    font-size: .78rem;
    line-height: 1.65;
}

.selected-path {
    display: flex;
    gap: 11px;
    margin-top: 1.3rem;
    padding: 13px;
    border: 1px solid rgba(245,158,11,.18);
    border-radius: 14px;
    background: rgba(245,158,11,.075);
}

.selected-path-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    color: #FCD34D;
    background: rgba(245,158,11,.13);
}

.selected-path > div {
    display: flex;
    flex-direction: column;
}

.selected-path small {
    color: rgba(255,255,255,.36);
    font-size: .58rem;
    text-transform: uppercase;
}

.selected-path strong {
    margin: 2px 0;
    color: #fff;
    font-size: .8rem;
}

.selected-path span {
    color: #FCD34D;
    font-size: .68rem;
}

.interview-steps {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 1.3rem;
}

.interview-steps div {
    display: flex;
    align-items: center;
    gap: 9px;
    color: rgba(255,255,255,.57);
    font-size: .7rem;
}

.interview-steps span {
    width: 25px;
    height: 25px;
    flex: 0 0 25px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    font-size: .61rem;
    font-weight: 800;
}

.interview-form-card {
    padding: 1.55rem;
}

.interview-form-head {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 1.3rem;
}

.interview-form-head > span {
    width: 47px;
    height: 47px;
    flex: 0 0 47px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    font-size: 1.08rem;
}

.interview-form-head h2 {
    margin: 0;
    color: #fff;
    font-size: 1.12rem;
    font-weight: 850;
}

.interview-form-head p {
    margin: 3px 0 0;
    color: rgba(255,255,255,.38);
    font-size: .64rem;
}

.interview-alert,
.submission-summary {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-bottom: 1rem;
    padding: 11px 12px;
    border-radius: 12px;
    font-size: .68rem;
}

.interview-alert {
    border: 1px solid rgba(239,68,68,.16);
    color: #FCA5A5;
    background: rgba(239,68,68,.08);
}

.interview-alert div {
    display: flex;
    flex-direction: column;
}

.submission-summary {
    border: 1px solid rgba(34,197,94,.18);
    color: #D1FAE5;
    background: rgba(34,197,94,.075);
}

.submission-summary.written {
    border-color: rgba(56,189,248,.18);
    color: #E0F2FE;
    background: rgba(14,165,233,.075);
}

.submission-summary div {
    display: flex;
    flex-direction: column;
}

.submission-summary span {
    margin-top: 2px;
    color: rgba(255,255,255,.48);
    font-size: .62rem;
}

.form-grid {
    display: grid;
    grid-template-columns:
        repeat(2,minmax(0,1fr));
    gap: 12px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-field.full {
    grid-column: 1 / -1;
}

.form-field label {
    color: rgba(255,255,255,.58);
    font-size: .67rem;
    font-weight: 700;
}

.form-field input,
.form-field select,
.form-field textarea {
    width: 100%;
    min-height: 45px;
    padding: 10px 12px;
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 11px;
    outline: none;
    color: rgba(255,255,255,.88);
    background: rgba(255,255,255,.045);
    font-size: .73rem;
    transition:
        border-color .2s ease,
        box-shadow .2s ease,
        background .2s ease;
}

.form-field textarea {
    resize: vertical;
}

.form-field input:focus,
.form-field select:focus,
.form-field textarea:focus {
    border-color: rgba(96,165,250,.6);
    background: rgba(255,255,255,.065);
    box-shadow:
        0 0 0 3px rgba(37,99,235,.1);
}

.form-field input[type="date"],
.form-field input[type="time"],
.form-field select {
    color-scheme: dark;
}

.form-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 1.4rem 0 .85rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,.065);
}

.form-section-title > span {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;
    display: grid;
    place-items: center;
    border-radius: 9px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    font-size: .65rem;
    font-weight: 850;
}

.form-section-title > div {
    display: flex;
    flex-direction: column;
}

.form-section-title strong {
    color: rgba(255,255,255,.82);
    font-size: .75rem;
}

.form-section-title small {
    margin-top: 2px;
    color: rgba(255,255,255,.34);
    font-size: .59rem;
}

.method-grid {
    display: grid;
    grid-template-columns:
        repeat(3,minmax(0,1fr));
    gap: 9px;
}

.method-option {
    position: relative;
    cursor: pointer;
}

.method-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.method-card {
    position: relative;
    min-height: 142px;
    display: flex;
    flex-direction: column;
    padding: 13px;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    background: rgba(255,255,255,.035);
    transition:
        transform .2s ease,
        border-color .2s ease,
        background .2s ease;
}

.method-card > i {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    margin-bottom: 10px;
    border-radius: 10px;
    color: #93C5FD;
    background: rgba(37,99,235,.12);
    font-size: .9rem;
}

.method-card strong {
    color: rgba(255,255,255,.82);
    font-size: .7rem;
}

.method-card small {
    margin-top: 5px;
    color: rgba(255,255,255,.35);
    font-size: .57rem;
    line-height: 1.45;
}

.method-card b {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 21px;
    height: 21px;
    display: none;
    place-items: center;
    border-radius: 50%;
    color: #fff;
    background: #2563EB;
    font-size: .56rem;
}

.method-option:hover .method-card {
    transform: translateY(-2px);
    border-color: rgba(96,165,250,.3);
}

.method-option input:checked + .method-card {
    border-color: rgba(96,165,250,.65);
    background: rgba(37,99,235,.11);
    box-shadow:
        0 0 0 3px rgba(37,99,235,.08);
}

.method-option input:checked + .method-card b {
    display: grid;
}

.interview-submit {
    width: 100%;
    min-height: 49px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    margin-top: 1.3rem;
    border: 0;
    border-radius: 13px;
    color: #fff;
    background:
        linear-gradient(
            135deg,
            #2563EB,
            #7C3AED
        );
    box-shadow:
        0 14px 30px rgba(37,99,235,.2);
    font-size: .76rem;
    font-weight: 850;
    cursor: pointer;
    transition:
        transform .2s ease,
        filter .2s ease;
}

.interview-submit:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
}

.form-security {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin: .9rem 0 0;
    color: rgba(255,255,255,.28);
    font-size: .58rem;
}

html.light-mode .interview-page {
    background:
        radial-gradient(
            circle at 12% 15%,
            rgba(37,99,235,.08),
            transparent 31%
        ),
        linear-gradient(
            135deg,
            #F3F6FC,
            #E9EEF7
        );
}

html.light-mode .interview-summary,
html.light-mode .interview-form-card {
    border-color: rgba(15,23,42,.08);
    background: rgba(255,255,255,.95);
    box-shadow:
        0 22px 55px rgba(15,23,42,.09);
}

html.light-mode .interview-summary h1,
html.light-mode .interview-form-head h2,
html.light-mode .selected-path strong {
    color: #172033;
}

html.light-mode .interview-summary > p,
html.light-mode .interview-form-head p,
html.light-mode .form-field label,
html.light-mode .form-section-title small,
html.light-mode .form-security {
    color: #64748B;
}

html.light-mode .interview-steps div,
html.light-mode .form-section-title strong,
html.light-mode .method-card strong {
    color: #334155;
}

html.light-mode .form-field input,
html.light-mode .form-field select,
html.light-mode .form-field textarea,
html.light-mode .method-card {
    border-color: rgba(15,23,42,.1);
    color: #172033;
    background: rgba(15,23,42,.035);
}

html.light-mode .form-field input[type="date"],
html.light-mode .form-field input[type="time"],
html.light-mode .form-field select {
    color-scheme: light;
}

@media (max-width:900px) {
    .interview-layout {
        grid-template-columns: 1fr;
    }

    .interview-summary {
        position: static;
    }
}

@media (max-width:680px) {
    .interview-page {
        padding-top: 5.3rem;
    }

    .interview-summary,
    .interview-form-card {
        padding: 1.1rem;
        border-radius: 19px;
    }

    .form-grid,
    .method-grid {
        grid-template-columns: 1fr;
    }

    .method-card {
        min-height: 112px;
    }
}
</style>

@endsection
