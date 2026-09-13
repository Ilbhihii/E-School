(() => {
    'use strict';

    const MAX_BYTES = 100 * 1024 * 1024;

    function human(bytes) {
        return `${(bytes / (1024 * 1024)).toFixed(1)} Mo`;
    }

    function install(button) {
        const targetId = button.dataset.extraFilesTarget;
        const input = document.getElementById(targetId);
        const list = document.querySelector(
            `[data-extra-files-list="${targetId}"]`
        );

        if (!input) {
            return;
        }

        button.addEventListener('click', () => input.click());

        input.addEventListener('change', () => {
            const files = Array.from(input.files || []);

            if (!files.length) {
                if (list) {
                    list.innerHTML = '';
                }
                return;
            }

            const tooLarge = files.find(
                file => file.size > MAX_BYTES
            );

            if (tooLarge) {
                alert(
                    `${tooLarge.name} dépasse 100 Mo `
                    + `(${human(tooLarge.size)}).`
                );
                input.value = '';

                if (list) {
                    list.innerHTML = '';
                }

                return;
            }

            if (list) {
                list.innerHTML = files
                    .map(
                        file =>
                            `<span class="adm-badge adm-badge-info" `
                            + `style="margin:4px 6px 0 0;">`
                            + `<i class="bi bi-paperclip"></i> `
                            + `${escapeHtml(file.name)} · ${human(file.size)}`
                            + `</span>`
                    )
                    .join('');
            }

            button.innerHTML =
                `<i class="bi bi-plus-circle-fill"></i> `
                + `Ajouter encore des fichiers `
                + `(${files.length} sélectionné`
                + `${files.length > 1 ? 's' : ''})`;
        });
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document
            .querySelectorAll('[data-extra-files-target]')
            .forEach(install);
    });
})();
