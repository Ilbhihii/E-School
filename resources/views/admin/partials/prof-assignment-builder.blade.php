@php
    $builderId = $builderId ?? 'profAssignmentBuilder';
    $initialAssignments = $initialAssignments ?? [];
    $professorTimeSlotMap = $professorTimeSlotMap ?? [];

    if (!is_array($initialAssignments) || empty($initialAssignments)) {
        $initialAssignments = [[
            'subject_id' => '',
            'level_id' => '',
            'class_id' => '',
            'assignment_day_of_week' => '',
            'assignment_start_time' => '',
            'weekly_sessions' => 1,
        ]];
    }
@endphp

<div id="{{ $builderId }}" class="prof-multi-builder">
    <div class="prof-multi-builder-head">
        <div>
            <strong>Parcours pédagogiques</strong>
            <small>
                Même logique que l’assignation des étudiants :
                Matière → Niveau → Classe → Jour/Heure → Groupe automatique.
            </small>
        </div>

        <button
            type="button"
            class="adm-btn adm-btn-ghost adm-btn-sm"
            data-add-assignment
        >
            <i class="bi bi-plus-circle"></i>
            Ajouter un parcours
        </button>
    </div>

    <div class="prof-multi-rows" data-assignment-rows></div>

    <div class="prof-multi-help">
        <i class="bi bi-info-circle"></i>
        <span>
            Le groupe n’est plus choisi manuellement. Il dépend du rang chronologique de l’heure dans la journée :
            <strong>08:00 → D1</strong>, <strong>08:30 → D2</strong>,
            <strong>08:45 → D3</strong>, <strong>09:00 → D4</strong>, puis D5, D6… sans limite fixe.
            Le même principe s’applique aux groupes I1, I2… et A1, A2… selon la classe.
        </span>
    </div>
</div>

<style>
.prof-multi-builder {
    display: grid;
    gap: 12px;
}

.prof-multi-builder-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 13px;
    border: 1px solid rgba(99, 102, 241, .13);
    border-radius: 13px;
    background: rgba(99, 102, 241, .045);
}

.prof-multi-builder-head strong,
.prof-multi-builder-head small {
    display: block;
}

.prof-multi-builder-head strong {
    color: var(--adm-text);
    font-size: .72rem;
}

.prof-multi-builder-head small {
    margin-top: 3px;
    color: var(--adm-text-muted);
    font-size: .59rem;
    line-height: 1.5;
}

.prof-multi-rows {
    display: grid;
    gap: 10px;
}

.prof-multi-row {
    position: relative;
    padding: 13px;
    border: 1px solid rgba(255, 255, 255, .055);
    border-radius: 14px;
    background: rgba(7, 15, 30, .31);
}

.prof-multi-row-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 11px;
}

.prof-multi-row-title {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #C4B5FD;
    font-size: .65rem;
    font-weight: 800;
}

.prof-multi-row-number {
    display: grid;
    width: 24px;
    height: 24px;
    place-items: center;
    border: 1px solid rgba(139, 92, 246, .22);
    border-radius: 8px;
    background: rgba(124, 58, 237, .10);
}

.prof-multi-remove {
    width: 31px;
    height: 31px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(244, 63, 94, .16);
    border-radius: 9px;
    color: #FDA4AF;
    background: rgba(244, 63, 94, .055);
    cursor: pointer;
}

.prof-multi-remove:disabled {
    opacity: .32;
    cursor: not-allowed;
}

.prof-multi-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.prof-multi-field label {
    display: block;
    margin-bottom: 5px;
    color: var(--adm-text-muted);
    font-size: .58rem;
    font-weight: 780;
    text-transform: uppercase;
    letter-spacing: .035em;
}

.prof-time-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.prof-time-help {
    display: block;
    margin-top: 6px;
    color: var(--adm-text-muted);
    font-size: .56rem;
    line-height: 1.45;
}

.prof-auto-group {
    min-height: 38px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border: 1px solid rgba(34, 197, 94, .14);
    border-radius: 9px;
    background: rgba(34, 197, 94, .04);
}

.prof-auto-group strong {
    color: #86EFAC;
    font-size: .72rem;
}

.prof-auto-group span {
    color: var(--adm-text-muted);
    font-size: .58rem;
}

.prof-multi-path {
    margin-top: 10px;
    padding: 7px 9px;
    overflow: hidden;
    color: rgba(255, 255, 255, .48);
    border: 1px solid rgba(255, 255, 255, .045);
    border-radius: 9px;
    background: rgba(255, 255, 255, .018);
    font-size: .57rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.prof-multi-path.is-complete {
    color: #A7F3D0;
    border-color: rgba(34, 197, 94, .11);
    background: rgba(34, 197, 94, .035);
}

.prof-multi-help {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 11px;
    color: var(--adm-text-muted);
    border: 1px solid rgba(34, 197, 94, .10);
    border-radius: 11px;
    background: rgba(34, 197, 94, .035);
    font-size: .59rem;
    line-height: 1.55;
}

.prof-multi-help i {
    margin-top: 1px;
    color: #4ADE80;
}

@media (max-width: 767.98px) {
    .prof-multi-builder-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .prof-multi-grid,
    .prof-time-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById(@json($builderId));

    if (!root) {
        return;
    }

    const hierarchy = @json($assignmentHierarchy);
    const initialRows = @json($initialAssignments);
    const knownTimeMap = @json($professorTimeSlotMap);
    const rowsContainer = root.querySelector('[data-assignment-rows]');
    const addButton = root.querySelector('[data-add-assignment]');

    const text = value => value == null ? '' : String(value);

    const dayLabels = {
        '1': 'Lundi',
        '2': 'Mardi',
        '3': 'Mercredi',
        '4': 'Jeudi',
        '5': 'Vendredi',
        '6': 'Samedi',
        '7': 'Dimanche',
    };

    const makeOption = (value, label, selectedValue = '') => {
        const option = document.createElement('option');
        option.value = text(value);
        option.textContent = label;
        option.selected = text(value) === text(selectedValue);
        return option;
    };

    const resetSelect = (select, placeholder, disabled = true) => {
        select.replaceChildren(makeOption('', placeholder));
        select.disabled = disabled;
    };

    const subjectById = id => hierarchy.find(
        subject => text(subject.id) === text(id)
    );

    const levelById = (subject, id) => (
        subject?.levels || []
    ).find(level => text(level.id) === text(id)) || null;

    const optionLabel = select => {
        if (!select || !select.value || select.selectedIndex < 0) {
            return '';
        }

        return select.options[select.selectedIndex].textContent.trim();
    };

    const clean = value => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    const classPrefix = row => {
        const classRoom = row.querySelector('.js-prof-class');
        const normalized = clean(optionLabel(classRoom)).toLowerCase();

        if (normalized.includes('debut')) {
            return 'D';
        }

        if (normalized.includes('inter')) {
            return 'I';
        }

        if (normalized.includes('avance') || normalized.includes('adulte')) {
            return 'A';
        }

        const fallback = normalized
            .replace(/[^a-z0-9]/g, '')
            .charAt(0)
            .toUpperCase();

        return fallback || 'G';
    };

    const validTime = value => {
        const match = String(value || '').match(/^(\d{2}):(\d{2})$/);

        if (!match) {
            return false;
        }

        const hour = Number(match[1]);
        const minute = Number(match[2]);
        const total = hour * 60 + minute;

        return hour >= 0
            && hour <= 23
            && minute >= 0
            && minute <= 59
            && total >= 8 * 60
            && total <= 22 * 60;
    };

    /*
     * Le calcul inclut aussi les autres lignes non encore enregistrées
     * du formulaire. Deux nouvelles heures saisies en même temps reçoivent
     * donc déjà leur rang final correct dans la prévisualisation.
     */
    const previewSlotNumber = (selectedDay, selectedTime) => {
        const dayMap = knownTimeMap[String(selectedDay)]
            || knownTimeMap[Number(selectedDay)]
            || {};

        const times = Object.keys(dayMap);
        times.push('08:00');

        rowsContainer.querySelectorAll('.prof-multi-row').forEach(row => {
            const day = row.querySelector('.js-prof-day')?.value || '';
            const time = row.querySelector('.js-prof-time')?.value || '';

            if (String(day) === String(selectedDay) && validTime(time)) {
                times.push(time);
            }
        });

        times.push(selectedTime);

        const unique = Array.from(
            new Set(times.filter(validTime))
        ).sort();

        const index = unique.indexOf(selectedTime);
        return index >= 0 ? index + 1 : null;
    };

    const automaticGroup = row => {
        const subject = row.querySelector('.js-prof-subject');
        const level = row.querySelector('.js-prof-level');
        const classRoom = row.querySelector('.js-prof-class');
        const day = row.querySelector('.js-prof-day');
        const time = row.querySelector('.js-prof-time');

        if (
            !subject.value
            || !level.value
            || !classRoom.value
            || !day.value
            || !validTime(time.value)
        ) {
            return '';
        }

        const number = previewSlotNumber(day.value, time.value);
        return number ? classPrefix(row) + number : '';
    };

    const updatePath = row => {
        const subject = row.querySelector('.js-prof-subject');
        const level = row.querySelector('.js-prof-level');
        const classRoom = row.querySelector('.js-prof-class');
        const day = row.querySelector('.js-prof-day');
        const time = row.querySelector('.js-prof-time');
        const preview = row.querySelector('.prof-multi-path');
        const groupTarget = row.querySelector('.js-prof-auto-group-code');

        const groupCode = automaticGroup(row);
        groupTarget.textContent = groupCode || '—';

        const path = [
            optionLabel(subject),
            optionLabel(level),
            optionLabel(classRoom),
        ].filter(Boolean);

        if (day.value && time.value) {
            path.push(`${dayLabels[day.value] || 'Jour'} · ${time.value}`);
        }

        if (groupCode) {
            path.push(`Groupe ${groupCode}`);
        }

        preview.textContent = path.length
            ? path.join(' → ')
            : 'Matière → Niveau → Classe → Jour/Heure → Groupe automatique';

        preview.classList.toggle(
            'is-complete',
            Boolean(
                subject.value
                && level.value
                && classRoom.value
                && day.value
                && validTime(time.value)
                && groupCode
            )
        );
    };

    const refreshAllRows = () => {
        rowsContainer.querySelectorAll('.prof-multi-row').forEach(updatePath);
    };

    const fillClasses = (row, selectedClassId = '') => {
        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');

        const subject = subjectById(subjectSelect.value);
        const level = levelById(subject, levelSelect.value);

        resetSelect(
            classSelect,
            level ? 'Sélectionner une classe' : 'Choisissez d’abord un niveau',
            !level
        );

        if (level) {
            (level.classes || []).forEach(classRoom => {
                classSelect.appendChild(
                    makeOption(classRoom.id, classRoom.name, selectedClassId)
                );
            });

            classSelect.disabled = false;
            classSelect.value = text(selectedClassId);
        }

        refreshAllRows();
    };

    const fillLevels = (
        row,
        selectedLevelId = '',
        selectedClassId = ''
    ) => {
        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');
        const subject = subjectById(subjectSelect.value);

        resetSelect(
            levelSelect,
            subject ? 'Sélectionner un niveau' : 'Choisissez d’abord une matière',
            !subject
        );
        resetSelect(classSelect, 'Choisissez d’abord un niveau', true);

        if (subject) {
            (subject.levels || []).forEach(level => {
                levelSelect.appendChild(
                    makeOption(level.id, level.name, selectedLevelId)
                );
            });

            levelSelect.disabled = false;
            levelSelect.value = text(selectedLevelId);

            if (selectedLevelId) {
                fillClasses(row, selectedClassId);
            }
        }

        refreshAllRows();
    };

    const reindex = () => {
        const rows = [...rowsContainer.querySelectorAll('.prof-multi-row')];

        rows.forEach((row, index) => {
            row.dataset.index = String(index);
            row.querySelector('.prof-multi-row-number').textContent = String(index + 1);
            row.querySelector('.js-prof-subject').name = `assignments[${index}][subject_id]`;
            row.querySelector('.js-prof-level').name = `assignments[${index}][level_id]`;
            row.querySelector('.js-prof-class').name = `assignments[${index}][class_id]`;
            row.querySelector('.js-prof-day').name = `assignments[${index}][assignment_day_of_week]`;
            row.querySelector('.js-prof-time').name = `assignments[${index}][assignment_start_time]`;
            row.querySelector('.js-prof-weekly-sessions').name = `assignments[${index}][weekly_sessions]`;
        });

        rows.forEach(row => {
            row.querySelector('[data-remove-assignment]').disabled = rows.length <= 1;
        });

        refreshAllRows();
    };

    const addRow = (data = {}) => {
        const row = document.createElement('div');
        row.className = 'prof-multi-row';

        row.innerHTML = `
            <div class="prof-multi-row-head">
                <span class="prof-multi-row-title">
                    <span class="prof-multi-row-number">1</span>
                    Affectation
                </span>

                <button
                    type="button"
                    class="prof-multi-remove"
                    data-remove-assignment
                    title="Retirer cette ligne"
                >
                    <i class="bi bi-trash3"></i>
                </button>
            </div>

            <div class="prof-multi-grid">
                <div class="prof-multi-field">
                    <label>Matière *</label>
                    <select class="adm-form-select js-prof-subject" required></select>
                </div>

                <div class="prof-multi-field">
                    <label>Niveau *</label>
                    <select class="adm-form-select js-prof-level" required disabled></select>
                </div>

                <div class="prof-multi-field">
                    <label>Classe *</label>
                    <select class="adm-form-select js-prof-class" required disabled></select>
                </div>

                <div class="prof-multi-field">
                    <label>Jour et heure *</label>
                    <div class="prof-time-grid">
                        <select class="adm-form-select js-prof-day" required>
                            <option value="">Choisir un jour</option>
                            <option value="1">Lundi</option>
                            <option value="2">Mardi</option>
                            <option value="3">Mercredi</option>
                            <option value="4">Jeudi</option>
                            <option value="5">Vendredi</option>
                            <option value="6">Samedi</option>
                            <option value="7">Dimanche</option>
                        </select>
                        <input
                            type="time"
                            class="adm-form-control js-prof-time"
                            min="08:00"
                            max="22:00"
                            step="60"
                            required
                        >
                    </div>
                    <small class="prof-time-help">
                        L’horaire détermine automatiquement le groupe.
                    </small>
                </div>

                <div class="prof-multi-field">
                    <label>Groupe automatique</label>
                    <div class="prof-auto-group">
                        <strong class="js-prof-auto-group-code">—</strong>
                        <span>calculé depuis l’horaire</span>
                    </div>
                </div>

                <div class="prof-multi-field">
                    <label>Séances par semaine *</label>
                    <select class="adm-form-select js-prof-weekly-sessions" required>
                        <option value="1">1 séance / semaine</option>
                        <option value="2">2 séances / semaine</option>
                        <option value="3">3 séances / semaine</option>
                        <option value="4">4 séances / semaine</option>
                        <option value="5">5 séances / semaine</option>
                        <option value="6">6 séances / semaine</option>
                        <option value="7">7 séances / semaine</option>
                    </select>
                </div>
            </div>

            <div class="prof-multi-path">
                Matière → Niveau → Classe → Jour/Heure → Groupe automatique
            </div>
        `;

        rowsContainer.appendChild(row);

        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');
        const daySelect = row.querySelector('.js-prof-day');
        const timeInput = row.querySelector('.js-prof-time');
        const sessionsSelect = row.querySelector('.js-prof-weekly-sessions');

        subjectSelect.appendChild(makeOption('', 'Sélectionner une matière'));

        hierarchy.forEach(subject => {
            subjectSelect.appendChild(
                makeOption(subject.id, subject.name, data.subject_id || '')
            );
        });

        subjectSelect.value = text(data.subject_id || '');
        daySelect.value = text(data.assignment_day_of_week || '');
        timeInput.value = text(data.assignment_start_time || '').slice(0, 5);
        sessionsSelect.value = text(data.weekly_sessions || 1);

        resetSelect(levelSelect, 'Choisissez d’abord une matière', true);
        resetSelect(classSelect, 'Choisissez d’abord un niveau', true);

        if (subjectSelect.value) {
            fillLevels(
                row,
                data.level_id || '',
                data.class_id || ''
            );
        }

        subjectSelect.addEventListener('change', () => fillLevels(row));
        levelSelect.addEventListener('change', () => fillClasses(row));
        classSelect.addEventListener('change', refreshAllRows);
        daySelect.addEventListener('change', refreshAllRows);
        timeInput.addEventListener('input', refreshAllRows);
        timeInput.addEventListener('change', refreshAllRows);
        sessionsSelect.addEventListener('change', refreshAllRows);

        row.querySelector('[data-remove-assignment]').addEventListener('click', () => {
            row.remove();
            reindex();
        });

        reindex();
    };

    addButton.addEventListener('click', () => addRow());

    if (Array.isArray(initialRows) && initialRows.length) {
        initialRows.forEach(row => addRow(row || {}));
    } else {
        addRow();
    }
});
</script>
