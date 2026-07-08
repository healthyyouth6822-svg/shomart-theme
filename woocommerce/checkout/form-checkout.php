<?php
/**
 * Checkout Form
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

if (WC()->cart->is_empty()) {
    echo '<div style="padding:40px;text-align:center;background:#fff;border-radius:12px;margin:12px;">';
    echo '<p style="font-size:16px;">Your cart is empty. <a href="' . esc_url(wc_get_page_permalink('shop')) . '" style="color:var(--blue);">Continue Shopping</a></p>';
    echo '</div>';
    return;
}

// Remove coupon code field
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
remove_action('woocommerce_before_checkout_form_cart_notices', 'woocommerce_checkout_coupon_form', 10);

wc_print_notices();
do_action('woocommerce_before_checkout_form', $checkout);
?>

<div class="shomart-checkout-page">

    <form name="checkout" method="post" class="checkout woocommerce-checkout"
          action="<?php echo esc_url(wc_get_checkout_url()); ?>"
          enctype="multipart/form-data">

        <!-- ============ SECTION 1: CONTACT INFO ============ -->
        <div class="checkout-section">
            <h3 class="checkout-section-title">📞 Contact Information</h3>
            <div class="checkout-fields">
                <?php
                foreach ($checkout->get_checkout_fields('billing') as $key => $field) {
                    woocommerce_form_field($key, $field, $checkout->get_value($key));
                }
                ?>
            </div>
        </div>

        <!-- ============ SECTION 2: SHIPPING ============ -->
        <?php if (WC()->cart->needs_shipping_address()) : ?>
        <div class="checkout-section">
            <h3 class="checkout-section-title">🚚 Shipping Address</h3>
            <div class="checkout-fields">
                <?php
                foreach ($checkout->get_checkout_fields('shipping') as $key => $field) {
                    woocommerce_form_field($key, $field, $checkout->get_value($key));
                }
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ SECTION 3: ORDER REVIEW ============ -->
        <div class="checkout-section">
            <h3 class="checkout-section-title">🛒 Order Summary</h3>
            <div class="checkout-order-review">
                <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>
        </div>

        <!-- ============ SECTION 4: PAYMENT METHOD (Custom) ============ -->
        <div class="checkout-section">
            <h3 class="checkout-section-title">💳 Payment Method</h3>
            
            <div class="custom-payment-options">
                
                <!-- Online Payment Option -->
                <div class="payment-option-card" onclick="showOnlinePaymentAlert(event)">
                    <input type="radio" name="custom_payment" id="online_payment" disabled>
                    <label for="online_payment">
                        <span class="pay-icon">💳</span>
                        <div class="pay-info">
                            <strong>Online Payment</strong>
                            <small>Credit/Debit Card, UPI, Net Banking</small>
                        </div>
                    </label>
                </div>
                
                <!-- Cash on Delivery Option -->
                <div class="payment-option-card active" onclick="selectCOD()">
                    <input type="radio" name="custom_payment" id="cod_payment" checked>
                    <label for="cod_payment">
                        <span class="pay-icon">💵</span>
                        <div class="pay-info">
                            <strong>Cash on Delivery</strong>
                            <small>Pay when you receive your order</small>
                        </div>
                    </label>
                </div>
                
            </div>
            
            <!-- Hide default payment but keep it functional -->
            <div style="display:none;">
                <?php do_action('woocommerce_checkout_payment'); ?>
            </div>
            
            <!-- Custom Place Order Button -->
            <button type="submit" class="custom-place-order" name="woocommerce_checkout_place_order" id="place_order" value="Place order">
                Place Order →
            </button>
            
            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        </div>

        <!-- Order Notes -->
        <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
        <div class="checkout-section">
            <h3 class="checkout-section-title">📝 Order Notes (Optional)</h3>
            <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) :
                woocommerce_form_field($key, $field, $checkout->get_value($key));
            endforeach; ?>
        </div>
        <?php endif; ?>

        <?php do_action('woocommerce_checkout_after_order_review'); ?>

    </form>

</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>

<style>
.shomart-checkout-page {
    padding: 12px;
    padding-bottom: 120px;
}

/* Hide WooCommerce default coupon */
.woocommerce-form-coupon-toggle,
.woocommerce-form-coupon,
.checkout_coupon {
    display: none !important;
}

.checkout-section {
    background: var(--white);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.checkout-section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}

.checkout-fields .form-row {
    margin-bottom: 14px;
}

.checkout-fields label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-light);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.checkout-fields input,
.checkout-fields select,
.checkout-fields textarea {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 14px;
    outline: none;
    background: var(--grey-bg);
    transition: border-color 0.2s;
    font-family: inherit;
    box-sizing: border-box;
}

.checkout-fields input:focus,
.checkout-fields select:focus,
.checkout-fields textarea:focus {
    border-color: var(--blue);
    background: #fff;
}

/* Order Review Table */
.checkout-order-review table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.checkout-order-review table th,
.checkout-order-review table td {
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
    vertical-align: top;
}
.checkout-order-review table tfoot tr:last-child td,
.checkout-order-review table tfoot tr:last-child th {
    font-size: 16px;
    font-weight: 700;
    border-bottom: none;
}

/* ============ CUSTOM PAYMENT OPTIONS ============ */
.custom-payment-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 16px;
}

.payment-option-card {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
}

.payment-option-card.active {
    border-color: #2874f0;
    background: #f0f7ff;
}

.payment-option-card.disabled-payment {
    border-color: #e53935;
    background: #fff5f5;
}

.payment-option-card label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    margin: 0;
}

.payment-option-card input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #2874f0;
    cursor: pointer;
}

.pay-icon {
    font-size: 28px;
}

.pay-info strong {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #212121;
    margin-bottom: 2px;
}

.pay-info small {
    font-size: 11px;
    color: #878787;
}

/* Place Order Button */
.custom-place-order {
    background: #ffc200;
    color: #212121;
    font-weight: 700;
    font-size: 16px;
    border-radius: 10px;
    padding: 16px;
    width: 100%;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-top: 8px;
}

.custom-place-order:hover {
    background: #ffb000;
}

/* Online payment alert message */
.online-pay-alert {
    background: #fff5f5;
    border-left: 4px solid #e53935;
    color: #c62828;
    padding: 12px;
    border-radius: 6px;
    margin-top: 10px;
    font-size: 13px;
    font-weight: 600;
    display: none;
}

.online-pay-alert.show {
    display: block;
    animation: slideDown 0.3s;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Alert Container -->
<div id="payment-alert-container"></div>

<script>
function showOnlinePaymentAlert(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Remove old alert
    var oldAlert = document.querySelector('.online-pay-alert');
    if (oldAlert) oldAlert.remove();
    
    // Show alert
    var alertDiv = document.createElement('div');
    alertDiv.className = 'online-pay-alert show';
    alertDiv.innerHTML = '⚠️ Sorry! Online payment is not available at the moment. Please use Cash on Delivery.';
    
    var paymentOptions = document.querySelector('.custom-payment-options');
    paymentOptions.parentNode.insertBefore(alertDiv, paymentOptions.nextSibling);
    
    // Auto hide after 4 seconds
    setTimeout(function() {
        alertDiv.style.opacity = '0';
        setTimeout(function() { alertDiv.remove(); }, 300);
    }, 4000);
    
    return false;
}

function selectCOD() {
    document.querySelectorAll('.payment-option-card').forEach(function(card) {
        card.classList.remove('active');
    });
    var codCard = document.getElementById('cod_payment').closest('.payment-option-card');
    codCard.classList.add('active');
    document.getElementById('cod_payment').checked = true;
    
    // Select COD in hidden WooCommerce form
    var codRadio = document.querySelector('input[name="payment_method"][value="cod"]');
    if (codRadio) {
        codRadio.checked = true;
    }
}

// Auto-select COD on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var codRadio = document.querySelector('input[name="payment_method"][value="cod"]');
        if (codRadio) {
            codRadio.checked = true;
        }
    }, 500);
});
</script>