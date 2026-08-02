(function () {
    'use strict';

    function byId(id) {
        return document.getElementById(id);
    }

    function openSidebar() {
        const sidebar = byId('adminSidebar');
        const overlay = byId('adminSidebarOverlay');
        if (!sidebar || !overlay) return;

        sidebar.classList.add('open');
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        const sidebar = byId('adminSidebar');
        const overlay = byId('adminSidebarOverlay');
        if (!sidebar || !overlay) return;

        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        const sidebar = byId('adminSidebar');
        if (!sidebar) return;
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    }

    function closeUserMenu() {
        const button = byId('adminUserMenuButton');
        const dropdown = byId('adminUserDropdown');
        if (!button || !dropdown) return;

        dropdown.hidden = true;
        button.setAttribute('aria-expanded', 'false');
    }

    function toggleUserMenu() {
        const button = byId('adminUserMenuButton');
        const dropdown = byId('adminUserDropdown');
        if (!button || !dropdown) return;

        const isOpen = !dropdown.hidden;
        dropdown.hidden = isOpen;
        button.setAttribute('aria-expanded', String(!isOpen));
    }

    function enforceDarkTheme() {
        document.documentElement.classList.remove(
            'light-mode'
        );

        try {
            localStorage.removeItem(
                'ssa-admin-theme'
            );
        } catch (error) {
            // Le mode sombre reste actif sans stockage local.
        }
    }

    function prepareResponsiveTables() {
        document.querySelectorAll('.admin-content table').forEach(function (table) {
            const headers = Array.from(table.querySelectorAll('thead th')).map(function (header) {
                return header.textContent.trim();
            });

            if (!headers.length) return;

            table.classList.add('responsive-ready');

            table.querySelectorAll('tbody tr').forEach(function (row) {
                Array.from(row.children).forEach(function (cell, index) {
                    if (!cell.dataset.label) {
                        cell.dataset.label = headers[index] || 'Information';
                    }
                });
            });
        });
    }

    function bindTableSearch() {
        document.querySelectorAll('.adm-card-header input[type="text"], .appointments-search input[type="search"], .appointments-search input[type="text"]').forEach(function (input) {
            const card = input.closest('.adm-card, .appointments-panel');
            if (!card) return;

            const table = card.querySelector('table');
            if (!table) return;

            input.addEventListener('input', function () {
                const query = input.value.trim().toLocaleLowerCase('fr');

                table.querySelectorAll('tbody tr').forEach(function (row) {
                    const content = row.textContent.toLocaleLowerCase('fr');
                    row.hidden = query !== '' && !content.includes(query);
                });
            });
        });
    }

    function autoResizeTextareas() {
        document.querySelectorAll('.admin-content textarea').forEach(function (textarea) {
            const resize = function () {
                textarea.style.height = 'auto';
                textarea.style.height = Math.min(Math.max(textarea.scrollHeight, 116), 360) + 'px';
            };

            textarea.addEventListener('input', resize);
            if (textarea.value.trim()) resize();
        });
    }

    function bindAlerts() {
        document.querySelectorAll('[data-dismiss-alert]').forEach(function (button) {
            button.addEventListener('click', function () {
                const alert = button.closest('.adm-alert');
                if (!alert) return;
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-6px)';
                setTimeout(function () { alert.remove(); }, 220);
            });
        });

        document.querySelectorAll('.admin-flash-message').forEach(function (alert) {
            setTimeout(function () {
                if (!alert.isConnected) return;
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-6px)';
                setTimeout(function () { alert.remove(); }, 220);
            }, 6000);
        });
    }

    function bindPasswordVisibility() {
        document.querySelectorAll('.admin-content input[type="password"]').forEach(function (input) {
            if (input.dataset.visibilityReady === 'true') return;
            input.dataset.visibilityReady = 'true';

            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('aria-label', 'Afficher ou masquer le mot de passe');
            button.innerHTML = '<i class="bi bi-eye"></i>';
            button.style.cssText = 'position:absolute;right:8px;top:50%;transform:translateY(-50%);width:34px;height:34px;border:0;border-radius:9px;background:transparent;color:#94a3b8;display:grid;place-items:center;cursor:pointer;';
            wrapper.appendChild(button);
            input.style.paddingRight = '48px';

            button.addEventListener('click', function () {
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        enforceDarkTheme();

        const menuButton = byId('adminMenuButton');
        const closeButton = byId('adminSidebarClose');
        const overlay = byId('adminSidebarOverlay');
        const userButton = byId('adminUserMenuButton');

        if (menuButton) menuButton.addEventListener('click', toggleSidebar);
        if (closeButton) closeButton.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
        if (userButton) userButton.addEventListener('click', function (event) {
            event.stopPropagation();
            toggleUserMenu();
        });

        document.querySelectorAll('.admin-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 1080) closeSidebar();
            });
        });

        document.addEventListener('click', function (event) {
            const dropdown = byId('adminUserDropdown');
            const button = byId('adminUserMenuButton');
            if (!dropdown || !button) return;
            if (!dropdown.contains(event.target) && !button.contains(event.target)) closeUserMenu();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
                closeUserMenu();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1080) closeSidebar();
        });

        prepareResponsiveTables();
        bindTableSearch();
        autoResizeTextareas();
        bindAlerts();
        bindPasswordVisibility();
    });

    window.toggleSidebar = toggleSidebar;
    window.toggleUserMenu = toggleUserMenu;
})();
