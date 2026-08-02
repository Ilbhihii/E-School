window.StudentSpace = (function () {
    const root = document.documentElement;

    function sidebar() {
        return document.getElementById('studentSidebar');
    }

    function overlay() {
        return document.getElementById('studentSidebarOverlay');
    }

    function dropdown() {
        return document.getElementById('studentUserDropdown');
    }

    function userButton() {
        return document.getElementById('studentUserButton');
    }

    function forceDarkMode() {
        root.classList.remove('light-mode');

        try {
            localStorage.removeItem('ssa-theme');
            localStorage.removeItem('theme');
        } catch (error) {
            // Le portail reste en mode sombre.
        }
    }

    function openSidebar() {
        const side = sidebar();
        const backdrop = overlay();

        if (!side || !backdrop) {
            return;
        }

        side.classList.add('open');
        backdrop.classList.add('show');
        document.body.classList.add('student-menu-open');
    }

    function closeSidebar() {
        const side = sidebar();
        const backdrop = overlay();

        if (!side || !backdrop) {
            return;
        }

        side.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.classList.remove('student-menu-open');
    }

    function toggleUserMenu() {
        const menu = dropdown();
        const button = userButton();

        if (!menu || !button) {
            return;
        }

        const shouldOpen = menu.hasAttribute('hidden');

        if (shouldOpen) {
            menu.removeAttribute('hidden');
        } else {
            menu.setAttribute('hidden', '');
        }

        button.setAttribute(
            'aria-expanded',
            shouldOpen ? 'true' : 'false'
        );
    }

    function closeUserMenu() {
        const menu = dropdown();
        const button = userButton();

        if (!menu || !button) {
            return;
        }

        menu.setAttribute('hidden', '');
        button.setAttribute('aria-expanded', 'false');
    }

    function init() {
        forceDarkMode();

        document.addEventListener('click', function (event) {
            if (
                !event.target.closest('.student-user-menu') &&
                dropdown() &&
                !dropdown().hasAttribute('hidden')
            ) {
                closeUserMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
                closeUserMenu();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1200) {
                closeSidebar();
            }
        });

        document
            .querySelectorAll('.student-sidebar-nav a')
            .forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 1200) {
                        closeSidebar();
                    }
                });
            });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return {
        openSidebar,
        closeSidebar,
        toggleUserMenu,
        closeUserMenu
    };
})();