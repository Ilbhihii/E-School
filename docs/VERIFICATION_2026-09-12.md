# Vérification technique — 12/09/2026

## Corrections incluses

- routes paiements étudiants restaurées (`admin.student-payments.*`) ;
- routes création assistée étudiant restaurées (`admin.users.create`, `admin.users.store`) ;
- chat privé Professeur ↔ Étudiant conservé ;
- `Assignment::classRoom()` présent ;
- numérotation automatique des devoirs par semaine conservée ;
- date limite automatique J+5 conservée ;
- initialisation Firebase silencieuse en production si elle n'est pas configurée ;
- migration historique `class_user.class_id` rendue compatible avec une base neuve ;
- modèle `.env.production.example` ajouté avec `APP_DEBUG=false`.

## Vérifications exécutées

- syntaxe PHP sur `app/`, `routes/` et `database/migrations` ;
- `php artisan route:list` ;
- `php artisan route:cache` ;
- `php artisan view:cache` ;
- `php artisan config:cache`.

## Production

Le vrai fichier `.env` n'est volontairement pas fourni dans l'archive distribuable.
Sur le VPS, conserver les mots de passe et secrets dans `.env` uniquement et utiliser :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.smart-school-academy.com
```

Puis :

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
