# V11 — GitHub puis VPS

## PC Windows

Après avoir copié V11 par-dessus V10 :

```bat
git status
git diff --check
```

Ajouter uniquement les fichiers V11 :

```bat
git add .env.example
git add .env.production.example
git add app/Console/Commands/NotificationDiagnostics.php
git add app/Console/Commands/SendLiveReminders.php
git add app/Console/Commands/SendTestNotification.php
git add app/Console/Kernel.php
git add app/Http/Controllers/API/NotificationController.php
git add app/Http/Controllers/NotificationController.php
git add app/Models/DeviceToken.php
git add app/Providers/AppServiceProvider.php
git add app/Services/AdminNotificationService.php
git add app/Services/PushNotificationService.php
git add app/Services/SchoolNotificationService.php
git add config/push.php
git add database/migrations/2026_10_03_030000_expand_device_tokens_for_web_push.php
git add public/js/ssa-push-notifications-v1.js
git add public/sw.js
git add resources/views/components/notification-bell.blade.php
git add routes/notifications.php
git add docs/V11_PUSH_NOTIFICATIONS.md
git add docs/V11_GIT_VPS_COMMANDS.md
```

Puis :

```bat
git diff --cached --check
git status
git commit -m "Enable push notifications for admin professor and students"
git push origin main
git push origin main:master
```

## VPS

Toujours vérifier avant le pull :

```bash
cd /var/www/e-school
git status
git fetch origin
git log --oneline HEAD..origin/main
```

Si le working tree est propre :

```bash
php artisan down --retry=60
git pull --ff-only origin main
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

Configurer ensuite Firebase dans `.env`, puis :

```bash
php artisan config:clear
php artisan config:cache
php artisan notifications:diagnose
```

Ne jamais commiter le JSON du compte de service Firebase.
