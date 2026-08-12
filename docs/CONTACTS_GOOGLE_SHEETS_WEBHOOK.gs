/**
 * Smart School Academy — Synchronisation automatique des contacts
 * vers Google Sheets.
 *
 * Installation :
 * 1. Créer un Google Sheet.
 * 2. Extensions > Apps Script.
 * 3. Coller ce fichier.
 * 4. Modifier CHANGE_THIS_SECRET ci-dessous.
 * 5. Exécuter setupContactSheet() une fois.
 * 6. Déployer > Nouveau déploiement > Application Web.
 * 7. Exécuter en tant que : Moi.
 * 8. Accès : Toute personne disposant du lien.
 * 9. Copier l'URL /exec dans CONTACT_SHEET_WEBHOOK_URL.
 */

const CONTACT_SHEET_NAME = 'Contacts';

function setupContactSheet() {
  const secret = 'CHANGE_THIS_SECRET';

  if (
    secret === 'CHANGE_THIS_SECRET'
    || secret.length < 16
  ) {
    throw new Error(
      'Modifiez CHANGE_THIS_SECRET avec une valeur longue et aléatoire.'
    );
  }

  PropertiesService
    .getScriptProperties()
    .setProperty(
      'CONTACT_SHEET_SECRET',
      secret
    );

  const sheet = ensureContactSheet_();

  SpreadsheetApp
    .getActiveSpreadsheet()
    .setActiveSheet(sheet);

  sheet.autoResizeColumns(1, 12);
}

function doPost(e) {
  const lock =
    LockService.getScriptLock();

  try {
    lock.waitLock(10000);

    const body = JSON.parse(
      e.postData.contents || '{}'
    );

    const configuredSecret =
      PropertiesService
        .getScriptProperties()
        .getProperty(
          'CONTACT_SHEET_SECRET'
        );

    if (
      !configuredSecret
      || body.secret !== configuredSecret
    ) {
      return jsonResponse_(
        {
          ok: false,
          message: 'Unauthorized',
        }
      );
    }

    const sheet =
      ensureContactSheet_();

    const row = [
      String(body.contact_id || ''),
      String(body.last_name || ''),
      String(body.first_name || ''),
      String(body.email || ''),
      String(body.phone || ''),
      String(body.country || ''),
      String(body.reason || ''),
      Number(
        body.submissions_count || 1
      ),
      String(
        body.first_contact_at || ''
      ),
      String(
        body.last_contact_at || ''
      ),
      body.marketing_consent
        ? 'Oui'
        : 'Non',
      new Date(),
    ];

    const existingRow =
      findExistingContactRow_(
        sheet,
        body
      );

    if (existingRow) {
      sheet
        .getRange(
          existingRow,
          1,
          1,
          row.length
        )
        .setValues([row]);
    } else {
      sheet.appendRow(row);
    }

    return jsonResponse_(
      {
        ok: true,
        action: existingRow
          ? 'updated'
          : 'created',
        row: existingRow
          || sheet.getLastRow(),
      }
    );
  } catch (error) {
    return jsonResponse_(
      {
        ok: false,
        message: String(
          error.message || error
        ),
      }
    );
  } finally {
    try {
      lock.releaseLock();
    } catch (ignored) {
      // Rien à faire.
    }
  }
}

function ensureContactSheet_() {
  const spreadsheet =
    SpreadsheetApp
      .getActiveSpreadsheet();

  let sheet =
    spreadsheet.getSheetByName(
      CONTACT_SHEET_NAME
    );

  if (!sheet) {
    sheet = spreadsheet.insertSheet(
      CONTACT_SHEET_NAME
    );
  }

  const headers = [
    'ID',
    'Nom',
    'Prénom',
    'E-mail',
    'Téléphone',
    'Pays',
    'Raison / commentaire récent',
    'Nombre de remplissages',
    'Première demande',
    'Dernière demande',
    'Consentement mailing',
    'Synchronisé le',
  ];

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(headers);
  } else {
    sheet
      .getRange(
        1,
        1,
        1,
        headers.length
      )
      .setValues([headers]);
  }

  sheet.setFrozenRows(1);

  return sheet;
}

function findExistingContactRow_(
  sheet,
  body
) {
  const lastRow =
    sheet.getLastRow();

  if (lastRow < 2) {
    return null;
  }

  const values =
    sheet
      .getRange(
        2,
        1,
        lastRow - 1,
        12
      )
      .getValues();

  const contactId =
    String(
      body.contact_id || ''
    ).trim();

  const email =
    normalizeEmail_(
      body.email
    );

  const phone =
    normalizePhone_(
      body.phone
    );

  for (
    let index = 0;
    index < values.length;
    index++
  ) {
    const row = values[index];

    const rowId =
      String(row[0] || '').trim();

    const rowEmail =
      normalizeEmail_(row[3]);

    const rowPhone =
      normalizePhone_(row[4]);

    if (
      contactId
      && rowId === contactId
    ) {
      return index + 2;
    }

    if (
      email
      && rowEmail === email
    ) {
      return index + 2;
    }

    if (
      phone
      && rowPhone === phone
    ) {
      return index + 2;
    }
  }

  return null;
}

function normalizeEmail_(value) {
  return String(value || '')
    .trim()
    .toLowerCase();
}

function normalizePhone_(value) {
  return String(value || '')
    .replace(/\D/g, '');
}

function jsonResponse_(data) {
  return ContentService
    .createTextOutput(
      JSON.stringify(data)
    )
    .setMimeType(
      ContentService.MimeType.JSON
    );
}
