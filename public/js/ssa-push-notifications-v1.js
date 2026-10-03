(function () {
    'use strict';

    const root = document.querySelector('[data-ssa-push-manager]');

    if (!root) {
        return;
    }

    const button = root.querySelector('[data-ssa-push-enable]');
    const testButton = root.querySelector('[data-ssa-push-test]');
    const status = root.querySelector('[data-ssa-push-status]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const endpoints = {
        config: root.dataset.configUrl || '',
        register: root.dataset.registerUrl || '',
        unregister: root.dataset.unregisterUrl || '',
        test: root.dataset.testUrl || '',
    };

    const TOKEN_KEY = 'ssa:web-push-token';
    const FIREBASE_APP = 'ssa-web-push';
    let messaging = null;
    let currentToken = localStorage.getItem(TOKEN_KEY) || '';

    function setStatus(message, state) {
        if (status) {
            status.textContent = message;
            status.dataset.state = state || 'info';
        }

        root.dataset.pushState = state || 'info';
    }

    function setButton(label, disabled) {
        if (!button) return;
        button.disabled = Boolean(disabled);
        const labelNode = button.querySelector('[data-ssa-push-label]');
        if (labelNode) labelNode.textContent = label;
    }

    async function post(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(payload || {}),
        });

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        return response.json();
    }

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const existing = document.querySelector('script[src="' + src + '"]');

            if (existing) {
                if (window.firebase) resolve();
                else existing.addEventListener('load', resolve, { once: true });
                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async function ensureFirebase() {
        if (window.firebase && window.firebase.messaging) {
            return;
        }

        await loadScript(
            'https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js'
        );
        await loadScript(
            'https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging-compat.js'
        );
    }

    async function loadConfig() {
        if (!endpoints.config) {
            throw new Error('Configuration push absente.');
        }

        const response = await fetch(endpoints.config, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Configuration push inaccessible.');
        }

        return response.json();
    }

    async function registerToken(config) {
        if (!config.enabled) {
            setStatus('Notifications non configurées sur le serveur.', 'warning');
            setButton('Indisponible', true);
            return '';
        }

        await ensureFirebase();

        let app = null;

        try {
            app = window.firebase.app(FIREBASE_APP);
        } catch (error) {
            app = window.firebase.initializeApp(config.firebase, FIREBASE_APP);
        }

        messaging = app.messaging();

        const registration = await navigator.serviceWorker.register(
            config.service_worker || '/sw.js',
            { scope: '/' }
        );

        await navigator.serviceWorker.ready;

        const token = await messaging.getToken({
            vapidKey: config.vapid_key,
            serviceWorkerRegistration: registration,
        });

        if (!token) {
            throw new Error('Firebase n’a pas fourni de token.');
        }

        await post(endpoints.register, { token });

        currentToken = token;
        localStorage.setItem(TOKEN_KEY, token);

        messaging.onMessage(payload => {
            const notification = payload.notification || {};
            const data = payload.data || {};
            const title = notification.title || data.title || 'Smart School Academy';
            const body = notification.body || data.body || 'Nouvelle notification';
            const url = data.url || '/notifications';

            navigator.serviceWorker.ready
                .then(registration => registration.showNotification(title, {
                    body,
                    icon: '/images/icons/icon-192x192.png',
                    badge: '/images/icons/icon-192x192.png',
                    data: { url },
                }))
                .catch(() => {
                    // Le centre de notifications web reste disponible.
                });

            window.dispatchEvent(
                new CustomEvent('ssa:push-received', { detail: payload })
            );
        });

        setStatus('Notifications activées sur cet appareil.', 'success');
        setButton('Activées', true);

        if (testButton) testButton.hidden = false;

        return token;
    }

    async function activate() {
        if (!window.isSecureContext) {
            setStatus('HTTPS est obligatoire pour les notifications.', 'danger');
            setButton('HTTPS requis', true);
            return;
        }

        if (
            !('Notification' in window)
            || !('serviceWorker' in navigator)
            || !('PushManager' in window)
        ) {
            setStatus('Ce navigateur ne prend pas en charge les notifications push.', 'warning');
            setButton('Non compatible', true);
            return;
        }

        let permission = Notification.permission;

        if (permission === 'default') {
            permission = await Notification.requestPermission();
        }

        if (permission !== 'granted') {
            setStatus(
                permission === 'denied'
                    ? 'Notifications bloquées dans les réglages du navigateur.'
                    : 'Autorisation non accordée.',
                'warning'
            );
            setButton('Autoriser les notifications', false);
            return;
        }

        setStatus('Activation en cours…', 'info');
        setButton('Activation…', true);

        try {
            const config = await loadConfig();
            await registerToken(config);
        } catch (error) {
            console.warn('[SSA Push]', error);
            setStatus('Impossible d’activer les notifications pour le moment.', 'danger');
            setButton('Réessayer', false);
        }
    }

    async function bootstrap() {
        if (
            !window.isSecureContext
            || !('Notification' in window)
            || !('serviceWorker' in navigator)
            || !('PushManager' in window)
        ) {
            setStatus('Notifications navigateur non disponibles.', 'warning');
            setButton('Non compatible', true);
            return;
        }

        if (Notification.permission === 'denied') {
            setStatus('Notifications bloquées dans les réglages du navigateur.', 'warning');
            setButton('Notifications bloquées', true);
            return;
        }

        if (Notification.permission === 'granted') {
            setStatus('Synchronisation de cet appareil…', 'info');
            setButton('Synchronisation…', true);

            try {
                const config = await loadConfig();
                await registerToken(config);
            } catch (error) {
                console.warn('[SSA Push]', error);
                setStatus('Réactivation nécessaire sur cet appareil.', 'warning');
                setButton('Réactiver', false);
            }

            return;
        }

        setStatus('Recevez les lives, devoirs, messages et mises à jour.', 'info');
        setButton('Activer sur cet appareil', false);
    }

    button?.addEventListener('click', activate);

    testButton?.addEventListener('click', async function () {
        if (!currentToken) {
            await activate();
            return;
        }

        testButton.disabled = true;

        try {
            await post(endpoints.test, {});
            setStatus('Notification de test envoyée.', 'success');
        } catch (error) {
            setStatus('Échec du test de notification.', 'danger');
        } finally {
            window.setTimeout(() => {
                testButton.disabled = false;
            }, 1200);
        }
    });

    document.querySelectorAll('form[action$="/logout"]').forEach(form => {
        form.addEventListener('submit', async function (event) {
            if (!currentToken || !endpoints.unregister) return;

            event.preventDefault();

            try {
                await post(endpoints.unregister, { token: currentToken });
                localStorage.removeItem(TOKEN_KEY);
                currentToken = '';
            } catch (error) {
                // La déconnexion ne doit jamais être bloquée par Firebase.
            }

            HTMLFormElement.prototype.submit.call(form);
        });
    });

    bootstrap();
})();
