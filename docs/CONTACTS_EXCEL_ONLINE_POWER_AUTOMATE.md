# Excel Online — utilisation du même webhook Laravel

Le code Laravel fourni n'est pas limité à Google Sheets.

`ContactSpreadsheetSyncService` envoie un JSON HTTP après chaque prise
de contact. Tu peux donc mettre comme `CONTACT_SHEET_WEBHOOK_URL`
l'URL d'un flux **Power Automate** qui écrit dans un fichier Excel Online.

## Colonnes conseillées dans le tableau Excel

Crée un tableau nommé `Contacts` avec ces colonnes :

- ID
- Nom
- Prénom
- Email
- Telephone
- Raison
- NombreRemplissages
- PremiereDemande
- DerniereDemande
- ConsentementMailing

## JSON reçu par Power Automate

```json
{
  "secret": "votre-secret",
  "contact_id": 15,
  "last_name": "Alaoui",
  "first_name": "Mohamed",
  "email": "mohamed@example.com",
  "phone": "0612345678",
  "reason": "Je souhaite connaître les tarifs.",
  "submissions_count": 3,
  "first_contact_at": "2026-08-01T10:00:00+01:00",
  "last_contact_at": "2026-08-11T12:00:00+01:00",
  "marketing_consent": true
}
```

## Logique du flux

1. Déclencheur HTTP.
2. Vérifier que `secret` correspond à ton secret.
3. Chercher une ligne où `ID = contact_id`.
4. Si elle existe : **mettre à jour la ligne**.
5. Sinon : **ajouter une ligne**.

Laravel calcule déjà `submissions_count`. Power Automate ne doit donc
pas l'incrémenter lui-même : il copie simplement la valeur reçue.

Cela évite qu'une même personne apparaisse plusieurs fois.
