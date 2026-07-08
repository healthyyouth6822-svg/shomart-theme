<?php
/**
 * My Account page
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

// Handle form submissions BEFORE any output
if (!empty($_POST['action']) && in_array($_POST['action'], array('edit_account', 'edit_address', 'save_account_details', 'save-account-details'))) {
    // Let WooCommerce handle the form submission
    do_action('template_redirect');
}
global $wp;

// Detect which endpoint we're on
$current_endpoint = '';
$endpoint_value   = '';

$endpoints = array(
    'orders',
    'view-order',
    'downloads',
    'edit-account',
    'edit-address',
    'payment-methods',
    'add-payment-method',
    'lost-password',
    'customer-logout',
);

foreach ($endpoints as $ep) {
    if (isset($wp->query_vars[$ep])) {
        $current_endpoint = $ep;
        $endpoint_value   = $wp->query_vars[$ep];
        break;
    }
}

// If on a sub-page, show WooCommerce content
if (!empty($current_endpoint) && is_user_logged_in()) {
    ?>
    <div class="shomart-account-subpage">

        <!-- Back to Account Button -->
        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="back-to-account">
            ← Back to My Account
        </a>

        <?php
        wc_print_notices();

        // Handle different endpoints
    
switch ($current_endpoint) {

    case 'orders':
    echo '<h2 class="subpage-title">📦 My Orders</h2>';
    do_action('woocommerce_account_orders_endpoint');
    break;
        
        $has_orders = 0 < $customer_orders->total;
        
        wc_get_template('myaccount/orders.php', array(
            'current_page'    => absint($current_page),
            'customer_orders' => $customer_orders,
            'has_orders'      => $has_orders,
            'wp_button_class' => function_exists('wc_wp_theme_get_element_class_name') ? ' ' . wc_wp_theme_get_element_class_name('button') : '',
        ));
        break;

    case 'view-order':
    $order_id = absint($endpoint_value);
    $order = wc_get_order($order_id);
    
    if (!$order || !current_user_can('view_order', $order_id)) {
        echo '<div class="woocommerce-error" style="padding:16px;background:#fff;border-radius:8px;">Invalid order. <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '">My account</a></div>';
        break;
    }
    
    $order_date = wc_format_datetime($order->get_date_created());
    $order_status = wc_get_order_status_name($order->get_status());
    
    echo '<h2 class="subpage-title">📋 Order Details</h2>';
    echo '<div class="order-info-card">';
    echo '<p>Your order was placed on <strong>' . esc_html($order_date) . '</strong></p>';
    echo '<p>Status: <span class="order-status status-' . esc_attr($order->get_status()) . '">' . esc_html($order_status) . '</span></p>';
    echo '</div>';
    
    // Hide the default order number text
    add_filter('woocommerce_my_account_my_orders_query', function($args) { return $args; });
    
    wc_get_template('myaccount/view-order.php', array(
        'order_id' => $order_id,
        'order'    => $order,
    ));
    break;

    case 'downloads':
        echo '<h2 class="subpage-title">⬇️ Downloads</h2>';
        $downloads     = WC()->customer->get_downloadable_products();
        $has_downloads = (bool) $downloads;
        
        wc_get_template('myaccount/downloads.php', array(
            'downloads'       => $downloads,
            'has_downloads'   => $has_downloads,
            'wp_button_class' => function_exists('wc_wp_theme_get_element_class_name') ? ' ' . wc_wp_theme_get_element_class_name('button') : '',
        ));
        break;

  case 'edit-account':
    echo '<h2 class="subpage-title">👤 Account Details</h2>';
    
    // Manually trigger the form save action
    if (isset($_POST['save-account-details-nonce']) && wp_verify_nonce($_POST['save-account-details-nonce'], 'save_account_details')) {
        WC_Form_Handler::save_account_details();
    }
    
    wc_get_template('myaccount/form-edit-account.php', array(
        'user' => get_user_by('id', get_current_user_id()),
    ));
    break;
case 'edit-address':
    // Handle address save
    if (isset($_POST['action']) && $_POST['action'] === 'edit_address') {
        WC_Form_Handler::save_address();
    }
    
    if (empty($endpoint_value)) {
        echo '<h2 class="subpage-title">📍 Addresses</h2>';
        wc_get_template('myaccount/my-address.php');
    } else {
        $load_address = wc_edit_address_i18n(sanitize_title($endpoint_value), true);
        $address = WC()->countries->get_address_fields(
            esc_attr(get_user_meta(get_current_user_id(), $load_address . '_country', true)),
            $load_address . '_'
        );
        echo '<h2 class="subpage-title">📍 ' . ucfirst($endpoint_value) . ' Address</h2>';
        wc_get_template('myaccount/form-edit-address.php', array(
            'load_address' => $load_address,
            'address'      => apply_filters('woocommerce_address_to_edit', $address, $load_address),
        ));
    }
    break;

    case 'payment-methods':
        echo '<h2 class="subpage-title">💳 Payment Methods</h2>';
        wc_get_template('myaccount/payment-methods.php');
        break;

    default:
        do_action('woocommerce_account_' . $current_endpoint . '_endpoint', $endpoint_value);
        break;
}
        ?>
    </div>

    <style>
/* ============ HIDE ORDER NUMBER & DEFAULT WOOCOMMERCE TEXT ============ */
.shomart-account-subpage p:has(mark),
.shomart-account-subpage .woocommerce-order-details > p,
.shomart-account-subpage .woocommerce-order-details h2,
.shomart-account-subpage .woocommerce-order-overview,
.shomart-account-subpage .woocommerce-MyAccount-content > p:first-of-type,
.shomart-account-subpage > p:first-child {
    display: none !important;
}

.shomart-account-subpage p mark {
    display: none !important;
}

/* ============ MAIN SUBPAGE CONTAINER ============ */
.shomart-account-subpage {
    padding: 12px;
    padding-bottom: 100px;
}
/* Become Seller highlight */
.seller-item {
    background: linear-gradient(135deg, #2874f0 0%, #1a5dc9 100%);
    color: #fff !important;
}

.seller-item .menu-icon,
.seller-item .menu-arrow {
    color: #fff !important;
}

.seller-item:active {
    opacity: 0.9;
}
.back-to-account {
    display: inline-block;
    background: #fff;
    color: #2874f0;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 12px;
    text-decoration: none;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.subpage-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #212121;
    padding: 0 4px;
}

/* ============ ORDER INFO CARD ============ */
.order-info-card {
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.order-info-card p {
    font-size: 13px;
    color: #333;
    margin: 4px 0;
}

.order-status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.status-processing { background: #fff3cd; color: #856404; }
.status-completed  { background: #d4edda; color: #155724; }
.status-pending    { background: #d1ecf1; color: #0c5460; }
.status-cancelled  { background: #f8d7da; color: #721c24; }
.status-refunded   { background: #e2e3e5; color: #383d41; }
.status-failed     { background: #f8d7da; color: #721c24; }
.status-on-hold    { background: #fff3cd; color: #856404; }

/* ============ ORDERS TABLE ============ */
.woocommerce-orders-table,
.woocommerce-MyAccount-orders {
    width: 100%;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border-collapse: collapse;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    font-size: 13px;
}
.woocommerce-orders-table thead {
    background: #f1f2f4;
}
.woocommerce-orders-table th {
    padding: 10px 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #878787;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}
.woocommerce-orders-table td {
    padding: 12px 8px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 13px;
    color: #333;
}
.woocommerce-orders-table .button,
.woocommerce-orders-table a.button {
    background: #2874f0 !important;
    color: #fff !important;
    padding: 6px 12px !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    display: inline-block !important;
}

/* ============ ORDER DETAILS ============ */
.woocommerce-order-details,
.woocommerce-customer-details {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.woocommerce-order-details h2,
.woocommerce-column__title {
    font-size: 15px;
    font-weight: 700;
    color: #212121;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}
.woocommerce-table--order-details {
    width: 100%;
    font-size: 13px;
}
.woocommerce-table--order-details th,
.woocommerce-table--order-details td {
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

/* ============ ADDRESS SELECTION ============ */
.woocommerce-MyAccount-content .woocommerce-Address,
.u-columns .col-1,
.u-columns .col-2 {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.woocommerce-Address-title h3 {
    font-size: 15px;
    font-weight: 700;
    color: #212121;
    margin-bottom: 10px;
}
.woocommerce-Address-title .edit {
    float: right;
    background: #2874f0;
    color: #fff !important;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
}
.woocommerce-Address address {
    font-size: 13px;
    color: #333;
    line-height: 1.6;
    font-style: normal;
}

/* ============ FORMS (Edit Address, Edit Account) ============ */
.shomart-account-subpage form {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.shomart-account-subpage .form-row,
.shomart-account-subpage p.form-row {
    margin-bottom: 14px;
}
.shomart-account-subpage label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #878787;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.shomart-account-subpage input[type="text"],
.shomart-account-subpage input[type="email"],
.shomart-account-subpage input[type="tel"],
.shomart-account-subpage input[type="password"],
.shomart-account-subpage select,
.shomart-account-subpage textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    background: #f1f2f4;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
}
.shomart-account-subpage input:focus,
.shomart-account-subpage select:focus,
.shomart-account-subpage textarea:focus {
    border-color: #2874f0;
    background: #fff;
}
.shomart-account-subpage .select2-container {
    width: 100% !important;
}
.shomart-account-subpage .select2-selection {
    height: 46px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    background: #f1f2f4 !important;
    padding: 8px 10px !important;
}
.shomart-account-subpage .select2-selection__rendered {
    line-height: 28px !important;
    font-size: 14px !important;
}

/* ============ UPDATE/SAVE BUTTONS ============ */
.shomart-account-subpage .button,
.shomart-account-subpage button[type="submit"],
.shomart-account-subpage input[type="submit"] {
    background: #ffc200 !important;
    color: #212121 !important;
    border: none !important;
    padding: 14px 24px !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    cursor: pointer !important;
    width: 100% !important;
    margin-top: 10px !important;
    text-decoration: none !important;
    display: block !important;
    text-align: center !important;
    box-sizing: border-box !important;
}
.shomart-account-subpage .woocommerce-Button {
    background: #ffc200 !important;
    color: #212121 !important;
}

/* Password field group */
.shomart-account-subpage fieldset {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 14px;
    margin-top: 14px;
}
.shomart-account-subpage fieldset legend {
    font-size: 13px;
    font-weight: 700;
    padding: 0 8px;
    color: #2874f0;
}

.shomart-account-subpage p {
    font-size: 14px;
    color: #333;
    margin-bottom: 12px;
}
.shomart-account-subpage a {
    color: #2874f0;
    text-decoration: none;
}

/* Required asterisk */
.required {
    color: #e53935;
    font-weight: 700;
}
</style>
    <?php
    return;
}

// MAIN DASHBOARD VIEW
?>

<div class="shomart-account-page">

    <?php if (is_user_logged_in()) :
        $current_user = wp_get_current_user();
        $account_url  = wc_get_page_permalink('myaccount');
    ?>

        <!-- ============ USER PROFILE CARD ============ -->
        <div class="account-profile-card">
            <div class="account-avatar">
                <?php echo get_avatar($current_user->user_email, 60); ?>
            </div>
            <div class="account-user-info">
                <h2><?php echo esc_html($current_user->display_name); ?></h2>
                <p><?php echo esc_html($current_user->user_email); ?></p>
            </div>
        </div>

        <!-- ============ QUICK STATS ============ -->
        <div class="account-stats-row">
            <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', $account_url)); ?>" class="stat-card">
                <span class="stat-icon">📦</span>
                <span class="stat-label">Orders</span>
            </a>
            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', '', $account_url)); ?>" class="stat-card">
                <span class="stat-icon">📍</span>
                <span class="stat-label">Address</span>
            </a>
            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account', '', $account_url)); ?>" class="stat-card">
                <span class="stat-icon">✏️</span>
                <span class="stat-label">Profile</span>
            </a>
        </div>

        <!-- ============ MENU LIST ============ -->
        <div class="account-menu-list">

            <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', $account_url)); ?>" class="account-menu-item">
                <span class="menu-icon">📦</span>
                <span class="menu-text">My Orders</span>
                <span class="menu-arrow">›</span>
            </a>

            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', '', $account_url)); ?>" class="account-menu-item">
                <span class="menu-icon">📍</span>
                <span class="menu-text">Saved Addresses</span>
                <span class="menu-arrow">›</span>
            </a>
            <a href="<?php echo esc_url(home_url('/become-seller/')); ?>" class="account-menu-item seller-item">
    <span class="menu-icon">🏪</span>
    <span class="menu-text">Become a Seller</span>
    <span class="menu-arrow">›</span>
</a>

            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account', '', $account_url)); ?>" class="account-menu-item">
                <span class="menu-icon">👤</span>
                <span class="menu-text">Account Details</span>
                <span class="menu-arrow">›</span>
            </a>

            <a href="<?php echo esc_url(wc_get_endpoint_url('downloads', '', $account_url)); ?>" class="account-menu-item">
                <span class="menu-icon">⬇️</span>
                <span class="menu-text">Downloads</span>
                <span class="menu-arrow">›</span>
            </a>

            <?php
            $privacy_page = get_page_by_path('privacy-policy');
            if ($privacy_page) : ?>
            <a href="<?php echo esc_url(get_permalink($privacy_page->ID)); ?>" class="account-menu-item">
                <span class="menu-icon">🔒</span>
                <span class="menu-text">Privacy Policy</span>
                <span class="menu-arrow">›</span>
            </a>
            <?php endif; ?>
            <?php
            $refund_page = get_page_by_path('refund-return-policy');
            if ($refund_page) : ?>
            <a href="<?php echo esc_url(get_permalink($refund_page->ID)); ?>" class="account-menu-item">
                <span class="menu-icon">↩️</span>
                <span class="menu-text">Refund & Return Policy</span>
                <span class="menu-arrow">›</span>
            </a>
            <?php endif; ?>

            <a href="#" class="account-menu-item logout-item" onclick="showLogoutModal(event);">
                <span class="menu-icon">🚪</span>
                <span class="menu-text">Logout</span>
                <span class="menu-arrow">›</span>
            </a>

        </div>

        <!-- Logout Modal -->
        <div id="logoutModal" class="logout-modal" style="display:none;">
            <div class="logout-modal-content">
                <div class="logout-icon">🚪</div>
                <h3>Logout?</h3>
                <p>Are you sure you want to logout from your account?</p>
                <div class="logout-buttons">
                    <button type="button" onclick="closeLogoutModal()" class="btn-cancel">Cancel</button>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn-confirm">Yes, Logout</a>
                </div>
            </div>
        </div>

    <?php else : ?>

        <!-- ============ LOGIN / REGISTER ============ -->
        <div class="account-auth-wrap">

            <div class="auth-header">
                <h2>Welcome to Shomart</h2>
                <p>Login or create an account</p>
            </div>

            <div class="auth-tabs">
                <button type="button" class="auth-tab active" data-tab="login">Login</button>
                <button type="button" class="auth-tab" data-tab="register">Register</button>
            </div>

            <!-- Login Form -->
            <div class="auth-form-wrap active" id="tab-login">
                <form method="post" class="woocommerce-form woocommerce-form-login login">
                    <?php do_action('woocommerce_login_form_start'); ?>

                    <div class="form-field">
                        <label>Username or Email</label>
                        <input type="text" name="username" class="form-input" required>
                    </div>

                    <div class="form-field">
                        <label>Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>

                    <div class="form-check">
                        <label>
                            <input type="checkbox" name="rememberme" value="forever"> Remember me
                        </label>
                        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-link">Forgot password?</a>
                    </div>

                    <?php do_action('woocommerce_login_form'); ?>
                    <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>

                    <button type="submit" class="shomart-btn" name="login" value="Login">Login</button>

                    <?php do_action('woocommerce_login_form_end'); ?>
                </form>
            </div>

            <!-- Register Form -->
            <div class="auth-form-wrap" id="tab-register">
                <form method="post" class="woocommerce-form woocommerce-form-register register">
                    <?php do_action('woocommerce_register_form_start'); ?>

                    <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                    <div class="form-field">
                        <label>Username</label>
                        <input type="text" name="username" class="form-input" required>
                    </div>
                    <?php endif; ?>

                    <div class="form-field">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>

                    <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                    <div class="form-field">
                        <label>Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <?php endif; ?>

                    <?php do_action('woocommerce_register_form'); ?>
                    <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>

                    <button type="submit" class="shomart-btn" name="register" value="Register">Create Account</button>

                    <?php do_action('woocommerce_register_form_end'); ?>
                </form>
            </div>

        </div>

    <?php endif; ?>

</div>

<style>
.shomart-account-page {
    padding: 12px;
    padding-bottom: 100px;
}

.account-profile-card {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #2874f0;
    color: #fff;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
}
.account-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.5);
}
.account-user-info h2 { font-size: 16px; font-weight: 700; margin: 0; }
.account-user-info p  { font-size: 12px; opacity: 0.85; margin: 2px 0 0 0; }

.account-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 14px 8px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    text-decoration: none;
    color: inherit;
}
.stat-icon { font-size: 22px; }
.stat-label { font-size: 11px; font-weight: 600; color: #333; }

.account-menu-list {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.account-menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 16px;
    border-bottom: 1px solid #e5e7eb;
    color: #212121;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
}
.account-menu-item:last-child { border-bottom: none; }
.menu-icon { font-size: 18px; width: 24px; text-align: center; }
.menu-text { flex: 1; }
.menu-arrow { color: #878787; font-size: 18px; }
.logout-item { color: #e53935; }

/* Logout Modal */
.logout-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.logout-modal-content {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    width: 90%;
    max-width: 320px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}
.logout-icon { font-size: 48px; margin-bottom: 12px; }
.logout-modal-content h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #212121;
}
.logout-modal-content p {
    font-size: 14px;
    color: #878787;
    margin-bottom: 20px;
}
.logout-buttons { display: flex; gap: 10px; }
.btn-cancel, .btn-confirm {
    flex: 1;
    padding: 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    box-sizing: border-box;
}
.btn-cancel { background: #f1f2f4; color: #212121; }
.btn-confirm { background: #e53935; color: #fff; }

/* Auth Forms */
.account-auth-wrap {
    background: #fff;
    border-radius: 12px;
    padding: 20px 16px;
}
.auth-header { text-align: center; margin-bottom: 20px; }
.auth-header h2 { font-size: 20px; font-weight: 700; margin: 0; }
.auth-header p  { font-size: 13px; color: #878787; margin: 4px 0 0 0; }

.auth-tabs {
    display: flex;
    background: #f1f2f4;
    border-radius: 8px;
    padding: 4px;
    margin-bottom: 20px;
    gap: 4px;
}
.auth-tab {
    flex: 1;
    padding: 10px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    color: #878787;
}
.auth-tab.active {
    background: #fff;
    color: #2874f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

.auth-form-wrap { display: none; }
.auth-form-wrap.active { display: block; }

.form-field { margin-bottom: 14px; }
.form-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #878787;
    margin-bottom: 6px;
    text-transform: uppercase;
}
.form-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 14px;
    outline: none;
    background: #f1f2f4;
    box-sizing: border-box;
}
.form-input:focus { border-color: #2874f0; background: #fff; }

.form-check {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    margin-bottom: 16px;
    color: #333;
}
.forgot-link { color: #2874f0; font-weight: 500; text-decoration: none; }

.shomart-btn {
    background: #ffc200;
    color: #212121;
    font-weight: 700;
    border: none;
    padding: 14px;
    border-radius: 8px;
    width: 100%;
    font-size: 15px;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('.auth-tab');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.auth-form-wrap').forEach(function(f) { f.classList.remove('active'); });
            this.classList.add('active');
            var target = document.getElementById('tab-' + this.dataset.tab);
            if (target) target.classList.add('active');
        });
    });
});

function showLogoutModal(e) {
    e.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
}
function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}
</script>