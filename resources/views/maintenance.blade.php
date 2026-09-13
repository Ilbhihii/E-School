<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Maintenance — Smart School Academy
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(
                    circle at top,
                    #18244d,
                    #050a16 62%
                );
            color: #fff;
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        .maintenance-card {
            width: min(760px, 100%);
            padding: 52px 34px;
            text-align: center;
            border: 1px solid #293856;
            border-radius: 28px;
            background: rgba(14, 24, 43, .95);
            box-shadow:
                0 34px 90px rgba(0, 0, 0, .45);
        }

        .maintenance-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            color: #fff;
            font-size: 18px;
            font-weight: 800;
        }

        .maintenance-logo-mark {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background:
                linear-gradient(
                    135deg,
                    #4f6df5,
                    #7c3aed
                );
        }

        .maintenance-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto;
            display: grid;
            place-items: center;
            border-radius: 25px;
            font-size: 40px;
            background:
                linear-gradient(
                    135deg,
                    #536dfe,
                    #7c3aed
                );
            box-shadow:
                0 18px 42px rgba(99, 102, 241, .28);
        }

        h1 {
            margin: 25px 0 10px;
            font-size: clamp(30px, 5vw, 42px);
        }

        p {
            max-width: 610px;
            margin: 0 auto;
            color: #a7b2c6;
            font-size: 17px;
            line-height: 1.65;
        }

        .countdown {
            margin: 30px auto 0;
            padding: 21px;
            border: 1px solid #243452;
            border-radius: 18px;
            background: #081326;
        }

        .countdown-label {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        #timer {
            margin-top: 8px;
            color: #a78bfa;
            font-size: clamp(34px, 7vw, 48px);
            font-weight: 900;
            letter-spacing: 3px;
        }

        .return-time {
            margin-top: 20px;
            color: #94a3b8;
            font-size: 14px;
        }

        .return-time strong {
            color: #e2e8f0;
        }
    </style>
</head>

<body>

<div class="maintenance-card">

    <div class="maintenance-logo">
        <span class="maintenance-logo-mark">SSA</span>
        Smart School Academy
    </div>

    <div class="maintenance-icon">
        🛠️
    </div>

    <h1>
        Maintenance en cours
    </h1>

    <p>
        {{ $maintenance->message }}
    </p>

    <div class="countdown">
        <div class="countdown-label">
            Temps restant estimé
        </div>

        <div id="timer">
            --:--:--
        </div>
    </div>

    <div class="return-time">
        Retour prévu le

        <strong>
            {{
                $maintenance
                    ->end_at
                    ->format('d/m/Y à H:i')
            }}
        </strong>
    </div>

</div>

<script>
    const endAt = new Date(
        @json(
            $maintenance
                ->end_at
                ->toIso8601String()
        )
    ).getTime();

    function updateCountdown() {
        const now = Date.now();
        const distance = endAt - now;

        if (distance <= 0) {
            window.location.reload();
            return;
        }

        const totalSeconds = Math.floor(distance / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor(
            (totalSeconds % 3600) / 60
        );
        const seconds = totalSeconds % 60;

        document.getElementById('timer').textContent =
            String(hours).padStart(2, '0')
            + ':'
            + String(minutes).padStart(2, '0')
            + ':'
            + String(seconds).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>

</body>
</html>
