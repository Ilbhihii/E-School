(() => {
    'use strict';

    const ONE_GIB = 1024 * 1024 * 1024;

    function humanSize(bytes) {
        const gb = bytes / (1024 * 1024 * 1024);

        if (gb >= 1) {
            return `${gb.toFixed(2)} Go`;
        }

        const mb = bytes / (1024 * 1024);
        return `${mb.toFixed(1)} Mo`;
    }

    function install(input) {
        if (!input || input.dataset.upload1gbReady === '1') {
            return;
        }

        input.dataset.upload1gbReady = '1';

        const info = document.createElement('small');
        info.className = 'course-upload-size-info';
        info.style.display = 'block';
        info.style.marginTop = '6px';
        info.style.fontSize = '.72rem';
        info.style.color = 'var(--adm-text-muted, #94a3b8)';

        input.insertAdjacentElement('afterend', info);

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];

            if (!file) {
                info.textContent = '';
                return;
            }

            if (file.size > ONE_GIB) {
                const label =
                    input.name === 'pdf'
                        ? 'Le document PDF'
                        : 'La vidéo';

                alert(
                    `${label} dépasse 1 Go (${humanSize(file.size)}). `
                    + 'Choisissez un fichier de 1 Go maximum.'
                );

                input.value = '';
                info.textContent = '';
                return;
            }

            info.textContent =
                `${file.name} — ${humanSize(file.size)} / 1 Go`;
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document
            .querySelectorAll(
                'input[type="file"][name="video"], '
                + 'input[type="file"][name="pdf"]'
            )
            .forEach(install);
    });
})();
