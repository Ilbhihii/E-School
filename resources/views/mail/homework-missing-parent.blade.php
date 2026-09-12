<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suivi pédagogique</title>
</head>
<body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;">
<tr><td align="center">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;width:100%;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(15,23,42,.08);">
<tr><td style="padding:24px 28px;background:linear-gradient(135deg,#7c2d12,#dc2626);color:#fff;">
    <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.88;">Smart School Academy</div>
    <h1 style="margin:8px 0 0;font-size:22px;">Suivi pédagogique de votre enfant</h1>
</td></tr>
<tr><td style="padding:28px;line-height:1.65;font-size:15px;">
    <p>Bonjour {{ $parent->name }},</p>
    <p>Nous souhaitons attirer votre attention sur le suivi scolaire de votre enfant <strong>{{ $student->name }}</strong>.</p>
    <p>À ce jour, <strong>plusieurs devoirs demandés par ses enseignants n'ont pas été remis dans les délais prévus</strong>.</p>

    <div style="margin:20px 0;padding:16px 18px;background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;">
        <strong>Devoirs non remis : {{ $missingAssignments->count() }}</strong>
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:18px 0;">
        @foreach($missingAssignments->take(10) as $item)
            <tr>
                <td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;">
                    <strong>{{ $item->assignment->title }}</strong><br>
                    <span style="color:#64748b;font-size:13px;">
                        {{ optional($item->assignment->subject)->name ?? 'Matière' }}
                        @if($item->assignment->classRoom)
                            · {{ $item->assignment->classRoom->name }}
                        @endif
                        @if($item->assignment->due_date)
                            · échéance {{ $item->assignment->due_date->format('d/m/Y') }}
                        @endif
                    </span>
                </td>
            </tr>
        @endforeach
    </table>

    <p>Nous vous invitons à échanger avec votre enfant afin qu'il puisse régulariser les devoirs manquants et poursuivre son apprentissage dans de bonnes conditions.</p>
    <p>Pour toute information complémentaire, notre équipe reste à votre disposition.</p>
    <p style="margin-bottom:0;">Cordialement,<br><strong>Smart School Academy</strong><br><span style="color:#64748b;">L'école à portée de main</span></p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
