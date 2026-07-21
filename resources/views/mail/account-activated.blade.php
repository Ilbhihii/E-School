<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre compte étudiant est activé</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:Arial,Helvetica,sans-serif;color:#172033;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eef2f7;padding:30px 12px;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px;background:#ffffff;border-radius:22px;overflow:hidden;box-shadow:0 18px 55px rgba(15,23,42,.12);">
            <tr><td style="height:6px;background:linear-gradient(90deg,#10b981,#2563eb,#6366f1);"></td></tr>
            <tr>
                <td style="padding:34px 42px 27px;background:#091426;text-align:center;">
                    <img src="{{ $logoUrl }}" width="76" height="76" alt="Smart School Academy" style="display:block;margin:0 auto 16px;border-radius:18px;object-fit:contain;background:#ffffff;box-shadow:0 10px 30px rgba(0,0,0,.22);">
                    <div style="font-size:21px;line-height:1.25;font-weight:800;color:#ffffff;">Smart School Academy</div>
                    <div style="margin-top:6px;font-size:11px;color:#8fa1ba;letter-spacing:1.5px;text-transform:uppercase;">Plateforme éducative</div>
                </td>
            </tr>
            <tr>
                <td style="padding:38px 42px 18px;">
                    <div style="width:50px;height:50px;line-height:50px;text-align:center;border-radius:15px;background:#ecfdf5;color:#059669;font-size:23px;margin-bottom:22px;">✓</div>
                    <h1 style="margin:0 0 14px;font-size:25px;line-height:1.25;color:#172033;letter-spacing:-.5px;">Votre compte est maintenant actif</h1>
                    <p style="margin:0 0 12px;font-size:15px;line-height:1.75;color:#526078;">Bonjour {{ $user->name ?: 'cher étudiant' }},</p>
                    <p style="margin:0;font-size:15px;line-height:1.75;color:#526078;">L’administration vient de valider votre compte étudiant. Vous pouvez désormais vous connecter et accéder à votre parcours pédagogique.</p>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding:20px 42px 30px;">
                    <a href="{{ $dashboardUrl }}" style="display:inline-block;padding:15px 28px;border-radius:12px;background:#4f46e5;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;box-shadow:0 10px 24px rgba(79,70,229,.25);">Accéder à mon espace étudiant</a>
                </td>
            </tr>
            <tr>
                <td style="padding:0 42px 32px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc;border:1px solid #e5eaf1;border-radius:14px;">
                        <tr><td style="padding:19px;">
                            <div style="font-size:14px;font-weight:700;color:#334155;margin-bottom:12px;">Votre espace vous permet maintenant de :</div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr><td style="padding:6px 0;font-size:13px;color:#64748b;"><span style="color:#10b981;font-weight:bold;">✓</span>&nbsp; Consulter les cours de votre matière et de votre classe</td></tr>
                                <tr><td style="padding:6px 0;font-size:13px;color:#64748b;"><span style="color:#10b981;font-weight:bold;">✓</span>&nbsp; Participer aux lives et envoyer vos devoirs</td></tr>
                                <tr><td style="padding:6px 0;font-size:13px;color:#64748b;"><span style="color:#10b981;font-weight:bold;">✓</span>&nbsp; Suivre votre progression et vos résultats</td></tr>
                                <tr><td style="padding:6px 0;font-size:13px;color:#64748b;"><span style="color:#10b981;font-weight:bold;">✓</span>&nbsp; Échanger avec vos professeurs et l’administration</td></tr>
                            </table>
                        </td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding:23px 42px;background:#f8fafc;border-top:1px solid #e8edf3;text-align:center;">
                    <p style="margin:0 0 7px;font-size:12px;line-height:1.6;color:#64748b;">Besoin d’aide ? Connectez-vous puis utilisez le chat avec l’administration.</p>
                    <p style="margin:0;font-size:11px;color:#a0aec0;">© {{ date('Y') }} Smart School Academy · Tous droits réservés</p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
