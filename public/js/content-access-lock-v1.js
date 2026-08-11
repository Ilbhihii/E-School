\
(function () {
    'use strict';

    const cfg =
        window.SSAContentAccess || null;

    if (
        !cfg
        || !cfg.heartbeatUrl
        || !cfg.csrfToken
    ) {
        return;
    }

    const heartbeatMs =
        Math.max(
            15000,
            Number(cfg.heartbeatSeconds || 30)
                * 1000
        );

    let lockLost = false;
    let requestRunning = false;

    async function heartbeat() {
        if (
            lockLost
            || requestRunning
        ) {
            return;
        }

        requestRunning = true;

        try {
            const response = await fetch(
                cfg.heartbeatUrl,
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            cfg.csrfToken,
                        'X-Requested-With':
                            'XMLHttpRequest',
                    },
                }
            );

            if (
                response.status === 423
            ) {
                lockLost = true;

                let message =
                    'Ce compte utilise déjà '
                    + 'un contenu protégé '
                    + 'sur un autre appareil.';

                try {
                    const data =
                        await response.json();

                    if (data.message) {
                        message =
                            data.message;
                    }
                } catch (error) {
                    // Réponse non JSON :
                    // conserver le message générique.
                }

                window.alert(message);

                if (cfg.redirectUrl) {
                    window.location.href =
                        cfg.redirectUrl;
                }

                return;
            }

            if (
                response.status === 401
                || response.status === 419
            ) {
                lockLost = true;

                if (cfg.loginUrl) {
                    window.location.href =
                        cfg.loginUrl;
                }
            }
        } catch (error) {
            /*
             * Une coupure réseau ne libère pas immédiatement
             * le compte : le TTL serveur prend le relais.
             */
        } finally {
            requestRunning = false;
        }
    }

    /*
     * Le serveur a déjà acquis le verrou lors de l'ouverture
     * du cours. Ce heartbeat le maintient tant que la page
     * reste réellement ouverte sur cet appareil.
     */
    window.setInterval(
        heartbeat,
        heartbeatMs
    );

    window.addEventListener(
        'online',
        heartbeat
    );

    window.addEventListener(
        'focus',
        heartbeat
    );
})();
