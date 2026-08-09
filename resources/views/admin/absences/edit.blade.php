@extends('layouts.admin')

@section('title', 'Modifier absence')
@section('page_title', 'Modifier absence')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau → Modifier'
)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-page-header">
            <div>
                <h1>Modifier l’absence</h1>

                <div class="subtitle">
                    Étudiant :
                    <strong>
                        {{ $absence->user?->name ?? 'Étudiant' }}
                    </strong>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="adm-alert adm-alert-danger mb-4">
                <strong>
                    La modification n’a pas été enregistrée.
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{
                route(
                    'admin.absences.update',
                    $absence
                )
            }}"
        >
            @csrf
            @method('PUT')

            @include(
                'components.pedagogical-path-edit',
                [
                    'hierarchy' => $editHierarchy,
                    'prefix' => 'adminAbsenceEdit',
                    'selectedSubject' =>
                        $selectedSubjectId,
                    'selectedLevel' =>
                        $selectedLevelId,
                    'selectedClass' =>
                        $selectedClassId,
                    'selectedSlot' =>
                        $selectedSlotId,
                ]
            )

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i class="bi bi-person-check-fill"></i>
                        Présence
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div
                        class="adm-card mb-4"
                        style="
                            background:rgba(99,102,241,.05);
                            border-color:rgba(99,102,241,.12);
                        "
                    >
                        <div
                            class="adm-card-body"
                            style="padding:1rem;"
                        >
                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                "
                            >
                                <span
                                    class="adm-avatar"
                                    style="
                                        background:
                                            var(--adm-gradient-primary);
                                    "
                                >
                                    {{
                                        mb_strtoupper(
                                            mb_substr(
                                                $absence
                                                    ->user
                                                    ?->name
                                                ?? 'E',
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </span>

                                <div>
                                    <strong>
                                        {{
                                            $absence
                                                ->user
                                                ?->name
                                            ?? 'Étudiant'
                                        }}
                                    </strong>

                                    @if(
                                        $absence
                                            ->user
                                            ?->email
                                    )
                                        <small
                                            style="
                                                display:block;
                                                color:
                                                var(--adm-text-muted);
                                            "
                                        >
                                            {{
                                                $absence
                                                    ->user
                                                    ->email
                                            }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">
                                    Date
                                </label>

                                <input
                                    type="date"
                                    name="date"
                                    value="{{
                                        old(
                                            'date',
                                            optional(
                                                $absence->date
                                            )->format('Y-m-d')
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('date') error @enderror"
                                    required
                                >

                                @error('date')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">
                                    Statut
                                </label>

                                <select
                                    name="present"
                                    class="adm-form-select
                                        @error('present') error @enderror"
                                    required
                                >
                                    <option
                                        value="1"
                                        {{
                                            (string) old(
                                                'present',
                                                $absence->present
                                                    ? '1'
                                                    : '0'
                                            ) === '1'
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        Présent
                                    </option>

                                    <option
                                        value="0"
                                        {{
                                            (string) old(
                                                'present',
                                                $absence->present
                                                    ? '1'
                                                    : '0'
                                            ) === '0'
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        Absent
                                    </option>
                                </select>

                                @error('present')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a
                    href="{{
                        route(
                            'admin.absences.show',
                            $absence
                        )
                    }}"
                    class="adm-btn adm-btn-ghost flex-fill text-center"
                >
                    <i class="bi bi-arrow-left"></i>
                    Annuler
                </a>

                <button
                    type="submit"
                    class="adm-btn adm-btn-primary flex-fill"
                >
                    <i class="bi bi-save"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
