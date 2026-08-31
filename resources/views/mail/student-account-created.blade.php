<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos accès étudiant — Smart School Academy</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f1f5f9;padding:30px 12px;">
    <tr>
        <td align="center">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 35px rgba(15,23,42,.10);">

                <tr>
                    <td align="center"
                        style="padding:30px 26px 26px;background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 58%,#7c3aed 100%);">

                        <div style="background:#ffffff;width:82px;height:82px;border-radius:18px;padding:6px;box-sizing:border-box;display:inline-block;box-shadow:0 8px 20px rgba(0,0,0,.18);">
                            <img
                                src="{{ asset('images/logoSmartSchool.jpg') }}"
                                width="70"
                                height="70"
                                alt="Smart School Academy"
                                style="display:block;width:70px;height:70px;border-radius:13px;object-fit:cover;border:0;"
                            >
                        </div>

                        <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;font-weight:700;color:#bfdbfe;margin-top:16px;">
                            Espace étudiant
                        </div>

                        <h1 style="margin:7px 0 0;font-size:26px;line-height:1.2;color:#ffffff;">
                            Smart School Academy
                        </h1>

                        <p style="margin:8px 0 0;color:#dbeafe;font-size:14px;">
                            Votre compte étudiant est prêt
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:34px 34px 10px;">
                        <p style="margin:0 0 18px;font-size:16px;line-height:1.7;color:#334155;">
                            Bonjour <strong style="color:#0f172a;">{{ $student->name }}</strong>,
                        </p>

                        <p style="margin:0;font-size:15px;line-height:1.75;color:#475569;">
                            Notre équipe a créé votre compte étudiant Smart School Academy afin de vous permettre
                            d’accéder facilement à la plateforme.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 34px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;">
                            <tr>
                                <td style="padding:20px 22px 12px;">
                                    <div style="font-size:11px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;color:#64748b;margin-bottom:7px;">
                                        Adresse e-mail
                                    </div>
                                    <div style="font-size:16px;font-weight:700;color:#1e3a8a;word-break:break-all;">
                                        {{ $student->email }}
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0 22px;">
                                    <div style="height:1px;background:#e2e8f0;"></div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 22px 20px;">
                                    <div style="font-size:11px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;color:#64748b;margin-bottom:8px;">
                                        Mot de passe temporaire
                                    </div>
                                    <div style="display:inline-block;background:#0f172a;color:#ffffff;padding:10px 14px;border-radius:9px;font-family:Consolas,Monaco,monospace;font-size:18px;font-weight:700;letter-spacing:1px;">
                                        {{ $temporaryPassword }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:4px 34px 10px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;">
                            <tr>
                                <td style="padding:15px 17px;color:#1e3a8a;font-size:13px;line-height:1.6;">
                                    <strong>🔒 Sécurité :</strong>
                                    ce mot de passe temporaire est valable jusqu’au
                                    <strong>{{ $expiresAt }}</strong>.
                                    Pensez à le remplacer lors de votre première connexion.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:24px 34px 34px;">
                        <a href="{{ $loginUrl }}"
                           style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 24px;border-radius:10px;box-shadow:0 8px 20px rgba(37,99,235,.22);">
                            Se connecter à mon espace étudiant
                        </a>

                        <p style="margin:20px 0 0;font-size:12px;line-height:1.6;color:#94a3b8;">
                            Si le bouton ne fonctionne pas, vous pouvez accéder directement à la page de connexion
                            depuis le site Smart School Academy.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:20px 26px;background:#0f172a;">
                        <div style="font-size:13px;font-weight:700;color:#ffffff;">
                            Smart School Academy
                        </div>
                        <div style="margin-top:6px;font-size:11px;color:#94a3b8;">
                            L’école à portée de main
                        </div>
                    </td>
                </tr>

            </table>

            <p style="max-width:640px;margin:16px auto 0;font-size:11px;line-height:1.5;color:#94a3b8;text-align:center;">
                Cet e-mail contient des informations de connexion confidentielles. Ne partagez jamais votre mot de passe.
            </p>

        </td>
    </tr>
</table>
</body>
</html>
