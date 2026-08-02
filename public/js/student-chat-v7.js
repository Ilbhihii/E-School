document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById(
        'studentChatSearch'
    );

    const subjectCards = Array.from(
        document.querySelectorAll(
            '.student-chat-subject-card'
        )
    );

    const searchEmpty = document.getElementById(
        'studentChatSearchEmpty'
    );

    if (searchInput && subjectCards.length > 0) {
        searchInput.addEventListener('input', function () {
            const query = searchInput.value
                .trim()
                .toLocaleLowerCase('fr');

            let visibleCount = 0;

            subjectCards.forEach(function (card) {
                const subject =
                    card.dataset.chatSubject || '';

                const visible =
                    query === ''
                    || subject.includes(query);

                card.hidden = !visible;

                if (visible) {
                    visibleCount += 1;
                }
            });

            if (searchEmpty) {
                searchEmpty.hidden = visibleCount !== 0;
            }
        });
    }

    const chatBox = document.getElementById(
        'studentChatBox'
    );

    if (chatBox) {
        requestAnimationFrame(function () {
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    }

    const form = document.getElementById(
        'studentChatForm'
    );

    const input = document.getElementById(
        'studentChatInput'
    );

    const sendButton = document.getElementById(
        'studentChatSendButton'
    );

    const characterCount = document.getElementById(
        'studentChatCharacterCount'
    );

    if (!form || !input || !sendButton) {
        return;
    }

    function updateComposer() {
        const length = input.value.length;
        const hasText = input.value.trim().length > 0;

        input.style.height = 'auto';

        input.style.height = Math.min(
            input.scrollHeight,
            150
        ) + 'px';

        sendButton.disabled = !hasText;

        if (characterCount) {
            characterCount.textContent =
                length + ' / 5000';
        }
    }

    input.addEventListener('input', updateComposer);

    input.addEventListener('keydown', function (event) {
        if (
            event.key === 'Enter'
            && !event.shiftKey
        ) {
            event.preventDefault();

            if (input.value.trim() !== '') {
                form.requestSubmit();
            }
        }
    });

    form.addEventListener('submit', function (event) {
        if (input.value.trim() === '') {
            event.preventDefault();
            input.focus();
            return;
        }

        sendButton.disabled = true;
    });

    updateComposer();

    if (input.value.trim() !== '') {
        input.focus();
    }
});