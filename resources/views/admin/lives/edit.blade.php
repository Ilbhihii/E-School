@extends('layouts.admin')

@section('title', 'Modifier le live')
@section('page_title', 'Modifier live')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau → Modifier'
)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-page-header">
            <div>
                <h1>Modifier le live</h1>
                <div class="subtitle">
                    Conservez le live dans une structure exacte :
                    Matière → Niveau → Classe → Créneau.
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
            action="{{ route('admin.lives.update', $live) }}"
        >
            @csrf
            @method('PUT')

            @include(
                'components.pedagogical-path-edit',
                [
                    'hierarchy' => $editHierarchy,
                    'prefix' => 'adminLiveEdit',
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
                        <i
                            class="bi bi-broadcast-pin"
                            style="color:#FB7185;"
                        ></i>
                        Informations du live
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="liveTitle"
                        >
                            Titre du live
                        </label>

                        <input
                            id="liveTitle"
                            type="text"
                            name="title"
                            value="{{
                                old(
                                    'title',
                                    $live->title
                                )
                            }}"
                            class="adm-form-control
                                @error('title') error @enderror"
                            maxlength="255"
                            required
                        >

                        @error('title')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="professorId"
                        >
                            Personne / professeur affecté au lien
                        </label>

                        <select
                            id="professorId"
                            name="professor_id"
                            class="adm-form-select @error('professor_id') error @enderror"
                            required
                        >
                            <option value="">
                                Choisir un professeur...
                            </option>

                            @foreach($professors as $professor)
                                <option
                                    value="{{ $professor->id }}"
                                    {{ (string) old('professor_id', $selectedProfessorId) === (string) $professor->id ? 'selected' : '' }}
                                >
                                    {{ $professor->name }}
                                    @if($professor->email)
                                        — {{ $professor->email }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('professor_id')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="streamUrl"
                        >
                            Lien du live
                        </label>

                        <input
                            id="streamUrl"
                            type="url"
                            name="stream_url"
                            value="{{
                                old(
                                    'stream_url',
                                    $live->stream_url
                                )
                            }}"
                            class="adm-form-control
                                @error('stream_url') error @enderror"
                            placeholder="https://..."
                            required
                        >

                        @error('stream_url')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveDate"
                                >
                                    Date
                                </label>

                                <input
                                    id="liveDate"
                                    type="date"
                                    name="live_date"
                                    value="{{
                                        old(
                                            'live_date',
                                            optional(
                                                $live->live_date
                                            )->format('Y-m-d')
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('live_date') error @enderror"
                                    required
                                >

                                @error('live_date')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveStart"
                                >
                                    Heure de début
                                </label>

                                <input
                                    id="liveStart"
                                    type="time"
                                    name="start_time"
                                    min="08:00"
                                    max="22:00"
                                    step="1800"
                                    value="{{
                                        old(
                                            'start_time',
                                            substr(
                                                (string) $live->start_time,
                                                0,
                                                5
                                            )
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('start_time') error @enderror"
                                    required
                                >

                                @error('start_time')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveEnd"
                                >
                                    Heure de fin
                                </label>

                                <input
                                    id="liveEnd"
                                    type="time"
                                    name="end_time"
                                    value="{{
                                        old(
                                            'end_time',
                                            substr(
                                                (string) $live->end_time,
                                                0,
                                                5
                                            )
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('end_time') error @enderror"
                                    required
                                >

                                @error('end_time')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div
                        id="admin_live_edit_code_preview"
                        style="
                            display:none;
                            margin-top:12px;
                            padding:10px 12px;
                            border-radius:10px;
                            border:1px solid rgba(99,102,241,.18);
                            background:rgba(99,102,241,.06);
                            color:#C4B5FD;
                            font-size:.8rem;
                            font-weight:700;
                        "
                    >
                        Résultat final :
                        <strong id="admin_live_edit_code">—</strong>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a
                    href="{{ route('admin.lives.index') }}"
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
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
<!-- LIVE_PEDAGOGICAL_CODE_ADMIN_PROF_V1_EDIT -->
<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {
        const hierarchy =
            @json($editHierarchy);

        const subject =
            document.getElementById(
                'adminLiveEditSubject'
            );

        const level =
            document.getElementById(
                'adminLiveEditLevel'
            );

        const classroom =
            document.getElementById(
                'adminLiveEditClass'
            );

        const slot =
            document.getElementById(
                'adminLiveEditSlot'
            );

        const date =
            document.getElementById(
                'liveDate'
            );

        const start =
            document.getElementById(
                'liveStart'
            );

        const end =
            document.getElementById(
                'liveEnd'
            );

        const output =
            document.getElementById(
                'admin_live_edit_code'
            );

        const preview =
            document.getElementById(
                'admin_live_edit_code_preview'
            );

        if (
            !subject
            || !level
            || !classroom
            || !slot
            || !date
            || !start
            || !output
            || !preview
        ) {
            return;
        }

        const strip =
            value =>
                String(value || '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');

        const dayCode =
            value => {
                if (!value) {
                    return '';
                }

                const d =
                    new Date(
                        value
                        + 'T12:00:00'
                    );

                return {
                    0:'D',
                    1:'L',
                    2:'MA',
                    3:'M',
                    4:'J',
                    5:'V',
                    6:'S',
                }[d.getDay()] || '';
            };

        const slotNumber =
            value => {
                const match =
                    String(value || '')
                        .match(
                            /^(\d{2}):(\d{2})$/
                        );

                if (!match) {
                    return null;
                }

                const diff =
                    (
                        Number(match[1])
                        * 60
                        + Number(match[2])
                    )
                    - (8 * 60);

                if (
                    diff < 0
                    || diff > 14 * 60
                    || diff % 30 !== 0
                ) {
                    return null;
                }

                return (
                    diff / 30
                ) + 1;
            };

        const add90 =
            value => {
                const match =
                    String(value || '')
                        .match(
                            /^(\d{2}):(\d{2})$/
                        );

                if (!match) {
                    return '';
                }

                let total =
                    Number(match[1]) * 60
                    + Number(match[2])
                    + 90;

                total %= 24 * 60;

                return (
                    String(
                        Math.floor(total / 60)
                    ).padStart(2, '0')
                    + ':'
                    + String(total % 60)
                        .padStart(2, '0')
                );
            };

        const update =
            () => {
                const s =
                    hierarchy.find(
                        item =>
                            String(item.id)
                            === String(subject.value)
                    );

                const l =
                    s?.levels?.find(
                        item =>
                            String(item.id)
                            === String(level.value)
                    );

                const c =
                    l?.classes?.find(
                        item =>
                            String(item.id)
                            === String(classroom.value)
                    );

                const g =
                    c?.slots?.find(
                        item =>
                            String(item.id)
                            === String(slot.value)
                    );

                const subjectCode =
                    strip(s?.name)
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ''
                        )
                        .slice(0, 2);

                const normalizedClass =
                    strip(c?.name)
                        .toLowerCase();

                let classCode = '';

                if (
                    normalizedClass.includes(
                        'debut'
                    )
                ) {
                    classCode = 'D';
                } else if (
                    normalizedClass.includes(
                        'inter'
                    )
                ) {
                    classCode = 'I';
                } else if (
                    normalizedClass.includes(
                        'avance'
                    )
                ) {
                    classCode = 'A';
                } else {
                    classCode =
                        strip(c?.name)
                            .replace(
                                /[^A-Za-z0-9]/g,
                                ''
                            )
                            .charAt(0)
                            .toUpperCase();
                }

                const groupMatch =
                    String(g?.code || '')
                        .match(
                            /(\d+)$/
                        );

                const groupNumber =
                    groupMatch
                        ? groupMatch[1]
                        : '';

                const code =
                    dayCode(date.value)
                    && slotNumber(start.value)
                    && subjectCode
                    && classCode
                    && groupNumber
                        ? (
                            dayCode(date.value)
                            + slotNumber(start.value)
                            + subjectCode
                            + classCode
                            + groupNumber
                        )
                        : '';

                output.textContent =
                    code || '—';

                preview.style.display =
                    code
                        ? 'block'
                        : 'none';

                if (
                    start.value
                    && end
                ) {
                    end.value =
                        add90(
                            start.value
                        );
                }
            };

        [
            subject,
            level,
            classroom,
            slot,
            date,
            start,
        ].forEach(
            element => {
                element.addEventListener(
                    'change',
                    () =>
                        setTimeout(
                            update,
                            0
                        )
                );

                element.addEventListener(
                    'input',
                    () =>
                        setTimeout(
                            update,
                            0
                        )
                );
            }
        );

        setTimeout(
            update,
            0
        );
    }
);
</script>

@endsection
