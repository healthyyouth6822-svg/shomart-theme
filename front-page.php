<?php
/**
 * Home Page
 * - Uses YOUR WooCommerce categories (with images you uploaded)
 * - "For You" is ALWAYS first and active by default
 * - Dynamic tabs with working product links
 * - NO duplicate category titles
 */
get_header();

// Get all TOP-LEVEL product categories
$cats = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'parent'     => 0,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

// Force "For You" to be first
if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
    $foryou_key = null;

    foreach ( $cats as $key => $cat ) {
        $slug = strtolower( $cat->slug );
        $name = strtolower( $cat->name );

        if ( strpos( $slug, 'you' ) !== false || strpos( $name, 'for you' ) !== false || strpos( $name, 'foryou' ) !== false ) {
            $foryou_key = $key;
            break;
        }
    }

    if ( $foryou_key !== null ) {
        $foryou_cat = $cats[ $foryou_key ];
        unset( $cats[ $foryou_key ] );
        array_unshift( $cats, $foryou_cat );
    }
}
?>

<!-- ============ CATEGORY SCROLL BAR ============ -->
<div class="cat-bar-wrapper" id="shomart-cat-bar">
    <div class="cat-scroll-bar">

        <?php
        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
            $i = 0;
            foreach ( $cats as $cat ) {
                $thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                $img_url  = $thumb_id ? wp_get_attachment_url( $thumb_id ) : '';
                $is_first = ( $i === 0 );
                ?>
                <div class="cat-item <?php echo $is_first ? 'active' : ''; ?>" data-target="tab-cat-<?php echo esc_attr( $cat->term_id ); ?>">
                    <?php if ( $img_url ) : ?>
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>">
                    <?php else : ?>
                        <div class="cat-img-placeholder"><?php echo esc_html( mb_substr( $cat->name, 0, 1 ) ); ?></div>
                    <?php endif; ?>
                    <span class="cat-label"><?php echo esc_html( $cat->name ); ?></span>
                </div>
                <?php
                $i++;
            }
        }
        ?>

    </div>
</div>


<!-- ============ CATEGORY TABS ============ -->
<?php
if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
    $i = 0;
    foreach ( $cats as $cat ) {
        $is_first = ( $i === 0 );
        ?>
        <div class="tab-content <?php echo $is_first ? 'active' : ''; ?>" id="tab-cat-<?php echo esc_attr( $cat->term_id ); ?>">

            <?php
            // Subcategories (3-column grid)
            $subcats = get_terms( array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'parent'     => $cat->term_id,
            ) );

            if ( ! empty( $subcats ) && ! is_wp_error( $subcats ) ) {
                echo '<div class="subcat-grid">';
                foreach ( $subcats as $subcat ) {
                    $thumb_id = get_term_meta( $subcat->term_id, 'thumbnail_id', true );
                    $img_url  = $thumb_id ? wp_get_attachment_url( $thumb_id ) : '';
                    $link     = get_term_link( $subcat );
                    ?>
                    <a href="<?php echo esc_url( $link ); ?>" class="subcat-card">
                        <?php if ( $img_url ) : ?>
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $subcat->name ); ?>">
                        <?php else : ?>
                            <div class="subcat-img" style="display:flex;align-items:center;justify-content:center;background:var(--grey-bg);aspect-ratio:1;border-radius:8px;">
                                <?php echo esc_html( mb_substr( $subcat->name, 0, 1 ) ); ?>
                            </div>
                        <?php endif; ?>
                        <div class="subcat-name"><?php echo esc_html( $subcat->name ); ?></div>
                    </a>
                    <?php
                }
                echo '</div>';
            }
            ?>

            <!-- Products (NO title heading - removed duplicate) -->
            <div class="container">
                <?php echo do_shortcode( '[product_category category="' . $cat->slug . '" per_page="8" columns="2"]' ); ?>
            </div>

        </div>
        <?php
        $i++;
    }
}
?>


<!-- ============ BEST SELLERS SECTION ============ -->
<h2 class="section-title best-sellers-title">Most Ordered Items</h2>
<div class="container">
    <?php echo do_shortcode( '[best_selling_products per_page="8" columns="2"]' ); ?>
</div>


<!-- ============ TOP DEALS BANNER ============ -->
<div class="container" style="margin-top: 12px;">
    <div style="background: linear-gradient(135deg, #2874f0, #1a5dc9); color: #fff; padding: 20px; border-radius: 10px; text-align: center;">
        <h3 style="font-size: 16px; font-weight: 700;">Top Deals</h3>
        <p style="font-size: 12px; opacity: 0.9; margin-top: 4px;">Up to 80% off on selected items</p>
    </div>
</div>

<?php
get_footer();
