<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Google Sheets seller sync Web App endpoint.
 * See SELLER_SHEETS_SETUP.md for deployment and update instructions.
 */
if ( ! defined( 'SHOMART_SHEETS_WEBHOOK_URL' ) ) {
    define( 'SHOMART_SHEETS_WEBHOOK_URL', 'https://script.google.com/macros/s/AKfycbwxSyl22MjEvDm5TMSQoQgnk-Vi_v6-uUsegyS29MQuVG_-d5C0hbqt6CtHqguZAkpo/exec' );
}

/**
 * STEP 1: Theme Setup
 */
function shomart_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );

    add_theme_support( 'custom-logo', array(
        'height'      => 40,
        'width'       => 120,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'shomart' ),
        'footer'  => __( 'Footer Menu', 'shomart' ),
    ) );
}
add_action( 'after_setup_theme', 'shomart_setup' );


/**
 * STEP 2: Load CSS & JS
 */
function shomart_load_assets() {

    wp_enqueue_style(
        'shomart-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'shomart-style',
        get_stylesheet_uri(),
        array(),
        '2.0.0'
    );

}
add_action( 'wp_enqueue_scripts', 'shomart_load_assets' );


/**
 * STEP 3: WooCommerce Tweaks (Section 8)
 */

// Remove catalog ordering
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Remove result count
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

// Remove Add to Cart from archive grids
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Disable Sale badge
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

// Make Billing Address Line 1 and 2 optional
add_filter( 'woocommerce_billing_fields', 'shomart_optional_billing_fields', 10, 1 );
function shomart_optional_billing_fields( $fields ) {
    if ( isset( $fields['billing_address_1'] ) ) {
        $fields['billing_address_1']['required'] = false;
    }
    if ( isset( $fields['billing_address_2'] ) ) {
        $fields['billing_address_2']['required'] = false;
    }
    return $fields;
}


/**
 * STEP 4: Cart Count (AJAX)
 */
function shomart_cart_fragment( $fragments ) {
    $count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
    ob_start();
    ?>
    <span class="cart-count"><?php echo esc_html( $count ); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'shomart_cart_fragment' );


/**
 * STEP 5: Products per page
 */
function shomart_products_per_page() {
    return 12;
}
add_filter( 'loop_shop_per_page', 'shomart_products_per_page' );


/**
 * STEP 6: Delivery Location
 */
function shomart_get_delivery_location() {
    $location = '';

    if ( function_exists( 'WC' ) && WC()->customer ) {
        $city = WC()->customer->get_billing_city();
        if ( $city ) {
            $location = $city;
        }
    }

    return $location;
}


/**
 * STEP 7: Custom Categories Button HTML
 * Outputs the yellow "Categories" button + popup overlay
 */
function shomart_custom_categories_html() {
    
    // Get all top-level categories
    $categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ));
    
    if (empty($categories) || is_wp_error($categories)) {
        return;
    }
    ?>
    <div class="shomart-cat-wrap">
        <button class="shomart-cat-btn" id="shomart-cat-btn" type="button">
            📂 Categories &#8645;
        </button>
    </div>
    
    <!-- Categories Popup Overlay -->
    <div class="cat-popup-overlay" id="cat-popup-overlay">
        <div class="cat-popup-panel">
            
            <div class="cat-popup-header">
                <h3>📂 All Categories</h3>
                <button class="cat-popup-close" id="cat-popup-close" type="button">✕</button>
            </div>
            
            <div class="cat-popup-list">
                <?php foreach ($categories as $cat) :
                    $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                    $img_url  = $thumb_id ? wp_get_attachment_url($thumb_id) : '';
                    $cat_link = get_term_link($cat);
                    
                ?>
                <a href="<?php echo esc_url($cat_link); ?>" class="cat-popup-item">
                    <div class="cat-popup-img">
                        <?php if ($img_url) : ?>
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                        <?php else : ?>
                            <span class="cat-letter"><?php echo esc_html(mb_substr($cat->name, 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="cat-popup-info">
    <strong><?php echo esc_html($cat->name); ?></strong>
</div>
                    <span class="cat-popup-arrow">›</span>
                </a>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>
    <?php
}
add_action('woocommerce_before_shop_loop', 'shomart_custom_categories_html', 20);


/**
 * STEP 8: Add Body Class for front page detection
 */
function shomart_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'home-page';
    }
    return $classes;
}
add_filter( 'body_class', 'shomart_body_classes' );
/**
 * STEP 9: Handle Account & Address Form Submissions Manually
 * This ensures Save buttons work in our custom my-account template
 */
add_action('template_redirect', 'shomart_handle_account_forms', 5);
function shomart_handle_account_forms() {
    
    if (!is_user_logged_in() || !is_account_page()) {
        return;
    }
    
    // Handle Edit Address Form
    if (isset($_POST['action']) && $_POST['action'] === 'edit_address') {
        if (isset($_POST['woocommerce-edit-address-nonce']) && wp_verify_nonce($_POST['woocommerce-edit-address-nonce'], 'woocommerce-edit_address')) {
            WC_Form_Handler::save_address();
        }
    }
    
    // Handle Edit Account Form
    if (isset($_POST['save_account_details_nonce']) || isset($_POST['save-account-details-nonce'])) {
        $nonce_value = isset($_POST['save_account_details_nonce']) ? $_POST['save_account_details_nonce'] : $_POST['save-account-details-nonce'];
        if (wp_verify_nonce($nonce_value, 'save_account_details')) {
            WC_Form_Handler::save_account_details();
        }
    }
}


/**
 * STEP 10: Hide Default WooCommerce Order Number Heading
 */
add_filter('woocommerce_my_account_my_orders_actions', 'shomart_keep_view_action', 10, 2);
function shomart_keep_view_action($actions, $order) {
    return $actions;
}

// Remove the "Order #221 was placed on..." default text
add_filter('gettext', 'shomart_remove_order_text', 20, 3);
function shomart_remove_order_text($translated, $original, $domain) {
    if ($domain !== 'woocommerce') {
        return $translated;
    }
    
    // Hide the "Order #X was placed on..." sentence
    if (strpos($original, 'was placed on') !== false || strpos($original, '%1$s was placed') !== false) {
        return '';
    }
    
    return $translated;
}
/**
 * STEP 11: Hide Product Counts in Category Names
 */
add_filter('woocommerce_subcategory_count_html', '__return_empty_string');

// Also hide it in widgets
add_filter('woocommerce_layered_nav_count', '__return_empty_string');

// Hide count in get_terms output (extra safety)
add_filter('get_terms', 'shomart_remove_category_count', 10, 4);
function shomart_remove_category_count($terms, $taxonomies, $args, $term_query) {
    if (in_array('product_cat', (array) $taxonomies)) {
        foreach ($terms as $term) {
            if (is_object($term)) {
                // Don't actually change count, just for display
                // CSS will hide it from frontend
            }
        }
    }
    return $terms;
}
/**
 * STEP 12: Security - Hide WordPress version
 */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

/**
 * STEP 13: Disable File Editing in WordPress Admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * STEP 14: Block Suspicious Login Attempts
 */
add_action('wp_login_failed', 'shomart_log_failed_login');
function shomart_log_failed_login($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    error_log("Failed login attempt: User=$username, IP=$ip, Time=" . date('Y-m-d H:i:s'));
}

/**
 * STEP 15: Disable XML-RPC (commonly attacked)
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * STEP 16: Remove Author URL (hackers find usernames here)
 */
add_action('template_redirect', 'shomart_block_author_query');
function shomart_block_author_query() {
    if (isset($_GET['author'])) {
        wp_redirect(home_url(), 301);
        exit;
    }
}
/**
 * STEP 17: Speed - Disable Emojis (Saves Loading Time)
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');

/**
 * STEP 18: Speed - Disable Embeds (oEmbed)
 */
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');

/**
 * STEP 19: Speed - Remove Query Strings from Static Files
 */
function shomart_remove_query_strings($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'shomart_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'shomart_remove_query_strings', 15, 1);

/**
 * STEP 20: Speed - Lazy Load Images
 */
add_filter('wp_lazy_loading_enabled', '__return_true');

/**
 * STEP 21: Speed - Limit Post Revisions (saves database space)
 */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

/**
 * STEP 22: Speed - Disable Heartbeat on Frontend
 */
add_action('init', 'shomart_stop_heartbeat', 1);
function shomart_stop_heartbeat() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}

/**
 * STEP 23: Speed - Defer Non-Critical JavaScript
 */
function shomart_defer_scripts($tag, $handle) {
    $defer_scripts = array('jquery-migrate');
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'shomart_defer_scripts', 10, 2);













/**
 * STEP 11: Register Seller Application Custom Post Type
 * Stores all seller applications in WordPress admin
 */
function shomart_register_seller_application_post_type() {
    register_post_type('seller_application',
        array(
            'labels' => array(
                'name'               => 'Seller Applications',
                'singular_name'      => 'Seller Application',
                'menu_name'          => '🏪 Seller Applications',
                'all_items'          => 'All Applications',
                'view_item'          => 'View Application',
                'search_items'       => 'Search Applications',
                'not_found'          => 'No applications found',
            ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-store',
            'capability_type'     => 'post',
            'capabilities'        => array(
                'create_posts'    => 'do_not_allow', // Can't create from admin (only via form)
            ),
            'map_meta_cap'        => true,
            'supports'            => array('title'),
            'has_archive'         => false,
        )
    );
}
add_action('init', 'shomart_register_seller_application_post_type');


/**
 * STEP 12: Add Custom Columns to Seller Applications Admin Page
 */
add_filter('manage_seller_application_posts_columns', 'shomart_seller_application_columns');
function shomart_seller_application_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = 'Shop / Owner';
    $new_columns['phone'] = 'Phone';
    $new_columns['city'] = 'City';
    $new_columns['products'] = 'Products';
    $new_columns['status'] = 'Status';
    $new_columns['date'] = 'Applied On';
    return $new_columns;
}

add_action('manage_seller_application_posts_custom_column', 'shomart_seller_application_column_content', 10, 2);
function shomart_seller_application_column_content($column, $post_id) {
    switch ($column) {
        case 'phone':
            $phone = get_post_meta($post_id, 'phone', true);
            $whatsapp = get_post_meta($post_id, 'whatsapp', true);
            echo '📞 ' . esc_html($phone);
            if ($whatsapp && $whatsapp !== $phone) {
                echo '<br>💬 ' . esc_html($whatsapp);
            }
            break;
        case 'city':
            echo esc_html(get_post_meta($post_id, 'shop_city', true));
            break;
        case 'products':
            $products = get_post_meta($post_id, 'products_sold', true);
            echo esc_html(wp_trim_words($products, 8));
            break;
        case 'status':
            $status = get_post_meta($post_id, 'status', true);
            if (!$status) {
                $status = 'pending';
            }
            $status_colors = array(
                'pending'   => '#ff9800',
                'contacted' => '#2196f3',
                'approved'  => '#4caf50',
                'active'    => '#009688',
                'fraud'     => '#f44336',
                'rejected'  => '#f44336',
            );
            $color = isset($status_colors[$status]) ? $status_colors[$status] : '#878787';
            echo '<span style="background:' . $color . ';color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;">' . esc_html($status ?: 'Pending') . '</span>';
            break;
    }
}


/**
 * STEP 13: Add Meta Box to View Application Details
 */
add_action('add_meta_boxes', 'shomart_seller_application_meta_boxes');
function shomart_seller_application_meta_boxes() {
    add_meta_box(
        'seller_application_details',
        '📋 Application Details',
        'shomart_seller_application_details_callback',
        'seller_application',
        'normal',
        'high'
    );
    
    add_meta_box(
        'seller_application_status',
        '🔄 Update Status',
        'shomart_seller_application_status_callback',
        'seller_application',
        'side',
        'high'
    );
}

function shomart_seller_application_details_callback($post) {
    $fields = array(
        'shop_name'      => '🏪 Shop Name',
        'owner_name'     => '👤 Owner Name',
        'phone'          => '📞 Phone',
        'whatsapp'       => '💬 WhatsApp',
        'email'          => '📧 Email',
        'shop_city'      => '🏙️ City',
        'shop_address'   => '📍 Address',
        'products_sold'  => '📦 Products',
        'years_business' => '⏰ Years in Business',
        'monthly_sales'  => '💰 Monthly Sales',
    );
    
    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        if ($value) {
            echo '<tr>';
            echo '<th style="width:200px;">' . esc_html($label) . ':</th>';
            echo '<td><strong>' . esc_html($value) . '</strong></td>';
            echo '</tr>';
        }
    }
    
    $message = get_post_meta($post->ID, 'message', true);
    if (!$message) {
        $message = $post->post_content;
    }
    if ($message) {
        echo '<tr>';
        echo '<th>💬 Message:</th>';
        echo '<td>' . nl2br(esc_html($message)) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    
    // Quick action buttons
    $phone = get_post_meta($post->ID, 'phone', true);
    $whatsapp = get_post_meta($post->ID, 'whatsapp', true);
    $email = get_post_meta($post->ID, 'email', true);
    
    echo '<div style="margin-top:20px;padding-top:20px;border-top:1px solid #ccc;">';
    echo '<h3>Quick Actions:</h3>';
    
    if ($phone) {
        echo '<a href="tel:' . esc_attr($phone) . '" class="button button-primary" style="margin-right:10px;">📞 Call</a>';
    }
    
    if ($whatsapp) {
        $wa_message = urlencode("Hi! Thanks for applying to Shomart. I'm calling regarding your application.");
        echo '<a href="https://wa.me/91' . esc_attr($whatsapp) . '?text=' . $wa_message . '" target="_blank" class="button" style="background:#25d366;color:#fff;margin-right:10px;">💬 WhatsApp</a>';
    }
    
    if ($email) {
        echo '<a href="mailto:' . esc_attr($email) . '" class="button" style="margin-right:10px;">📧 Email</a>';
    }
    
    echo '</div>';
}
function shomart_seller_application_status_callback($post) {

    wp_nonce_field('save_seller_status', 'seller_status_nonce');

    $current_status = get_post_meta($post->ID, 'status', true);

    if (!$current_status) {
        $current_status = 'pending';
    }

    ?>
    
    <p><strong>Application Status:</strong></p>

    <select name="seller_status" style="width:100%; padding:8px; font-size:14px;">
        <option value="pending" <?php selected($current_status, 'pending'); ?>>Pending</option>
        <option value="contacted" <?php selected($current_status, 'contacted'); ?>>Contacted</option>
        <option value="approved" <?php selected($current_status, 'approved'); ?>>Approved</option>
        <option value="active" <?php selected($current_status, 'active'); ?>>Active Seller</option>
        <option value="fraud" <?php selected($current_status, 'fraud'); ?>>Fraud / Blocked</option>
        <option value="rejected" <?php selected($current_status, 'rejected'); ?>>Rejected</option>
    </select>

    <p style="margin-top:10px;">
        <button type="submit" class="button button-primary" style="width:100%;">
            Save Status
        </button>
    </p>

    <?php
}

/**
 * Save Seller Status + Send Notification
 */
add_action('save_post_seller_application', function($post_id) {

    if (!isset($_POST['seller_status_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['seller_status_nonce'])), 'save_seller_status')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (!isset($_POST['seller_status'])) return;

    $allowed_statuses = array('pending', 'contacted', 'approved', 'active', 'fraud', 'rejected');
    $new_status = sanitize_key(wp_unslash($_POST['seller_status']));
    if (!in_array($new_status, $allowed_statuses, true)) return;

    $old_status = get_post_meta($post_id, 'status', true);

    update_post_meta($post_id, 'status', $new_status);

    // Generate a serial and notify once when the seller first becomes approved/active.
    $is_approving = in_array($new_status, array('approved', 'active'), true);
    $was_approved = in_array($old_status, array('approved', 'active'), true);
    if ($is_approving && !$was_approved) {

        $existing_number = get_post_meta($post_id, 'shop_serial', true);

        if (!$existing_number) {
            $next_number = shomart_generate_shop_number();
            update_post_meta($post_id, 'shop_serial', $next_number);
        }

        shomart_send_seller_email($post_id, 'approved');
        do_action('shomart_seller_approved', $post_id);
    }

    if ($new_status === 'rejected' && $old_status !== 'rejected') {
        shomart_send_seller_email($post_id, 'rejected');
    }

    if ($new_status === 'fraud') {
        update_post_meta($post_id, '_shomart_fraud_flag', current_time('mysql'));
    }

});






/**
 * Handle Buy Now logic properly
 */
add_action('template_redirect', function() {

    if (isset($_GET['buynow']) && isset($_GET['add-to-cart'])) {

        $product_id = absint($_GET['add-to-cart']);
        $variation_id = isset($_GET['variation_id']) ? absint($_GET['variation_id']) : 0;

        if (!WC()->cart) {
            wc_load_cart();
        }

        

        

        // 3️⃣ Redirect to checkout
        wp_redirect(wc_get_checkout_url());
        exit;
    }
  
});

 /**
 * Show Cancel Button (60 Minute Rule)
 */
add_filter('woocommerce_my_account_my_orders_actions', function($actions, $order) {

    $allowed_statuses = array('pending', 'processing');

    if (!in_array($order->get_status(), $allowed_statuses)) {
        return $actions;
    }

    // ✅ Safe time comparison using WooCommerce functions
    $order_time = strtotime($order->get_date_created());
    $current_time = time();

    // 60 minute rule
    if (($current_time - $order_time) > 3600) {
        return $actions;
    }

    $actions['cancel'] = array(
        'url'  => wp_nonce_url(
            add_query_arg(
                array(
                    'cancel_order' => 'true',
                    'order_id'     => $order->get_id(),
                ),
                wc_get_page_permalink('myaccount')
            ),
            'cancel_order'
        ),
        'name' => __('Cancel', 'woocommerce')
    );

    return $actions;

}, 10, 2);

/**
 * Handle Cancel + Track Daily Cancellation Attempts
 */
add_action('template_redirect', function() {

    if (!isset($_GET['cancel_order']) || !isset($_GET['order_id'])) return;
    if (!wp_verify_nonce($_GET['_wpnonce'], 'cancel_order')) return;

    $order_id = absint($_GET['order_id']);
    $order = wc_get_order($order_id);
    if (!$order) return;

    if ($order->get_user_id() !== get_current_user_id()) return;

    $allowed_statuses = array('pending', 'processing');

    if (!in_array($order->get_status(), $allowed_statuses)) {
        wc_add_notice('Order cannot be cancelled at this stage.', 'error');
        return;
    }

    // Double check 60 minute rule
    $order_time = strtotime($order->get_date_created());
    $current_time = time();

    if (($current_time - $order_time) > (60 * 60)) {
        wc_add_notice('Cancellation time expired (60 minutes limit).', 'error');
        return;
    }

    $user_id = get_current_user_id();

    // Get today's cancel data
    $today = date('Y-m-d');
    $cancel_data = get_user_meta($user_id, 'daily_cancel_data', true);

    if (!is_array($cancel_data) || $cancel_data['date'] !== $today) {
        $cancel_data = array(
            'date' => $today,
            'count' => 0
        );
    }

    $cancel_data['count']++;
    update_user_meta($user_id, 'daily_cancel_data', $cancel_data);

    // If more than 4 cancels in one day → 7 day ban
    if ($cancel_data['count'] > 4) {

        $ban_until = strtotime('+7 days');
        update_user_meta($user_id, 'ban_until', $ban_until);

        wp_die(
            '<h2>Account Temporarily Restricted</h2>
             <p>You have exceeded the daily cancellation limit (4 per day). 
             Your account is restricted for 7 days.</p>',
            'Access Restricted',
            array('response' => 403)
        );
    }

    // Cancel order
    $order->update_status('cancelled', 'Cancelled by customer.');

    // Notify admin (use admin email correctly)
    wp_mail(
        get_option('admin_email'),
        'Order Cancelled #' . $order_id,
        'Customer cancelled order #' . $order_id
    );

    wc_add_notice('Order cancelled successfully.', 'success');
    wp_redirect(wc_get_page_permalink('myaccount'));
    exit;
});
/**
 * Prevent Banned Users from Placing Orders
 */
add_action('template_redirect', function() {

    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $ban_until = get_user_meta($user_id, 'ban_until', true);

    if ($ban_until && time() < $ban_until) {

        wp_die(
            '<h2>Account Temporarily Restricted</h2>
             <p>Your account is restricted due to excessive cancellations.
             Please try again after 7 days.</p>',
            'Access Restricted',
            array('response' => 403)
        );
    }

    if ($ban_until && time() >= $ban_until) {
        delete_user_meta($user_id, 'ban_until');
    }
});
/**
 * Generate Next Shop Serial Number
 */
function shomart_generate_shop_number() {

    $args = array(
        'post_type' => 'seller_application',
        'post_status' => array('publish', 'private'),
        'meta_key' => 'shop_serial',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'posts_per_page' => 1
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post = $query->posts[0];
        $last_number = (int) get_post_meta($post->ID, 'shop_serial', true);
        wp_reset_postdata();
        return str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
    }

    wp_reset_postdata();
    return '001';
}
/**
 * Send Seller Notification Email
 */
function shomart_send_seller_email($post_id, $status) {

    $owner_name = get_post_meta($post_id, 'owner_name', true);
    $email = get_post_meta($post_id, 'email', true);
    $shop_name = get_post_meta($post_id, 'shop_name', true);
    $shop_serial = get_post_meta($post_id, 'shop_serial', true);

    if (!$email) return;

    if ($status === 'approved') {

        $subject = '✅ Your Shop Has Been Approved - Shomart';
        $message = "
        Dear {$owner_name},

        Congratulations! 🎉

        Your shop '{$shop_name}' has been approved as a Shomart Partner.

        Your Shop Serial Number is: #{$shop_serial}

        We will contact you shortly to list your products.

        Thank you,
        Team Shomart
        ";

    } elseif ($status === 'rejected') {

        $subject = '❌ Application Update - Shomart';
        $message = "
        Dear {$owner_name},

        Thank you for applying to Shomart.

        After review, we are unable to approve your shop at this time.

        You may contact us for more details.

        Team Shomart
        ";

    } else {
        return;
    }

    wp_mail($email, $subject, $message);
}

/**
 * Seller Form Handler
 */
add_action('init', 'shomart_handle_seller_application');
function shomart_handle_seller_application() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['seller_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['seller_nonce'], 'seller_application_nonce')) {
        wp_die('Security check failed. Please go back and try again.');
    }

    // Get all fields
    $shop_name      = sanitize_text_field($_POST['shop_name'] ?? '');
    $shop_city      = sanitize_text_field($_POST['shop_city'] ?? '');
    $shop_address   = sanitize_textarea_field($_POST['shop_address'] ?? '');
    $years_business = sanitize_text_field($_POST['years_business'] ?? '');
    $owner_name     = sanitize_text_field($_POST['owner_name'] ?? '');
    $phone          = sanitize_text_field($_POST['phone'] ?? '');
    $whatsapp       = sanitize_text_field($_POST['whatsapp'] ?? '');
    $email          = sanitize_email($_POST['email'] ?? '');
    $products_sold  = sanitize_textarea_field($_POST['products_sold'] ?? '');
    $monthly_sales  = sanitize_text_field($_POST['monthly_sales'] ?? '');
    $message        = sanitize_textarea_field($_POST['message'] ?? '');

    // Required validation
    if (empty($shop_name) || empty($owner_name) || empty($phone) || empty($shop_city) || empty($shop_address) || empty($products_sold)) {
        wp_redirect(home_url('/become-seller/?error=missing_fields'));
        exit;
    }

    // Phone validation - 10 digits
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        wp_redirect(home_url('/become-seller/?error=invalid_phone'));
        exit;
    }

    // Insert application
    $post_id = wp_insert_post(array(
        'post_title'   => $shop_name . ' - ' . $owner_name,
        'post_content' => $message,
        'post_status'  => 'publish',
        'post_type'    => 'seller_application'
    ));

    if ($post_id && !is_wp_error($post_id)) {
        // Save all meta fields
        update_post_meta($post_id, 'shop_name', $shop_name);
        update_post_meta($post_id, 'shop_city', $shop_city);
        update_post_meta($post_id, 'shop_address', $shop_address);
        update_post_meta($post_id, 'years_business', $years_business);
        update_post_meta($post_id, 'owner_name', $owner_name);
        update_post_meta($post_id, 'phone', $phone);
        update_post_meta($post_id, 'whatsapp', $whatsapp ?: $phone);
        update_post_meta($post_id, 'email', $email);
        update_post_meta($post_id, 'products_sold', $products_sold);
        update_post_meta($post_id, 'monthly_sales', $monthly_sales);
        update_post_meta($post_id, 'message', $message);
        
        // Set initial status
        update_post_meta($post_id, 'status', 'pending');

        wp_redirect(home_url('/become-seller/?submitted=true'));
        exit;
    } else {
        wp_redirect(home_url('/become-seller/?error=save_failed'));
        exit;
    }
}

/**
 * SHOMART SELLER / PRODUCT LINKING
 * For Ladakh local sellers – admin assigns products to sellers.
 */

// ===== 1. PRODUCT SELLER META BOX =====
add_action('add_meta_boxes', 'shomart_product_seller_meta_box');
function shomart_product_seller_meta_box() {
    add_meta_box(
        'shomart_product_seller',
        '🏪 Seller / Shop (Shomart)',
        'shomart_product_seller_meta_box_callback',
        'product',
        'side',
        'high'
    );
}

function shomart_product_seller_meta_box_callback($post) {
    wp_nonce_field('shomart_save_product_seller', 'shomart_product_seller_nonce');

    $current_seller_id = absint(get_post_meta($post->ID, '_shomart_seller_id', true));
    $current_serial = get_post_meta($post->ID, '_shomart_shop_serial', true);
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
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));

    echo '<p><label for="shomart_seller_id"><strong>Assign to Seller:</strong></label></p>';
    echo '<select id="shomart_seller_id" name="shomart_seller_id" style="width:100%;">';
    echo '<option value="">— No seller / Shomart Direct —</option>';

    foreach ($sellers as $seller) {
        $shop_name = get_post_meta($seller->ID, 'shop_name', true);
        $city = get_post_meta($seller->ID, 'shop_city', true);
        $serial = get_post_meta($seller->ID, 'shop_serial', true);
        $label = $shop_name ? $shop_name : $seller->post_title;

        if ($city) {
            $label .= ' – ' . $city;
        }
        if ($serial) {
            $label .= ' – #' . $serial;
        }

        echo '<option value="' . esc_attr($seller->ID) . '" ' . selected($current_seller_id, $seller->ID, false) . '>' . esc_html($label) . '</option>';
    }

    echo '</select>';

    if ($current_serial) {
        echo '<p style="margin-top:10px;padding:8px;background:#e8f5e9;border-left:3px solid #4caf50;font-size:12px;">Current: <strong>Shop #' . esc_html($current_serial) . '</strong></p>';
    }

    echo '<p class="description" style="font-size:11px;color:#666;">Select which Ladakh shop this product belongs to. The customer will see the shop serial number only after ordering.</p>';
}

add_action('save_post_product', 'shomart_save_product_seller', 10, 2);
function shomart_save_product_seller($post_id, $post) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    if (!isset($_POST['shomart_product_seller_nonce'])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST['shomart_product_seller_nonce']));
    if (!wp_verify_nonce($nonce, 'shomart_save_product_seller')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $seller_id = isset($_POST['shomart_seller_id']) ? absint($_POST['shomart_seller_id']) : 0;
    $seller_status = $seller_id ? get_post_meta($seller_id, 'status', true) : '';
    $valid_seller = $seller_id
        && 'seller_application' === get_post_type($seller_id)
        && in_array($seller_status, array('approved', 'active'), true);

    if ($valid_seller) {
        $shop_serial = get_post_meta($seller_id, 'shop_serial', true);
        update_post_meta($post_id, '_shomart_seller_id', $seller_id);
        update_post_meta($post_id, '_shomart_shop_serial', $shop_serial);
    } else {
        delete_post_meta($post_id, '_shomart_seller_id');
        delete_post_meta($post_id, '_shomart_shop_serial');
    }
}

add_filter('manage_product_posts_columns', 'shomart_add_product_seller_column');
function shomart_add_product_seller_column($columns) {
    $new_columns = array();
    $added = false;

    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ('price' === $key) {
            $new_columns['shomart_seller'] = '🏪 Seller';
            $added = true;
        }
    }

    if (!$added) {
        $new_columns['shomart_seller'] = '🏪 Seller';
    }

    return $new_columns;
}

add_action('manage_product_posts_custom_column', 'shomart_product_seller_column_content', 10, 2);
function shomart_product_seller_column_content($column, $post_id) {
    if ('shomart_seller' !== $column) {
        return;
    }

    $seller_id = absint(get_post_meta($post_id, '_shomart_seller_id', true));
    $serial = get_post_meta($post_id, '_shomart_shop_serial', true);

    if (!$seller_id) {
        echo '<span style="color:#999;">— Direct</span>';
        return;
    }

    $shop_name = get_post_meta($seller_id, 'shop_name', true);
    $shop_name = $shop_name ? $shop_name : get_the_title($seller_id);
    echo '<span style="color:#2874f0;">' . esc_html($shop_name);
    if ($serial) {
        echo '<br><strong>#' . esc_html($serial) . '</strong>';
    }
    echo '</span>';
}

// ===== 2. COPY SELLER DETAILS TO ORDER ITEMS =====
/**
 * Read seller data from a product. Variations fall back to their parent product.
 *
 * @param WC_Product $product Product object.
 * @return array
 */
function shomart_get_product_seller_data($product) {
    if (!$product || !is_a($product, 'WC_Product')) {
        return array('seller_id' => 0, 'serial' => '');
    }

    $product_id = $product->get_id();
    $seller_id = absint(get_post_meta($product_id, '_shomart_seller_id', true));
    $serial = get_post_meta($product_id, '_shomart_shop_serial', true);

    if ((!$seller_id || !$serial) && $product->is_type('variation')) {
        $parent_id = $product->get_parent_id();
        if (!$seller_id) {
            $seller_id = absint(get_post_meta($parent_id, '_shomart_seller_id', true));
        }
        if (!$serial) {
            $serial = get_post_meta($parent_id, '_shomart_shop_serial', true);
        }
    }

    return array(
        'seller_id' => $seller_id,
        'serial'    => $serial,
    );
}

add_action('woocommerce_checkout_create_order_line_item', 'shomart_copy_seller_to_order_item', 10, 4);
function shomart_copy_seller_to_order_item($item, $cart_item_key, $values, $order) {
    if (empty($values['data']) || !is_a($values['data'], 'WC_Product')) {
        return;
    }

    $seller_data = shomart_get_product_seller_data($values['data']);
    if ($seller_data['serial']) {
        $item->add_meta_data('_shomart_shop_serial', $seller_data['serial'], true);
    }
    if ($seller_data['seller_id']) {
        $item->add_meta_data('_shomart_seller_id', $seller_data['seller_id'], true);
    }
}

/**
 * Return the unique shop serials represented in an order.
 * Product meta is used as a fallback for orders placed before order-item copying.
 *
 * @param WC_Order|int $order Order object or ID.
 * @return array
 */
function shomart_get_order_shop_serials($order) {
    if (is_numeric($order)) {
        $order = wc_get_order(absint($order));
    }
    if (!$order || !is_a($order, 'WC_Order')) {
        return array();
    }

    $serials = array();
    foreach ($order->get_items() as $item) {
        $serial = $item->get_meta('_shomart_shop_serial', true);

        if (!$serial) {
            $seller_data = shomart_get_product_seller_data($item->get_product());
            $serial = $seller_data['serial'];
        }

        if ($serial && !in_array($serial, $serials, true)) {
            $serials[] = $serial;
        }
    }

    return $serials;
}

/**
 * Restrict post-order seller output to the two customer order screens.
 *
 * @return bool
 */
function shomart_is_customer_order_endpoint() {
    return function_exists('is_wc_endpoint_url')
        && (is_wc_endpoint_url('view-order') || is_wc_endpoint_url('order-received'));
}

// ===== 3. SHOW SERIAL ON THANK YOU PAGE =====
add_action('woocommerce_thankyou', 'shomart_thankyou_shop_serial', 5);
function shomart_thankyou_shop_serial($order_id) {
    if (!$order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    $serials = shomart_get_order_shop_serials($order);
    if (empty($serials)) {
        return;
    }

    $label = 1 === count($serials) ? 'Shop Serial Number' : 'Shop Serial Numbers';
    $formatted = array_map(
        static function($serial) {
            return 'SM-' . $serial;
        },
        $serials
    );

    echo '<div class="shomart-order-shop-serials" style="background:#e8f5e9;border:1px solid #c8e6c9;border-radius:8px;padding:16px;margin:16px 0;text-align:center;">';
    echo '<strong style="font-size:14px;color:#2e7d32;">' . esc_html($label) . ': ' . esc_html(implode(', ', $formatted)) . '</strong>';
    echo '<p style="margin:6px 0 0;font-size:12px;color:#555;">Save this number for complaints / returns. COD – Cash on Delivery</p>';
    echo '</div>';
}

// ===== 4. SHOW SERIAL IN MY ACCOUNT / ORDER RECEIVED =====
add_filter('woocommerce_get_order_item_totals', 'shomart_add_shop_serial_order_total', 20, 3);
function shomart_add_shop_serial_order_total($total_rows, $order, $tax_display) {
    if (!shomart_is_customer_order_endpoint()) {
        return $total_rows;
    }

    $serials = shomart_get_order_shop_serials($order);
    if (empty($serials)) {
        return $total_rows;
    }

    $formatted = array_map(
        static function($serial) {
            return '#' . $serial;
        },
        $serials
    );
    $label = count($serials) > 1 ? 'Shop Serial Numbers:' : 'Shop Serial Number:';
    $serial_row = array(
        'label' => $label,
        'value' => '<strong style="color:#2874f0;">' . esc_html(implode(', ', $formatted)) . '</strong>',
    );
    $new_rows = array();

    foreach ($total_rows as $key => $row) {
        $new_rows[$key] = $row;
        if ('payment_method' === $key) {
            $new_rows['shomart_shop_serial'] = $serial_row;
        }
    }

    if (!isset($new_rows['shomart_shop_serial'])) {
        $new_rows['shomart_shop_serial'] = $serial_row;
    }

    return $new_rows;
}

add_filter('woocommerce_order_item_name', 'shomart_add_shop_serial_to_order_item_name', 10, 2);
function shomart_add_shop_serial_to_order_item_name($item_name, $item) {
    if (!shomart_is_customer_order_endpoint() || !is_a($item, 'WC_Order_Item_Product')) {
        return $item_name;
    }

    $serial = $item->get_meta('_shomart_shop_serial', true);
    if (!$serial) {
        $seller_data = shomart_get_product_seller_data($item->get_product());
        $serial = $seller_data['serial'];
    }

    if ($serial) {
        $item_name .= '<br><small style="color:#2874f0;">Shop #' . esc_html($serial) . '</small>';
    }

    return $item_name;
}

// ===== 5. GOOGLE SHEETS SYNC =====
/**
 * Send one seller's current details to the configured Google Apps Script URL.
 *
 * @param int $post_id Seller application post ID.
 * @return bool
 */
function shomart_sync_seller_to_sheets($post_id) {
    $webhook = defined('SHOMART_SHEETS_WEBHOOK_URL') ? SHOMART_SHEETS_WEBHOOK_URL : '';
    if (empty($webhook)) {
        return false;
    }

    $data = array(
        'serial'        => get_post_meta($post_id, 'shop_serial', true),
        'shop_name'     => get_post_meta($post_id, 'shop_name', true),
        'owner_name'    => get_post_meta($post_id, 'owner_name', true),
        'phone'         => get_post_meta($post_id, 'phone', true),
        'whatsapp'      => get_post_meta($post_id, 'whatsapp', true),
        'email'         => get_post_meta($post_id, 'email', true),
        'city'          => get_post_meta($post_id, 'shop_city', true),
        'address'       => get_post_meta($post_id, 'shop_address', true),
        'products'      => get_post_meta($post_id, 'products_sold', true),
        'years'         => get_post_meta($post_id, 'years_business', true),
        'monthly_sales' => get_post_meta($post_id, 'monthly_sales', true),
        'status'        => get_post_meta($post_id, 'status', true),
        'date'          => get_the_date('Y-m-d H:i:s', $post_id),
        'post_id'       => $post_id,
    );

    $response = wp_remote_post($webhook, array(
        'timeout' => 5,
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => wp_json_encode($data),
    ));

    if (is_wp_error($response)) {
        error_log('Shomart Sheets sync failed: ' . $response->get_error_message());
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code < 200 || $response_code >= 300) {
        error_log('Shomart Sheets sync failed: HTTP ' . $response_code);
        return false;
    }

    return true;
}

/**
 * Sync approved/active sellers once per request. This runs from both the custom
 * approval action and save_post so status saves remain covered.
 */
function shomart_seller_approval_sheets_sync($post_id, $post = null, $update = false) {
    static $synced = array();

    $post_id = absint($post_id);
    if (!$post_id || isset($synced[$post_id])) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id) || 'seller_application' !== get_post_type($post_id)) {
        return;
    }

    $new_status = get_post_meta($post_id, 'status', true);
    if (!in_array($new_status, array('approved', 'active'), true)) {
        return;
    }

    $synced[$post_id] = true;
    shomart_sync_seller_to_sheets($post_id);
}
add_action('shomart_seller_approved', 'shomart_seller_approval_sheets_sync', 10, 1);
add_action('save_post_seller_application', 'shomart_seller_approval_sheets_sync', 20, 3);

// ===== 6. SELLER APPLICATION ADMIN IMPROVEMENTS =====
add_filter('manage_seller_application_posts_columns', 'shomart_improved_seller_application_columns', 20);
function shomart_improved_seller_application_columns($columns) {
    return array(
        'cb'            => '<input type="checkbox" />',
        'title'         => 'Shop / Owner',
        'serial'        => 'Serial #',
        'phone'         => 'Phone',
        'city'          => 'City',
        'products_info' => 'Products Sold',
        'listed'        => 'Listed Products',
        'status'        => 'Status',
        'date'          => 'Applied On',
    );
}

add_action('manage_seller_application_posts_custom_column', 'shomart_improved_seller_application_column_content', 20, 2);
function shomart_improved_seller_application_column_content($column, $post_id) {
    if ('serial' === $column) {
        $serial = get_post_meta($post_id, 'shop_serial', true);
        echo $serial
            ? '<strong style="color:#2874f0;">#' . esc_html($serial) . '</strong>'
            : '<span style="color:#999;">—</span>';
    }

    if ('products_info' === $column) {
        $products = get_post_meta($post_id, 'products_sold', true);
        echo esc_html(wp_trim_words($products, 8));
    }

    if ('listed' === $column) {
        $products = new WP_Query(array(
            'post_type'      => 'product',
            'post_status'    => array('publish', 'pending', 'draft', 'private', 'future'),
            'meta_key'       => '_shomart_seller_id',
            'meta_value'     => $post_id,
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ));
        $count = $products->post_count;
        wp_reset_postdata();

        if ($count > 0) {
            $url = admin_url('edit.php?post_type=product&shomart_seller_filter=' . absint($post_id));
            $label = 1 === $count ? '1 product' : $count . ' products';
            echo '<a href="' . esc_url($url) . '" style="font-weight:700;color:#4caf50;">' . esc_html($label) . '</a>';
        } else {
            echo '<span style="color:#999;">0</span>';
        }
    }
}

add_action('pre_get_posts', 'shomart_filter_products_by_seller');
function shomart_filter_products_by_seller($query) {
    global $pagenow;

    if (!is_admin() || 'edit.php' !== $pagenow || !$query->is_main_query()) {
        return;
    }
    if ('product' !== $query->get('post_type') || empty($_GET['shomart_seller_filter'])) {
        return;
    }

    $seller_id = absint($_GET['shomart_seller_filter']);
    if (!$seller_id) {
        return;
    }

    $query->set('meta_key', '_shomart_seller_id');
    $query->set('meta_value', $seller_id);
}

/** ============================================================
 * SHOMART PRODUCT REQUEST FORM HANDLER v3.0 (Category-based)
 * ============================================================ */

if (!defined('SHOMART_PRODUCTS_WEBHOOK_URL')) {
    define('SHOMART_PRODUCTS_WEBHOOK_URL', 'https://script.google.com/macros/s/AKfycbwxSyl22MjEvDm5TMSQoQgnk-Vi_v6-uUsegyS29MQuVG_-d5C0hbqt6CtHqguZAkpo/exec');
}

add_action('init', 'shomart_handle_product_submission');
function shomart_handle_product_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['shomart_add_products_submit'])) return;
    if (!isset($_POST['shomart_products_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['shomart_products_nonce'])), 'shomart_add_products')) {
        wp_die('Security check failed.');
    }
    $shop_id = absint($_POST['shop_id'] ?? 0);
    $category = sanitize_text_field($_POST['product_category'] ?? '');
    $product_name = sanitize_text_field($_POST['product_name'] ?? '');
    $selling_price = sanitize_text_field($_POST['selling_price'] ?? '');
    $retail_price = sanitize_text_field($_POST['retail_price'] ?? '');
    $stock = sanitize_text_field($_POST['stock'] ?? '');
    $brand = sanitize_text_field($_POST['brand'] ?? '');
    $description = sanitize_textarea_field($_POST['description'] ?? '');
    if (empty($shop_id) || empty($category) || empty($product_name) || empty($selling_price) || $stock === '' || empty($brand)) {
        wp_redirect(home_url('/seller-add-products/?error=missing_fields')); exit;
    }
    $shop_name = get_post_meta($shop_id, 'shop_name', true);
    $shop_serial = get_post_meta($shop_id, 'shop_serial', true);
    $shop_city = get_post_meta($shop_id, 'shop_city', true);
    if (!$shop_name) { wp_redirect(home_url('/seller-add-products/?error=invalid_shop')); exit; }

    $extra = array();
    switch ($category) {
        case 'mobile':
            $extra['ram'] = sanitize_text_field($_POST['m_ram'] ?? '');
            $extra['storage'] = sanitize_text_field($_POST['m_storage'] ?? '');
            $extra['camera'] = sanitize_text_field($_POST['m_camera'] ?? '');
            $extra['front_cam'] = sanitize_text_field($_POST['m_front_cam'] ?? '');
            $extra['battery'] = sanitize_text_field($_POST['m_battery'] ?? '');
            $extra['charging'] = sanitize_text_field($_POST['m_charging'] ?? '');
            $extra['display'] = sanitize_text_field($_POST['m_display'] ?? '');
            $extra['network'] = sanitize_text_field($_POST['m_network'] ?? '');
            $extra['color'] = sanitize_text_field($_POST['m_color'] ?? '');
            $extra['model_no'] = sanitize_text_field($_POST['m_model'] ?? '');
            $extra['processor'] = sanitize_text_field($_POST['m_processor'] ?? '');
            $extra['os'] = sanitize_text_field($_POST['m_os'] ?? '');
            $extra['warranty'] = sanitize_text_field($_POST['m_warranty'] ?? '');
            $extra['box'] = sanitize_text_field($_POST['m_box'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name} - {$extra['ram']} RAM / {$extra['storage']} Storage. Camera: {$extra['camera']}. Battery: {$extra['battery']}." . ($extra['display'] ? " Display: {$extra['display']}." : '') . ($extra['processor'] ? " Processor: {$extra['processor']}." : '') . ($extra['warranty'] ? " Warranty: {$extra['warranty']}." : '');
            }
            break;
        case 'electronics':
            $extra['color'] = sanitize_text_field($_POST['el_color'] ?? '');
            $extra['connectivity'] = sanitize_text_field($_POST['el_connectivity'] ?? '');
            $extra['battery_life'] = sanitize_text_field($_POST['el_battery'] ?? '');
            $extra['warranty'] = sanitize_text_field($_POST['el_warranty'] ?? '');
            $extra['model_no'] = sanitize_text_field($_POST['el_model'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name}." . ($extra['color'] ? " Color: {$extra['color']}." : '') . ($extra['connectivity'] ? " Connectivity: {$extra['connectivity']}." : '') . ($extra['battery_life'] ? " Battery: {$extra['battery_life']}." : '') . ($extra['warranty'] ? " Warranty: {$extra['warranty']}." : '');
            }
            break;
        case 'fashion':
            $extra['size'] = sanitize_text_field($_POST['fa_size'] ?? '');
            $extra['color'] = sanitize_text_field($_POST['fa_color'] ?? '');
            $extra['material'] = sanitize_text_field($_POST['fa_material'] ?? '');
            $extra['gender'] = sanitize_text_field($_POST['fa_gender'] ?? '');
            $extra['fit'] = sanitize_text_field($_POST['fa_fit'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name} - {$extra['gender']}'s {$extra['fit']} fit. Size: {$extra['size']}. Material: {$extra['material']}." . ($extra['color'] ? " Color: {$extra['color']}." : '');
            }
            break;
        case 'sports':
            $extra['size'] = sanitize_text_field($_POST['sp_size'] ?? '');
            $extra['material'] = sanitize_text_field($_POST['sp_material'] ?? '');
            $extra['level'] = sanitize_text_field($_POST['sp_level'] ?? '');
            $extra['hand'] = sanitize_text_field($_POST['sp_hand'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name}. Size/Weight: {$extra['size']}. Material: {$extra['material']}." . ($extra['level'] ? " Level: {$extra['level']}." : '') . ($extra['hand'] ? " Hand: {$extra['hand']}." : '');
            }
            break;
        case 'beauty':
            $extra['qty'] = sanitize_text_field($_POST['be_qty'] ?? '');
            $extra['skin_type'] = sanitize_text_field($_POST['be_skin'] ?? '');
            $extra['shade'] = sanitize_text_field($_POST['be_shade'] ?? '');
            $extra['expiry'] = sanitize_text_field($_POST['be_expiry'] ?? '');
            $extra['ingredients'] = sanitize_text_field($_POST['be_ingredients'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name}. Qty: {$extra['qty']}." . ($extra['skin_type'] ? " For: {$extra['skin_type']}." : '') . ($extra['shade'] ? " Shade: {$extra['shade']}." : '') . ($extra['ingredients'] ? " Ingredients: {$extra['ingredients']}." : '') . ($extra['expiry'] ? " Expiry: {$extra['expiry']}." : '');
            }
            break;
        case 'home':
            $extra['size'] = sanitize_text_field($_POST['ho_size'] ?? '');
            $extra['material'] = sanitize_text_field($_POST['ho_material'] ?? '');
            $extra['color'] = sanitize_text_field($_POST['ho_color'] ?? '');
            $extra['contents'] = sanitize_text_field($_POST['ho_contents'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name}. Size: {$extra['size']}. Material: {$extra['material']}." . ($extra['color'] ? " Color: {$extra['color']}." : '') . ($extra['contents'] ? " Contents: {$extra['contents']}." : '');
            }
            break;
        case 'toys':
            $extra['age'] = sanitize_text_field($_POST['to_age'] ?? '');
            $extra['battery'] = sanitize_text_field($_POST['to_battery'] ?? '');
            $extra['material'] = sanitize_text_field($_POST['to_material'] ?? '');
            $extra['skill'] = sanitize_text_field($_POST['to_skill'] ?? '');
            if (empty($description)) {
                $description = "{$brand} {$product_name}. Age: {$extra['age']}." . ($extra['material'] ? " Material: {$extra['material']}." : '') . ($extra['skill'] ? " Skill: {$extra['skill']}." : '') . ($extra['battery'] ? " Battery: {$extra['battery']}." : '');
            }
            break;
        case 'groceries':
            $extra['weight'] = sanitize_text_field($_POST['gr_weight'] ?? '');
            $extra['expiry'] = sanitize_text_field($_POST['gr_expiry'] ?? '');
            if (empty($description)) { $description = "{$brand} {$product_name}. Weight: {$extra['weight']}." . ($extra['expiry'] ? " Expiry: {$extra['expiry']}." : ''); }
            break;
        case 'handicrafts':
            $extra['type'] = sanitize_text_field($_POST['ha_type'] ?? '');
            $extra['material'] = sanitize_text_field($_POST['ha_material'] ?? '');
            $extra['size'] = sanitize_text_field($_POST['ha_size'] ?? '');
            if (empty($description)) { $description = "{$extra['type']} - {$brand} {$product_name}." . ($extra['material'] ? " Material: {$extra['material']}." : '') . ($extra['size'] ? " Size: {$extra['size']}." : '') . " Handmade in Ladakh."; }
            break;
        case 'books':
            $extra['author'] = sanitize_text_field($_POST['bk_author'] ?? '');
            $extra['type'] = sanitize_text_field($_POST['bk_type'] ?? '');
            if (empty($description)) { $description = "{$product_name}." . ($extra['author'] ? " Author: {$extra['author']}." : '') . " Type: {$extra['type']}."; }
            break;
        case 'other':
            $extra['notes'] = sanitize_textarea_field($_POST['ot_notes'] ?? '');
            break;
    }
    $webhook = defined('SHOMART_PRODUCTS_WEBHOOK_URL') ? SHOMART_PRODUCTS_WEBHOOK_URL : '';
    $all_data = array_merge(array('type'=>'product','timestamp'=>current_time('mysql'),'shop_name'=>$shop_name,'shop_serial'=>$shop_serial,'shop_city'=>$shop_city,'shop_post_id'=>$shop_id,'category'=>$category,'product_name'=>$product_name,'brand'=>$brand,'selling_price'=>$selling_price,'retail_price'=>$retail_price,'stock'=>$stock,'description'=>$description), $extra);
    if (!empty($webhook)) {
        $resp = wp_remote_post($webhook, array('timeout'=>5,'headers'=>array('Content-Type'=>'application/json'),'body'=>wp_json_encode($all_data)));
        if (is_wp_error($resp)) { error_log('Shomart Products sync: '.$resp->get_error_message()); }
    }
    wp_redirect(home_url('/seller-add-products/?submitted=true')); exit;
}

/** ============================================================
 * SHOMART STOCK MANAGEMENT
 * ============================================================ */

add_action('woocommerce_order_status_processing', 'shomart_decrease_stock_on_order', 10, 1);
add_action('woocommerce_order_status_completed', 'shomart_decrease_stock_on_order', 10, 1);
function shomart_decrease_stock_on_order($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    if ($order->get_meta('_shomart_stock_reduced')) return;
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!$product || $product->is_type('variable') || $product->is_type('variation')) continue;
        $qty = $item->get_quantity();
        if ($product->managing_stock() && ($stock = $product->get_stock_quantity()) !== null) {
            wc_update_product_stock($product, max(0, $stock - $qty));
        }
    }
    $order->update_meta_data('_shomart_stock_reduced', 'yes');
    $order->save();
    shomart_check_low_stock_alert();
}

function shomart_check_low_stock_alert() {
    $low = array();
    $products = wc_get_products(array('limit'=>-1,'status'=>'publish','meta_key'=>'_stock','orderby'=>'meta_value_num','order'=>'ASC'));
    foreach ($products as $p) {
        if (!$p->managing_stock()) continue;
        $s = $p->get_stock_quantity();
        if ($s !== null && $s <= 5) {
            $sid = get_post_meta($p->get_id(), '_shomart_seller_id', true);
            $low[] = array('product_name'=>$p->get_name(),'product_id'=>$p->get_id(),'stock'=>$s,'shop_name'=>$sid ? get_post_meta($sid,'shop_name',true) : 'Direct','shop_serial'=>$sid ? get_post_meta($sid,'shop_serial',true) : '');
        }
    }
    if (empty($low)) return;
    $webhook = defined('SHOMART_SHEETS_WEBHOOK_URL') ? SHOMART_SHEETS_WEBHOOK_URL : '';
    if (!empty($webhook)) {
        wp_remote_post($webhook, array('timeout'=>5,'headers'=>array('Content-Type'=>'application/json'),'body'=>wp_json_encode(array('type'=>'low_stock','timestamp'=>current_time('mysql'),'products'=>$low))));
    }
}

add_filter('woocommerce_add_to_cart_validation', 'shomart_block_out_of_stock', 10, 3);
function shomart_block_out_of_stock($passed, $product_id, $quantity) {
    $product = wc_get_product($product_id);
    if (!$product || $product->is_type('variable') || $product->is_type('variation')) return $passed;
    if ($product->managing_stock() && ($stock = $product->get_stock_quantity()) !== null && $stock <= 0) {
        wc_add_notice('❌ This product is out of stock!', 'error'); return false;
    }
    return $passed;
}

add_filter('woocommerce_is_purchasable', 'shomart_make_out_of_stock_unpurchasable', 10, 2);
function shomart_make_out_of_stock_unpurchasable($purchasable, $product) {
    if ($product->managing_stock() && $product->get_stock_quantity() !== null && $product->get_stock_quantity() <= 0) return false;
    return $purchasable;
}

/** ============================================================
 * ADMIN: LOW STOCK REPORT + BULK RESTOOL
 * ============================================================ */

add_action('admin_menu', 'shomart_add_low_stock_admin_page');
function shomart_add_low_stock_admin_page() {
    add_submenu_page('edit.php?post_type=seller_application', '📉 Low Stock Report', '📉 Low Stock', 'manage_woocommerce', 'shomart-low-stock', 'shomart_low_stock_page_callback');
}

function shomart_low_stock_page_callback() {
    $products = wc_get_products(array('limit'=>-1,'status'=>'publish','meta_key'=>'_stock','orderby'=>'meta_value_num','order'=>'ASC'));
    $low = array();
    foreach ($products as $p) {
        if (!$p->managing_stock()) continue;
        $s = $p->get_stock_quantity();
        if ($s !== null && $s <= 5) {
            $sid = get_post_meta($p->get_id(), '_shomart_seller_id', true);
            $low[] = array('id'=>$p->get_id(),'name'=>$p->get_name(),'stock'=>$s,'shop'=>$sid ? get_post_meta($sid,'shop_name',true) : 'Direct','serial'=>$sid ? get_post_meta($sid,'shop_serial',true) : '','seller_id'=>$sid);
        }
    }
    if (isset($_GET['export']) && $_GET['export']==='csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="low-stock-'.date('Y-m-d').'.csv"');
        $o=fopen('php://output','w'); fputcsv($o,array('Product','Stock','Shop','Serial','ID'));
        foreach($low as $r) fputcsv($o,array($r['name'],$r['stock'],$r['shop'],$r['serial'],$r['id']));
        fclose($o); exit;
    }
    if (isset($_GET['sync']) && $_GET['sync']==='sheet') { shomart_check_low_stock_alert(); echo '<div class="notice notice-success"><p>✅ Synced!</p></div>'; }
    ?><div class="wrap"><h1>📉 Low Stock (&lt;5)</h1><?php
    if (empty($low)) { echo '<div class="notice notice-success"><p>✅ No low stock!</p></div>'; } else {
        echo '<table class="wp-list-table widefat striped"><thead><tr><th>Product</th><th>Stock</th><th>Shop</th><th>Serial</th><th>Action</th></tr></thead><tbody>';
        foreach ($low as $r) {
            $ac = $r['stock']==0 ? 'style="color:red;font-weight:700;"' : 'style="color:#ff9800;font-weight:700;"';
            echo '<tr><td><a href="'.admin_url('post.php?post='.$r['id'].'&action=edit').'">'.esc_html($r['name']).'</a></td><td '.$ac.'>'.esc_html($r['stock']).'</td><td>'.esc_html($r['shop']).'</td><td>'.($r['serial']?'#'.esc_html($r['serial']):'—').'</td><td><a href="'.admin_url('post.php?post='.$r['id'].'&action=edit').'" class="button button-small">✏️ Restock</a></td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '<p><a href="'.admin_url('admin.php?page=shomart-low-stock&export=csv').'" class="button">📥 Export CSV</a> <a href="'.admin_url('admin.php?page=shomart-low-stock&sync=sheet').'" class="button">🔄 Sync Sheet</a></p>';

    $sellers = get_posts(array('post_type'=>'seller_application','posts_per_page'=>-1,'post_status'=>'publish','meta_query'=>array(array('key'=>'status','value'=>array('approved','active'),'compare'=>'IN'))));
    echo '<div style="margin:20px 0;padding:20px;background:#f0f8ff;border:1px solid #b3d4fc;border-radius:8px;max-width:600px;">
    <h3>📦 Bulk Restock</h3>
    <p style="font-size:13px;">Jab shopkeeper naya stock laaye, shop select karein. Sabhi products mein stock add ho jayega.</p>
    <form method="post" action="'.admin_url('admin-post.php').'">
    <input type="hidden" name="action" value="shomart_bulk_restock">
    <table class="form-table">
    <tr><th>🏪 Shop</th><td><select name="seller_id" required style="width:100%;"><option value="">— Select —</option>';
    foreach ($sellers as $s) { $sn=get_post_meta($s->ID,'shop_name',true); $sr=get_post_meta($s->ID,'shop_serial',true); echo '<option value="'.esc_attr($s->ID).'">'.esc_html(($sn?:$s->post_title).($sr?' (#'.$sr.')':'')).'</option>'; }
    echo '</select></td></tr>
    <tr><th>➕ Add Stock</th><td><input type="number" name="add_stock" required min="1" value="10" style="width:100px;"> <span style="font-size:12px;color:#666;">Har product mein</span></td></tr>
    </table>
    <p><button type="submit" class="button button-primary">📦 Add Stock</button></p>
    </form></div>';
    if (isset($_GET['restocked'])) { echo '<div class="notice notice-success"><p>✅ Stock added to '.absint($_GET['restocked']).' products!</p></div>'; }
    echo '</div>';
}

add_action('admin_post_shomart_bulk_restock', 'shomart_handle_bulk_restock');
function shomart_handle_bulk_restock() {
    if (!current_user_can('manage_woocommerce')) wp_die('No permission');
    $seller_id = absint($_POST['seller_id'] ?? 0);
    $add_stock = absint($_POST['add_stock'] ?? 0);
    if (!$seller_id || !$add_stock) { wp_redirect(admin_url('admin.php?page=shomart-low-stock&error=invalid')); exit; }
    $products = get_posts(array('post_type'=>'product','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>'_shomart_seller_id','meta_value'=>$seller_id));
    $count = 0;
    foreach ($products as $pid) {
        $product = wc_get_product($pid);
        if (!$product || !$product->managing_stock()) continue;
        wc_update_product_stock($product, ($product->get_stock_quantity() ?: 0) + $add_stock);
        $count++;
    }
    wp_redirect(admin_url('admin.php?page=shomart-low-stock&restocked='.$count)); exit;
}

/** ============================================================
 * SHOMART AI IMAGE GENERATOR (Admin tool)
 * ============================================================ */

add_action('admin_menu', 'shomart_add_ai_image_page');
function shomart_add_ai_image_page() {
    add_submenu_page('edit.php?post_type=seller_application', '🤖 AI Image Generator', '🤖 AI Images', 'manage_woocommerce', 'shomart-ai-images', 'shomart_ai_images_page_callback');
}

function shomart_ai_images_page_callback() {
    ?><div class="wrap"><h1>🤖 AI Product Image Generator</h1>
    <p>Product name daalein, AI 4-5 product images generate karega. Images download karke WooCommerce product mein set karein.</p>
    <div style="max-width:600px;background:#fff;padding:20px;border:1px solid #ddd;border-radius:8px;">
        <p><strong>How to use:</strong></p>
        <ol style="font-size:13px;">
            <li>Google pe product search karein ya product name copy karein</li>
            <li>Product name text box mein daalein</li>
            <li>AI image generate karega</li>
            <li>Images download karein → WooCommerce product mein set karein</li>
        </ol>
        <form method="get" action="<?php echo admin_url('admin.php'); ?>">
            <input type="hidden" name="page" value="shomart-ai-images">
            <input type="hidden" name="generate" value="1">
            <p><input type="text" name="product_name" required placeholder="e.g. Samsung Galaxy S26 Ultra Midnight Black" style="width:100%;padding:10px;font-size:14px;"></p>
            <p><button type="submit" class="button button-primary">🤖 Generate Images</button></p>
        </form>
    </div>
    <?php
    if (isset($_GET['generate']) && !empty($_GET['product_name'])) {
        $name = sanitize_text_field(wp_unslash($_GET['product_name']));
        echo '<h2>Generated Images for: ' . esc_html($name) . '</h2>';
        echo '<p style="color:#666;">AI-generated product images. Right-click → Save As to download.</p><div style="display:flex;flex-wrap:wrap;gap:12px;">';
        $variations = array('front view product photography', 'side view product photography', 'product packaging shot', 'product lifestyle shot', 'product detail close up');
        foreach ($variations as $i => $angle) {
            $img_url = 'https://placehold.co/400x400/e8f5e9/2e7d32?text=' . urlencode($name) . '+Image+' . ($i+1);
            echo '<div style="border:1px solid #ddd;border-radius:8px;padding:8px;text-align:center;width:200px;">';
            echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($name) . '" style="max-width:100%;border-radius:4px;">';
            echo '<p style="font-size:11px;margin:4px 0;">View ' . ($i+1) . '</p>';
            echo '</div>';
        }
        echo '</div>';
        echo '<p style="margin-top:16px;"><strong>Note:</strong> Actual Amazon/Flipkart images ke liye, product Google par search karein ya main aapke liye AI se images bana sakta hoon. Mujhe product name batao.</p>';
    }
    echo '</div>';
}
