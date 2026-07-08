
 <?php
/**
 * Cart Page
 *
 * @package WooCommerce\Templates
 * @version 10.8.0
 */
defined('ABSPATH') || exit;
?>

<?php do_action('woocommerce_before_cart'); ?>

<div class="shomart-cart-page">

    <?php if (WC()->cart->is_empty()) : ?>

        <!-- ============ EMPTY CART ============ -->
        <div class="cart-empty-state">
            <div class="cart-empty-icon">🛒</div>
            <h2>Your cart is empty!</h2>
            <p>Looks like you haven't added anything yet.</p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="shomart-btn">
                Start Shopping
            </a>
        </div>

    <?php else : ?>

        <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">

            <?php do_action('woocommerce_before_cart_table'); ?>

            <!-- ============ CART ITEMS ============ -->
            <div class="cart-items-list">

                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>

                <div class="cart-item-card">

                    <!-- Product Image -->
                    <div class="cart-item-img">
                        <?php
                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                        if (!$product_permalink) {
                            echo $thumbnail;
                        } else {
                            echo '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
                        }
                        ?>
                    </div>

                    <!-- Product Details -->
                    <div class="cart-item-details">

                        <!-- Name -->
                        <div class="cart-item-name">
                            <?php
                            if (!$product_permalink) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                            } else {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                            }
                            ?>
                        </div>

                        <!-- Variation -->
                        <?php echo wc_get_formatted_cart_item_data($cart_item); ?>

                        <!-- Price -->
                        <div class="cart-item-price">
                            <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                        </div>

                        <!-- Quantity + Remove Row -->
                        <div class="cart-item-actions">

                            <!-- Quantity Stepper -->
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn qty-minus" data-key="<?php echo esc_attr($cart_item_key); ?>">−</button>
                                <input
                                    type="number"
                                    class="qty-input"
                                    name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                    value="<?php echo esc_attr($cart_item['quantity']); ?>"
                                    min="0"
                                    step="1"
                                    data-key="<?php echo esc_attr($cart_item_key); ?>"
                                >
                                <button type="button" class="qty-btn qty-plus" data-key="<?php echo esc_attr($cart_item_key); ?>">+</button>
                            </div>

                            <!-- Remove -->
                            <?php
                            echo apply_filters(
                                'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<a href="%s" class="cart-item-remove" aria-label="%s">🗑 Remove</a>',
                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                                    esc_attr__('Remove this item', 'shomart')
                                ),
                                $cart_item_key
                            );
                            ?>

                        </div>
                    </div>
                </div>

                <?php
                    endif;
                endforeach;
                ?>

            </div>

            <?php do_action('woocommerce_cart_contents'); ?>

            <!-- Hidden update cart -->
            <button type="submit" class="button" name="update_cart" value="Update cart" style="display:none;">
                <?php esc_html_e('Update cart', 'woocommerce'); ?>
            </button>

            <?php do_action('woocommerce_after_cart_contents'); ?>
            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>

        </form>

        <?php do_action('woocommerce_after_cart_table'); ?>

        
        <!-- ============ ORDER SUMMARY ============ -->
        <?php do_action('woocommerce_before_cart_collaterals'); ?>

        <div class="cart-summary-box">
            <h3 class="summary-title">Price Details</h3>

            <div class="summary-row">
                <span>Subtotal</span>
                <span><?php woocommerce_cart_totals_subtotal_html(); ?></span>
            </div>

            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="summary-row discount-row">
                <span>Discount (<?php echo esc_html($code); ?>)</span>
                <span class="discount-amount">− <?php wc_cart_totals_coupon_html($coupon); ?></span>
            </div>
            <?php endforeach; ?>

            <div class="summary-row">
                <span>Shipping</span>
                <span>
                    <?php
                    $shipping = WC()->cart->get_shipping_total();
                    echo $shipping > 0 ? wc_price($shipping) : '<span class="free-ship">FREE</span>';
                    ?>
                </span>
            </div>

            <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
            <div class="summary-row">
                <span>Tax</span>
                <span><?php woocommerce_cart_totals_taxes_total_html(); ?></span>
            </div>
            <?php endif; ?>

            <div class="summary-row summary-total">
                <span>Total Amount</span>
                <span><?php woocommerce_cart_totals_order_total_html(); ?></span>
            </div>

            <?php
            $savings = WC()->cart->get_discount_total();
            if ($savings > 0) : ?>
            <div class="summary-savings">
                You save <strong><?php echo wc_price($savings); ?></strong> on this order 🎉
            </div>
            <?php endif; ?>
        </div>

        <!-- ============ CHECKOUT BUTTON ============ -->
        <div class="cart-checkout-wrap">
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="checkout-btn-main">
                Proceed to Checkout →
            </a>
        </div>

        <?php do_action('woocommerce_after_cart'); ?>

    <?php endif; ?>

</div>

<!-- ============ CART PAGE CSS ============ -->
<style>
.shomart-cart-page {
    padding: 12px;
    padding-bottom: 100px;
}

/* Empty State */
.cart-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--white);
    border-radius: 12px;
    margin-top: 20px;
}
.cart-empty-icon { font-size: 60px; margin-bottom: 16px; }
.cart-empty-state h2 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.cart-empty-state p { color: var(--text-light); font-size: 14px; margin-bottom: 20px; }

/* Cart Items */
.cart-items-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }

.cart-item-card {
    display: flex;
    gap: 12px;
    background: var(--white);
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.cart-item-img {
    width: 90px;
    flex-shrink: 0;
}
.cart-item-img img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 8px;
    mix-blend-mode: multiply;
}

.cart-item-details { flex: 1; min-width: 0; }

.cart-item-name {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-dark);
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.cart-item-name a { color: inherit; }

.cart-item-price {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 6px 0;
}

/* Quantity Stepper */
.cart-item-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
}

.qty-stepper {
    display: flex;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: 6px;
    overflow: hidden;
}

.qty-btn {
    background: var(--grey-bg);
    border: none;
    width: 32px;
    height: 32px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: var(--text-dark);
}

.qty-input {
    width: 40px;
    height: 32px;
    border: none;
    border-left: 1px solid var(--border);
    border-right: 1px solid var(--border);
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    outline: none;
    -moz-appearance: textfield;
}
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }

.cart-item-remove {
    font-size: 12px;
    color: #e53935;
    font-weight: 500;
    cursor: pointer;
}


/* Summary Box */
.cart-summary-box {
    background: var(--white);
    border-radius: 10px;
    padding: 16px 12px;
    margin-bottom: 10px;
}
.summary-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}
.summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
    color: var(--text-mid);
}
.summary-row:last-of-type { border-bottom: none; }
.summary-total {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-dark);
    padding-top: 12px;
}
.discount-amount { color: #2e7d32; font-weight: 600; }
.free-ship { color: #2e7d32; font-weight: 600; }
.summary-savings {
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 13px;
    padding: 8px 12px;
    border-radius: 6px;
    margin-top: 10px;
    text-align: center;
}

/* Checkout Button */
.cart-checkout-wrap {
    position: fixed;
    bottom: 65px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 24px);
    max-width: 456px;
    z-index: 100;
}
.checkout-btn-main {
    display: block;
    background: var(--yellow);
    color: var(--text-dark);
    text-align: center;
    padding: 16px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<!-- ============ CART JS ============ -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Quantity stepper
    document.querySelectorAll('.qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var key    = this.dataset.key;
            var input  = document.querySelector('.qty-input[data-key="' + key + '"]');
            var val    = parseInt(input.value) || 1;

            if (this.classList.contains('qty-plus')) {
                input.value = val + 1;
            } else if (this.classList.contains('qty-minus')) {
                input.value = Math.max(0, val - 1);
            }

            // Auto-submit after short delay
            clearTimeout(input._timer);
            input._timer = setTimeout(function() {
                document.querySelector('[name="update_cart"]').click();
            }, 600);
        });
    });

    // Input change also triggers update
    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('change', function() {
            clearTimeout(this._timer);
            this._timer = setTimeout(function() {
                document.querySelector('[name="update_cart"]').click();
            }, 600);
        });
    });
});
</script>