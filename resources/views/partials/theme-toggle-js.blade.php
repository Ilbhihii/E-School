<script>
(function() {
    'use strict';

    const html = document.documentElement;
    const toggleBtn = document.getElementById('themeToggle');
    const STORAGE_KEY = 'app-theme-preference';

    function setTheme(mode) {
        if (mode === 'light') {
            html.classList.add('light-mode');
        } else {
            html.classList.remove('light-mode');
        }
        try { localStorage.setItem(STORAGE_KEY, mode); } catch(e) {}
    }

    function toggleTheme() {
        const isLight = html.classList.contains('light-mode');
        setTheme(isLight ? 'dark' : 'light');
        // Marquer que l'utilisateur a fait un choix manuel
        try { localStorage.setItem(STORAGE_KEY + '-source', 'manual'); } catch(e) {}
        if (toggleBtn) {
            toggleBtn.style.transform = 'rotate(180deg) scale(0.8)';
            setTimeout(() => { toggleBtn.style.transform = ''; }, 300);
        }
    }

    let saved;
    let source;
    try {
        saved = localStorage.getItem(STORAGE_KEY);
        source = localStorage.getItem(STORAGE_KEY + '-source');
    } catch(e) {}

    if (source === 'manual' && (saved === 'light' || saved === 'dark')) {
        // Choix manuel explicite de l'utilisateur → respecter
        setTheme(saved);
    } else {
        // Pas de choix manuel → détecter le thème du système d'exploitation
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        setTheme(prefersDark.matches ? 'dark' : 'light');

        // Écouter les changements de thème système (OS)
        function onSystemThemeChange(e) {
            let currentSource;
            try { currentSource = localStorage.getItem(STORAGE_KEY + '-source'); } catch(ex) {}
            if (currentSource !== 'manual') {
                setTheme(e.matches ? 'dark' : 'light');
            }
        }
        if (prefersDark.addEventListener) {
            prefersDark.addEventListener('change', onSystemThemeChange);
        } else if (prefersDark.addListener) {
            prefersDark.addListener(onSystemThemeChange);
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleTheme);
    }
})();
</script>
