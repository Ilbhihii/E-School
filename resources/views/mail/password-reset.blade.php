<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #0f172a;
            min-height: 100vh;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .email-container {
            background: #1e293b;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .email-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 48px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .email-header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 160px;
            height: 160px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .email-header::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -40px;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .email-header .lock-icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
        }
        .email-header p {
            color: rgba(255,255,255,0.7);
            font-size: 15px;
            margin: 0;
        }
        .email-body {
            padding: 40px;
            background: #1e293b;
        }
        .email-body h2 {
            color: #f1f5f9;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 16px;
        }
        .email-body p {
            color: #94a3b8;
            font-size: 15px;
            margin: 0 0 12px;
            line-height: 1.7;
        }
        .email-body strong {
            color: #e2e8f0;
        }
        .btn-reset {
            display: block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 32px 0;
            box-shadow: 0 8px 24px rgba(59,130,246,0.35);
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(59,130,246,0.45);
        }
        .btn-fallback {
            text-align: center;
            margin: 16px 0 0;
        }
        .btn-fallback a {
            color: #60a5fa;
            font-size: 13px;
            word-break: break-all;
        }
        .email-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 32px 0;
        }
        .email-info-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px;
            padding: 20px;
            margin: 24px 0;
        }
        .email-info-box p {
            font-size: 13px;
            margin: 0;
            color: #64748b;
        }
        .email-info-box .icon {
            font-size: 20px;
            margin-bottom: 8px;
            display: block;
        }
        .email-footer {
            background: #0f172a;
            padding: 32px 40px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.04);
        }
        .email-footer .brand {
            font-size: 18px;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        .email-footer .brand span {
            background: linear-gradient(135deg, #f8fafc 50%, rgba(248,250,252,0.6));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .email-footer p {
            color: #475569;
            font-size: 12px;
            margin: 4px 0;
        }
        .email-footer .social-links {
            margin-top: 16px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .email-footer .social-links a {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .email-wrapper { padding: 16px; }
            .email-header { padding: 32px 24px 28px; }
            .email-body { padding: 28px 24px; }
            .email-footer { padding: 24px; }
            .email-header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="lock-icon">🔐</div>
                <h1>Réinitialisation de mot de passe</h1>
                <p>Vous avez demandé un nouveau mot de passe</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h2>Bonjour <strong>{{ $user->name }}</strong>,</h2>

                <p>
                    Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte
                    <strong>Smart School Academy</strong>. Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.
                </p>

                <a href="{{ $url }}" class="btn-reset">
                    🔑 Réinitialiser mon mot de passe
                </a>

                <div class="btn-fallback">
                    <small style="color:#64748b;">Si le bouton ne fonctionne pas, copiez ce lien :</small><br>
                    <a href="{{ $url }}" style="color:#60a5fa;font-size:13px;">{{ $url }}</a>
                </div>

                <hr class="email-divider">

                <div class="email-info-box">
                    <span class="icon">⏰</span>
                    <p>
                        Ce lien de réinitialisation expire dans <strong style="color:#e2e8f0;">{{ $expireCount }} minutes</strong>.
                        Si vous n'êtes pas à l'origine de cette demande, aucune action n'est requise — votre mot de passe reste inchangé.
                    </p>
                </div>

                <div class="email-info-box">
                    <span class="icon">🛡️</span>
                    <p>
                        Pour votre sécurité, ne partagez jamais ce lien avec personne.
                        Notre équipe ne vous demandera jamais votre mot de passe.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <div class="brand"><span>Smart School Academy</span></div>
                <p>Plateforme Éducative Moderne</p>
                <p>&copy; {{ date('Y') }} Smart School Academy — Tous droits réservés</p>
            </div>
        </div>
    </div>
</body>
</html>
