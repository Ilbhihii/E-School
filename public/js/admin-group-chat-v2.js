(() => {
    'use strict';

    function initChat() {
        const messages =
            document.getElementById(
                'adminChatMessages'
            );

        const form =
            document.getElementById(
                'adminChatForm'
            );

        const textarea =
            document.getElementById(
                'adminChatMessage'
            );

        const counter =
            document.getElementById(
                'adminChatCharacterCount'
            );

        const emojiButton =
            document.getElementById(
                'adminChatEmojiButton'
            );

        const contactSearch =
            document.getElementById(
                'adminChatContactSearch'
            );

        const contactList =
            document.getElementById(
                'adminChatContactList'
            );

        if (messages) {
            messages.scrollTop =
                messages.scrollHeight;
        }

        const resizeTextarea = () => {
            if (!textarea) {
                return;
            }

            textarea.style.height = 'auto';

            textarea.style.height =
                `${Math.min(
                    textarea.scrollHeight,
                    120
                )}px`;
        };

        const updateCounter = () => {
            if (
                !textarea
                || !counter
            ) {
                return;
            }

            counter.textContent =
                `${textarea.value.length} / 5000`;
        };

        if (textarea) {
            resizeTextarea();
            updateCounter();

            textarea.addEventListener(
                'input',
                () => {
                    resizeTextarea();
                    updateCounter();
                }
            );

            textarea.addEventListener(
                'keydown',
                event => {
                    if (
                        event.key === 'Enter'
                        && !event.shiftKey
                    ) {
                        event.preventDefault();

                        if (
                            form
                            && textarea.value
                                .trim()
                                .length > 0
                        ) {
                            form.requestSubmit();
                        }
                    }
                }
            );
        }

        if (
            emojiButton
            && textarea
        ) {
            emojiButton.addEventListener(
                'click',
                () => {
                    const emoji = '🙂';
                    const start =
                        textarea.selectionStart
                        ?? textarea.value.length;

                    const end =
                        textarea.selectionEnd
                        ?? start;

                    textarea.value =
                        textarea.value.slice(
                            0,
                            start
                        )
                        + emoji
                        + textarea.value.slice(
                            end
                        );

                    textarea.selectionStart =
                        textarea.selectionEnd =
                            start + emoji.length;

                    textarea.focus();
                    resizeTextarea();
                    updateCounter();
                }
            );
        }

        if (
            contactSearch
            && contactList
        ) {
            contactSearch.addEventListener(
                'input',
                () => {
                    const query =
                        contactSearch.value
                            .trim()
                            .toLocaleLowerCase();

                    contactList
                        .querySelectorAll(
                            '[data-contact-name]'
                        )
                        .forEach(item => {
                            const name =
                                item.dataset
                                    .contactName
                                || '';

                            item.style.display =
                                !query
                                || name.includes(query)
                                    ? ''
                                    : 'none';
                        });
                }
            );
        }
    }

    if (
        document.readyState === 'loading'
    ) {
        document.addEventListener(
            'DOMContentLoaded',
            initChat
        );
    } else {
        initChat();
    }
})();
