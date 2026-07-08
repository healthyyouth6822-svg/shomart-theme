<?php
/**
 * Template Name: Become a Seller
 * Shomart - Seller Registration Page
 */

get_header();

// Handle form submission
$success_message = '';
$error_message = '';


?>
<?php if (isset($_GET['submitted'])): ?>
    <div class="alert alert-success">
        ✅ Application submitted successfully! We will contact you soon.
    </div>
<?php endif; ?>

<div class="shomart-seller-page">

    <!-- ============ HERO SECTION ============ -->
    <div class="seller-hero">
        <h1>🏪 Become a Shomart Seller!</h1>
        <p>Join Ladakh's fastest growing online marketplace</p>
    </div>

    <!-- ============ BENEFITS SECTION ============ -->
    <div class="seller-benefits">
        <h2>Why Partner with Us?</h2>
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">💰</div>
                <strong>FREE Registration</strong>
                <small>No fees, no hidden charges</small>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">📈</div>
                <strong>More Customers</strong>
                <small>Reach all of Ladakh online</small>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🚀</div>
                <strong>Easy to Use</strong>
                <small>We handle the technology</small>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🎯</div>
                <strong>Marketing Support</strong>
                <small>We promote your products</small>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">💵</div>
                <strong>Cash on Delivery</strong>
                <small>You get paid directly</small>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🤝</div>
                <strong>Trusted Partner</strong>
                <small>Long-term partnership</small>
            </div>
        </div>
    </div>

    <!-- ============ HOW IT WORKS ============ -->
    <div class="how-it-works">
        <h2>How It Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <strong>Apply Online</strong>
                <small>Fill the form below</small>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <strong>We Call You</strong>
                <small>Within 24-48 hours</small>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <strong>Add Products</strong>
                <small>We add to website</small>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <strong>Start Selling!</strong>
                <small>Receive orders</small>
            </div>
        </div>
    </div>

    <!-- ============ APPLICATION FORM ============ -->
    <div class="seller-form-section">
        <h2>📝 Apply Now</h2>
        <p class="form-subtitle">Fill the form below and we'll contact you soon!</p>
        
        <?php if ($success_message) : ?>
            <div class="alert alert-success">
                ✅ <?php echo esc_html($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message) : ?>
            <div class="alert alert-error">
                ⚠️ <?php echo esc_html($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" class="seller-application-form" id="sellerForm">
            
            <?php wp_nonce_field('seller_application_nonce', 'seller_nonce'); ?>
            
            <div class="form-section">
                <h3>🏪 Shop Information</h3>
                
                <div class="form-group">
                    <label>Shop Name <span class="required">*</span></label>
                    <input type="text" name="shop_name" required placeholder="e.g., Chospa Mobile Store">
                </div>
                
                <div class="form-group">
                    <label>City <span class="required">*</span></label>
                    <select name="shop_city" required>
                        <option value="">Select your city</option>
                        <option value="Leh">Leh</option>
                        <option value="Kargil">Kargil</option>
                        <option value="Choglamsar">Choglamsar</option>
                        <option value="Spituk">Spituk</option>
                        <option value="Nubra">Nubra</option>
                        <option value="Other">Other (specify in address)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Shop Address <span class="required">*</span></label>
                    <textarea name="shop_address" required rows="3" placeholder="Full shop address with landmark"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Years in Business</label>
                    <select name="years_business">
                        <option value="">Select years</option>
                        <option value="Less than 1 year">Less than 1 year</option>
                        <option value="1-3 years">1-3 years</option>
                        <option value="3-5 years">3-5 years</option>
                        <option value="5-10 years">5-10 years</option>
                        <option value="More than 10 years">More than 10 years</option>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h3>👤 Owner Details</h3>
                
                <div class="form-group">
                    <label>Owner Name <span class="required">*</span></label>
                    <input type="text" name="owner_name" required placeholder="Your full name">
                </div>
                
                <div class="form-group">
                    <label>Phone Number <span class="required">*</span></label>
                    <input type="tel" name="phone" required maxlength="10" pattern="[0-9]{10}" placeholder="10-digit mobile number">
                </div>
                
                <div class="form-group">
                    <label>WhatsApp Number</label>
                    <input type="tel" name="whatsapp" maxlength="10" pattern="[0-9]{10}" placeholder="Same as above if same">
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="your-email@example.com">
                </div>
            </div>
            
            <div class="form-section">
                <h3>📦 Products & Sales</h3>
                
                <div class="form-group">
                    <label>What products do you sell? <span class="required">*</span></label>
                    <textarea name="products_sold" required rows="3" placeholder="e.g., Mobiles (Samsung, Apple, Vivo), Accessories, Headphones..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Approximate Monthly Sales</label>
                    <select name="monthly_sales">
                        <option value="">Select range</option>
                        <option value="Less than ₹50,000">Less than ₹50,000</option>
                        <option value="₹50,000 - ₹1,00,000">₹50,000 - ₹1,00,000</option>
                        <option value="₹1,00,000 - ₹5,00,000">₹1,00,000 - ₹5,00,000</option>
                        <option value="₹5,00,000 - ₹10,00,000">₹5,00,000 - ₹10,00,000</option>
                        <option value="More than ₹10,00,000">More than ₹10,00,000</option>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h3>💬 Additional Information</h3>
                
                <div class="form-group">
                    <label>Why do you want to join Shomart? (Optional)</label>
                    <textarea name="message" rows="4" placeholder="Tell us anything else about your shop..."></textarea>
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" required>
                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank">Terms & Conditions</a>
                </label>
            </div>
            
            <button type="submit" name="seller_application_submit" class="submit-btn">
                🚀 Submit Application
            </button>
            
        </form>
    </div>

    <!-- ============ CONTACT INFO ============ -->
    <div class="contact-section">
        <h3>📞 Prefer to Talk?</h3>
        <p>Contact us directly!</p>
        <div class="contact-options">
            <a href="tel:+91XXXXXXXXXX" class="contact-btn phone-btn">
                <span>📞</span>
                <div>
                    <strong>Call Us</strong>
                    <small>+91-788-944-3687</small>
                </div>
            </a>
            <a href="https://wa.me/91XXXXXXXXXX?text=Hi%20I%20want%20to%20join%20Shomart" class="contact-btn whatsapp-btn">
                <span>💬</span>
                <div>
                    <strong>WhatsApp</strong>
                    <small>Quick response</small>
                </div>
            </a>
        </div>
    </div>

</div>

<style>
.shomart-seller-page {
    padding: 12px;
    padding-bottom: 100px;
}

/* ============ HERO ============ */
.seller-hero {
    background: linear-gradient(135deg, #2874f0 0%, #1a5dc9 100%);
    color: #fff;
    border-radius: 16px;
    padding: 30px 20px;
    text-align: center;
    margin-bottom: 16px;
    box-shadow: 0 4px 12px rgba(40,116,240,0.3);
}

.seller-hero h1 {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 8px;
}

.seller-hero p {
    font-size: 14px;
    opacity: 0.95;
}

/* ============ BENEFITS ============ */
.seller-benefits {
    background: #fff;
    border-radius: 12px;
    padding: 20px 16px;
    margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.seller-benefits h2 {
    font-size: 18px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 16px;
    color: #212121;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.benefit-card {
    background: #f1f2f4;
    padding: 16px 10px;
    border-radius: 10px;
    text-align: center;
    transition: transform 0.2s;
}

.benefit-card:hover {
    transform: translateY(-2px);
}

.benefit-icon {
    font-size: 32px;
    margin-bottom: 8px;
}

.benefit-card strong {
    display: block;
    font-size: 13px;
    color: #212121;
    margin-bottom: 4px;
}

.benefit-card small {
    font-size: 11px;
    color: #555;
    line-height: 1.3;
}

/* ============ HOW IT WORKS ============ */
.how-it-works {
    background: #fff;
    border-radius: 12px;
    padding: 20px 16px;
    margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.how-it-works h2 {
    font-size: 18px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 16px;
    color: #212121;
}

.steps {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.step {
    text-align: center;
    padding: 12px;
    background: #f1f2f4;
    border-radius: 10px;
    position: relative;
}

.step-num {
    width: 32px;
    height: 32px;
    background: #2874f0;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin: 0 auto 8px;
    font-size: 14px;
}

.step strong {
    display: block;
    font-size: 13px;
    margin-bottom: 2px;
    color: #212121;
}

.step small {
    font-size: 11px;
    color: #555;
}

/* ============ FORM SECTION ============ */
.seller-form-section {
    background: #fff;
    border-radius: 12px;
    padding: 20px 16px;
    margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.seller-form-section h2 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #212121;
}

.form-subtitle {
    font-size: 12px;
    color: #878787;
    margin-bottom: 20px;
}

.form-section {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 14px;
}

.form-section h3 {
    font-size: 14px;
    font-weight: 700;
    color: #2874f0;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}

.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #555;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.required {
    color: #e53935;
    font-weight: 700;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    background: #fff;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #2874f0;
    box-shadow: 0 0 0 2px rgba(40,116,240,0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.checkbox-label {
    display: flex !important;
    align-items: center;
    gap: 8px;
    text-transform: none !important;
    font-size: 13px !important;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: auto !important;
    cursor: pointer;
}

.checkbox-label a {
    color: #2874f0;
    text-decoration: none;
}

.submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
    color: #fff;
    padding: 16px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(255,152,0,0.3);
    transition: all 0.2s;
    margin-top: 8px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255,152,0,0.4);
}

.submit-btn:active {
    transform: translateY(0);
}

/* ============ ALERTS ============ */
.alert {
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: 600;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* ============ CONTACT SECTION ============ */
.contact-section {
    background: #fff;
    border-radius: 12px;
    padding: 20px 16px;
    text-align: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.contact-section h3 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #212121;
}

.contact-section p {
    font-size: 13px;
    color: #878787;
    margin-bottom: 16px;
}

.contact-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.contact-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 10px;
    text-decoration: none;
    transition: transform 0.2s;
}

.contact-btn:hover {
    transform: translateY(-2px);
}

.contact-btn span {
    font-size: 24px;
}

.contact-btn strong {
    display: block;
    font-size: 13px;
    color: #fff;
    margin-bottom: 2px;
}

.contact-btn small {
    font-size: 11px;
    color: #fff;
    opacity: 0.9;
}

.phone-btn {
    background: linear-gradient(135deg, #2874f0 0%, #1a5dc9 100%);
}

.whatsapp-btn {
    background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
}
</style>

<script>
// Form validation
document.getElementById('sellerForm').addEventListener('submit', function(e) {
    var phone = document.querySelector('input[name="phone"]').value;
    
    if (phone.length !== 10 || !/^[0-9]+$/.test(phone)) {
        e.preventDefault();
        alert('⚠️ Please enter a valid 10-digit phone number!');
        return false;
    }
    
    // Show loading state
    var btn = document.querySelector('.submit-btn');
    btn.innerHTML = '⏳ Submitting...';
    btn.disabled = true;
});

// Auto-fill WhatsApp from phone
document.querySelector('input[name="phone"]').addEventListener('blur', function() {
    var whatsappField = document.querySelector('input[name="whatsapp"]');
    if (!whatsappField.value) {
        whatsappField.value = this.value;
    }
});
</script>

<?php get_footer(); ?>