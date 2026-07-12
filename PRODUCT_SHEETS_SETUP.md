# Shomart – Product Request Google Sheets Setup

Ye guide aapko batayega ki **shopkeepers ke product requests** automatically Google Sheet mein kaise save hote hain.

---

## Sheet Setup

**Same Google Sheet:** `https://docs.google.com/spreadsheets/d/1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g/edit`

**New Tab:** Ek naya tab banayein jiska naam **"Products"** ho.

### Step 1: Header Row

Products tab ke **Row 1** mein ye headers daalein:

| A | B | C | D | E | F | G | H | I | J | K | L | M | N | O | P | Q | R | S | T | U |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Timestamp | Shop Name | Shop Serial | Shop City | Shop Post ID | Category | Product Name | Selling Price (₹) | MRP (₹) | Stock | Color | Description | Brand | Model | Warranty | Condition | Gender | Size | Material | Weight | Expiry Date |

*(Columns badhate/ghatate rahein jaise category-specific fields aate hain — Type, Author, Notes etc.)*

---

### Step 2: Apps Script Code

1. Google Sheet kholen: [Click here](https://docs.google.com/spreadsheets/d/1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g/edit)
2. **Extensions → Apps Script** par click karein.
3. **`function myFunction() { }`** ko poori tarah delete karein.
4. Neeche diya code **paste** karein.
5. **💾 Save** karein (Ctrl+S).

```javascript
function doPost(e) {
  try {
    var ss = SpreadsheetApp.openById('1WbV88hcdYehKNrLSHOz9Hw1xwfylWHyTZokHtr4CR_g');
    
    // Parse incoming data
    var data = JSON.parse(e.postData.contents);
    
    // === SELLER SYNC (Tab 1: Sheet1) ===
    if (data.shop_name && data.owner_name) {
      var sheet1 = ss.getSheets()[0];
      sheet1.appendRow([
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
      return ContentService.createTextOutput(
        JSON.stringify({status: 'ok', type: 'seller'})
      ).setMimeType(ContentService.MimeType.JSON);
    }
    
    // === PRODUCT REQUEST (Tab 2: "Products") ===
    if (data.product_name) {
      var sheet2 = ss.getSheetByName('Products');
      if (!sheet2) {
        // Auto-create "Products" tab if it doesn't exist
        sheet2 = ss.insertSheet('Products');
        sheet2.appendRow([
          'Timestamp', 'Shop Name', 'Shop Serial', 'Shop City', 'Shop Post ID',
          'Category', 'Product Name', 'Selling Price (₹)', 'MRP (₹)', 'Stock',
          'Color', 'Description', 'Brand', 'Model', 'Warranty', 'Condition',
          'Gender', 'Size', 'Material', 'Weight', 'Expiry Date',
          'Type', 'Author', 'Notes'
        ]);
      }
      
      sheet2.appendRow([
        new Date(),
        data.shop_name || '',
        data.shop_serial || '',
        data.shop_city || '',
        data.shop_post_id || '',
        data.category || '',
        data.product_name || '',
        data.selling_price || '',
        data.retail_price || '',
        data.stock || '',
        data.color || '',
        data.description || '',
        data.brand || '',
        data.model || '',
        data.warranty || '',
        data.condition || '',
        data.gender || '',
        data.size || '',
        data.material || '',
        data.weight || '',
        data.expiry || '',
        data.type || '',
        data.author || '',
        data.notes || ''
      ]);
      
      return ContentService.createTextOutput(
        JSON.stringify({status: 'ok', type: 'product'})
      ).setMimeType(ContentService.MimeType.JSON);
    }
    
    return ContentService.createTextOutput(
      JSON.stringify({status: 'error', message: 'Unknown data type'})
    ).setMimeType(ContentService.MimeType.JSON);
    
  } catch (err) {
    return ContentService.createTextOutput(
      JSON.stringify({status: 'error', message: err.toString()})
    ).setMimeType(ContentService.MimeType.JSON);
  }
}
```

### Mazedar baat! 🔥

Yeh **ek hi Apps Script** dono kaam karega:
- ✅ Seller applications sync (Tab 1) — jo pehle se kaam kar raha hai
- ✅ Product requests sync (Tab 2 / "Products") — naya feature

**Aapko naya Web App deploy karne ki zaroorat nahi hai.** Wohi purana URL kaam karega!

---

### Step 3: Deploy (only if first time)

Agar aapne **pehle hi** seller sync ke liye deploy kar liya hai, to **ye step skip karein** — bas code update karein aur save karein.

Agar pehli baar deploy kar rahe hain:

1. **Deploy → New deployment** par click karein.
2. **Type:** Web app select karein.
3. **Execute as:** Me
4. **Who has access:** Anyone
5. **Deploy** karein.
6. URL copy karein.

---

### Step 4: functions.php mein URL set karein

`functions.php` mein dono URLs set hain:

```php
define('SHOMART_SHEETS_WEBHOOK_URL', 'https://script.google.com/macros/s/YOUR_URL/exec');
define('SHOMART_PRODUCTS_WEBHOOK_URL', 'https://script.google.com/macros/s/YOUR_URL/exec');
```

> ⚠️ **Dono URLs same honge** (ek hi Apps Script dono handle karta hai).

---

## Test Kaise Karein

1. Apni website par jayein: `yoursite.com/seller-add-products/`
2. Shop select karein.
3. Category select karein (jaise 📱 Electronics).
4. Product details bharein.
5. Submit karein.
6. Google Sheet khol kar **"Products" tab** check karein — naya row aa jayega!

---

## Sheet Organization

**Tab 1 (Default):** Seller Applications
- Har approved seller ki details
- Columns: Timestamp, Serial, Shop Name, Owner, Phone, etc.

**Tab 2 (Products):** Product Requests
- Har product request ki details
- Columns: Timestamp, Shop Name, Serial, Category, Product Name, etc.

**Admin Workflow:**
1. Products tab mein naye entries aati hain ✅
2. Aap review karein ✅
3. WooCommerce product create karein ✅
4. Seller assign karein ✅

---

*Last updated: July 2026*
