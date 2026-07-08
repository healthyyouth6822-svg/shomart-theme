<?php
/**
 * Shomart - Privacy Policy Page
 * Create a WordPress page with slug: privacy-policy
 * Set its template to this file
 */

get_header();
?>

<div class="shomart-policy-page">

    <div class="policy-header">
        <h1>🔒 Privacy Policy</h1>
        <p>Last updated: <?php echo date('F Y'); ?></p>
    </div>

    <div class="policy-content">

        <div class="policy-section">
            <h2>1. Information We Collect</h2>
            <p>When you place an order or create an account on Shomart, we collect the following information:</p>
            <ul>
                <li>Name, email address, phone number</li>
                <li>Billing and shipping address</li>
                <li>Payment information (processed securely)</li>
                <li>Order history and preferences</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>2. How We Use Your Information</h2>
            <p>We use your personal information to:</p>
            <ul>
                <li>Process and deliver your orders</li>
                <li>Send order confirmations and updates</li>
                <li>Provide customer support</li>
                <li>Improve our website and services</li>
                <li>Send promotional offers (only with your consent)</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Information Sharing</h2>
            <p>We do <strong>not</strong> sell or rent your personal information to third parties. We may share information with:</p>
            <ul>
                <li>Delivery partners to fulfill your orders</li>
                <li>Payment gateways for secure transactions</li>
                <li>Legal authorities if required by law</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Data Security</h2>
            <p>We implement industry-standard security measures to protect your personal information. All payment transactions are encrypted using SSL technology.</p>
        </div>

        <div class="policy-section">
            <h2>5. Cookies</h2>
            <p>Our website uses cookies to enhance your shopping experience. Cookies help us remember your preferences and keep items in your cart. You can disable cookies in your browser settings, but some features may not work properly.</p>
        </div>

        <div class="policy-section">
            <h2>6. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access your personal data</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of marketing communications</li>
            </ul>
        </div>
        <div class="policy-section">
    <h2>Shop Identification Policy</h2>
    <p>
        For privacy and operational reasons, partner shops are identified 
        by a unique serial number instead of displaying their full name.
    </p>
    <p>
        If a customer needs to file a complaint, they must mention 
        the assigned shop serial number provided at the time of order.
    </p>
</div>
        

        <div class="policy-section">
            <h2>7. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us:</p>
            <div class="policy-contact">
                <p>📧 Email: <?php echo esc_html(get_option('admin_email')); ?></p>
                <p>🌐 Website: <?php echo esc_url(home_url()); ?></p>
                <p>📍 Location: Ladakh, India</p>
            </div>
        </div>

    </div>

</div>

<style>
.shomart-policy-page {
    padding: 12px;
    padding-bottom: 100px;
}

.policy-header {
    background: var(--blue);
    color: #fff;
    border-radius: 12px;
    padding: 20px 16px;
    margin-bottom: 16px;
    text-align: center;
}
.policy-header h1 { font-size: 20px; font-weight: 700; }
.policy-header p  { font-size: 12px; opacity: 0.8; margin-top: 4px; }

.policy-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.policy-section {
    background: var(--white);
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.policy-section h2 {
    font-size: 15px;
    font-weight: 700;
    color: var(--blue);
    margin-bottom: 10px;
}

.policy-section p {
    font-size: 13px;
    color: var(--text-mid);
    line-height: 1.7;
    margin-bottom: 8px;
}

.policy-section ul {
    padding-left: 16px;
    list-style: disc;
}

.policy-section ul li {
    font-size: 13px;
    color: var(--text-mid);
    line-height: 1.7;
    margin-bottom: 4px;
}

.policy-contact {
    background: var(--grey-bg);
    border-radius: 8px;
    padding: 12px;
    margin-top: 8px;
}
.policy-contact p {
    margin-bottom: 6px;
    font-size: 13px;
}
</style>

<?php get_footer(); ?>