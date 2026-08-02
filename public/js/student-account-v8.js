document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById(
        'studentProfilePhoto'
    );

    const previewImage = document.getElementById(
        'studentPhotoPreviewImage'
    );

    const fallback = document.getElementById(
        'studentPhotoFallback'
    );

    const fileName = document.getElementById(
        'studentPhotoFileName'
    );

    const removeButton = document.getElementById(
        'studentRemovePhotoButton'
    );

    const removeInput = document.getElementById(
        'studentRemovePhotoInput'
    );

    if (
        photoInput
        && previewImage
        && fallback
        && fileName
        && removeInput
    ) {
        photoInput.addEventListener('change', function () {
            const file = photoInput.files[0];

            if (!file) {
                return;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (!allowedTypes.includes(file.type)) {
                photoInput.value = '';

                fileName.textContent =
                    'Format non accepté. Utilisez JPG, PNG ou WEBP.';

                return;
            }

            const maxSize = 4 * 1024 * 1024;

            if (file.size > maxSize) {
                photoInput.value = '';

                fileName.textContent =
                    'La photo dépasse la taille maximale de 4 Mo.';

                return;
            }

            const reader = new FileReader();

            reader.addEventListener('load', function () {
                previewImage.src = reader.result;
                previewImage.hidden = false;
                fallback.hidden = true;
            });

            reader.readAsDataURL(file);

            const sizeInMb =
                (file.size / (1024 * 1024)).toFixed(2);

            fileName.textContent =
                file.name + ' · ' + sizeInMb + ' Mo';

            removeInput.value = '0';

            if (removeButton) {
                removeButton.disabled = false;
            }
        });

        if (removeButton) {
            removeButton.addEventListener(
                'click',
                function () {
                    photoInput.value = '';
                    previewImage.src = '';
                    previewImage.hidden = true;
                    fallback.hidden = false;
                    removeInput.value = '1';

                    fileName.textContent =
                        'La photo sera supprimée après enregistrement.';

                    removeButton.disabled = true;
                }
            );
        }
    }

    document
        .querySelectorAll('.student-password-toggle')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId =
                    button.dataset.passwordTarget;

                const input =
                    document.getElementById(targetId);

                if (!input) {
                    return;
                }

                const show =
                    input.type === 'password';

                input.type = show
                    ? 'text'
                    : 'password';

                const icon =
                    button.querySelector('i');

                if (icon) {
                    icon.className = show
                        ? 'bi bi-eye-slash'
                        : 'bi bi-eye';
                }

                button.setAttribute(
                    'aria-label',
                    show
                        ? 'Masquer le mot de passe'
                        : 'Afficher le mot de passe'
                );
            });
        });

    const newPassword = document.getElementById(
        'studentNewPassword'
    );

    const confirmation = document.getElementById(
        'studentPasswordConfirmation'
    );

    const strengthBar = document.getElementById(
        'studentPasswordStrengthBar'
    );

    const strengthLabel = document.getElementById(
        'studentPasswordStrengthLabel'
    );

    const matchLabel = document.getElementById(
        'studentPasswordMatch'
    );

    function passwordScore(value) {
        let score = 0;

        if (value.length >= 8) {
            score += 1;
        }

        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) {
            score += 1;
        }

        if (/\d/.test(value)) {
            score += 1;
        }

        if (/[^A-Za-z0-9]/.test(value)) {
            score += 1;
        }

        return score;
    }

    function updatePasswordStrength() {
        if (!newPassword || !strengthBar || !strengthLabel) {
            return;
        }

        const value = newPassword.value;
        const score = passwordScore(value);

        const settings = [
            {
                width: '0%',
                color: '#e14c5a',
                label: 'Saisissez un nouveau mot de passe.',
            },
            {
                width: '25%',
                color: '#e14c5a',
                label: 'Mot de passe faible.',
            },
            {
                width: '50%',
                color: '#dca241',
                label: 'Mot de passe moyen.',
            },
            {
                width: '75%',
                color: '#58a3f5',
                label: 'Bon mot de passe.',
            },
            {
                width: '100%',
                color: '#20b885',
                label: 'Mot de passe fort.',
            },
        ];

        const state = value.length === 0
            ? settings[0]
            : settings[score];

        strengthBar.style.width = state.width;
        strengthBar.style.background = state.color;
        strengthLabel.textContent = state.label;
    }

    function updatePasswordMatch() {
        if (!newPassword || !confirmation || !matchLabel) {
            return;
        }

        if (confirmation.value === '') {
            matchLabel.textContent = '';
            matchLabel.className =
                'student-password-match';

            return;
        }

        const matches =
            newPassword.value === confirmation.value;

        matchLabel.textContent = matches
            ? 'Les mots de passe correspondent.'
            : 'Les mots de passe ne correspondent pas.';

        matchLabel.className =
            'student-password-match '
            + (matches ? 'success' : 'danger');
    }

    if (newPassword) {
        newPassword.addEventListener(
            'input',
            function () {
                updatePasswordStrength();
                updatePasswordMatch();
            }
        );
    }

    if (confirmation) {
        confirmation.addEventListener(
            'input',
            updatePasswordMatch
        );
    }

    updatePasswordStrength();
    updatePasswordMatch();
});