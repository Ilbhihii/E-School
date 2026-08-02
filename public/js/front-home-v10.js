(function () {
    document.documentElement.classList.add('ssa-js');

    function forceDarkHomepage() {
        document.documentElement.classList.remove('light-mode');

        const lightStylesheet = document.getElementById(
            'globalLightTheme'
        );

        if (lightStylesheet) {
            lightStylesheet.disabled = true;
        }
    }

    function revealElements() {
        const elements = Array.from(
            document.querySelectorAll(
                '.ssa-home .ssa-reveal'
            )
        );

        if (elements.length === 0) {
            return;
        }

        if (
            !('IntersectionObserver' in window)
            || window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches
        ) {
            elements.forEach(function (element) {
                element.classList.add('is-visible');
            });

            return;
        }

        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add(
                        'is-visible'
                    );

                    observer.unobserve(entry.target);
                });
            },
            {
                rootMargin: '0px 0px -55px 0px',
                threshold: 0.08,
            }
        );

        elements.forEach(function (element, index) {
            element.style.transitionDelay =
                Math.min(index % 4, 3) * 55 + 'ms';

            observer.observe(element);
        });
    }

    function init() {
        forceDarkHomepage();
        revealElements();
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