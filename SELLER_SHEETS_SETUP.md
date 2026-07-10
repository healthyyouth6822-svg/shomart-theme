# Shomart – Google Sheets Auto-Sync Setup

Sheet: https://docs.google.com/spreadsheets/d/1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g/edit

## Step 1 – Add the header row

Open the sheet and add these headings to row 1:

`Timestamp | Serial | Shop Name | Owner Name | Phone | WhatsApp | Email | City | Address | Products | Years | Monthly Sales | Status | Post ID`

## Step 2 – Add the Apps Script

In Google Sheets, open **Extensions → Apps Script**. Delete any sample code and paste this:

```javascript
function doPost(e) {
  try {
    var ss = SpreadsheetApp.openById('1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g');
    var sheet = ss.getSheets()[0];
    var data = JSON.parse(e.postData.contents);
    sheet.appendRow([
      new Date(),
      data.serial || '',
      data.shop_name || '',
      data.owner_name || '',
      data.phone || '',
      data.whatsapp || '',
      data.email || '',
      data.city || '',
      data.address || '',
      data.products || '',
      data.years || '',
      data.monthly_sales || '',
      data.status || '',
      data.post_id || ''
    ]);
    return ContentService.createTextOutput(JSON.stringify({status:'ok'}))
      .setMimeType(ContentService.MimeType.JSON);
  } catch(err) {
    return ContentService.createTextOutput(JSON.stringify({status:'error', message: err.toString()}))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
```

Click **Save**.

## Step 3 – Deploy the Web App

1. Select **Deploy → New deployment**.
2. Choose **Web app** as the deployment type.
3. Set **Execute as** to **Me**.
4. Set **Who has access** to **Anyone**.
5. Click **Deploy** and complete Google's authorization prompts.
6. Copy the Web App URL. It should end in `/exec`.

## Step 4 – Enable sync in the theme

In `functions.php`, replace the empty value in this line with the copied Web App URL:

```php
define('SHOMART_SHEETS_WEBHOOK_URL', 'https://script.google.com/macros/s/YOUR_ID/exec');
```

Leave the value empty to keep sync disabled:

```php
define('SHOMART_SHEETS_WEBHOOK_URL', '');
```

Seller details are posted automatically when an application is saved as **Approved** or **Active**. If Google Sheets cannot be reached, the admin save still completes and the failure is written only to the WordPress/PHP error log.
