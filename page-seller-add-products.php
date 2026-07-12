<?php
/**
 * Template Name: 🏪 Seller - Add Products (v3.0)
 * Description: Dynamic category-based form with all fields. Stock + AI image support.
 * 
 * CATEGORIES: Electronics | Mobile 📱 | Sports 🏏 | Fashion 👕 | Beauty 💄 | Home 🏠 | Toys 🧸
 * Each category has required + optional fields.
 * Data → Google Sheet → Admin reviews → WooCommerce product created
 *
 * @package Shomart
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$sellers = get_posts(array(
    'post_type'      => 'seller_application',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => array(
        array(
            'key'     => 'status',
            'value'   => array('approved', 'active'),
            'compare' => 'IN',
        ),
    ),
    'orderby'        => 'meta_value',
    'meta_key'       => 'shop_serial',
    'order'          => 'ASC',
));

$submitted = isset($_GET['submitted']) && 'true' === $_GET['submitted'];
$error = isset($_GET['error']) ? sanitize_key($_GET['error']) : '';
?>

<style>
* { box-sizing: border-box; }
.shomart-pf-wrap { max-width: 680px; margin: 0 auto; padding: 12px; font-family: 'Inter', -apple-system, sans-serif; }
.shomart-pf-wrap h1 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
.shomart-pf-wrap .subtitle { font-size: 13px; color: #666; margin-bottom: 20px; }
.shomart-pf { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.pf-group { margin-bottom: 16px; }
.pf-group label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 5px; }
.pf-group label .req { color: #f44336; }
.pf-group label .opt { color: #999; font-weight: 400; font-size: 11px; }
.pf-group select, .pf-group input, .pf-group textarea {
    width: 100%; padding: 10px 12px;
    border: 1.5px solid #ddd; border-radius: 8px;
    font-size: 14px; font-family: inherit; background: #fafafa;
    transition: border-color .2s;
}
.pf-group select:focus, .pf-group input:focus, .pf-group textarea:focus {
    outline: none; border-color: #2874f0; background: #fff;
}
.pf-group textarea { min-height: 60px; resize: vertical; }
.pf-group .hint { font-size: 11px; color: #888; margin-top: 3px; }
.pf-row { display: flex; gap: 10px; flex-wrap: wrap; }
.pf-row .pf-group { flex: 1; min-width: 120px; }

.cat-fields { display: none; border-top: 2px dashed #e0e0e0; padding-top: 18px; margin-top: 18px; }
.cat-fields.active { display: block; }
.cat-fields h3 { font-size: 15px; font-weight: 600; color: #2874f0; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #2874f0; }
.cat-fields h3 .badge { font-size: 11px; background: #e3f2fd; color: #1565c0; padding: 2px 8px; border-radius: 10px; margin-left: 8px; }

.pf-btn { width: 100%; padding: 13px; background: #2874f0; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background .2s; margin-top: 6px; }
.pf-btn:hover { background: #1a5cc8; }
.pf-btn:disabled { background: #90b4f0; cursor: not-allowed; }

.msg-success { background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 16px; }
.msg-success h2 { color: #2e7d32; font-size: 18px; margin-bottom: 6px; }
.msg-success p { color: #555; font-size: 13px; margin: 0; }
.msg-success .btn { display: inline-block; margin-top: 12px; padding: 8px 20px; background: #4caf50; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; }
.msg-error { background: #ffebee; border: 1px solid #ef9a9a; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; color: #c62828; font-size: 13px; }
.shop-badge { background: #e8f5e9; border-left: 3px solid #4caf50; padding: 8px 12px; border-radius: 6px; font-size: 12px; color: #2e7d32; margin-bottom: 16px; }

@media (max-width:480px) {
    .shomart-pf-wrap { padding: 6px; }
    .shomart-pf { padding: 12px; }
    .pf-group select, .pf-group input, .pf-group textarea { padding: 9px 10px; font-size: 13px; }
}
</style>

<div class="shomart-pf-wrap">
    <h1>📦 Add Products</h1>
    <p class="subtitle">Shop ke products add karein. Category select karein → form apne aap adjust ho jayega.</p>

    <?php if ($submitted) : ?>
        <div class="msg-success">
            <h2>✅ Product Submitted!</h2>
            <p>Aapke product details record ho gaye hain.<br>Admin review kar ke website par publish karega.</p>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="btn">➕ Add More</a>
        </div>
    <?php endif; ?>

    <?php if ($error === 'missing_fields') : ?><div class="msg-error">❌ Please fill all required fields (marked with *).</div>
    <?php elseif ($error === 'save_failed') : ?><div class="msg-error">❌ Something went wrong. Try again.</div>
    <?php elseif ($error === 'invalid_shop') : ?><div class="msg-error">❌ Please select a valid shop.</div>
    <?php endif; ?>

    <?php if (empty($sellers)) : ?>
        <div class="shop-badge">⚠️ No approved sellers yet. Wait for admin approval first.</div>
    <?php else : ?>
        <div class="shop-badge">🏪 <?php echo count($sellers); ?> approved shop(s). Select your shop below.</div>

        <form method="post" action="" class="shomart-pf" id="shomart-pf">
            <?php wp_nonce_field('shomart_add_products', 'shomart_products_nonce'); ?>
            <input type="hidden" name="shomart_add_products_submit" value="1">

            <!-- SHOP SELECT -->
            <div class="pf-group">
                <label>🏪 Select Your Shop <span class="req">*</span></label>
                <select name="shop_id" required>
                    <option value="">— Select —</option>
                    <?php foreach ($sellers as $s) :
                        $sn = get_post_meta($s->ID, 'shop_name', true);
                        $sr = get_post_meta($s->ID, 'shop_serial', true);
                        $sc = get_post_meta($s->ID, 'shop_city', true);
                        $l = $sn ?: $s->post_title;
                        if ($sr) $l .= ' (#' . $sr . ')';
                        if ($sc) $l .= ' - ' . $sc;
                    ?><option value="<?php echo esc_attr($s->ID); ?>"><?php echo esc_html($l); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- CATEGORY SELECT -->
            <div class="pf-group">
                <label>📂 Category <span class="req">*</span></label>
                <select name="product_category" id="pf-cat" required>
                    <option value="">— Select category —</option>
                    <option value="mobile">📱 Mobile Phones</option>
                    <option value="electronics">🎧 Electronics (Earphones, Smartwatches, Speakers)</option>
                    <option value="fashion">👕 Fashion (Clothes, Shoes, Accessories)</option>
                    <option value="sports">🏏 Sports (Bats, Balls, Shoes, Gloves)</option>
                    <option value="beauty">💄 Beauty & Personal Care</option>
                    <option value="home">🏠 Home (Bedsheets, Decor, Kitchen)</option>
                    <option value="toys">🧸 Toys & Games</option>
                    <option value="groceries">🍎 Groceries</option>
                    <option value="handicrafts">🎨 Handicrafts</option>
                    <option value="books">📚 Books & Stationery</option>
                    <option value="other">📦 Other</option>
                </select>
            </div>

            <!-- ===== COMMON FIELDS (All categories) ===== -->
            <div class="pf-group">
                <label>📦 Product Name <span class="req">*</span></label>
                <input type="text" name="product_name" required placeholder="e.g. Samsung Galaxy S26 Ultra 256GB">
            </div>
            <div class="pf-row">
                <div class="pf-group">
                    <label>💰 Selling Price (₹) <span class="req">*</span></label>
                    <input type="number" name="selling_price" required min="1" step="1" placeholder="45000">
                </div>
                <div class="pf-group">
                    <label>🏷️ MRP / Retail Price (₹) <span class="opt">optional</span></label>
                    <input type="number" name="retail_price" min="0" step="1" placeholder="49999">
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-group" style="flex:0 0 100px;">
                    <label>📊 Stock <span class="req">*</span></label>
                    <input type="number" name="stock" required min="0" step="1" value="1" placeholder="Qty">
                </div>
                <div class="pf-group" style="flex:2;">
                    <label>Brand <span class="req">*</span></label>
                    <input type="text" name="brand" required placeholder="e.g. Samsung, Nike, Apple">
                </div>
            </div>
            <div class="pf-group">
                <label>📝 Description <span class="opt">optional</span></label>
                <textarea name="description" placeholder="Product features, details, warranty info..."></textarea>
                <div class="hint">AI auto-fill karega agar aap khali chhodenge</div>
            </div>

            <!-- ===== CATEGORY: MOBILE 📱 ===== -->
            <div class="cat-fields" id="cf-mobile">
                <h3>📱 Mobile Phone Details <span class="badge">RAM, Camera, Battery zaroori</span></h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>RAM <span class="req">*</span></label>
                        <select name="m_ram" required>
                            <option value="">— Select —</option>
                            <option>2GB</option><option>3GB</option><option>4GB</option><option>6GB</option>
                            <option>8GB</option><option>12GB</option><option>16GB</option>
                        </select>
                    </div>
                    <div class="pf-group">
                        <label>Storage <span class="req">*</span></label>
                        <select name="m_storage" required>
                            <option value="">— Select —</option>
                            <option>16GB</option><option>32GB</option><option>64GB</option>
                            <option>128GB</option><option>256GB</option><option>512GB</option><option>1TB</option>
                        </select>
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>📷 Rear Camera <span class="req">*</span></label>
                        <input type="text" name="m_camera" required placeholder="e.g. 50MP Main + 2MP Depth">
                    </div>
                    <div class="pf-group">
                        <label>🤳 Front Camera <span class="opt">optional</span></label>
                        <input type="text" name="m_front_cam" placeholder="e.g. 16MP Selfie">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🔋 Battery <span class="req">*</span></label>
                        <input type="text" name="m_battery" required placeholder="e.g. 5000 mAh">
                    </div>
                    <div class="pf-group">
                        <label>⚡ Charging <span class="opt">optional</span></label>
                        <input type="text" name="m_charging" placeholder="e.g. 33W Fast Charging">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🖥️ Display Size <span class="opt">optional</span></label>
                        <input type="text" name="m_display" placeholder="e.g. 6.7 inch AMOLED">
                    </div>
                    <div class="pf-group">
                        <label>📶 Network <span class="opt">optional</span></label>
                        <select name="m_network">
                            <option value="">— Select —</option>
                            <option>4G</option><option>5G</option><option>4G+5G</option>
                        </select>
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🎨 Color(s) <span class="opt">optional</span></label>
                        <input type="text" name="m_color" placeholder="e.g. Midnight Black, Cosmic Blue">
                    </div>
                    <div class="pf-group">
                        <label>🆔 Model No. <span class="req">*</span></label>
                        <input type="text" name="m_model" required placeholder="e.g. SM-S938B">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🧠 Processor <span class="opt">optional</span></label>
                        <input type="text" name="m_processor" placeholder="e.g. Snapdragon 8 Gen 3">
                    </div>
                    <div class="pf-group">
                        <label>OS <span class="opt">optional</span></label>
                        <input type="text" name="m_os" placeholder="e.g. Android 14">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🛡️ Warranty <span class="opt">optional</span></label>
                        <select name="m_warranty">
                            <option value="">— Select —</option>
                            <option>No Warranty</option><option>7 Days</option><option>15 Days</option>
                            <option>1 Month</option><option>6 Months</option><option>1 Year</option>
                        </select>
                    </div>
                    <div class="pf-group">
                        <label>📦 In The Box <span class="opt">optional</span></label>
                        <input type="text" name="m_box" placeholder="e.g. Handset, Charger, Cable">
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY: ELECTRONICS 🎧 ===== -->
            <div class="cat-fields" id="cf-electronics">
                <h3>🎧 Electronics Details</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🎨 Color <span class="opt">optional</span></label>
                        <input type="text" name="el_color" placeholder="e.g. Black, White">
                    </div>
                    <div class="pf-group">
                        <label>📶 Connectivity <span class="opt">optional</span></label>
                        <select name="el_connectivity">
                            <option value="">— Select —</option>
                            <option>Bluetooth</option><option>Wired</option><option>Wi-Fi</option>
                            <option>Bluetooth + Wi-Fi</option>
                        </select>
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🔋 Battery Life <span class="opt">optional</span></label>
                        <input type="text" name="el_battery" placeholder="e.g. 20 hours playback">
                    </div>
                    <div class="pf-group">
                        <label>🛡️ Warranty <span class="opt">optional</span></label>
                        <select name="el_warranty">
                            <option value="">— Select —</option>
                            <option>No Warranty</option><option>6 Months</option><option>1 Year</option>
                        </select>
                    </div>
                </div>
                <div class="pf-group">
                    <label>🆔 Model Number <span class="opt">optional</span></label>
                    <input type="text" name="el_model" placeholder="e.g. WF-1000XM5">
                    <div class="hint">Model number se AI product search karega</div>
                </div>
            </div>

            <!-- ===== CATEGORY: FASHION 👕 ===== -->
            <div class="cat-fields" id="cf-fashion">
                <h3>👕 Fashion Details</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>Size <span class="req">*</span></label>
                        <input type="text" name="fa_size" required placeholder="e.g. M, L, XL, UK 8">
                    </div>
                    <div class="pf-group">
                        <label>🎨 Color <span class="opt">optional</span></label>
                        <input type="text" name="fa_color" placeholder="e.g. Navy Blue, Red">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🧵 Material <span class="req">*</span></label>
                        <input type="text" name="fa_material" required placeholder="e.g. 100% Cotton, Denim">
                    </div>
                    <div class="pf-group">
                        <label>👤 Gender <span class="req">*</span></label>
                        <select name="fa_gender" required>
                            <option value="">— Select —</option>
                            <option>Men</option><option>Women</option><option>Unisex</option><option>Kids</option>
                        </select>
                    </div>
                </div>
                <div class="pf-group">
                    <label>👔 Fit Type <span class="opt">optional</span></label>
                    <select name="fa_fit">
                        <option value="">— Select —</option>
                        <option>Slim Fit</option><option>Regular Fit</option><option>Oversized</option><option>Relaxed Fit</option>
                    </select>
                </div>
            </div>

            <!-- ===== CATEGORY: SPORTS 🏏 ===== -->
            <div class="cat-fields" id="cf-sports">
                <h3>🏏 Sports Details</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>Size / Weight <span class="req">*</span></label>
                        <input type="text" name="sp_size" required placeholder="e.g. Size 5, 1100g-1200g">
                    </div>
                    <div class="pf-group">
                        <label>🧱 Material <span class="req">*</span></label>
                        <input type="text" name="sp_material" required placeholder="e.g. English Willow, Synthetic">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🎯 Age/Level <span class="opt">optional</span></label>
                        <select name="sp_level">
                            <option value="">— Select —</option>
                            <option>Beginner</option><option>Professional</option><option>Kids</option><option>Adults</option>
                        </select>
                    </div>
                    <div class="pf-group">
                        <label>✋ Hand <span class="opt">optional</span></label>
                        <select name="sp_hand">
                            <option value="">— Select —</option>
                            <option>Left-handed</option><option>Right-handed</option><option>Both</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY: BEAUTY 💄 ===== -->
            <div class="cat-fields" id="cf-beauty">
                <h3>💄 Beauty & Personal Care</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>📏 Quantity / Volume <span class="req">*</span></label>
                        <input type="text" name="be_qty" required placeholder="e.g. 50ml, 100g">
                    </div>
                    <div class="pf-group">
                        <label>🧴 Skin/Hair Type <span class="opt">optional</span></label>
                        <input type="text" name="be_skin" placeholder="e.g. Oily Skin, Dry Hair">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🎨 Shade <span class="opt">optional</span></label>
                        <input type="text" name="be_shade" placeholder="e.g. Natural Beige, Rose Gold">
                    </div>
                    <div class="pf-group">
                        <label>📅 Expiry Date <span class="req">*</span></label>
                        <input type="date" name="be_expiry" required>
                    </div>
                </div>
                <div class="pf-group">
                    <label>🧪 Key Ingredients <span class="opt">optional</span></label>
                    <input type="text" name="be_ingredients" placeholder="e.g. Aloe Vera, Vitamin C">
                </div>
            </div>

            <!-- ===== CATEGORY: HOME 🏠 ===== -->
            <div class="cat-fields" id="cf-home">
                <h3>🏠 Home & Kitchen</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>📐 Dimensions / Size <span class="req">*</span></label>
                        <input type="text" name="ho_size" required placeholder="e.g. 90x100 inches">
                    </div>
                    <div class="pf-group">
                        <label>🧱 Material <span class="req">*</span></label>
                        <input type="text" name="ho_material" required placeholder="e.g. Cotton, Steel">
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🎨 Color / Pattern <span class="opt">optional</span></label>
                        <input type="text" name="ho_color" placeholder="e.g. Floral, Solid White">
                    </div>
                    <div class="pf-group">
                        <label>📦 Box Contents <span class="opt">optional</span></label>
                        <input type="text" name="ho_contents" placeholder="e.g. 1 Bedsheet + 2 Pillow Covers">
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY: TOYS 🧸 ===== -->
            <div class="cat-fields" id="cf-toys">
                <h3>🧸 Toys & Games</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>👶 Age Group <span class="req">*</span></label>
                        <input type="text" name="to_age" required placeholder="e.g. 3+ Years, 8-12 Years">
                    </div>
                    <div class="pf-group">
                        <label>🔋 Battery Required <span class="opt">optional</span></label>
                        <select name="to_battery">
                            <option value="">— Select —</option>
                            <option>Yes</option><option>No</option>
                        </select>
                    </div>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🧱 Material <span class="opt">optional</span></label>
                        <input type="text" name="to_material" placeholder="e.g. Non-toxic Plastic, Wooden">
                    </div>
                    <div class="pf-group">
                        <label>🧠 Skill Type <span class="opt">optional</span></label>
                        <input type="text" name="to_skill" placeholder="e.g. Educational, Puzzle">
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY: GROCERIES 🍎 ===== -->
            <div class="cat-fields" id="cf-groceries">
                <h3>🍎 Groceries Details</h3>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>⚖️ Weight / Quantity <span class="req">*</span></label>
                        <input type="text" name="gr_weight" required placeholder="e.g. 1kg, 500g, 12 pcs">
                    </div>
                    <div class="pf-group">
                        <label>📅 Expiry Date <span class="req">*</span></label>
                        <input type="date" name="gr_expiry" required>
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY: HANDICRAFTS 🎨 ===== -->
            <div class="cat-fields" id="cf-handicrafts">
                <h3>🎨 Handicrafts Details</h3>
                <div class="pf-group">
                    <label>Type <span class="req">*</span></label>
                    <select name="ha_type" required>
                        <option value="">— Select —</option>
                        <option>Pashmina Shawl</option><option>Woodwork / Carving</option>
                        <option>Carpet / Rug</option><option>Pottery</option>
                        <option>Jewelry</option><option>Painting / Thangka</option><option>Other</option>
                    </select>
                </div>
                <div class="pf-row">
                    <div class="pf-group">
                        <label>🧱 Material <span class="opt">optional</span></label>
                        <input type="text" name="ha_material" placeholder="e.g. Pashmina wool, Silver">
                    </div>
                    <div class="pf-group">
                        <label>📐 Size <span class="opt">optional</span></label>
                        <input type="text" name="ha_size" placeholder="e.g. 2m x 1.5m">
                    </div>
                </div>
            </div>

            <!-- ===== CATEGORY: BOOKS 📚 ===== -->
            <div class="cat-fields" id="cf-books">
                <h3>📚 Books & Stationery</h3>
                <div class="pf-group">
                    <label>✍️ Author / Publisher <span class="opt">optional</span></label>
                    <input type="text" name="bk_author" placeholder="Author name">
                </div>
                <div class="pf-group">
                    <label>Type <span class="req">*</span></label>
                    <select name="bk_type" required>
                        <option value="">— Select —</option>
                        <option>Textbook</option><option>Novel</option><option>Magazine</option>
                        <option>Stationery</option><option>Other</option>
                    </select>
                </div>
            </div>

            <!-- ===== CATEGORY: OTHER 📦 ===== -->
            <div class="cat-fields" id="cf-other">
                <h3>📦 Other Details</h3>
                <div class="pf-group">
                    <label>Additional Notes <span class="opt">optional</span></label>
                    <textarea name="ot_notes" placeholder="Koi extra detail?"></textarea>
                </div>
            </div>

            <button type="submit" class="pf-btn" id="pf-submit">📤 Submit Product</button>
            <p style="text-align:center;font-size:11px;color:#999;margin-top:10px;">Admin review karega aur publish karega. Photos AI se auto-generate hongi.</p>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cat = document.getElementById('pf-cat');
    var all = document.querySelectorAll('.cat-fields');
    var form = document.getElementById('shomart-pf');
    var btn = document.getElementById('pf-submit');

    cat.addEventListener('change', function() {
        all.forEach(function(el) { el.classList.remove('active'); });
        var v = this.value;
        if (v) {
            var t = document.getElementById('cf-' + v);
            if (t) t.classList.add('active');
        }
    });

    form.addEventListener('submit', function() {
        if (btn) {
            btn.innerHTML = '⏳ Submitting...';
            setTimeout(function() { btn.disabled = true; }, 50);
        }
    });
});
</script>

<?php get_footer(); ?>
