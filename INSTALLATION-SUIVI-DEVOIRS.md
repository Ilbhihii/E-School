# Smart School Academy — Détection automatique des devoirs non remis

## Fonctionnement

Le système vérifie chaque jour les devoirs dont la date limite est dépassée.

- si l'étudiant n'a pas remis le devoir : un rappel e-mail étudiant est envoyé ;
- à partir de 3 devoirs non remis sur les 30 derniers jours : une réclamation est envoyée aux parents liés au compte ;
- la réclamation parent n'est pas renvoyée tous les jours ;
- par défaut, une nouvelle réclamation parent n'est envoyée qu'après 7 jours et lorsque le nombre de devoirs manquants a augmenté d'au moins 2 ;
- tous les envois sont enregistrés dans `assignment_reminders`.

## E-mail parent

Le texte utilise une formulation professionnelle :

> Plusieurs devoirs demandés par ses enseignants n'ont pas été remis dans les délais prévus.

L'e-mail liste les devoirs concernés, leurs matières, classes et dates limites.

## Administration

Nouvelle page :

`/admin/homework-reminders`

Elle affiche :

- devoirs actuellement non remis ;
- étudiants concernés ;
- historique des rappels ;
- réclamations parents ;
- bouton « Vérifier et envoyer ».

## Installation

```bash
php artisan migrate
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Test manuel sans attendre le scheduler :

```bash
php artisan homework:check-missing
```

## Scheduler VPS

Vérifiez que le cron Laravel existe :

```cron
* * * * * cd /var/www/e-school && php artisan schedule:run >> /dev/null 2>&1
```

Laravel lancera alors `homework:check-missing` chaque jour à 08:00.

## Réglages `.env`

```env
HOMEWORK_REMINDERS_ENABLED=true
HOMEWORK_STUDENT_EMAIL_ENABLED=true
HOMEWORK_PARENT_EMAIL_ENABLED=true
HOMEWORK_PARENT_THRESHOLD=3
HOMEWORK_PARENT_WINDOW_DAYS=30
HOMEWORK_PARENT_REPEAT_STEP=2
HOMEWORK_PARENT_COOLDOWN_DAYS=7
HOMEWORK_REMINDER_SCHEDULE_TIME=08:00
```

## SMS

Un adaptateur Twilio est inclus (`HomeworkSmsService`) mais le projet actuel ne stocke pas encore de numéro de téléphone sur les comptes `users`.
L'e-mail fonctionne immédiatement avec le SMTP déjà configuré. Pour activer réellement le SMS, il faudra ajouter les numéros de téléphone aux comptes puis renseigner :

```env
HOMEWORK_SMS_ENABLED=true
TWILIO_SID=...
TWILIO_TOKEN=...
TWILIO_FROM=...
```

## Important

La détection réutilise la structure actuelle du projet où `assignments` contient à la fois les devoirs professeur et les remises étudiant. Elle respecte le parcours Matière → Classe et borne les remises entre la création d'un devoir et le devoir suivant du même parcours.
