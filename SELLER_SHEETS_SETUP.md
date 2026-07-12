# Shomart – Google Sheets Auto-Sync Setup

Sheet: https://docs.google.com/spreadsheets/d/1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g/edit

## Sheet Tabs (3 tabs needed)

| Tab Name | Purpose |
|---|---|
| **Sheet1** (default) | Seller Applications |
| **Products** | Product requests from form |
| **Low Stock** | Auto-alerts when stock < 5 |

---

## Step 1 – Add Header Rows

### Tab 1: Sheet1 (Seller Applications)
Row 1: `Timestamp | Serial | Shop Name | Owner Name | Phone | WhatsApp | Email | City | Address | Products | Years | Monthly Sales | Status | Post ID`

### Tab 2: Products (Create new tab)
Row 1: `Timestamp | Shop Name | Shop Serial | Shop City | Category | Product Name | Brand | Selling Price | MRP | Stock | Description | (category-specific fields)`

### Tab 3: Low Stock (Create new tab)
Row 1: `Timestamp | Product Name | Product ID | Stock | Shop Name | Shop Serial`

---

## Step 2 – Add the Apps Script (UNIFIED)

In Google Sheets, open **Extensions → Apps Script**. Delete any sample code and paste:

```javascript
function doPost(e) {
  try {
    var ss = SpreadsheetApp.openById('1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g');
    var data = JSON.parse(e.postData.contents);

    // === SELLER SYNC ===
    if (data.type === undefined && data.shop_name && data.owner_name) {
      var s1 = ss.getSheets()[0];
      s1.appendRow([new Date(), data.serial||'', data.shop_name||'', data.owner_name||'', data.phone||'', data.whatsapp||'', data.email||'', data.city||'', data.address||'', data.products||'', data.years||'', data.monthly_sales||'', data.status||'', data.post_id||'']);
      return ContentService.createTextOutput(JSON.stringify({status:'ok',type:'seller'})).setMimeType(ContentService.MimeType.JSON);
    }

    // === PRODUCT REQUEST ===
    if (data.type === 'product') {
      var s2 = ss.getSheetByName('Products');
      if (!s2) { s2 = ss.insertSheet('Products'); s2.appendRow(['Timestamp','Shop Name','Shop Serial','Shop City','Category','Product Name','Brand','Selling Price','MRP','Stock','Description']); }
      s2.appendRow([new Date(), data.shop_name||'', data.shop_serial||'', data.shop_city||'', data.category||'', data.product_name||'', data.brand||'', data.selling_price||'', data.retail_price||'', data.stock||'', data.description||'']);
      return ContentService.createTextOutput(JSON.stringify({status:'ok',type:'product'})).setMimeType(ContentService.MimeType.JSON);
    }

    // === LOW STOCK ALERT ===
    if (data.type === 'low_stock') {
      var s3 = ss.getSheetByName('Low Stock');
      if (!s3) { s3 = ss.insertSheet('Low Stock'); s3.appendRow(['Timestamp','Product Name','Product ID','Stock','Shop Name','Shop Serial']); }
      data.products.forEach(function(p) {
        s3.appendRow([new Date(), p.product_name||'', p.product_id||'', p.stock||'', p.shop_name||'', p.shop_serial||'']);
      });
      return ContentService.createTextOutput(JSON.stringify({status:'ok',type:'low_stock'})).setMimeType(ContentService.MimeType.JSON);
    }

    return ContentService.createTextOutput(JSON.stringify({status:'error',message:'Unknown type'})).setMimeType(ContentService.MimeType.JSON);
  } catch(err) {
    return ContentService.createTextOutput(JSON.stringify({status:'error',message:err.toString()})).setMimeType(ContentService.MimeType.JSON);
  }
}
```

Click **Save** (💾).

---

## Step 3 – Deploy (one time only)

1. **Deploy → New deployment**
2. Type: **Web app**
3. Execute as: **Me**
4. Who has access: **Anyone**
5. Click **Deploy** → Authorize → Copy URL

---

## Step 4 – Set URL in functions.php

Both constants use the SAME URL (one script handles everything):

```php
define('SHOMART_SHEETS_WEBHOOK_URL', 'https://script.google.com/macros/s/YOUR_ID/exec');
define('SHOMART_PRODUCTS_WEBHOOK_URL', 'https://script.google.com/macros/s/YOUR_ID/exec');
```

---

## How Stock Management Works

1. **Order placed** → Stock auto-decreases ✅
2. **Stock = 0** → Product can't be ordered ❌
3. **Stock < 5** → Google Sheet "Low Stock" tab updated ⚠️
4. **Shopkeeper brings new stock** → Admin uses **"Bulk Restock"** tool in WP Admin → 📉 Low Stock page
5. **Admin can also edit individual product stock** from Product edit page

**For restock:** Shopkeeper calls/WhatsApps admin → Admin opens WP Admin → 📉 Low Stock → Select shop → Add stock quantity → Done!

---

*Last updated: July 2026*
