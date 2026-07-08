<?php
/**
 * =========================================================
 *  Shomart — woocommerce.php
 * =========================================================
 *  This wraps WooCommerce pages (shop, cart, checkout, etc.)
 *  with your header and footer.
 *
 *  Without this file, WooCommerce would use its own layout
 *  and ignore your header/footer.
 * =========================================================
 */

get_header();
?>

<main>
    <?php
    // This single function loads whatever WooCommerce page you're on:
    // - Shop page → product grid
    // - Cart page → cart table
    // - Checkout → checkout form
    // - Product page → product details
    woocommerce_content();
    ?>
</main>

<?php
get_footer();
