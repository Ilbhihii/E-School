(function () {
    const root = document.documentElement;

    function syncGlobalTheme() {
        const lightStylesheet = document.getElementById('globalLightTheme');
        if (lightStylesheet) {
            lightStylesheet.disabled = !root.classList.contains('light-mode');
        }
    }

    syncGlobalTheme();
    new MutationObserver(syncGlobalTheme).observe(root, {
        attributes: true,
        attributeFilter: ['class'],
    });

    document.addEventListener('DOMContentLoaded', syncGlobalTheme);
})();
