# V10 — Production hardening

Cette version est basée sur le ZIP fourni le 03/10/2026 et inclut les correctifs précédents présents dans la branche `main` jusqu'au commit `9bf10ea`.

## Corrections prioritaires intégrées

1. **Migration `pedagogical_time_slots` réparable automatiquement**
   - Suppression de l'ancien index UNIQUE `(day_of_week, slot_number)` qui provoquait l'erreur MySQL `Duplicate entry '1-2'` pendant la renumérotation des créneaux.
   - La migration qui avait échoué peut être relancée directement avec `php artisan migrate --force`.
   - Une migration corrective supplémentaire protège les bases où l'ancienne migration aurait déjà été enregistrée.

2. **Protection des routes admin des tests flexibles**
   - Ajout du middleware `isAdmin` au niveau des routes.
   - Le contrôle interne du contrôleur est conservé en défense supplémentaire.

3. **Correction de validation des tests flexibles**
   - `class_id` valide maintenant `class_rooms.id` au lieu de l'ancienne table `classes`.

4. **Authentification publique renforcée**
   - Limitation des inscriptions et demandes de mot de passe.
   - Login API protégé par un verrou par couple e-mail/IP après 5 échecs.
   - Réponse de mot de passe oublié identique qu'un compte existe ou non pour éviter l'énumération des comptes.
   - Les détails de configuration mail ne sont plus exposés aux visiteurs.

5. **CORS production**
   - Les origines ne sont plus ouvertes à `*` par défaut.
   - Variable : `CORS_ALLOWED_ORIGINS=https://smart-school-academy.com`
   - Plusieurs domaines peuvent être séparés par des virgules.

6. **Mode debug production**
   - Quand `APP_ENV=production`, le code force `app.debug=false` même si `APP_DEBUG=true` est laissé par erreur.
   - Le VPS doit malgré tout être configuré avec `APP_ENV=production` et `APP_DEBUG=false`.

7. **Accès professeur aux contenus étudiants**
   - Les fichiers/copies étudiants sont maintenant vérifiés avec le périmètre exact du professeur : matière, niveau, classe, groupe et, lorsqu'ils existent, jour/heure/code pédagogique.
   - Les nouveaux cours sont également isolés par groupe/code pour les professeurs.
   - Les anciennes données sans groupe/code gardent un fallback de compatibilité.

8. **Backups hors `public/`**
   - `public/storage_backup_*` est désormais ignoré par Git.
   - Tout backup déjà présent sur un serveur doit être déplacé hors du web root. Scripts fournis : `scripts/v10-move-public-backups.sh` (Linux/VPS) et `scripts/v10-move-public-backups.ps1` (Windows).

9. **Nettoyage Git local**
   - `APPLY_*.php`, `FIX_*.php`, `storage/feature-backups/` et `.claude/` sont ignorés afin d'éviter leur ajout accidentel au dépôt.

10. **Préflight de production**
    - Nouvelle commande : `php artisan production:preflight`
    - Elle vérifie environnement, debug, HTTPS, CORS, backups publics et espace disque.

## À faire sur le VPS avant remise en ligne

Vérifier/adapter `.env` :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://smart-school-academy.com
LOG_LEVEL=warning
CORS_ALLOWED_ORIGINS=https://smart-school-academy.com
```

Pour les fichiers jusqu'à 2 Go, PHP-FPM et Nginx doivent aussi accepter cette taille. La commande `production:preflight` donne un avertissement pour les valeurs PHP CLI, mais PHP-FPM doit être vérifié séparément.

## Point important restant pour une phase ultérieure

Laravel 8 est ancien et ne devrait pas être migré brutalement en même temps que ces correctifs. Prévoir une montée de version séparée, avec tests de non-régression.
