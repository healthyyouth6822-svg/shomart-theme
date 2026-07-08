<?php
/**
 * =========================================================
 *  Shomart — page.php
 * =========================================================
 *  Default template for WordPress pages.
 *  Used by: Cart, Checkout, My Account, Privacy Policy, etc.
 * =========================================================
 */

get_header();
?>

<main class="shomart-page-wrap">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <div class="shomart-page-content">
            <?php the_content(); ?>
        </div>
        <?php
    endwhile;
    ?>
</main>

<style>
.shomart-page-wrap {
    min-height: 60vh;
    padding: 0;
}

.shomart-page-content {
    padding: 0;
}

/* Hide default page title on WooCommerce pages (we have our own header) */
.shomart-page-content > h1.entry-title,
.shomart-page-content > .page-title {
    display: none;
}
</style>

<?php
get_footer();