(function () {
    function init() {
        const d = window.studentAssignmentPathData || {};

        const panel = document.getElementById('sendAssignmentPanel');
        const form = panel ? panel.querySelector('form') : null;

        const subject = document.getElementById('assignmentSubject');
        const level = document.getElementById('assignmentLevel');
        const cls = document.getElementById('assignmentClass');
        const clsWrap = document.getElementById('assignmentClassSelectWrap');
        const autoCls = document.getElementById('assignmentClassAuto');
        const autoInput = document.getElementById('assignmentClassAutoInput');
        const slot = document.getElementById('assignmentSlot');
        const title = document.getElementById('assignmentTitle');
        const sourceAssignment = document.getElementById(
            'assignmentSourceProfessorId'
        );
        const autoNotice = document.getElementById(
            'assignmentAutoFillNotice'
        );
        const autoNoticeTitle = document.getElementById(
            'assignmentAutoFillTitle'
        );

        const preview = document.getElementById('assignmentPathPreview');
        const pSubject = document.getElementById('assignmentPathSubject');
        const pLevel = document.getElementById('assignmentPathLevel');
        const pClass = document.getElementById('assignmentPathClass');
        const pSlot = document.getElementById('assignmentPathSlot');

        if (!subject || !level || !cls || !slot) {
            return;
        }

        if (clsWrap) clsWrap.hidden = false;
        if (autoCls) autoCls.hidden = true;

        if (autoInput) {
            autoInput.disabled = true;
            autoInput.value = '';
        }

        cls.hidden = false;

        const add = (select, value, label, selected = false) => {
            const option = document.createElement('option');
            option.value = String(value);
            option.textContent = label;
            option.selected = selected;
            select.appendChild(option);
        };

        const selectedText = select =>
            select
            && select.value
            && select.selectedIndex >= 0
                ? select.options[
                    select.selectedIndex
                ].textContent.trim()
                : '';

        function updatePreview() {
            const complete =
                subject.value
                && level.value
                && cls.value
                && slot.value;

            if (preview) {
                preview.hidden = !complete;
            }

            if (complete) {
                pSubject.textContent = selectedText(subject);
                pLevel.textContent = selectedText(level);
                pClass.textContent = selectedText(cls);
                pSlot.textContent = selectedText(slot);
            }
        }

        function fillSlots(wanted = '') {
            slot.innerHTML = '';
            add(slot, '', 'Choisir un groupe');

            const opts =
                (((d.slotsByPath || {})[
                    String(subject.value)
                ] || {})[
                    String(level.value)
                ] || {})[
                    String(cls.value)
                ] || [];

            opts.forEach(item => {
                add(
                    slot,
                    item.id,
                    item.code,
                    String(item.id) === String(wanted)
                );
            });

            slot.disabled =
                !cls.value
                || opts.length === 0;

            if (wanted) {
                slot.value = String(wanted);
            }

            updatePreview();
        }

        function fillClasses(
            wanted = '',
            wantedSlot = ''
        ) {
            cls.innerHTML = '';
            add(cls, '', 'Choisir une classe');

            const opts =
                ((d.classesBySubjectLevel || {})[
                    String(subject.value)
                ] || {})[
                    String(level.value)
                ] || [];

            opts.forEach(item => {
                add(
                    cls,
                    item.id,
                    item.name,
                    String(item.id) === String(wanted)
                );
            });

            cls.disabled =
                !level.value
                || opts.length === 0;

            if (wanted) {
                cls.value = String(wanted);
            }

            fillSlots(wantedSlot);
            updatePreview();
        }

        function fillLevels(
            wanted = '',
            wantedClass = '',
            wantedSlot = ''
        ) {
            level.innerHTML = '';
            add(level, '', 'Choisir un niveau');

            const opts =
                (d.levelsBySubject || {})[
                    String(subject.value)
                ] || [];

            opts.forEach(item => {
                add(
                    level,
                    item.id,
                    item.name,
                    String(item.id) === String(wanted)
                );
            });

            level.disabled =
                !subject.value
                || opts.length === 0;

            if (wanted) {
                level.value = String(wanted);
            }

            fillClasses(
                wantedClass,
                wantedSlot
            );

            updatePreview();
        }

        function setLocked(locked) {
            if (title) {
                title.readOnly = locked;
                title.classList.toggle(
                    'assignment-auto-locked',
                    locked
                );
            }

            [subject, level, cls, slot]
                .forEach(select => {
                    select.classList.toggle(
                        'assignment-auto-locked',
                        locked
                    );
                    select.setAttribute(
                        'aria-readonly',
                        locked ? 'true' : 'false'
                    );
                });

            if (form) {
                form.classList.toggle(
                    'is-prof-assignment-mode',
                    locked
                );
            }
        }

        function selectProfessorAssignment(button) {
            if (!button) {
                return;
            }

            const assignmentId =
                button.dataset.assignmentId || '';

            const assignmentTitle =
                button.dataset.assignmentTitle || '';

            const subjectId =
                button.dataset.subjectId || '';

            const levelId =
                button.dataset.levelId || '';

            const classId =
                button.dataset.classId || '';

            const slotId =
                button.dataset.slotId || '';

            if (sourceAssignment) {
                sourceAssignment.value = assignmentId;
            }

            if (title) {
                title.value = assignmentTitle;
            }

            subject.value = String(subjectId);

            fillLevels(
                levelId,
                classId,
                slotId
            );

            setLocked(true);

            if (autoNotice) {
                autoNotice.hidden = false;
            }

            if (autoNoticeTitle) {
                autoNoticeTitle.textContent =
                    assignmentTitle;
            }

            updatePreview();

            if (panel) {
                panel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }

            window.setTimeout(() => {
                const file =
                    document.getElementById(
                        'assignmentFile'
                    );

                if (file) {
                    file.focus({
                        preventScroll: true,
                    });
                }
            }, 450);
        }

        function manualMode() {
            if (sourceAssignment) {
                sourceAssignment.value = '';
            }

            setLocked(false);

            if (autoNotice) {
                autoNotice.hidden = true;
            }

            if (panel) {
                panel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }
        }

        subject.addEventListener(
            'change',
            () => {
                if (
                    sourceAssignment
                    && sourceAssignment.value
                ) {
                    return;
                }

                fillLevels();
            }
        );

        level.addEventListener(
            'change',
            () => {
                if (
                    sourceAssignment
                    && sourceAssignment.value
                ) {
                    return;
                }

                fillClasses();
            }
        );

        cls.addEventListener(
            'change',
            () => {
                if (
                    sourceAssignment
                    && sourceAssignment.value
                ) {
                    return;
                }

                fillSlots();
            }
        );

        slot.addEventListener(
            'change',
            updatePreview
        );

        if (d.selectedSubjectId) {
            subject.value =
                String(d.selectedSubjectId);
        }

        fillLevels(
            d.selectedLevelId || '',
            d.selectedClassId || '',
            d.selectedSlotId || ''
        );

        document
            .querySelectorAll(
                '[data-submit-prof-assignment]'
            )
            .forEach(button => {
                button.addEventListener(
                    'click',
                    event => {
                        event.preventDefault();
                        selectProfessorAssignment(
                            button
                        );
                    }
                );
            });

        document
            .querySelectorAll(
                '[data-manual-assignment-submit]'
            )
            .forEach(button => {
                button.addEventListener(
                    'click',
                    event => {
                        event.preventDefault();
                        manualMode();
                    }
                );
            });

        if (d.selectedProfAssignmentId) {
            const previousButton =
                document.querySelector(
                    '[data-submit-prof-assignment]'
                    + '[data-assignment-id="'
                    + CSS.escape(
                        String(
                            d.selectedProfAssignmentId
                        )
                    )
                    + '"]'
                );

            if (previousButton) {
                selectProfessorAssignment(
                    previousButton
                );
            }
        }

        const dropZone =
            document.getElementById(
                'assignmentDropZone'
            );

        const fileInput =
            document.getElementById(
                'assignmentFile'
            );

        const fileName =
            document.getElementById(
                'assignmentFileName'
            );

        if (
            dropZone
            && fileInput
            && fileName
        ) {
            const updateFileName = file => {
                fileName.textContent = file
                    ? `${file.name} · ${
                        (
                            file.size
                            / (1024 * 1024)
                        ).toFixed(2)
                    } Mo`
                    : 'PDF, DOCX, JPG, PNG, MP3, MP4… — maximum 100 Mo';
            };

            fileInput.addEventListener(
                'change',
                () => updateFileName(
                    fileInput.files[0]
                )
            );

            ['dragenter', 'dragover']
                .forEach(eventName => {
                    dropZone.addEventListener(
                        eventName,
                        event => {
                            event.preventDefault();
                            dropZone.classList.add(
                                'dragging'
                            );
                        }
                    );
                });

            ['dragleave', 'drop']
                .forEach(eventName => {
                    dropZone.addEventListener(
                        eventName,
                        event => {
                            event.preventDefault();
                            dropZone.classList.remove(
                                'dragging'
                            );
                        }
                    );
                });

            dropZone.addEventListener(
                'drop',
                event => {
                    const files =
                        event.dataTransfer.files;

                    if (!files || !files.length) {
                        return;
                    }

                    const transfer =
                        new DataTransfer();

                    transfer.items.add(files[0]);
                    fileInput.files =
                        transfer.files;

                    updateFileName(files[0]);
                }
            );
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            init,
            { once: true }
        );
    } else {
        init();
    }
})();
