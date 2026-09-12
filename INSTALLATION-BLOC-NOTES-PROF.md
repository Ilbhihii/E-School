# Smart School Academy — Backend complet avec Bloc-notes Professeur
Date : 12/09/2026

Cette archive contient le backend complet corrigé avec le nouveau module
**Bloc-notes pédagogique** dans l'espace Professeur.

## Nouveau module

Parcours :

Matière → Niveau → Classe → Étudiant

Le professeur peut :

- choisir uniquement une matière / un niveau / une classe qui lui sont affectés ;
- voir uniquement les étudiants de cette classe ;
- ajouter un **point positif** ou un **point négatif** ;
- choisir de 1 à 5 points ;
- écrire une observation ;
- choisir la date ;
- consulter le total positif ;
- consulter le total négatif ;
- consulter le solde ;
- consulter l'historique ;
- modifier une observation ;
- supprimer une observation.

Les observations sont privées au professeur qui les a créées.

## Routes ajoutées

- `prof.behavior-notes.index`
- `prof.behavior-notes.show`
- `prof.behavior-notes.store`
- `prof.behavior-notes.update`
- `prof.behavior-notes.destroy`

URL principale :

`/prof/bloc-notes`

## Base de données

Nouvelle migration :

`database/migrations/2026_09_12_103000_create_student_behavior_notes_table.php`

Nouvelle table :

`student_behavior_notes`

## Installation sur le VPS

Après déploiement du code :

```bash
cd /var/www/e-school
php artisan migrate
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Vérification :

```bash
php artisan route:list | grep behavior-notes
php artisan route:list | grep bloc-notes
```

## Installation locale Windows

```cmd
cd /d F:\backend
php artisan migrate
php artisan optimize:clear
php artisan route:list | findstr behavior-notes
php artisan route:list | findstr bloc-notes
```

## Vérifications effectuées avant création du ZIP

- 621 fichiers PHP / Blade analysés : aucune erreur de syntaxe ;
- routes du Bloc-notes : chargées correctement ;
- `php artisan route:list` : OK ;
- `php artisan view:cache` : OK.

## Sécurité

L'archive n'inclut volontairement pas :

- `.env`
- `.git/`
- `vendor/`
- `node_modules/`
- logs Laravel
- données utilisateurs privées de `storage/app/`

Conservez donc votre `.env` existant.

En production :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.smart-school-academy.com
```
