@extends('layouts.admin')

@section('title', 'Rendez-vous — Administration')
@section('page_title', 'Rendez-vous')
@section('breadcrumb', 'Tests reçus')

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
            Entretiens BAC, récitations vocales et
            tests écrits demandés par les étudiants.
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
                            @else
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

                        <td>
                            <div class="d-flex gap-1">
                                @if(
                                    $appointment->status
                                    === 'pending'
                                )
                                    <form
                                        method="POST"
                                        action="{{
                                            route(
                                                'admin.appointments.confirm',
                                                $appointment
                                            )
                                        }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="adm-action-btn
                                                adm-action-edit"
                                            title="Confirmer"
                                        >
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{
                                            route(
                                                'admin.appointments.cancel',
                                                $appointment
                                            )
                                        }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="adm-action-btn
                                                adm-action-danger"
                                            title="Annuler"
                                        >
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif

                                <form
                                    method="POST"
                                    action="{{
                                        route(
                                            'admin.appointments.destroy',
                                            $appointment
                                        )
                                    }}"
                                    onsubmit="
                                        return confirm(
                                            'Supprimer ce rendez-vous et ses fichiers ?'
                                        )
                                    "
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="adm-action-btn
                                            adm-action-danger"
                                        title="Supprimer"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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
});
</script>

@endsection
