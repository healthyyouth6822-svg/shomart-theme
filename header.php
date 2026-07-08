<?php
/**
 * Global Header
 * - Home page: logo image + no back button
 * - Other pages: home button + back button
 * - Working search bar
 * - Cart icon with count
 * - Location bar
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php wp_head(); ?>
</head>

<?php
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
$account_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'myaccount' ) ) : '#';
$edit_addr   = function_exists( 'wc_get_endpoint_url' ) ? wc_get_endpoint_url( 'edit-address', '', $account_url ) : '#';
$cart_count  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

$delivery_location = shomart_get_delivery_location();

$logo_url = '';
if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
    $logo_url = wp_get_attachment_url( get_theme_mod( 'custom_logo' ) );
}
if ( ! $logo_url ) {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/logo.png';
}
?>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>


<!-- ============ STICKY HEADER ============ -->
<div class="shomart-top-header">
    <div class="header-top-row">

       

        <!-- Logo (home page) / Home Icon (other pages) -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-logo-area">
            <img class="image-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="Shomart">
            <span class="home-btn">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
        </a>

        <!-- Search Bar -->
        <div class="search-wrap">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" placeholder="&#128269; Search products" value="<?php echo esc_attr( get_search_query() ); ?>">
                <input type="hidden" name="post_type" value="product">
            </form>
        </div>

        <!-- Cart Icon -->
        <a href="<?php echo esc_url( $cart_url ); ?>" class="cart-icon-link">
            &#128722;
            <?php if ( $cart_count > 0 ) : ?>
                <span class="cart-count"><?php echo esc_html( $cart_count ); ?></span>
            <?php endif; ?>
        </a>

    </div>

    <!-- Location Bar -->
    <div class="location-bar">
        <a href="<?php echo esc_url( $edit_addr ); ?>">
            <span class="pin">&#128205;</span>
            <?php if ( ! empty( $delivery_location ) ) : ?>
                <span>Deliver to <strong><?php echo esc_html( $delivery_location ); ?></strong></span>
            <?php else : ?>
                <span>Select delivery location</span>
            <?php endif; ?>
            <span>&rsaquo;</span>
        </a>
    </div>

</div>


<!-- ============ CONTENT AREA ============ -->
<div class="shomart-content">
