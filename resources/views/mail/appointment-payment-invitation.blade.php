<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation au paiement</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:Arial,Helvetica,sans-serif;color:#172033;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eef2f7;padding:30px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;overflow:hidden;border-radius:22px;background:#ffffff;box-shadow:0 18px 55px rgba(15,23,42,.12);">
                <tr>
                    <td style="height:6px;background:linear-gradient(90deg,#2563eb,#7c3aed,#f59e0b);"></td>
                </tr>
                <tr>
                    <td align="center" style="padding:32px 38px 25px;background:#091426;">
                        <img src="{{ asset('images/logoSmartSchool.jpg') }}" width="76" height="76" alt="Smart School Academy" style="display:block;margin:0 auto 15px;border-radius:18px;object-fit:contain;background:#ffffff;">
                        <div style="color:#ffffff;font-size:21px;font-weight:800;">Smart School Academy</div>
                        <div style="margin-top:6px;color:#8fa1ba;font-size:11px;letter-spacing:1.4px;text-transform:uppercase;">Finalisation de l’inscription</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 40px 16px;">
                        <div style="width:50px;height:50px;line-height:50px;margin-bottom:20px;border-radius:15px;color:#ffffff;background:linear-gradient(135deg,#2563eb,#7c3aed);font-size:23px;text-align:center;">✓</div>
                        <h1 style="margin:0 0 14px;color:#172033;font-size:25px;line-height:1.3;">Votre parcours est prêt</h1>
                        <p style="margin:0 0 12px;color:#526078;font-size:15px;line-height:1.75;">Bonjour <strong>{{ $appointment->full_name }}</strong>,</p>
                        <p style="margin:0;color:#526078;font-size:15px;line-height:1.75;">Votre rendez-vous a été validé. Vous pouvez maintenant finaliser votre inscription en accédant à la page de paiement.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 40px 22px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e5eaf1;border-radius:15px;background:#f8fafc;">
                            <tr>
                                <td style="padding:19px;">
                                    <div style="margin-bottom:5px;color:#64748b;font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;">Offre sélectionnée</div>
                                    <div style="color:#172033;font-size:19px;font-weight:800;">{{ $plan['name'] }}</div>
                                    <div style="margin-top:7px;color:#4f46e5;font-size:23px;font-weight:900;">
                                        {{ $plan['amount_display'] }} {{ $plan['currency_symbol'] }}
                                        <span style="color:#94a3b8;font-size:12px;font-weight:600;">/ {{ $plan['period'] ?? 'an' }}</span>
                                    </div>
                                    @if($subjectName)
                                        <div style="margin-top:13px;color:#64748b;font-size:13px;line-height:1.6;">
                                            <strong style="color:#334155;">Parcours :</strong>
                                            {{ $subjectName }}
                                            @if($levelName) → {{ $levelName }} @endif
                                            @if($className) → {{ $className }} @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:4px 40px 30px;">
                        <a href="{{ $paymentUrl }}" style="display:inline-block;padding:15px 27px;border-radius:12px;color:#ffffff;background:linear-gradient(135deg,#2563eb,#7c3aed);box-shadow:0 10px 24px rgba(79,70,229,.25);font-size:15px;font-weight:800;text-decoration:none;">Accéder à la page de paiement</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 40px 25px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #fde68a;border-radius:13px;background:#fffbeb;">
                            <tr>
                                <td style="padding:15px 17px;color:#92400e;font-size:12px;line-height:1.65;">
                                    <strong>Lien personnel</strong><br>
                                    Ce lien est valable pendant 7 jours. Ne le partagez pas.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 40px 31px;">
                        <p style="margin:0 0 9px;color:#94a3b8;font-size:12px;line-height:1.6;">Le bouton ne fonctionne pas ? Copiez le lien suivant :</p>
                        <p style="margin:0;word-break:break-all;color:#64748b;font-size:11px;line-height:1.6;"><a href="{{ $paymentUrl }}" style="color:#4f46e5;text-decoration:none;">{{ $paymentUrl }}</a></p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:22px 38px;border-top:1px solid #e8edf3;background:#f8fafc;">
                        <p style="margin:0 0 6px;color:#64748b;font-size:12px;">Une question ? Répondez directement à cet e-mail.</p>
                        <p style="margin:0;color:#a0aec0;font-size:11px;">© {{ date('Y') }} Smart School Academy</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
