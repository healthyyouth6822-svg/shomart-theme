<?php
/**
 * The Template for displaying all single products
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

get_header('shop');
?>

<?php
while (have_posts()) :
    the_post();
    wc_get_template_part('content', 'single-product');
endwhile;
?>

<?php get_footer('shop'); ?>