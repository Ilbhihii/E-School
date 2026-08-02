document.addEventListener('DOMContentLoaded', function () {
    const pathData =
        window.studentAssignmentPathData || null;

    const subjectSelect = document.getElementById(
        'assignmentSubject'
    );

    const levelSelect = document.getElementById(
        'assignmentLevel'
    );

    const classSelect = document.getElementById(
        'assignmentClass'
    );

    const classSelectWrap = document.getElementById(
        'assignmentClassSelectWrap'
    );

    const autoClass = document.getElementById(
        'assignmentClassAuto'
    );

    const autoClassName = document.getElementById(
        'assignmentClassAutoName'
    );

    const autoClassInput = document.getElementById(
        'assignmentClassAutoInput'
    );

    const pathPreview = document.getElementById(
        'assignmentPathPreview'
    );

    const pathSubject = document.getElementById(
        'assignmentPathSubject'
    );

    const pathLevel = document.getElementById(
        'assignmentPathLevel'
    );

    const pathClass = document.getElementById(
        'assignmentPathClass'
    );

    function appendOption(
        select,
        value,
        label,
        selected = false
    ) {
        const option = document.createElement('option');

        option.value = String(value);
        option.textContent = label;
        option.selected = selected;

        select.appendChild(option);
    }

    function selectedText(select) {
        if (
            !select
            || select.selectedIndex < 0
            || !select.value
        ) {
            return '';
        }

        return select.options[
            select.selectedIndex
        ].textContent.trim();
    }

    function currentClassName() {
        if (
            autoClassInput
            && !autoClassInput.disabled
            && autoClassInput.value
        ) {
            return autoClassName
                ? autoClassName.textContent.trim()
                : '';
        }

        return selectedText(classSelect);
    }

    function updatePathPreview() {
        if (
            !pathPreview
            || !pathSubject
            || !pathLevel
            || !pathClass
        ) {
            return;
        }

        const subjectName = selectedText(
            subjectSelect
        );

        const levelName = selectedText(
            levelSelect
        );

        const className = currentClassName();

        const complete =
            subjectName
            && levelName
            && className;

        pathPreview.hidden = !complete;

        if (!complete) {
            return;
        }

        pathSubject.textContent = subjectName;
        pathLevel.textContent = levelName;
        pathClass.textContent = className;
    }

    function showClassSelect(message) {
        if (
            !classSelect
            || !classSelectWrap
            || !autoClass
            || !autoClassInput
        ) {
            return;
        }

        classSelectWrap.hidden = false;
        classSelect.hidden = false;
        classSelect.disabled = true;
        classSelect.innerHTML = '';

        appendOption(
            classSelect,
            '',
            message,
            true
        );

        autoClass.hidden = true;
        autoClassInput.disabled = true;
        autoClassInput.value = '';

        updatePathPreview();
    }

    function showAutomaticClass(classRoom) {
        if (
            !classSelect
            || !classSelectWrap
            || !autoClass
            || !autoClassName
            || !autoClassInput
        ) {
            return;
        }

        classSelect.disabled = true;
        classSelect.hidden = true;
        classSelectWrap.hidden = true;

        autoClassName.textContent = classRoom.name;
        autoClass.hidden = false;

        autoClassInput.value = String(classRoom.id);
        autoClassInput.disabled = false;

        updatePathPreview();
    }

    function showMultipleClasses(
        classes,
        preferredClassId = ''
    ) {
        if (
            !classSelect
            || !classSelectWrap
            || !autoClass
            || !autoClassInput
        ) {
            return;
        }

        classSelectWrap.hidden = false;
        classSelect.hidden = false;
        classSelect.disabled = false;
        classSelect.innerHTML = '';

        appendOption(
            classSelect,
            '',
            'Choisir une classe',
            !preferredClassId
        );

        classes.forEach(function (classRoom) {
            appendOption(
                classSelect,
                classRoom.id,
                classRoom.name,
                String(classRoom.id)
                    === String(preferredClassId)
            );
        });

        autoClass.hidden = true;
        autoClassInput.disabled = true;
        autoClassInput.value = '';

        updatePathPreview();
    }

    function fillClasses(
        preferredClassId = ''
    ) {
        if (
            !pathData
            || !subjectSelect
            || !levelSelect
        ) {
            return;
        }

        const subjectId = subjectSelect.value;
        const levelId = levelSelect.value;

        if (!subjectId) {
            showClassSelect(
                'Choisissez d’abord une matière'
            );

            return;
        }

        if (!levelId) {
            showClassSelect(
                'Choisissez d’abord un niveau'
            );

            return;
        }

        const subjectClasses =
            pathData.classesBySubjectLevel[
                subjectId
            ] || {};

        const classes =
            subjectClasses[levelId] || [];

        if (classes.length === 0) {
            showClassSelect(
                'Aucune classe assignée'
            );

            return;
        }

        if (classes.length === 1) {
            showAutomaticClass(classes[0]);
            return;
        }

        showMultipleClasses(
            classes,
            preferredClassId
        );
    }

    function fillLevels(
        preferredLevelId = '',
        preferredClassId = ''
    ) {
        if (
            !pathData
            || !subjectSelect
            || !levelSelect
        ) {
            return;
        }

        const subjectId = subjectSelect.value;

        levelSelect.innerHTML = '';

        if (!subjectId) {
            levelSelect.disabled = true;

            appendOption(
                levelSelect,
                '',
                'Choisissez d’abord une matière',
                true
            );

            showClassSelect(
                'Choisissez d’abord un niveau'
            );

            return;
        }

        const levels =
            pathData.levelsBySubject[
                subjectId
            ] || [];

        if (levels.length === 0) {
            levelSelect.disabled = true;

            appendOption(
                levelSelect,
                '',
                'Aucun niveau assigné',
                true
            );

            showClassSelect(
                'Aucune classe assignée'
            );

            return;
        }

        levelSelect.disabled = false;

        const preferredExists = levels.some(
            function (level) {
                return String(level.id)
                    === String(preferredLevelId);
            }
        );

        const automaticallySelectedId =
            preferredExists
                ? String(preferredLevelId)
                : (
                    levels.length === 1
                        ? String(levels[0].id)
                        : ''
                );

        appendOption(
            levelSelect,
            '',
            'Choisir un niveau',
            !automaticallySelectedId
        );

        levels.forEach(function (level) {
            appendOption(
                levelSelect,
                level.id,
                level.name,
                String(level.id)
                    === automaticallySelectedId
            );
        });

        fillClasses(preferredClassId);
        updatePathPreview();
    }

    if (
        pathData
        && subjectSelect
        && levelSelect
        && classSelect
    ) {
        subjectSelect.addEventListener(
            'change',
            function () {
                fillLevels();
                updatePathPreview();
            }
        );

        levelSelect.addEventListener(
            'change',
            function () {
                fillClasses();
                updatePathPreview();
            }
        );

        classSelect.addEventListener(
            'change',
            updatePathPreview
        );

        fillLevels(
            pathData.selectedLevelId || '',
            pathData.selectedClassId || ''
        );

        updatePathPreview();
    }

    const dropZone = document.getElementById(
        'assignmentDropZone'
    );

    const fileInput = document.getElementById(
        'assignmentFile'
    );

    const fileName = document.getElementById(
        'assignmentFileName'
    );

    if (!dropZone || !fileInput || !fileName) {
        return;
    }

    function updateFileName(file) {
        if (!file) {
            fileName.textContent =
                'PDF, DOC ou DOCX — maximum 10 Mo';

            return;
        }

        const sizeInMb =
            (file.size / (1024 * 1024)).toFixed(2);

        fileName.textContent =
            file.name + ' · ' + sizeInMb + ' Mo';
    }

    fileInput.addEventListener(
        'change',
        function () {
            updateFileName(
                fileInput.files[0]
            );
        }
    );

    ['dragenter', 'dragover'].forEach(
        function (eventName) {
            dropZone.addEventListener(
                eventName,
                function (event) {
                    event.preventDefault();

                    dropZone.classList.add(
                        'dragging'
                    );
                }
            );
        }
    );

    ['dragleave', 'drop'].forEach(
        function (eventName) {
            dropZone.addEventListener(
                eventName,
                function (event) {
                    event.preventDefault();

                    dropZone.classList.remove(
                        'dragging'
                    );
                }
            );
        }
    );

    dropZone.addEventListener(
        'drop',
        function (event) {
            const files =
                event.dataTransfer.files;

            if (
                !files
                || files.length === 0
            ) {
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
});