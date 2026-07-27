# API Smart School Academy

Documentation complète de l'API REST pour l'application mobile Flutter.

**Base URL :** `http://localhost:8000/api`  
**Format des réponses :** JSON  
**Authentification :** Bearer Token (Laravel Sanctum)

---

## Sommaire

1. [Authentification](#1-authentification)
2. [Matières (Subjects)](#2-matières-subjects)
3. [Niveaux (Levels)](#3-niveaux-levels)
4. [Classes (ClassRooms)](#4-classes-classrooms)
5. [Cours (Courses)](#5-cours-courses)
6. [Lives](#6-lives)
7. [Dashboard & Statistiques](#7-dashboard--statistiques)
8. [Rendez-vous (Appointments)](#8-rendez-vous-appointments)
9. [Test Vocal](#9-test-vocal)
10. [Progression (Progress)](#10-progression-progress)
11. [Codes d'erreur](#11-codes-derreur)
12. [Structure des réponses](#12-structure-des-réponses)

---

## 1. Authentification

Toutes les routes d'authentification sont **publiques** (sauf logout).

### 1.1 Inscription

**`POST /api/register`**

Créer un nouveau compte utilisateur.

**Requête :**
```json
{
  "name": "Jean Dupont",
  "email": "jean@example.com",
  "password": "motdepasse123",
  "password_confirmation": "motdepasse123",
  "role": "student"
}
```

| Champ | Type | Requis | Défaut | Description |
|-------|------|--------|--------|-------------|
| `name` | string | ✅ | - | Nom complet |
| `email` | string | ✅ | - | Email unique |
| `password` | string | ✅ | - | Min. 8 caractères |
| `password_confirmation` | string | ✅ | - | Doit correspondre à `password` |
| `role` | string | ❌ | `student` | `student` ou `prof` |

**Réponse (201) :**
```json
{
  "success": true,
  "message": "Compte créé avec succès. En attente d'activation.",
  "data": {
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com",
      "role": "student",
      "profile_photo": null,
      "is_active": false,
      "is_paid": false,
      "subscription_type": null,
      "test_passed": false,
      "class_id": null,
      "class_name": null,
      "classes": [],
      "created_at": "2026-07-27T10:00:00.000000Z"
    },
    "token": "1|abc123def456..."
  }
}
```

**Erreurs :**
- `422` : Validation échouée (email déjà pris, mot de passe trop court, etc.)

---

### 1.2 Connexion

**`POST /api/login`**

**Requête :**
```json
{
  "email": "jean@example.com",
  "password": "motdepasse123"
}
```

| Champ | Type | Requis |
|-------|------|--------|
| `email` | string | ✅ |
| `password` | string | ✅ |

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Connexion réussie.",
  "data": {
    "user": { "...": "..." },
    "token": "2|ghi789jkl012..."
  }
}
```

**Erreurs :**
- `422` : Identifiants incorrects
- `403` : Compte inactif (pas encore activé par l'administrateur)
  ```json
  { "success": false, "message": "Votre compte n'a pas encore été activé par l'administrateur." }
  ```

---

### 1.3 Déconnexion

**`POST /api/logout`** 🔒 Authentifié

Révoque le token actuel.

**En-tête :**
```
Authorization: Bearer 2|ghi789jkl012...
```

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Déconnexion réussie."
}
```

---

### 1.4 Profil utilisateur

**`GET /api/profile`** 🔒 Authentifié

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "role": "student",
    "profile_photo": "https://...",
    "is_active": true,
    "is_paid": false,
    "subscription_type": null,
    "test_passed": false,
    "class_id": 1,
    "class_name": "Classe A1",
    "classes": [
      { "id": 1, "name": "Classe A1" }
    ],
    "created_at": "2026-07-27T10:00:00.000000Z"
  }
}
```

---

### 1.5 Mise à jour du profil

**`PUT /api/profile`** 🔒 Authentifié

**Requête :**
```json
{
  "name": "Nouveau Nom",
  "email": "nouveau@email.com"
}
```

| Champ | Type | Requis |
|-------|------|--------|
| `name` | string | ❌ (sometimes) |
| `email` | string | ❌ (sometimes) |
| `password` | string | ❌ (sometimes, min 8) |
| `password_confirmation` | string | ❌ (required if password) |

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Profil mis à jour avec succès.",
  "data": { "...": "..." }
}
```

---

### 1.6 Photo de profil

**`POST /api/profile/photo`** 🔒 Authentifié

Uploader une photo de profil (multipart/form-data).

**Requête :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `profile_photo` | file | ✅ | Image (max 2 Mo, jpg/png) |

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Profil mis à jour avec succès.",
  "data": { "...": "..." }
}
```

**En-tête :**
```
Content-Type: multipart/form-data
```

---

### 1.7 Mot de passe oublié

**`POST /api/forgot-password`**

**Requête :**
```json
{
  "email": "jean@example.com"
}
```

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Nous vous avons envoyé un lien de réinitialisation par email."
}
```

---

## 2. Matières (Subjects)

Toutes les routes des matières sont **publiques**.

### 2.1 Liste des matières

**`GET /api/subjects`**

**Paramètres :** Aucun

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Arabe",
      "type": "scolaire",
      "description": null,
      "image": null,
      "courses_count": 12,
      "levels_count": 3,
      "classes_count": 4
    },
    {
      "id": 2,
      "name": "Coran",
      "type": "religieux",
      "description": null,
      "image": null,
      "courses_count": 8,
      "levels_count": 2,
      "classes_count": 3
    }
  ]
}
```

| Champ | Type | Description |
|-------|------|-------------|
| `id` | int | Identifiant unique |
| `name` | string | Nom de la matière |
| `type` | string | `scolaire` ou `religieux` |
| `courses_count` | int | Nombre de cours disponibles |
| `levels_count` | int | Nombre de niveaux |
| `classes_count` | int | Nombre de classes |

---

### 2.2 Détail d'une matière

**`GET /api/subjects/{subject}`**

**Paramètres :**
| Paramètre | Type | Description |
|-----------|------|-------------|
| `subject` | int | ID de la matière |

**Réponse (200) :** Même structure qu'un élément de la liste.

**Erreurs :** `404` : Matière introuvable

---

### 2.3 Niveaux d'une matière

**`GET /api/subjects/{subject}/levels`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "subject": {
      "id": 1,
      "name": "Arabe",
      "type": "scolaire"
    },
    "levels": [
      {
        "id": 1,
        "name": "1ère année",
        "description": "Niveau débutant",
        "order": 1,
        "courses_count": 4,
        "classes_count": 2
      },
      {
        "id": 2,
        "name": "2ème année",
        "description": "Niveau intermédiaire",
        "order": 2,
        "courses_count": 3,
        "classes_count": 1
      }
    ]
  }
}
```

---

## 3. Niveaux (Levels)

Routes **publiques**.

### 3.1 Détail d'un niveau

**`GET /api/levels/{level}`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "1ère année",
    "description": "Niveau débutant",
    "order": 1,
    "subject": { "id": 1, "name": "Arabe" },
    "classes_count": 2
  }
}
```

---

### 3.2 Classes d'un niveau

**`GET /api/levels/{level}/classes`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "level": {
      "id": 1,
      "name": "1ère année",
      "description": "Niveau débutant"
    },
    "classes": [
      {
        "id": 1,
        "name": "Classe A1",
        "courses_count": 6,
        "subjects_count": 2,
        "students_count": 15,
        "subjects": [
          { "id": 1, "name": "Arabe" },
          { "id": 2, "name": "Coran" }
        ]
      }
    ]
  }
}
```

---

## 4. Classes (ClassRooms)

Routes **publiques**.

### 4.1 Détail d'une classe

**`GET /api/classes/{classRoom}`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Classe A1",
    "level": { "id": 1, "name": "1ère année" },
    "courses_count": 6,
    "subjects_count": 2
  }
}
```

---

### 4.2 Cours d'une classe

**`GET /api/classes/{classRoom}/courses`**

**Paramètres optionnels :**
| Paramètre | Type | Description |
|-----------|------|-------------|
| `subject_id` | int | Filtrer par matière |
| `level_id` | int | Filtrer par niveau |

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Introduction à l'Arabe",
      "description": "Les bases de la langue arabe",
      "video_url": "https://youtube.com/...",
      "video": null,
      "pdf": "https://.../document.pdf",
      "is_free": true,
      "order": 1,
      "subject": { "id": 1, "name": "Arabe" },
      "level": { "id": 1, "name": "1ère année" },
      "has_test": true,
      "created_at": "2026-07-27T10:00:00.000000Z"
    }
  ]
}
```

---

### 4.3 Matières d'une classe

**`GET /api/classes/{classRoom}/subjects`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Arabe",
      "type": "scolaire",
      "description": null,
      "courses_count": 6
    }
  ]
}
```

---

## 5. Cours (Courses)

Routes **publiques** (sauf marquer complété).

### 5.1 Liste des cours

**`GET /api/courses`**

**Paramètres optionnels :**
| Paramètre | Type | Description |
|-----------|------|-------------|
| `subject_id` | int | Filtrer par matière |
| `level_id` | int | Filtrer par niveau |
| `class_id` | int | Filtrer par classe |

**Réponse (200) :** Liste d'objets cours (même structure qu'en 4.2).

---

### 5.2 Détail d'un cours

**`GET /api/courses/{course}`**

**Réponse (200) :** Objet cours enrichi avec le test et la progression :

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Introduction à l'Arabe",
    "description": "...",
    "video_url": null,
    "video": null,
    "pdf": null,
    "course_link": null,
    "is_free": true,
    "order": 1,
    "subject": { "id": 1, "name": "Arabe" },
    "level": { "id": 1, "name": "1ère année" },
    "class": { "id": 1, "name": "Classe A1" },
    "test": {
      "id": 1,
      "title": "Test de validation",
      "questions": [
        {
          "id": 1,
          "question": "Quelle est la première lettre de l'alphabet arabe ?",
          "type": "multiple_choice",
          "answers": [
            { "id": 1, "answer": "أ (Alif)" },
            { "id": 2, "answer": "ب (Ba)" },
            { "id": 3, "answer": "ت (Ta)" },
            { "id": 4, "answer": "ث (Tha)" }
          ]
        }
      ]
    },
    "progress": {
      "completed": false,
      "score": null
    },
    "created_at": "2026-07-27T10:00:00.000000Z"
  }
}
```

> ⚠️ Le champ `is_correct` des réponses est volontairement masqué côté client Flutter.

---

### 5.3 Marquer un cours comme complété

**`POST /api/courses/{course}/complete`** 🔒 Authentifié

**Requête :**
```json
{
  "score": 85
}
```

| Champ | Type | Défaut |
|-------|------|--------|
| `score` | int | `100` |

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Cours marqué comme terminé.",
  "data": {
    "completed": true,
    "score": 85
  }
}
```

---

## 6. Lives

### 6.1 Tous les lives

**`GET /api/lives`** 🟢 Public

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Cours en direct - Les lettres arabes",
      "stream_url": "https://teams.live.com/...",
      "provider": "teams",
      "live_date": "2026-07-28",
      "start_time": "10:00:00",
      "end_time": "11:00:00",
      "class": { "id": 1, "name": "Classe A1" },
      "user": { "id": 2, "name": "Professeur Ahmed" },
      "teams_app_url": "msteams://...",
      "created_at": "2026-07-27T10:00:00.000000Z"
    }
  ]
}
```

---

### 6.2 Lives à venir

**`GET /api/lives/upcoming`** 🟢 Public

Retourne uniquement les lives dont la date est >= aujourd'hui, triés par date croissante.

---

### 6.3 Lives de l'utilisateur

**`GET /api/user/lives`** 🔒 Authentifié

Retourne les lives de la classe de l'utilisateur connecté (ou ceux qu'il a créés s'il est professeur/admin).

---

## 7. Dashboard & Statistiques

### 7.1 Statistiques publiques

**`GET /api/home/stats`** 🟢 Public

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "total_classes": 5,
    "total_courses": 20,
    "total_subjects": 2,
    "upcoming_lives": [
      {
        "id": 1,
        "title": "Cours en direct",
        "live_date": "2026-07-28",
        "start_time": "10:00:00"
      }
    ]
  }
}
```

---

### 7.2 Dashboard utilisateur

**`GET /api/dashboard`** 🔒 Authentifié

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "user": { "id": 1, "name": "Jean Dupont", "role": "student" },
    "stats": {
      "total_courses": 20,
      "completed_courses": 5,
      "completion_percentage": 25.0
    },
    "available_subjects": [
      { "id": 1, "name": "Arabe", "type": "scolaire", "courses_count": 12 },
      { "id": 2, "name": "Coran", "type": "religieux", "courses_count": 8 }
    ],
    "recent_courses": [
      { "id": 1, "title": "Introduction à l'Arabe", "score": 85 }
    ],
    "upcoming_lives": [
      { "id": 1, "title": "Cours en direct", "live_date": "2026-07-28", "start_time": "10:00:00" }
    ]
  }
}
```

---

## 8. Rendez-vous (Appointments)

### 8.1 Types de rendez-vous

**`GET /api/appointments/types`** 🟢 Public

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    { "value": "test", "label": "Test de niveau / Entretien" },
    { "value": "information", "label": "Prendre des informations" },
    { "value": "communication", "label": "Communication avec l'administration" },
    { "value": "other", "label": "Autre" }
  ]
}
```

---

### 8.2 Créer un rendez-vous

**`POST /api/appointments`** 🔒 Authentifié

**Requête :**
```json
{
  "first_name": "Jean",
  "last_name": "Dupont",
  "phone": "+212600000000",
  "email": "jean@example.com",
  "city": "Casablanca",
  "country": "Maroc",
  "type": "test",
  "vocal_test_submission_id": null
}
```

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `first_name` | string | ✅ | Prénom |
| `last_name` | string | ✅ | Nom |
| `phone` | string | ✅ | Téléphone |
| `email` | string | ✅ | Email |
| `city` | string | ✅ | Ville |
| `country` | string | ✅ | Pays |
| `type` | string | ✅ | Une des valeurs de `/types` |
| `vocal_test_submission_id` | int | ❌ | ID de la soumission vocale |

**Réponse (201) :**
```json
{
  "success": true,
  "message": "Rendez-vous créé avec succès. Nous vous contacterons rapidement."
}
```

---

### 8.3 Liste des rendez-vous

**`GET /api/appointments`** 🔒 Authentifié

Retourne les rendez-vous associés à l'email ou au téléphone de l'utilisateur connecté.

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "first_name": "Jean",
      "last_name": "Dupont",
      "phone": "+212600000000",
      "email": "jean@example.com",
      "city": "Casablanca",
      "country": "Maroc",
      "type": "test",
      "type_label": "Test de niveau / Entretien",
      "status": "pending",
      "vocal_submission": {
        "subject": "Coran",
        "level": "1ère année"
      },
      "created_at": "2026-07-27T10:00:00.000000Z"
    }
  ]
}
```

| Statut | Description |
|--------|-------------|
| `pending` | En attente de confirmation |
| `confirmed` | Confirmé par l'administration |
| `cancelled` | Annulé |

---

## 9. Test Vocal

### 9.1 Texte de récitation

**`GET /api/vocal-test/text`** 🟢 Public

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "text": "بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ...",
    "source": "Sourate Al-Fatiha (الفاتحة)"
  }
}
```

---

### 9.2 Soumettre un test vocal

**`POST /api/vocal-test/submit`** 🔒 Authentifié

**Requête (multipart/form-data) :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `subject_id` | int | ✅ | ID de la matière (doit être "Coran") |
| `level_id` | int | ✅ | ID du niveau |
| `class_id` | int | ✅ | ID de la classe |
| `audio` | file | ✅ | Fichier audio (max 15 Mo, formats: webm, mp3, wav, ogg) |

**Réponse (201) :**
```json
{
  "success": true,
  "message": "Test vocal soumis avec succès.",
  "data": {
    "id": 1,
    "consumed_at": null
  }
}
```

**Erreurs :**
- `404` : La matière n'est pas "Coran"
- `422` : Validation échouée
- `413` : Fichier trop volumineux

---

### 9.3 Liste des soumissions

**`GET /api/vocal-test/submissions`** 🔒 Authentifié

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "subject": "Coran",
      "level": "1ère année",
      "class": "Classe A1",
      "consumed_at": "2026-07-27T11:00:00.000000Z",
      "has_appointment": true,
      "appointment_status": "pending",
      "created_at": "2026-07-27T10:00:00.000000Z"
    }
  ]
}
```

> `consumed_at` indique si la soumission a déjà été utilisée pour un rendez-vous. Si `null`, elle est encore disponible.

---

## 10. Progression (Progress)

Toutes les routes de progression sont **protégées** 🔒.

### 10.1 Progression globale

**`GET /api/progress`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "total_courses": 20,
    "completed_courses": 5,
    "completion_percentage": 25.0,
    "recent_progress": [
      {
        "course_id": 1,
        "course_title": "Introduction à l'Arabe",
        "completed": true,
        "score": 85,
        "updated_at": "2026-07-27T12:00:00.000000Z"
      }
    ]
  }
}
```

---

### 10.2 Progression par matière

**`GET /api/progress/by-subject`**

**Réponse (200) :**
```json
{
  "success": true,
  "data": [
    {
      "subject_id": 1,
      "subject_name": "Arabe",
      "total_courses": 12,
      "completed_courses": 3,
      "completion_percentage": 25.0
    },
    {
      "subject_id": 2,
      "subject_name": "Coran",
      "total_courses": 8,
      "completed_courses": 2,
      "completion_percentage": 25.0
    }
  ]
}
```

---

### 10.3 Marquer un cours comme complété

**`POST /api/progress/{course}`**

**Requête :**
```json
{
  "score": 100
}
```

| Champ | Type | Défaut |
|-------|------|--------|
| `score` | int | `100` |

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Progression mise à jour.",
  "data": {
    "completed": true,
    "score": 100
  }
}
```

---

## 11. Codes d'erreur

### Structure d'une erreur

```json
{
  "success": false,
  "message": "Description de l'erreur.",
  "errors": {
    "email": ["Le champ email est requis."],
    "password": ["Le mot de passe doit contenir au moins 8 caractères."]
  }
}
```

### Codes HTTP

| Code | Description | Quand |
|------|-------------|-------|
| **200** | Succès | Requête traitée avec succès |
| **201** | Créé | Ressource créée (inscription, rendez-vous, soumission vocale) |
| **401** | Non authentifié | Token manquant ou invalide |
| **403** | Accès refusé | Compte inactif ou permissions insuffisantes |
| **404** | Introuvable | Ressource inexistante (ID invalide) |
| **422** | Validation échouée | Données invalides ou manquantes |
| **500** | Erreur serveur | Erreur interne du serveur |

---

## 12. Structure des réponses

### Succès
```json
{
  "success": true,
  "data": { "...": "..." },
  "message": "Optionnel"
}
```

### Erreur
```json
{
  "success": false,
  "message": "Message d'erreur clair.",
  "errors": {
    "field_name": ["Erreur de validation spécifique"]
  }
}
```

### Pagination
Les listes d'éléments ne sont pas paginées pour l'instant (retour complet). La pagination pourra être ajoutée ultérieurement avec les paramètres `?page=` et `?per_page=`.

---

## En-têtes requis

### Pour les routes publiques
```
Accept: application/json
```

### Pour les routes protégées
```
Accept: application/json
Authorization: Bearer {votre_token}
Content-Type: application/json
```

### Pour les uploads (multipart)
```
Accept: application/json
Authorization: Bearer {votre_token}
Content-Type: multipart/form-data
```
