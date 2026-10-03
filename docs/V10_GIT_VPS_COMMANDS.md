# V10 — GitHub puis VPS

## 1. Windows / dépôt local

Après extraction du ZIP V10 à la racine de `F:\backend` :

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\v10-move-public-backups.ps1
```

Puis vérifier :

```bat
git status
git diff --check
```

Ajouter uniquement V10 :

```bat
git add .env.example .env.production.example .gitignore
git add app/Console/Commands/ProductionPreflight.php
git add app/Http/Controllers/API/AuthController.php
git add app/Http/Controllers/Admin/FlexibleTestController.php
git add app/Http/Controllers/AssignmentFileController.php
git add app/Http/Controllers/Auth/PasswordResetLinkController.php
git add app/Services/LearningPathService.php
git add config/app.php config/cors.php
git add database/migrations/2026_09_29_021500_create_pedagogical_time_slots_table.php
git add database/migrations/2026_09_29_024000_create_pedagogical_time_slots_and_live_scope.php
git add database/migrations/2026_10_03_010000_normalize_pedagogical_time_slot_indexes.php
git add routes/api.php routes/auth.php routes/web.php
git add scripts/v10-move-public-backups.ps1 scripts/v10-move-public-backups.sh
git add docs/V10_PRODUCTION_HARDENING.md docs/V10_GIT_VPS_COMMANDS.md
git add -u public/storage_backup_20260802
```

Contrôle avant commit :

```bat
git status
git diff --cached --check
git diff --cached --stat
```

Commit et push :

```bat
git commit -m "Harden production security and deployment safeguards"
git push origin main
git push origin main:master
```

Ne pas ajouter les anciens `APPLY_*.php`, `FIX_*.php` ni `storage/feature-backups/`.

## 2. VPS

Avant le pull :

```bash
cd /var/www/e-school
git status
git branch --show-current
```

Si le working tree est propre :

```bash
git fetch origin
git pull --ff-only origin main
```

Déplacer tout ancien backup hors de `public/` :

```bash
bash scripts/v10-move-public-backups.sh
```

Configurer `.env` au minimum :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://smart-school-academy.com
LOG_LEVEL=warning
CORS_ALLOWED_ORIGINS=https://smart-school-academy.com
```

Puis :

```bash
php artisan optimize:clear
php artisan production:preflight
php artisan down --retry=60
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan production:preflight
php artisan up
```

Vérification finale :

```bash
git status
git log --oneline -5
php artisan migrate:status
```

### Limite 2 Go

Le code Laravel accepte jusqu'à 2 Go, mais Nginx et PHP-FPM doivent également être configurés. Exemple :

```nginx
client_max_body_size 2050M;
client_body_timeout 3600s;
```

PHP-FPM :

```ini
upload_max_filesize = 2048M
post_max_size = 2050M
max_execution_time = 3600
max_input_time = 3600
```

Après modification, recharger les services concernés.
