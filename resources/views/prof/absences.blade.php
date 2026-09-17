@extends('layouts.prof')

@section('title', 'Présences et absences')
@section('page_title', 'Présences et absences')
@section('breadcrumb', 'Matière → Niveau → Classe')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow">
            <i class="bi bi-person-check-fill"></i>
            Suivi des étudiants
        </span>

        <h1 class="pp-page-title">Faire l’appel</h1>

        <p class="pp-page-description">
            Sélectionnez le parcours
            Matière → Niveau → Classe,
            puis marquez les présences.
        </p>
    </div>

    <div class="pp-page-actions">
        <a
            href="{{ route('prof.absences.list') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-clock-history"></i>
            Historique
        </a>
    </div>
</section>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title">
                <i class="bi bi-diagram-3-fill"></i>
                Parcours et date
            </h2>

            <p class="pp-panel-subtitle">
                Les étudiants sont chargés depuis
                la classe sélectionnée.
            </p>
        </div>
    </header>

    <div class="pp-panel-body">
        <form
            method="POST"
            action="{{ route('prof.absences.store') }}"
            id="attendanceForm"
        >
            @csrf

            <div class="pps-form-path">
                <div class="pp-field">
                    <label
                        for="attendanceSubject"
                        class="pp-label"
                    >
                        Matière
                    </label>

                    <select
                        name="subject_id"
                        id="attendanceSubject"
                        class="adm-form-select"
                        required
                    >
                        <option value="">
                            Choisir une matière
                        </option>
                    </select>
                </div>

                <div class="pp-field">
                    <label
                        for="attendanceLevel"
                        class="pp-label"
                    >
                        Niveau
                    </label>

                    <select
                        name="level_id"
                        id="attendanceLevel"
                        class="adm-form-select"
                        disabled
                        required
                    >
                        <option value="">
                            Choisir un niveau
                        </option>
                    </select>
                </div>

                <div class="pp-field">
                    <label
                        for="attendanceClass"
                        class="pp-label"
                    >
                        Classe
                    </label>

                    <select
                        name="class_id"
                        id="attendanceClass"
                        class="adm-form-select"
                        disabled
                        required
                    >
                        <option value="">
                            Choisir une classe
                        </option>
                    </select>
                </div>

            </div>

            <div class="pp-field mt-3">
                <label
                    for="attendanceDate"
                    class="pp-label"
                >
                    Date
                </label>

                <input
                    type="date"
                    id="attendanceDate"
                    name="date"
                    value="{{ old('date', now()->toDateString()) }}"
                    class="adm-form-control"
                    required
                >
            </div>

            <div class="teams-attendance-import mt-4">
                <div class="teams-attendance-import-head">
                    <div class="teams-attendance-icon">
                        <i class="bi bi-microsoft-teams"></i>
                    </div>

                    <div>
                        <strong>
                            Importer la présence Microsoft Teams
                        </strong>
                        <small>
                            Téléchargez le rapport de présence .CSV depuis Teams,
                            puis importez-le ici. Le système préremplit l'appel,
                            mais rien n'est enregistré avant votre validation.
                        </small>
                    </div>
                </div>

                <div class="teams-attendance-controls">
                    <div class="pp-field">
                        <label
                            for="teamsAttendanceReport"
                            class="pp-label"
                        >
                            Rapport Teams (.CSV)
                        </label>

                        <input
                            type="file"
                            id="teamsAttendanceReport"
                            class="adm-form-control"
                            accept=".csv,.txt,text/csv,text/plain"
                        >
                    </div>

                    <button
                        type="button"
                        id="teamsAttendanceImport"
                        class="adm-btn adm-btn-primary"
                    >
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        Importer et préparer l'appel
                    </button>
                </div>

                <div
                    id="teamsAttendanceStatus"
                    class="teams-attendance-status"
                    hidden
                ></div>
            </div>

            <div
                id="studentsList"
                class="mt-4"
            >
                <div class="pps-empty">
                    Choisissez Matière → Niveau → Classe.
                </div>
            </div>

            <div class="pp-form-actions mt-4">
                <button
                    type="submit"
                    id="attendanceSubmit"
                    class="adm-btn adm-btn-success"
                    disabled
                >
                    <i class="bi bi-check2-all"></i>
                    Enregistrer les présences
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('styles')
<style>
    .teams-attendance-import {
        padding: 18px;
        border: 1px solid rgba(96,165,250,.18);
        border-radius: 16px;
        background:
            linear-gradient(
                135deg,
                rgba(37,99,235,.07),
                rgba(124,58,237,.05)
            );
    }

    .teams-attendance-import-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }

    .teams-attendance-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        color: #c4b5fd;
        font-size: 1.15rem;
        background: rgba(124,58,237,.15);
        border: 1px solid rgba(167,139,250,.18);
    }

    .teams-attendance-import-head strong,
    .teams-attendance-import-head small {
        display: block;
    }

    .teams-attendance-import-head strong {
        color: #f8fafc;
        font-size: .9rem;
    }

    .teams-attendance-import-head small {
        margin-top: 4px;
        max-width: 850px;
        color: var(--prof-text-muted, #94a3b8);
        font-size: .73rem;
        line-height: 1.55;
    }

    .teams-attendance-controls {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .teams-attendance-status {
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        color: #bfdbfe;
        font-size: .76rem;
        line-height: 1.6;
        border: 1px solid rgba(59,130,246,.16);
        background: rgba(37,99,235,.06);
    }

    .teams-attendance-status.is-error {
        color: #fecaca;
        border-color: rgba(239,68,68,.20);
        background: rgba(239,68,68,.07);
    }

    .teams-attendance-status.is-success {
        color: #bbf7d0;
        border-color: rgba(34,197,94,.20);
        background: rgba(34,197,94,.07);
    }

    .teams-match-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 5px;
        color: #94a3b8;
        font-size: .68rem;
    }

    .teams-match-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 7px;
        border-radius: 999px;
        color: #86efac;
        background: rgba(34,197,94,.09);
        border: 1px solid rgba(34,197,94,.15);
    }

    .teams-no-match-badge {
        color: #fca5a5;
        background: rgba(239,68,68,.08);
        border-color: rgba(239,68,68,.14);
    }

    @media (max-width: 760px) {
        .teams-attendance-controls {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hierarchy = @json($profHierarchy);

    const subject =
        document.getElementById('attendanceSubject');

    const level =
        document.getElementById('attendanceLevel');

    const classroom =
        document.getElementById('attendanceClass');

    const list =
        document.getElementById('studentsList');

    const submit =
        document.getElementById('attendanceSubmit');

    const baseUrl =
        @json(url('/prof/class-students'));

    const option = (value, label) => {
        const item = document.createElement('option');
        item.value = String(value);
        item.textContent = label;
        return item;
    };

    const subjectData = () =>
        hierarchy.find(
            item => String(item.id) === String(subject.value)
        );

    const levelData = () =>
        subjectData()?.levels?.find(
            item => String(item.id) === String(level.value)
        );

    function clearStudents(message = 'Choisissez une classe pour charger les étudiants.') {
        submit.disabled = true;

        list.innerHTML = `
            <div class="pps-empty">
                ${message}
            </div>
        `;
    }

    function fillClasses() {
        classroom.innerHTML = '';
        classroom.appendChild(
            option('', 'Choisir une classe')
        );

        (levelData()?.classes || []).forEach(item => {
            classroom.appendChild(
                option(item.id, item.name)
            );
        });

        classroom.disabled = !levelData();
        clearStudents();
    }

    function fillLevels() {
        level.innerHTML = '';
        level.appendChild(
            option('', 'Choisir un niveau')
        );

        (subjectData()?.levels || []).forEach(item => {
            level.appendChild(
                option(item.id, item.name)
            );
        });

        level.disabled = !subjectData();
        fillClasses();
    }

    function initials(name) {
        return String(name || 'E')
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map(part => part.charAt(0).toUpperCase())
            .join('');
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value || '');
        return div.innerHTML;
    }

    async function loadStudents() {
        if (
            !subject.value
            || !level.value
            || !classroom.value
        ) {
            clearStudents();
            return;
        }

        list.innerHTML = `
            <div class="pps-empty">
                Chargement des étudiants...
            </div>
        `;

        const params =
            new URLSearchParams({
                subject_id: subject.value,
                level_id: level.value,
            });

        try {
            const response = await fetch(
                `${baseUrl}/${encodeURIComponent(classroom.value)}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                    },
                }
            );

            if (!response.ok) {
                throw new Error();
            }

            const students = await response.json();

            if (!students.length) {
                clearStudents(
                    'Aucun étudiant assigné à cette classe.'
                );
                return;
            }

            list.innerHTML =
                students
                    .map(student => `
                        <div class="pp-attendance-row">
                            <div class="pp-attendance-student">
                                <span class="pp-attendance-avatar">
                                    ${escapeHtml(initials(student.name))}
                                </span>

                                <strong class="pp-attendance-name">
                                    ${escapeHtml(student.name)}
                                </strong>
                            </div>

                            <div class="pp-attendance-options">
                                <label class="pp-attendance-option">
                                    <input
                                        type="radio"
                                        name="students[${Number(student.id)}]"
                                        value="1"
                                        checked
                                        required
                                    >
                                    <span>Présent</span>
                                </label>

                                <label class="pp-attendance-option">
                                    <input
                                        type="radio"
                                        name="students[${Number(student.id)}]"
                                        value="0"
                                        required
                                    >
                                    <span>Absent</span>
                                </label>
                            </div>
                        </div>
                    `)
                    .join('');

            submit.disabled = false;
        } catch (error) {
            clearStudents(
                'Impossible de charger les étudiants.'
            );
        }
    }

    hierarchy.forEach(item => {
        subject.appendChild(
            option(item.id, item.name)
        );
    });

    subject.addEventListener(
        'change',
        () => fillLevels()
    );

    level.addEventListener(
        'change',
        () => fillClasses()
    );

    classroom.addEventListener(
        'change',
        loadStudents
    );

    fillLevels();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const teamsImportButton =
        document.getElementById('teamsAttendanceImport');

    const teamsReport =
        document.getElementById('teamsAttendanceReport');

    const teamsStatus =
        document.getElementById('teamsAttendanceStatus');

    const subject =
        document.getElementById('attendanceSubject');

    const level =
        document.getElementById('attendanceLevel');

    const classroom =
        document.getElementById('attendanceClass');

    const date =
        document.getElementById('attendanceDate');

    const list =
        document.getElementById('studentsList');

    const submit =
        document.getElementById('attendanceSubmit');

    const endpoint =
        @json(route('prof.absences.teams-preview'));

    const csrf =
        document.querySelector(
            '#attendanceForm input[name="_token"]'
        )?.value || '';

    function escapeTeamsHtml(value) {
        const div =
            document.createElement('div');

        div.textContent =
            String(value ?? '');

        return div.innerHTML;
    }

    function initials(name) {
        return String(name || 'E')
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map(part => part.charAt(0).toUpperCase())
            .join('');
    }

    function showStatus(
        html,
        type = ''
    ) {
        teamsStatus.hidden = false;
        teamsStatus.className =
            `teams-attendance-status ${type}`.trim();

        teamsStatus.innerHTML = html;
    }

    function renderImportedStudents(students) {
        if (!students.length) {
            list.innerHTML = `
                <div class="pps-empty">
                    Aucun étudiant assigné à cette classe.
                </div>
            `;

            submit.disabled = true;
            return;
        }

        list.innerHTML =
            students
                .map(student => {
                    const present =
                        Boolean(student.present);

                    const duration =
                        student.duration
                            ? `
                                <span>
                                    <i class="bi bi-clock"></i>
                                    ${escapeTeamsHtml(student.duration)}
                                </span>
                            `
                            : '';

                    const match =
                        student.teams_matched
                            ? `
                                <span class="teams-match-badge">
                                    <i class="bi bi-check2-circle"></i>
                                    Trouvé dans Teams
                                </span>
                            `
                            : `
                                <span class="teams-match-badge teams-no-match-badge">
                                    <i class="bi bi-x-circle"></i>
                                    Non trouvé dans Teams
                                </span>
                            `;

                    const teamsName =
                        student.teams_name
                            && student.teams_name !== student.name
                            ? `
                                <span>
                                    Teams :
                                    ${escapeTeamsHtml(student.teams_name)}
                                </span>
                            `
                            : '';

                    return `
                        <div class="pp-attendance-row">
                            <div class="pp-attendance-student">
                                <span class="pp-attendance-avatar">
                                    ${escapeTeamsHtml(initials(student.name))}
                                </span>

                                <div>
                                    <strong class="pp-attendance-name">
                                        ${escapeTeamsHtml(student.name)}
                                    </strong>

                                    <div class="teams-match-meta">
                                        ${match}
                                        ${duration}
                                        ${teamsName}
                                    </div>
                                </div>
                            </div>

                            <div class="pp-attendance-options">
                                <label class="pp-attendance-option">
                                    <input
                                        type="radio"
                                        name="students[${Number(student.id)}]"
                                        value="1"
                                        ${present ? 'checked' : ''}
                                        required
                                    >
                                    <span>Présent</span>
                                </label>

                                <label class="pp-attendance-option">
                                    <input
                                        type="radio"
                                        name="students[${Number(student.id)}]"
                                        value="0"
                                        ${!present ? 'checked' : ''}
                                        required
                                    >
                                    <span>Absent</span>
                                </label>
                            </div>
                        </div>
                    `;
                })
                .join('');

        submit.disabled = false;
    }

    teamsImportButton?.addEventListener(
        'click',
        async function () {
            if (
                !subject.value
                || !level.value
                || !classroom.value
                || !date.value
            ) {
                showStatus(
                    'Choisissez d’abord Matière → Niveau → Classe et la date.',
                    'is-error'
                );
                return;
            }

            const file =
                teamsReport.files?.[0];

            if (!file) {
                showStatus(
                    'Ajoutez le fichier .CSV téléchargé depuis Microsoft Teams.',
                    'is-error'
                );
                return;
            }

            teamsImportButton.disabled = true;

            showStatus(
                '<i class="bi bi-arrow-repeat"></i> Analyse du rapport Teams...'
            );

            const data =
                new FormData();

            data.append('_token', csrf);
            data.append(
                'subject_id',
                subject.value
            );
            data.append(
                'level_id',
                level.value
            );
            data.append(
                'class_id',
                classroom.value
            );
            data.append(
                'date',
                date.value
            );
            data.append(
                'teams_report',
                file
            );

            try {
                const response =
                    await fetch(
                        endpoint,
                        {
                            method: 'POST',
                            body: data,
                            headers: {
                                'Accept':
                                    'application/json',
                            },
                        }
                    );

                const payload =
                    await response.json();

                if (!response.ok) {
                    const errors =
                        payload.errors || {};

                    const message =
                        Object.values(errors)
                            .flat()
                            .filter(Boolean)[0]
                        || payload.message
                        || 'Impossible d’analyser ce rapport Teams.';

                    throw new Error(message);
                }

                renderImportedStudents(
                    payload.students || []
                );

                const summary =
                    payload.summary || {};

                let html = `
                    <strong>Rapport Teams importé.</strong>
                    ${Number(summary.present_count || 0)}
                    étudiant(s) trouvé(s) dans Teams,
                    ${Number(summary.absent_count || 0)}
                    non trouvé(s).
                    Vérifiez l’appel ci-dessous avant de l’enregistrer.
                `;

                const unmatched =
                    payload.unmatched_teams || [];

                if (unmatched.length) {
                    const preview =
                        unmatched
                            .slice(0, 5)
                            .map(item =>
                                escapeTeamsHtml(
                                    item.name
                                    || item.email
                                    || 'Participant'
                                )
                            )
                            .join(', ');

                    html += `
                        <br>
                        <span style="color:#fbbf24;">
                            Participants Teams non associés à cette classe :
                            ${preview}
                            ${unmatched.length > 5
                                ? ` (+${unmatched.length - 5})`
                                : ''}
                        </span>
                    `;
                }

                showStatus(
                    html,
                    'is-success'
                );
            } catch (error) {
                showStatus(
                    escapeTeamsHtml(
                        error.message
                        || 'Erreur pendant l’import Teams.'
                    ),
                    'is-error'
                );
            } finally {
                teamsImportButton.disabled = false;
            }
        }
    );
});
</script>
@endpush
