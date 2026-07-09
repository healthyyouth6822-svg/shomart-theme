<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
        !wp_verify_nonce($_POST['seller_status_nonce'], 'save_seller_status')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (!isset($_POST['seller_status'])) return;

    $new_status = sanitize_text_field($_POST['seller_status']);
    $old_status = get_post_meta($post_id, 'status', true);

    update_post_meta($post_id, 'status', $new_status);

    // If approved and shop number not assigned yet
    if ($new_status === 'approved' && $old_status !== 'approved') {

        $existing_number = get_post_meta($post_id, 'shop_serial', true);

        if (!$existing_number) {
            $next_number = shomart_generate_shop_number();
            update_post_meta($post_id, 'shop_serial', $next_number);
        }

        shomart_send_seller_email($post_id, 'approved');
    }

    if ($new_status === 'rejected' && $old_status !== 'rejected') {
        shomart_send_seller_email($post_id, 'rejected');
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
        $query->the_post();
        $last_number = (int) get_post_meta(get_the_ID(), 'shop_serial', true);
        wp_reset_postdata();
        return str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
    }

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

add_action('init', function() {

<?php
/**
 * Seller Form Handler - FIXED
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
