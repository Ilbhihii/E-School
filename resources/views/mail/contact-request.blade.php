<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouvelle prise de contact</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 35px rgba(15,23,42,.08);">
                    <tr>
                        <td style="padding:28px 32px;background:#0b1220;color:#ffffff;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:1.4px;color:#f0b94d;text-transform:uppercase;margin-bottom:8px;">
                                Smart School Academy
                            </div>
                            <h1 style="margin:0;font-size:24px;line-height:1.25;">
                                Nouvelle prise de contact
                            </h1>
                            <p style="margin:9px 0 0;color:#aebbd0;font-size:14px;line-height:1.6;">
                                Une personne vient de remplir le formulaire de la page d’accueil.
                            </p>
                            @if(!empty($contact['is_repeat']))
                                <div style="display:inline-block;margin-top:14px;padding:7px 10px;border-radius:999px;background:#f59e0b;color:#111827;font-size:12px;font-weight:800;">
                                    Contact récurrent · {{ (int) $contact['submissions_count'] }} remplissages
                                </div>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">Prénom</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 20px;font-size:17px;font-weight:700;">{{ $contact['first_name'] }}</td>
                                </tr>

                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">Nom</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 20px;font-size:17px;font-weight:700;">{{ $contact['last_name'] }}</td>
                                </tr>

                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">E-mail</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 20px;font-size:16px;">
                                        <a href="mailto:{{ $contact['email'] }}" style="color:#4f6ff5;text-decoration:none;">{{ $contact['email'] }}</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">Téléphone</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 26px;font-size:16px;">
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['phone']) }}" style="color:#4f6ff5;text-decoration:none;">{{ $contact['phone'] }}</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">Pays</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 26px;font-size:16px;font-weight:700;">
                                        {{ $contact['country'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">
                                        Commentaire / Raison
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 26px;font-size:15px;line-height:1.7;color:#334155;white-space:pre-line;">
                                        {{ $contact['reason'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:0 0 9px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.7px;">
                                        Autorisation mailing
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 26px;font-size:15px;font-weight:700;color:{{ !empty($contact['marketing_consent']) ? '#15803d' : '#64748b' }};">
                                        {{ !empty($contact['marketing_consent']) ? 'Oui' : 'Non' }}
                                    </td>
                                </tr>
                            </table>

                            <a href="mailto:{{ $contact['email'] }}" style="display:inline-block;background:#4f6ff5;color:#ffffff;text-decoration:none;font-weight:700;padding:13px 20px;border-radius:10px;">
                                Répondre au contact
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 32px;background:#f8fafc;color:#64748b;font-size:12px;line-height:1.5;">
                            Message envoyé automatiquement depuis le formulaire « Prise de contact » de Smart School Academy.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
