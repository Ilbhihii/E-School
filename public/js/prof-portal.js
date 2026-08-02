(function () {
    'use strict';

    const body = document.body;
    const sidebar = document.getElementById('profSidebar');
    const overlay = document.getElementById('profSidebarOverlay');
    const menuButton = document.getElementById('profMenuButton');
    const closeButton = document.getElementById('profSidebarClose');
    const userButton = document.getElementById('profUserBtn');
    const userMenu = document.getElementById('profUserMenu');

    function setSidebar(open) {
        if (!sidebar || !overlay) return;

        sidebar.classList.toggle('open', open);
        overlay.classList.toggle('show', open);
        body.classList.toggle('sidebar-open', open);
        overlay.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (menuButton) {
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuButton.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        }
    }

    function setUserMenu(open) {
        if (!userButton || !userMenu) return;

        userMenu.hidden = !open;
        userButton.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function enforceDarkTheme() {
        document.documentElement.classList.remove(
            'light-mode'
        );

        try {
            localStorage.removeItem(
                'ssa-prof-theme'
            );
        } catch (error) {
            // Le mode sombre reste actif sans stockage local.
        }
    }

    menuButton?.addEventListener('click', function () {
        setSidebar(!sidebar?.classList.contains('open'));
    });

    closeButton?.addEventListener('click', function () {
        setSidebar(false);
    });

    overlay?.addEventListener('click', function () {
        setSidebar(false);
    });

    sidebar?.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) setSidebar(false);
        });
    });

    userButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        setUserMenu(userMenu?.hidden ?? true);
    });

    userMenu?.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('click', function () {
        setUserMenu(false);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        setSidebar(false);
        setUserMenu(false);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) setSidebar(false);
    });

    document.querySelectorAll('.prof-alert-close').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('.prof-toast')?.remove();
        });
    });

    document.querySelectorAll('.prof-toast').forEach(function (toast) {
        window.setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            window.setTimeout(function () {
                toast.remove();
            }, 260);
        }, 5500);
    });

    enforceDarkTheme();
})();
