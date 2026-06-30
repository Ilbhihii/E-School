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
        if (toggleBtn) {
            toggleBtn.style.transform = 'rotate(180deg) scale(0.8)';
            setTimeout(() => { toggleBtn.style.transform = ''; }, 300);
        }
    }

    let saved;
    try { saved = localStorage.getItem(STORAGE_KEY); } catch(e) {}

    if (saved === 'light' || saved === 'dark') {
        // Préférence explicite sauvegardée → l'utiliser
        setTheme(saved);
    } else {
        // Aucune préférence → détecter le thème système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        setTheme(prefersDark.matches ? 'dark' : 'light');

        // Écouter les changements de thème système (lorsque l'utilisateur change dans l'OS)
        function onSystemThemeChange(e) {
            let currentSaved;
            try { currentSaved = localStorage.getItem(STORAGE_KEY); } catch(ex) {}
            if (!currentSaved) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        }
        // Support à la fois l'API moderne et l'ancienne (Safari < 14)
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
