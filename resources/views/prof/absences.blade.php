@extends('layouts.prof')

@section('title', 'Présences et absences')
@section('page_title', 'Présences et absences')
@section('breadcrumb', 'Faire l’appel')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-person-check-fill"></i> Suivi des étudiants</span>
        <h1 class="pp-page-title">Faire l’appel</h1>
        <p class="pp-page-description">
            Suivez le parcours <strong>Matière → Niveau → Classe</strong>, choisissez la date, puis indiquez la présence de chaque étudiant.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-clock-history"></i> Consulter l’historique
        </a>
    </div>
</section>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-diagram-3-fill"></i> Parcours pédagogique</h2>
            <p class="pp-panel-subtitle">Sélectionnez d’abord la matière, ensuite le niveau, puis la classe.</p>
        </div>
        <span class="pp-panel-meta">Étape 1</span>
    </header>

    <div class="pp-panel-body">
        <div class="pp-attendance-toolbar">
            <div>
                <label for="attendanceSubject" class="pp-label"><i class="bi bi-journal-bookmark-fill"></i> Matière</label>
                <select id="attendanceSubject" class="adm-form-select">
                    <option value="">Sélectionner une matière</option>
                </select>
            </div>

            <div>
                <label for="attendanceLevel" class="pp-label"><i class="bi bi-layers-fill"></i> Niveau</label>
                <select id="attendanceLevel" class="adm-form-select" disabled>
                    <option value="">Sélectionner d’abord une matière</option>
                </select>
            </div>

            <div>
                <label for="attendanceClass" class="pp-label"><i class="bi bi-people-fill"></i> Classe</label>
                <select id="attendanceClass" class="adm-form-select" disabled>
                    <option value="">Sélectionner d’abord un niveau</option>
                </select>
            </div>

            <div>
                <label for="attendanceDate" class="pp-label"><i class="bi bi-calendar3"></i> Date de la séance</label>
                <input type="date" id="attendanceDate" class="adm-form-control" value="{{ now()->toDateString() }}">
            </div>
        </div>
    </div>
</section>

<section class="pp-panel pp-section-gap">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-people-fill"></i> Liste des étudiants</h2>
            <p class="pp-panel-subtitle" id="attendancePathLabel">Aucun parcours sélectionné.</p>
        </div>
        <div class="pp-attendance-head">
            <span class="pp-student-count" id="studentCount"><i class="bi bi-person"></i> 0 étudiant</span>
            <button type="button" class="adm-btn adm-btn-ghost adm-btn-sm" id="markAllPresent" disabled>
                <i class="bi bi-check2-all"></i> Tous présents
            </button>
        </div>
    </header>

    <form method="POST" action="{{ route('prof.absences.store') }}" id="attendanceForm">
        @csrf
        <input type="hidden" name="subject_id" id="subjectId">
        <input type="hidden" name="level_id" id="levelId">
        <input type="hidden" name="class_id" id="classId">
        <input type="hidden" name="date" id="formDate" value="{{ now()->toDateString() }}">

        <div class="pp-attendance-list" id="studentsTable">
            <div class="pp-empty">
                <div>
                    <span class="pp-empty-icon"><i class="bi bi-diagram-3"></i></span>
                    <h3>Sélectionnez une matière</h3>
                    <p>Commencez par la matière, puis choisissez le niveau et la classe.</p>
                </div>
            </div>
        </div>

        <div class="pp-attendance-footer">
            <span class="pp-panel-meta"><i class="bi bi-info-circle me-1"></i> Une réponse est requise pour chaque étudiant.</span>
            <button type="submit" class="adm-btn adm-btn-success" id="submitBtn" disabled>
                <i class="bi bi-check-circle-fill"></i> Enregistrer les présences
            </button>
        </div>
    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paths = @json($teachingPaths ?? []);
    const subjectSelect = document.getElementById('attendanceSubject');
    const levelSelect = document.getElementById('attendanceLevel');
    const classSelect = document.getElementById('attendanceClass');
    const dateInput = document.getElementById('attendanceDate');
    const formDate = document.getElementById('formDate');
    const subjectId = document.getElementById('subjectId');
    const levelId = document.getElementById('levelId');
    const classId = document.getElementById('classId');
    const list = document.getElementById('studentsTable');
    const submitButton = document.getElementById('submitBtn');
    const markAllButton = document.getElementById('markAllPresent');
    const count = document.getElementById('studentCount');
    const pathLabel = document.getElementById('attendancePathLabel');
    const studentsBaseUrl = @json(url('/prof/class-students'));

    function unique(items, idKey, nameKey) {
        const seen = new Map();
        items.forEach(function (item) {
            const id = String(item[idKey]);
            if (!seen.has(id)) seen.set(id, item[nameKey]);
        });
        return Array.from(seen, function ([id, name]) { return { id, name }; });
    }

    function setOptions(select, placeholder, items) {
        select.innerHTML = '';
        select.add(new Option(placeholder, ''));
        items.forEach(function (item) { select.add(new Option(item.name, item.id)); });
        select.disabled = items.length === 0;
    }

    function resetStudents(messageTitle, messageText) {
        subjectId.value = subjectSelect.value || '';
        levelId.value = levelSelect.value || '';
        classId.value = classSelect.value || '';
        submitButton.disabled = true;
        markAllButton.disabled = true;
        setStudentCount(0);
        list.innerHTML = `
            <div class="pp-empty">
                <div>
                    <span class="pp-empty-icon"><i class="bi bi-diagram-3"></i></span>
                    <h3>${escapeHtml(messageTitle)}</h3>
                    <p>${escapeHtml(messageText)}</p>
                </div>
            </div>`;
    }

    function fillSubjects() {
        setOptions(subjectSelect, 'Sélectionner une matière', unique(paths, 'subject_id', 'subject_name'));
    }

    function fillLevels() {
        const subject = subjectSelect.value;
        const items = unique(
            paths.filter(function (path) { return String(path.subject_id) === subject; }),
            'level_id',
            'level_name'
        );
        setOptions(levelSelect, subject ? 'Sélectionner un niveau' : 'Sélectionner d’abord une matière', items);
        setOptions(classSelect, 'Sélectionner d’abord un niveau', []);
    }

    function fillClasses() {
        const subject = subjectSelect.value;
        const level = levelSelect.value;
        const items = unique(
            paths.filter(function (path) {
                return String(path.subject_id) === subject && String(path.level_id) === level;
            }),
            'class_id',
            'class_name'
        );
        setOptions(classSelect, level ? 'Sélectionner une classe' : 'Sélectionner d’abord un niveau', items);
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function initials(name) {
        return String(name || 'Étudiant')
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map(function (part) { return part.charAt(0).toLocaleUpperCase('fr'); })
            .join('') || 'ET';
    }

    function setStudentCount(value) {
        count.innerHTML = '<i class="bi bi-person"></i> ' + value + ' étudiant' + (value > 1 ? 's' : '');
    }

    function currentPathLabel() {
        const subjectText = subjectSelect.options[subjectSelect.selectedIndex]?.text || '';
        const levelText = levelSelect.options[levelSelect.selectedIndex]?.text || '';
        const classText = classSelect.options[classSelect.selectedIndex]?.text || '';
        return [subjectText, levelText, classText].filter(Boolean).join(' → ');
    }

    dateInput.addEventListener('change', function () {
        formDate.value = dateInput.value;
    });

    markAllButton.addEventListener('click', function () {
        document.querySelectorAll('#studentsTable input[type="radio"][value="1"]').forEach(function (radio) {
            radio.checked = true;
        });
    });

    subjectSelect.addEventListener('change', function () {
        fillLevels();
        subjectId.value = subjectSelect.value;
        levelId.value = '';
        classId.value = '';
        pathLabel.textContent = subjectSelect.value ? subjectSelect.options[subjectSelect.selectedIndex].text : 'Aucun parcours sélectionné.';
        resetStudents('Sélectionnez un niveau', 'Choisissez maintenant le niveau associé à cette matière.');
    });

    levelSelect.addEventListener('change', function () {
        fillClasses();
        subjectId.value = subjectSelect.value;
        levelId.value = levelSelect.value;
        classId.value = '';
        pathLabel.textContent = levelSelect.value ? currentPathLabel() : 'Sélectionnez un niveau.';
        resetStudents('Sélectionnez une classe', 'Choisissez la classe pour afficher les étudiants.');
    });

    classSelect.addEventListener('change', async function () {
        const selectedClass = classSelect.value;
        subjectId.value = subjectSelect.value;
        levelId.value = levelSelect.value;
        classId.value = selectedClass;
        submitButton.disabled = true;
        markAllButton.disabled = true;
        setStudentCount(0);
        pathLabel.textContent = selectedClass ? currentPathLabel() : 'Sélectionnez une classe.';

        if (!selectedClass) {
            resetStudents('Sélectionnez une classe', 'La liste des étudiants sera chargée automatiquement.');
            return;
        }

        list.innerHTML = '<div class="pp-loader"><span class="pp-spinner" aria-label="Chargement"></span></div>';

        try {
            const params = new URLSearchParams({
                subject_id: subjectId.value,
                level_id: levelId.value
            });
            const response = await fetch(studentsBaseUrl + '/' + encodeURIComponent(selectedClass) + '?' + params.toString(), {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Chargement impossible');
            const students = await response.json();

            if (!students.length) {
                list.innerHTML = `
                    <div class="pp-empty">
                        <div>
                            <span class="pp-empty-icon"><i class="bi bi-person-x"></i></span>
                            <h3>Aucun étudiant assigné</h3>
                            <p>Cette classe ne contient actuellement aucun étudiant pour cette matière.</p>
                        </div>
                    </div>`;
                return;
            }

            list.innerHTML = students.map(function (student) {
                const safeName = escapeHtml(student.name);
                return `
                    <div class="pp-attendance-row">
                        <div class="pp-attendance-student">
                            <span class="pp-attendance-avatar">${escapeHtml(initials(student.name))}</span>
                            <strong class="pp-attendance-name">${safeName}</strong>
                        </div>
                        <div class="pp-attendance-options">
                            <label class="pp-attendance-option" style="--option-color:#4ade80;">
                                <input type="radio" name="students[${Number(student.id)}]" value="1" checked required>
                                <span><i class="bi bi-check-circle-fill"></i> Présent</span>
                            </label>
                            <label class="pp-attendance-option" style="--option-color:#f87171;">
                                <input type="radio" name="students[${Number(student.id)}]" value="0" required>
                                <span><i class="bi bi-x-circle-fill"></i> Absent</span>
                            </label>
                        </div>
                    </div>`;
            }).join('');

            setStudentCount(students.length);
            submitButton.disabled = false;
            markAllButton.disabled = false;
        } catch (error) {
            list.innerHTML = `
                <div class="pp-empty">
                    <div>
                        <span class="pp-empty-icon" style="color:#f87171;"><i class="bi bi-exclamation-triangle"></i></span>
                        <h3>Erreur de chargement</h3>
                        <p>Impossible de récupérer les étudiants. Réessayez dans quelques instants.</p>
                    </div>
                </div>`;
        }
    });

    fillSubjects();
});
</script>
@endpush
@endsection
