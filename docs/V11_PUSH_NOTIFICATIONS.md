# V11 — Notifications téléphone + navigateur

Cette version complète le centre de notifications Laravel existant avec Firebase Cloud Messaging (FCM).

## Cibles

- Web : Chrome, Edge, Firefox et Opera sous HTTPS.
- PWA installée sur téléphone ou PC : même mécanisme Web Push.
- Application mobile native : FCM via les routes API déjà présentes.
- Rôles activés côté Web : administrateur, professeur, étudiant.

Le navigateur exige obligatoirement une action de l'utilisateur pour afficher la demande d'autorisation. Le site ne peut pas contourner ce réglage.

## Événements branchés

- Administrateur : inscriptions, rendez-vous, contacts, tests, paiements, devoirs étudiants, messages et propositions de cours.
- Professeur : live créé/modifié, devoir étudiant reçu, messages, cours refusé/validé selon le cas.
- Étudiant : nouveau cours, nouveau devoir, correction de devoir, live créé/modifié, messages.
- Rappel live : environ 30 minutes avant le début si le scheduler Laravel est actif.

Toutes les notifications restent également visibles dans le centre de notifications du site.

## 1. Firebase Console

Utiliser le même projet Firebase pour le serveur, le Web et l'application mobile.

### Application Web Firebase

Dans Firebase Console > Project settings > Your apps, créer/ouvrir l'application Web puis copier :

- apiKey
- authDomain
- projectId
- storageBucket
- messagingSenderId
- appId

### Clé Web Push / VAPID

Firebase Console > Project settings > Cloud Messaging > Web Push certificates.

Générer ou utiliser la paire existante et copier la clé publique VAPID.

### Compte de service serveur

Firebase Console > Project settings > Service accounts > Generate new private key.

Ne jamais placer ce JSON dans `public/` ou dans Git.

Exemple VPS :

```bash
mkdir -p /var/www/e-school/storage/app/firebase
chmod 700 /var/www/e-school/storage/app/firebase
# copier le JSON dans :
# /var/www/e-school/storage/app/firebase/service-account.json
chmod 600 /var/www/e-school/storage/app/firebase/service-account.json
```

## 2. Variables `.env` VPS

```env
PUSH_NOTIFICATIONS_ENABLED=true
FIREBASE_CREDENTIALS=/var/www/e-school/storage/app/firebase/service-account.json

FIREBASE_WEB_API_KEY=...
FIREBASE_WEB_AUTH_DOMAIN=....firebaseapp.com
FIREBASE_WEB_PROJECT_ID=...
FIREBASE_WEB_STORAGE_BUCKET=....appspot.com
FIREBASE_WEB_MESSAGING_SENDER_ID=...
FIREBASE_WEB_APP_ID=...
FIREBASE_WEB_VAPID_KEY=...
```

Les valeurs de configuration Web et la clé VAPID sont des informations publiques nécessaires au navigateur. Le JSON du compte de service, lui, est secret.

## 3. Déploiement

V11 doit être posé après V10.

```bash
php artisan down --retry=60
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

## 4. Scheduler pour les rappels de live

Vérifier le cron VPS :

```cron
* * * * * cd /var/www/e-school && php artisan schedule:run >> /dev/null 2>&1
```

Test manuel :

```bash
php artisan notifications:live-reminders
```

## 5. Diagnostic

```bash
php artisan notifications:diagnose
```

Puis envoyer un test :

```bash
php artisan notifications:test --role=admin
php artisan notifications:test --role=prof
php artisan notifications:test --role=student
```

Pour un compte précis :

```bash
php artisan notifications:test --user=123
```

## 6. Activation dans le navigateur

1. Se connecter comme admin/prof/étudiant.
2. Ouvrir la cloche des notifications.
3. Cliquer `Activer sur cet appareil`.
4. Accepter la demande du navigateur.
5. Le bouton de test apparaît après l'enregistrement réussi.

Si l'utilisateur clique sur `Bloquer`, le site ne peut pas rouvrir lui-même la demande : il faut réautoriser le domaine dans les réglages du navigateur.

## 7. Application mobile / Flutter

Le backend accepte déjà les tokens FCM via Sanctum :

```http
POST /api/notifications/register-token
Authorization: Bearer <token-sanctum>
Content-Type: application/json

{
  "token": "<FCM_TOKEN>",
  "platform": "android"
}
```

Valeurs de `platform` : `android`, `ios`, `web`.

Au rafraîchissement du token Firebase, rappeler `register-token`.
Avant une déconnexion mobile, appeler :

```http
POST /api/notifications/unregister-token
{
  "token": "<FCM_TOKEN>"
}
```

Le code Flutter n'est pas inclus dans l'archive backend. Si l'application mobile ne réalise pas encore ces appels, son code source devra être modifié séparément.

## 8. Sécurité / confidentialité

V11 change aussi le service worker : les URLs `/admin`, `/prof`, `/student`, `/parent`, `/notifications` et `/api` ne sont plus mises en cache. Cela évite de conserver des pages authentifiées potentiellement sensibles dans le cache partagé du navigateur.
