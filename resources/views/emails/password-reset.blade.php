<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialisation du mot de passe</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:Arial,Helvetica,sans-serif;color:#172033;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eef2f7;padding:30px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px;background:#ffffff;border-radius:22px;overflow:hidden;box-shadow:0 18px 55px rgba(15,23,42,.12);">
                <tr>
                    <td style="height:6px;background:linear-gradient(90deg,#2563eb,#6366f1,#06b6d4);"></td>
                </tr>
                <tr>
                    <td style="padding:34px 42px 25px;background:#091426;text-align:center;">
                        <img src="{{ $logoUrl }}" width="76" height="76" alt="Smart School Academy" style="display:block;margin:0 auto 16px;border-radius:18px;object-fit:contain;background:#ffffff;box-shadow:0 10px 30px rgba(0,0,0,.22);">
                        <div style="font-size:21px;line-height:1.25;font-weight:800;color:#ffffff;letter-spacing:-.3px;">Smart School Academy</div>
                        <div style="margin-top:6px;font-size:11px;line-height:1.5;color:#8fa1ba;letter-spacing:1.5px;text-transform:uppercase;">Plateforme éducative</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:38px 42px 18px;">
                        <div style="width:48px;height:48px;line-height:48px;text-align:center;border-radius:14px;background:#eef2ff;color:#4f46e5;font-size:22px;margin-bottom:22px;">&#128273;</div>
                        <h1 style="margin:0 0 14px;font-size:25px;line-height:1.25;color:#172033;letter-spacing:-.5px;">Réinitialisez votre mot de passe</h1>
                        <p style="margin:0 0 12px;font-size:15px;line-height:1.75;color:#526078;">Bonjour {{ $user->name ?: 'cher utilisateur' }},</p>
                        <p style="margin:0;font-size:15px;line-height:1.75;color:#526078;">Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte. Cliquez sur le bouton ci-dessous pour en choisir un nouveau.</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:20px 42px 28px;">
                        <a href="{{ $resetUrl }}" style="display:inline-block;padding:15px 27px;border-radius:12px;background:#4f46e5;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;box-shadow:0 10px 24px rgba(79,70,229,.25);">Réinitialiser mon mot de passe</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 42px 30px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc;border:1px solid #e5eaf1;border-radius:14px;">
                            <tr>
                                <td style="padding:16px 18px;font-size:13px;line-height:1.65;color:#64748b;">
                                    <strong style="color:#334155;">Information de sécurité</strong><br>
                                    Ce lien est personnel et expirera dans {{ $expiresIn }} minutes. Ne le partagez avec personne.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 42px 32px;">
                        <p style="margin:0 0 10px;font-size:12px;line-height:1.6;color:#94a3b8;">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
                        <p style="margin:0;word-break:break-all;font-size:11px;line-height:1.6;color:#64748b;"><a href="{{ $resetUrl }}" style="color:#4f46e5;text-decoration:none;">{{ $resetUrl }}</a></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:23px 42px;background:#f8fafc;border-top:1px solid #e8edf3;text-align:center;">
                        <p style="margin:0 0 7px;font-size:12px;line-height:1.6;color:#64748b;">Vous n’avez pas demandé cette réinitialisation ? Ignorez simplement cet email.</p>
                        <p style="margin:0;font-size:11px;color:#a0aec0;">© {{ date('Y') }} Smart School Academy · Tous droits réservés</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
