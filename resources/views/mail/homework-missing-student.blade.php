<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Devoir non remis</title>
</head>
<body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;">
<tr><td align="center">
<table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(15,23,42,.08);">
<tr><td style="padding:24px 28px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);color:#fff;">
    <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.85;">Smart School Academy</div>
    <h1 style="margin:8px 0 0;font-size:22px;">Rappel de devoir</h1>
</td></tr>
<tr><td style="padding:28px;line-height:1.65;font-size:15px;">
    <p>Bonjour <strong>{{ $student->name }}</strong>,</p>
    <p>Nous vous informons que le devoir suivant n'a pas encore été remis dans le délai prévu :</p>
    <div style="padding:16px 18px;margin:18px 0;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
        <strong>{{ $assignment->title }}</strong><br>
        Matière : {{ optional($assignment->subject)->name ?? '—' }}<br>
        Classe : {{ optional($assignment->classRoom)->name ?? '—' }}<br>
        Date limite : {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : '—' }}
    </div>
    <p>Merci de consulter votre espace étudiant et de régulariser ce devoir dès que possible.</p>
    <p style="margin-bottom:0;">Cordialement,<br><strong>Smart School Academy</strong><br><span style="color:#64748b;">L'école à portée de main</span></p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
