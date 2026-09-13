(() => {
    'use strict';

    function boot() {
        const config = window.profDevoirGroupData || {};
        const hierarchy = config.hierarchy || [];

        const subject = document.getElementById(config.subjectId || '');
        const level = document.getElementById(config.levelId || '');
        const classroom = document.getElementById(config.classId || '');
        const slot = document.querySelector(
            config.slotSelector || '[name="class_slot_id"]'
        );
        const course = document.getElementById(config.courseId || '');

        if (!subject || !level || !classroom || !slot) {
            return;
        }

        const selectedSubject = () =>
            hierarchy.find(
                item => String(item.id) === String(subject.value)
            );

        const selectedLevel = () =>
            selectedSubject()?.levels?.find(
                item => String(item.id) === String(level.value)
            );

        const selectedClass = () =>
            selectedLevel()?.classes?.find(
                item => String(item.id) === String(classroom.value)
            );

        const option = (value, label) => {
            const item = document.createElement('option');
            item.value = String(value);
            item.textContent = label;
            return item;
        };

        const selectedSlotCode = () => {
            const found =
                selectedClass()?.slots?.find(
                    item =>
                        String(item.id)
                        === String(slot.value)
                );

            return String(found?.code || '')
                .trim()
                .toUpperCase();
        };

        function refreshCourses() {
            if (!course) {
                return;
            }

            const slotCode = selectedSlotCode();

            Array.from(course.options).forEach(item => {
                if (!item.value) {
                    item.hidden = false;
                    item.disabled = false;
                    return;
                }

                const sameBasePath =
                    String(item.dataset.subject || '')
                        === String(subject.value)
                    && String(item.dataset.level || '')
                        === String(level.value)
                    && String(item.dataset.class || '')
                        === String(classroom.value);

                const courseSlot =
                    String(item.dataset.slot || '')
                        .trim()
                        .toUpperCase();

                const sameGroup =
                    !courseSlot
                    || !slotCode
                    || courseSlot === slotCode;

                item.hidden =
                    !(sameBasePath && sameGroup);

                item.disabled = item.hidden;
            });

            const current =
                course.options[course.selectedIndex];

            if (
                current
                && current.value
                && current.disabled
            ) {
                course.value = '';
            }
        }

        function fillSlots(
            wanted = ''
        ) {
            const classItem = selectedClass();

            slot.innerHTML = '';
            slot.appendChild(
                option('', 'Choisir un groupe')
            );

            (classItem?.slots || []).forEach(item => {
                slot.appendChild(
                    option(
                        item.id,
                        item.code || 'Groupe'
                    )
                );
            });

            slot.disabled = !classItem;

            if (wanted) {
                slot.value = String(wanted);
            }

            refreshCourses();
        }

        const wantedSlot =
            String(config.selectedSlotId || '');

        window.setTimeout(
            () => {
                fillSlots(wantedSlot);
            },
            0
        );

        [subject, level, classroom].forEach(element => {
            element.addEventListener(
                'change',
                () => {
                    window.setTimeout(
                        () => fillSlots(''),
                        0
                    );
                }
            );
        });

        slot.addEventListener(
            'change',
            refreshCourses
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            boot,
            { once: true }
        );
    } else {
        boot();
    }
})();
