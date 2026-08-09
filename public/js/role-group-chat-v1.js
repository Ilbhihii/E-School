(() => {
    'use strict';

    function initRoleGroupChat() {
        document
            .querySelectorAll('[data-rgc-messages]')
            .forEach(box => {
                box.scrollTop = box.scrollHeight;
            });

        document
            .querySelectorAll('[data-rgc-form]')
            .forEach(form => {
                const textarea =
                    form.querySelector(
                        '[data-rgc-textarea]'
                    );

                const counter =
                    form.querySelector(
                        '[data-rgc-counter]'
                    );

                const emoji =
                    form.querySelector(
                        '[data-rgc-emoji]'
                    );

                const resize = () => {
                    if (!textarea) {
                        return;
                    }

                    textarea.style.height = 'auto';
                    textarea.style.height =
                        `${Math.min(
                            textarea.scrollHeight,
                            118
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
                    resize();
                    updateCounter();

                    textarea.addEventListener(
                        'input',
                        () => {
                            resize();
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
                                    textarea.value
                                        .trim()
                                ) {
                                    form.requestSubmit();
                                }
                            }
                        }
                    );
                }

                if (
                    emoji
                    && textarea
                ) {
                    emoji.addEventListener(
                        'click',
                        () => {
                            const symbol = '🙂';

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
                                + symbol
                                + textarea.value.slice(
                                    end
                                );

                            textarea.selectionStart =
                                textarea.selectionEnd =
                                    start + symbol.length;

                            textarea.focus();
                            resize();
                            updateCounter();
                        }
                    );
                }

                form.addEventListener(
                    'submit',
                    () => {
                        const button =
                            form.querySelector(
                                '[data-rgc-send]'
                            );

                        if (button) {
                            button.disabled = true;
                        }
                    }
                );
            });

        document
            .querySelectorAll('[data-rgc-search]')
            .forEach(search => {
                const targetId =
                    search.dataset.rgcSearch;

                const target =
                    document.getElementById(
                        targetId
                    );

                if (!target) {
                    return;
                }

                search.addEventListener(
                    'input',
                    () => {
                        const query =
                            search.value
                                .trim()
                                .toLocaleLowerCase();

                        target
                            .querySelectorAll(
                                '[data-rgc-item]'
                            )
                            .forEach(item => {
                                const name =
                                    item.dataset.rgcItem
                                    || '';

                                item.style.display =
                                    !query
                                    || name.includes(query)
                                        ? ''
                                        : 'none';
                            });
                    }
                );
            });
    }

    if (
        document.readyState === 'loading'
    ) {
        document.addEventListener(
            'DOMContentLoaded',
            initRoleGroupChat
        );
    } else {
        initRoleGroupChat();
    }
})();
