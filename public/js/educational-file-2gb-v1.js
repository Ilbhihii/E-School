(() => {
    'use strict';

    const MAX_BYTES = 2 * 1024 * 1024 * 1024;

    function human(bytes) {
        const gb = bytes / (1024 * 1024 * 1024);
        return gb >= 1
            ? `${gb.toFixed(2)} Go`
            : `${(bytes / (1024 * 1024)).toFixed(1)} Mo`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document
            .querySelectorAll('input[type="file"][data-educational-2gb]')
            .forEach(input => {
                input.addEventListener('change', () => {
                    const files = Array.from(input.files || []);
                    const tooLarge = files.find(file => file.size > MAX_BYTES);

                    if (!tooLarge) {
                        return;
                    }

                    alert(
                        `${tooLarge.name} dépasse 2 Go (${human(tooLarge.size)}). `
                        + 'La taille maximale autorisée est de 2 Go par fichier.'
                    );

                    input.value = '';
                });
            });
    });
})();
