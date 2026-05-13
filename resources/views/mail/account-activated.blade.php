<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre compte est activé !</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { font-size: 28px; margin: 0 0 10px; font-weight: 700; }
        .header p { font-size: 16px; opacity: 0.95; margin: 0; }
        .content { padding: 40px 30px; }
        .success-icon { font-size: 64px; color: #10b981; margin-bottom: 20px; display: block; }
        .btn { display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 8px 20px rgba(16,185,129,0.3); transition: all 0.3s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(16,185,129,0.4); }
        .info { background: #ecfdf5; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .info h3 { color: #065f46; margin: 0 0 10px; font-size: 18px; }
        .footer { background: #f8fafc; padding: 30px; text-align: center; color: #64748b; font-size: 14px; border-top: 1px solid #e2e8f0; }
        @media (max-width: 600px) { .container { margin: 10px; } .header, .content { padding-left: 20px; padding-right: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
            <h1>Félicitations {{ $user->name }} !</h1>
            <p>Votre compte étudiant vient d'être activé</p>
        </div>
        
        <div class="content">
            <div style="text-align: center; margin-bottom: 30px;">
                <span class="success-icon">🎉</span>
                <h2 style="color: #1f2937; margin: 20px 0 10px;">Accès complet à la plateforme</h2>
                <p style="color: #6b7280; font-size: 18px; margin: 0;">
                    Vous pouvez maintenant accéder à tous les cours, lives, tests et ressources de votre plateforme éducative.
                </p>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ route('student.dashboard') }}" class="btn">
                    🚀 Accéder à mon tableau de bord
                </a>
            </div>

            <div class="info">
                <h3>📋 Vos privilèges :</h3>
                <ul style="margin: 0; padding-left: 20px; color: #374151;">
                    <li>✅ Accès à tous les cours et classes</li>
                    <li>✅ Lives et sessions interactives</li>
                    <li>✅ Soumission de devoirs et QCM</li>
                    <li>✅ Chat avec professeurs et admin</li>
                    <li>✅ Suivi absences et notes</li>
                </ul>
            </div>

            <div style="text-align: center; color: #6b7280; font-size: 14px;">
                <p>Si vous avez des questions, contactez-nous via le chat de la plateforme.</p>
            </div>
        </div>

        <div class="footer">
            <p>Plateforme Éducative &bull; Vous êtes prêt à apprendre ! 📚</p>
        </div>
    </div>
</body>
</html>

