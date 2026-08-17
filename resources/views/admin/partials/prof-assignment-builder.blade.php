@php
    $builderId = $builderId ?? 'profAssignmentBuilder';
    $initialAssignments = $initialAssignments ?? [];

    if (!is_array($initialAssignments) || empty($initialAssignments)) {
        $initialAssignments = [[
            'subject_id' => '',
            'level_id' => '',
            'class_id' => '',
            'class_slot_id' => '',
            'weekly_sessions' => 1,
        ]];
    }
@endphp

<div
    id="{{ $builderId }}"
    class="prof-multi-builder"
>
    <div class="prof-multi-builder-head">
        <div>
            <strong>
                Parcours pédagogiques
            </strong>
            <small>
                Chaque ligne correspond à une affectation exacte :
                Matière → Niveau → Classe → Créneau + nombre de séances/semaine.
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

    <div
        class="prof-multi-rows"
        data-assignment-rows
    ></div>

    <div class="prof-multi-help">
        <i class="bi bi-info-circle"></i>
        <span>
            Vous pouvez ajouter plusieurs matières, niveaux, classes et
            créneaux au même professeur. Pour chaque créneau, choisissez de
            1 à 7 séances par semaine. Exemple : I2 avec 2 séances/semaine
            pourra être placé le mardi ET le samedi selon les disponibilités.
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

.prof-multi-field-sessions {
    grid-column: 1 / -1;
    max-width: 270px;
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

    .prof-multi-grid {
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
    const rowsContainer = root.querySelector('[data-assignment-rows]');
    const addButton = root.querySelector('[data-add-assignment]');

    const text = value => value == null ? '' : String(value);

    const makeOption = (value, label, selectedValue = '') => {
        const option = document.createElement('option');
        option.value = text(value);
        option.textContent = label;
        option.selected = text(value) === text(selectedValue);
        return option;
    };

    const resetSelect = (select, placeholder, disabled = true) => {
        select.replaceChildren(
            makeOption('', placeholder)
        );
        select.disabled = disabled;
    };

    const subjectById = id => hierarchy.find(
        subject => text(subject.id) === text(id)
    );

    const levelById = (subject, id) => {
        if (!subject) {
            return null;
        }

        return (subject.levels || []).find(
            level => text(level.id) === text(id)
        );
    };

    const classById = (level, id) => {
        if (!level) {
            return null;
        }

        return (level.classes || []).find(
            classRoom => text(classRoom.id) === text(id)
        );
    };

    const optionLabel = select => {
        if (!select.value || select.selectedIndex < 0) {
            return '';
        }

        return select.options[select.selectedIndex].textContent.trim();
    };

    const updatePath = row => {
        const subject = row.querySelector('.js-prof-subject');
        const level = row.querySelector('.js-prof-level');
        const classRoom = row.querySelector('.js-prof-class');
        const slot = row.querySelector('.js-prof-slot');
        const sessions = row.querySelector('.js-prof-weekly-sessions');
        const preview = row.querySelector('.prof-multi-path');

        const labels = [
            optionLabel(subject),
            optionLabel(level),
            optionLabel(classRoom),
            optionLabel(slot),
        ].filter(Boolean);

        const sessionCount = Math.max(
            1,
            Number(sessions?.value || 1)
        );

        preview.textContent = labels.length
            ? labels.join(' → ')
                + ` · ${sessionCount} séance${sessionCount > 1 ? 's' : ''}/sem.`
            : 'Matière → Niveau → Classe → Créneau';

        preview.classList.toggle(
            'is-complete',
            Boolean(
                subject.value
                && level.value
                && classRoom.value
                && slot.value
            )
        );
    };

    const fillSlots = (row, selectedId = '') => {
        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');
        const slotSelect = row.querySelector('.js-prof-slot');

        const subject = subjectById(subjectSelect.value);
        const level = levelById(subject, levelSelect.value);
        const classRoom = classById(level, classSelect.value);

        resetSelect(
            slotSelect,
            classRoom
                ? 'Sélectionner un créneau'
                : 'Choisissez d’abord une classe',
            !classRoom
        );

        if (classRoom) {
            (classRoom.slots || []).forEach(slot => {
                slotSelect.appendChild(
                    makeOption(
                        slot.id,
                        slot.code || slot.name || 'Créneau',
                        selectedId
                    )
                );
            });

            slotSelect.disabled = false;
            slotSelect.value = text(selectedId);
        }

        updatePath(row);
    };

    const fillClasses = (
        row,
        selectedClassId = '',
        selectedSlotId = ''
    ) => {
        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');
        const slotSelect = row.querySelector('.js-prof-slot');

        const subject = subjectById(subjectSelect.value);
        const level = levelById(subject, levelSelect.value);

        resetSelect(
            classSelect,
            level
                ? 'Sélectionner une classe'
                : 'Choisissez d’abord un niveau',
            !level
        );

        resetSelect(
            slotSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (level) {
            (level.classes || []).forEach(classRoom => {
                classSelect.appendChild(
                    makeOption(
                        classRoom.id,
                        classRoom.name,
                        selectedClassId
                    )
                );
            });

            classSelect.disabled = false;
            classSelect.value = text(selectedClassId);

            if (selectedClassId) {
                fillSlots(row, selectedSlotId);
            }
        }

        updatePath(row);
    };

    const fillLevels = (
        row,
        selectedLevelId = '',
        selectedClassId = '',
        selectedSlotId = ''
    ) => {
        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');
        const slotSelect = row.querySelector('.js-prof-slot');

        const subject = subjectById(subjectSelect.value);

        resetSelect(
            levelSelect,
            subject
                ? 'Sélectionner un niveau'
                : 'Choisissez d’abord une matière',
            !subject
        );

        resetSelect(
            classSelect,
            'Choisissez d’abord un niveau',
            true
        );

        resetSelect(
            slotSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (subject) {
            (subject.levels || []).forEach(level => {
                levelSelect.appendChild(
                    makeOption(
                        level.id,
                        level.name,
                        selectedLevelId
                    )
                );
            });

            levelSelect.disabled = false;
            levelSelect.value = text(selectedLevelId);

            if (selectedLevelId) {
                fillClasses(
                    row,
                    selectedClassId,
                    selectedSlotId
                );
            }
        }

        updatePath(row);
    };

    const reindex = () => {
        const rows = [...rowsContainer.querySelectorAll('.prof-multi-row')];

        rows.forEach((row, index) => {
            row.dataset.index = String(index);
            row.querySelector('.prof-multi-row-number').textContent = String(index + 1);
            row.querySelector('.js-prof-subject').name = `assignments[${index}][subject_id]`;
            row.querySelector('.js-prof-level').name = `assignments[${index}][level_id]`;
            row.querySelector('.js-prof-class').name = `assignments[${index}][class_id]`;
            row.querySelector('.js-prof-slot').name = `assignments[${index}][class_slot_id]`;
            row.querySelector('.js-prof-weekly-sessions').name = `assignments[${index}][weekly_sessions]`;
        });

        rows.forEach(row => {
            row.querySelector('[data-remove-assignment]').disabled = rows.length <= 1;
        });
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
                    <label>Créneau / groupe *</label>
                    <select class="adm-form-select js-prof-slot" required disabled></select>
                </div>

                <div class="prof-multi-field prof-multi-field-sessions">
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
                Matière → Niveau → Classe → Créneau
            </div>
        `;

        rowsContainer.appendChild(row);

        const subjectSelect = row.querySelector('.js-prof-subject');
        const levelSelect = row.querySelector('.js-prof-level');
        const classSelect = row.querySelector('.js-prof-class');
        const slotSelect = row.querySelector('.js-prof-slot');
        const sessionsSelect = row.querySelector('.js-prof-weekly-sessions');

        sessionsSelect.value = text(data.weekly_sessions || 1);

        subjectSelect.appendChild(
            makeOption('', 'Sélectionner une matière')
        );

        hierarchy.forEach(subject => {
            subjectSelect.appendChild(
                makeOption(
                    subject.id,
                    subject.name,
                    data.subject_id || ''
                )
            );
        });

        subjectSelect.value = text(data.subject_id || '');

        resetSelect(
            levelSelect,
            'Choisissez d’abord une matière',
            true
        );
        resetSelect(
            classSelect,
            'Choisissez d’abord un niveau',
            true
        );
        resetSelect(
            slotSelect,
            'Choisissez d’abord une classe',
            true
        );

        if (subjectSelect.value) {
            fillLevels(
                row,
                data.level_id || '',
                data.class_id || '',
                data.class_slot_id || ''
            );
        }

        subjectSelect.addEventListener('change', () => {
            fillLevels(row);
        });

        levelSelect.addEventListener('change', () => {
            fillClasses(row);
        });

        classSelect.addEventListener('change', () => {
            fillSlots(row);
        });

        slotSelect.addEventListener('change', () => {
            updatePath(row);
        });

        sessionsSelect.addEventListener('change', () => {
            updatePath(row);
        });

        row.querySelector('[data-remove-assignment]')
            .addEventListener('click', () => {
                row.remove();
                reindex();
            });

        updatePath(row);
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
