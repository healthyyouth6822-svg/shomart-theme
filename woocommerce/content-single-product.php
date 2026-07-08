<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

global $product;

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('shomart-single-product', $product); ?>>

    <!-- ============ PRODUCT GALLERY (SWIPEABLE) ============ -->
    <div class="shomart-gallery-wrap">
        <?php
        $attachment_ids = $product->get_gallery_image_ids();
        $main_image_id  = $product->get_image_id();
        
        // Combine main image + gallery
        $all_images = array_merge(array($main_image_id), $attachment_ids);
        $all_images = array_filter($all_images);
        ?>
        
        <div class="shomart-gallery-slider" id="shomartGallery">
            <?php foreach ($all_images as $img_id) :
                $img_url = wp_get_attachment_image_url($img_id, 'large');
                if (!$img_url) continue;
            ?>
                <div class="gallery-slide">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Dots Indicator -->
        <div class="gallery-dots" id="galleryDots">
            <?php foreach ($all_images as $index => $img_id) : ?>
                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
        
        <!-- Rating Badge -->
        <?php if ($product->get_average_rating() > 0) : ?>
        <div class="gallery-rating">
            ⭐ <?php echo esc_html($product->get_average_rating()); ?>
            <span class="rating-count">| <?php echo esc_html($product->get_review_count()); ?> ratings</span>
        </div>
        <?php endif; ?>
    </div>

    <!-- ============ PRODUCT INFO ============ -->
    <div class="shomart-product-info">

        <!-- Title -->
        <h1 class="product-title"><?php the_title(); ?></h1>

        <!-- Price -->
        <div class="product-price">
            <?php echo $product->get_price_html(); ?>
        </div>

    </div>

    <!-- ============ VARIATIONS (FOR VARIABLE PRODUCTS) ============ -->
    <?php if ($product->is_type('variable')) : 
        $attributes = $product->get_variation_attributes();
        $available_variations = $product->get_available_variations();
    ?>
    
    <form class="variations_form cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo htmlspecialchars(wp_json_encode($available_variations)); ?>">
        
        <?php foreach ($attributes as $attribute_name => $options) :
            $attribute_label = wc_attribute_label($attribute_name);
            $is_color = (stripos($attribute_name, 'color') !== false || stripos($attribute_name, 'colour') !== false);
        ?>
        
        <div class="variation-section">
            
            <div class="variation-label">
                <span><?php echo esc_html($attribute_label); ?>:</span>
                <strong class="selected-value" id="selected-<?php echo esc_attr($attribute_name); ?>">Select <?php echo esc_html($attribute_label); ?></strong>
            </div>

            <?php if ($is_color) : ?>
                <!-- ========== COLOR VARIATIONS (Image Thumbnails) ========== -->
                <div class="color-variations">
                    <?php
                    foreach ($options as $option) {
                        // Find variation with this color
                        $variation_image = '';
                        $is_in_stock = false;
                        
                        foreach ($available_variations as $variation) {
                            if (isset($variation['attributes']['attribute_' . $attribute_name])) {
                                $var_attr = $variation['attributes']['attribute_' . $attribute_name];
                                if (strtolower($var_attr) === strtolower($option) || $var_attr === $option) {
                                    if (!empty($variation['image']['src'])) {
                                        $variation_image = $variation['image']['src'];
                                    }
                                    if ($variation['is_in_stock']) {
                                        $is_in_stock = true;
                                    }
                                    break;
                                }
                            }
                        }
                        
                        // Fallback to main product image
                        if (empty($variation_image)) {
                            $variation_image = wp_get_attachment_image_url($product->get_image_id(), 'thumbnail');
                        }
                        ?>
                        <div class="color-option <?php echo !$is_in_stock ? 'out-of-stock' : ''; ?>" 
                             data-attribute="<?php echo esc_attr($attribute_name); ?>" 
                             data-value="<?php echo esc_attr($option); ?>">
                            <div class="color-img-wrap">
                                <img src="<?php echo esc_url($variation_image); ?>" alt="<?php echo esc_attr($option); ?>">
                                <?php if (!$is_in_stock) : ?>
                                    <span class="oos-label">Out of stock</span>
                                <?php endif; ?>
                            </div>
                            <span class="color-name"><?php echo esc_html($option); ?></span>
                        </div>
                        <?php
                    }
                    ?>
                </div>

            <?php else : ?>
                <!-- ========== OTHER VARIATIONS (Size/Storage Cards) ========== -->
                <div class="size-variations">
                    <?php
                    foreach ($options as $option) {
                        // Check stock and get price
                        $is_in_stock = false;
                        $variation_price = '';
                        $regular_price = '';
                        $sale_price = '';
                        
                        foreach ($available_variations as $variation) {
                            if (isset($variation['attributes']['attribute_' . $attribute_name])) {
                                $var_attr = $variation['attributes']['attribute_' . $attribute_name];
                                if (strtolower($var_attr) === strtolower($option) || $var_attr === $option) {
                                    if ($variation['is_in_stock']) {
                                        $is_in_stock = true;
                                    }
                                    $regular_price = $variation['display_regular_price'];
                                    $sale_price = $variation['display_price'];
                                    break;
                                }
                            }
                        }
                        ?>
                        <div class="size-option <?php echo !$is_in_stock ? 'out-of-stock' : ''; ?>"
                             data-attribute="<?php echo esc_attr($attribute_name); ?>"
                             data-value="<?php echo esc_attr($option); ?>">
                            <div class="size-name"><?php echo esc_html($option); ?></div>
                            
                            <?php if ($is_in_stock && $regular_price > $sale_price) : ?>
                                <div class="size-discount">
                                    ↓<?php echo round((($regular_price - $sale_price) / $regular_price) * 100); ?>%
                                    <span class="size-old-price"><?php echo wc_price($regular_price); ?></span>
                                </div>
                                <div class="size-price"><?php echo wc_price($sale_price); ?></div>
                            <?php elseif ($is_in_stock) : ?>
                                <div class="size-price"><?php echo wc_price($sale_price); ?></div>
                            <?php else : ?>
                                <div class="size-oos">Out of stock</div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Hidden select for WooCommerce -->
            <select name="attribute_<?php echo esc_attr($attribute_name); ?>" 
                    id="<?php echo esc_attr($attribute_name); ?>" 
                    class="hidden-variation-select" 
                    style="display:none;">
                <option value="">Choose</option>
                <?php foreach ($options as $option) : ?>
                    <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                <?php endforeach; ?>
            </select>

        </div>
        <?php endforeach; ?>

        <input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>">
        <input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>">
        <input type="hidden" name="variation_id" class="variation_id" value="0">
        <input type="hidden" name="quantity" value="1">

        <button type="submit" class="single_add_to_cart_button button alt" style="display:none;">Add to cart</button>
    </form>

    <?php else :
        // Simple product - hidden form
        ?>
        <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
            <input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="single_add_to_cart_button button alt" style="display:none;">Add to cart</button>
        </form>
    <?php endif; ?>

    <!-- ============ PRODUCT DETAILS DROPDOWN ============ -->
<?php if ($product->get_short_description() || $product->get_description()) : ?>
<div class="product-details-dropdown">
    <button type="button" class="details-toggle" onclick="toggleProductDetails()">
        <span>📋 Product Details</span>
        <span class="toggle-arrow" id="toggleArrow">▼</span>
    </button>
    <div class="details-content" id="detailsContent" style="display:none;">
        <?php
        if ($product->get_short_description()) {
            echo wpautop($product->get_short_description());
        }
        if ($product->get_description()) {
            echo wpautop($product->get_description());
        }
        
        // Product attributes
        $attributes = $product->get_attributes();
        if (!empty($attributes)) :
        ?>
        <div class="product-attributes">
            <h4>Specifications</h4>
            <table>
                <?php foreach ($attributes as $attribute) :
                    if ($attribute->get_visible() && !$attribute->is_taxonomy()) :
                ?>
                <tr>
                    <th><?php echo esc_html($attribute->get_name()); ?></th>
                    <td><?php echo esc_html(implode(', ', $attribute->get_options())); ?></td>
                </tr>
                <?php
                    endif;
                endforeach;
                ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- ============ RELATED PRODUCTS ============ -->
<?php
$related_products = wc_get_related_products($product->get_id(), 6);
if (!empty($related_products)) :
?>
<div class="related-products-section">
    <h3 class="related-title">🔥 You May Also Like</h3>
    <div class="related-products-grid">
        <?php
        foreach ($related_products as $related_id) :
            $related = wc_get_product($related_id);
            if (!$related) continue;
            $related_img = $related->get_image_id() ? wp_get_attachment_image_url($related->get_image_id(), 'medium') : wc_placeholder_img_src();
        ?>
        <a href="<?php echo esc_url($related->get_permalink()); ?>" class="related-product-card">
            <div class="related-img">
                <img src="<?php echo esc_url($related_img); ?>" alt="<?php echo esc_attr($related->get_name()); ?>">
            </div>
            <div class="related-name"><?php echo esc_html($related->get_name()); ?></div>
            <div class="related-price"><?php echo $related->get_price_html(); ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

</div>

<!-- ============ STYLES ============ -->
<style>
.shomart-single-product {
    background: #f1f2f4;
    padding-bottom: 80px;
}

/* ============ GALLERY ============ */
.shomart-gallery-wrap {
    position: relative;
    background: #fff;
    margin-bottom: 8px;
}

.shomart-gallery-slider {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-behavior: smooth;
}
.shomart-gallery-slider::-webkit-scrollbar {
    display: none;
}

.gallery-slide {
    flex: 0 0 100%;
    scroll-snap-align: start;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    aspect-ratio: 1;
}
.gallery-slide img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 20px;
}

/* Dots */
.gallery-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    padding: 10px;
    background: #fff;
}
.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    transition: all 0.3s;
}
.dot.active {
    background: #2874f0;
    width: 20px;
    border-radius: 4px;
}

/* Rating Badge */
.gallery-rating {
    position: absolute;
    bottom: 50px;
    left: 12px;
    background: #fff;
    color: #212121;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    z-index: 10;
}
.rating-count {
    color: #878787;
    font-weight: 500;
    margin-left: 4px;
}

/* ============ PRODUCT INFO ============ */
.shomart-product-info {
    background: #fff;
    padding: 14px;
    margin-bottom: 8px;
}
.product-title {
    font-size: 17px;
    font-weight: 600;
    color: #212121;
    line-height: 1.4;
    margin-bottom: 10px;
}
.product-price {
    font-size: 22px;
    font-weight: 700;
    color: #212121;
}
.product-price del {
    font-size: 14px;
    color: #878787;
    font-weight: 400;
    margin-left: 6px;
}
.product-price ins {
    text-decoration: none;
}

/* ============ VARIATIONS ============ */
.variation-section {
    background: #fff;
    padding: 14px;
    margin-bottom: 8px;
}

.variation-label {
    font-size: 14px;
    color: #878787;
    margin-bottom: 10px;
}
.variation-label .selected-value {
    color: #212121;
    font-weight: 700;
    margin-left: 4px;
}

/* ========== COLOR OPTIONS (Image Thumbnails) ========== */
.color-variations {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 6px;
    scrollbar-width: none;
}
.color-variations::-webkit-scrollbar {
    display: none;
}

.color-option {
    flex: 0 0 70px;
    cursor: pointer;
    transition: all 0.2s;
}

.color-img-wrap {
    position: relative;
    width: 70px;
    height: 70px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 4px;
    background: #fff;
    overflow: hidden;
    transition: all 0.2s;
}

.color-option.active .color-img-wrap {
    border-color: #2874f0;
    box-shadow: 0 0 0 1px #2874f0;
}

.color-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.color-option.out-of-stock .color-img-wrap {
    opacity: 0.5;
    cursor: not-allowed;
}
.color-option.out-of-stock .color-img-wrap::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.4);
}

.oos-label {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.6);
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    white-space: nowrap;
    z-index: 2;
}

.color-name {
    display: block;
    font-size: 11px;
    color: #333;
    text-align: center;
    margin-top: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ========== SIZE/STORAGE OPTIONS (Cards) ========== */
.size-variations {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 8px;
}

.size-option {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 8px;
    text-align: center;
    cursor: pointer;
    background: #fff;
    transition: all 0.2s;
}

.size-option.active {
    border-color: #2874f0;
    background: #f0f7ff;
    box-shadow: 0 0 0 1px #2874f0;
}

.size-option.out-of-stock {
    background: #f9f9f9;
    cursor: not-allowed;
    opacity: 0.7;
}

.size-name {
    font-size: 14px;
    font-weight: 700;
    color: #212121;
    margin-bottom: 4px;
}

.size-discount {
    font-size: 11px;
    color: #388e3c;
    font-weight: 600;
    margin-bottom: 2px;
}
.size-old-price {
    color: #878787;
    text-decoration: line-through;
    margin-left: 2px;
    font-weight: 400;
}

.size-price {
    font-size: 13px;
    font-weight: 700;
    color: #212121;
}

.size-oos {
    font-size: 11px;
    color: #e53935;
    font-weight: 600;
    margin-top: 4px;
}

/* ============ DESCRIPTION ============ */
.product-description-wrap {
    background: #fff;
    padding: 14px;
    margin-bottom: 8px;
}
.desc-title {
    font-size: 15px;
    font-weight: 700;
    color: #212121;
    margin-bottom: 10px;
}
.product-description {
    font-size: 13px;
    color: #333;
    line-height: 1.6;
}
.product-description h2,
.product-description h3 {
    font-size: 14px;
    font-weight: 700;
    margin: 10px 0 6px;
}
.product-description p {
    margin-bottom: 8px;
}
.product-description ul {
    padding-left: 18px;
    list-style: disc;
}
/* ============ PRODUCT DETAILS DROPDOWN ============ */
.product-details-dropdown {
    background: #fff;
    margin-bottom: 8px;
}

.details-toggle {
    width: 100%;
    background: #fff;
    border: none;
    padding: 16px 14px;
    font-size: 15px;
    font-weight: 700;
    color: #212121;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    text-align: left;
    border-bottom: 1px solid #f1f2f4;
}

.toggle-arrow {
    font-size: 12px;
    color: #2874f0;
    transition: transform 0.3s;
}

.toggle-arrow.rotated {
    transform: rotate(180deg);
}

.details-content {
    padding: 14px;
    font-size: 13px;
    color: #333;
    line-height: 1.6;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.product-attributes {
    margin-top: 14px;
    border-top: 1px solid #f1f2f4;
    padding-top: 14px;
}

.product-attributes h4 {
    font-size: 14px;
    font-weight: 700;
    color: #2874f0;
    margin-bottom: 10px;
}

.product-attributes table {
    width: 100%;
    border-collapse: collapse;
}

.product-attributes th,
.product-attributes td {
    padding: 8px 10px;
    text-align: left;
    font-size: 12px;
    border-bottom: 1px solid #f1f2f4;
}

.product-attributes th {
    background: #f9f9f9;
    color: #878787;
    font-weight: 600;
    width: 40%;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.product-attributes td {
    color: #212121;
    font-weight: 500;
}

/* ============ RELATED PRODUCTS ============ */
.related-products-section {
    background: #fff;
    padding: 14px;
    margin-bottom: 8px;
}

.related-title {
    font-size: 16px;
    font-weight: 700;
    color: #212121;
    margin-bottom: 12px;
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.related-product-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.related-product-card:active {
    transform: scale(0.98);
}

.related-img {
    width: 100%;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.related-img img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    mix-blend-mode: multiply;
}

.related-name {
    font-size: 12px;
    font-weight: 500;
    color: #212121;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 32px;
}

.related-price {
    font-size: 14px;
    font-weight: 700;
    color: #212121;
}

.related-price del {
    font-size: 11px;
    color: #878787;
    font-weight: 400;
    margin-left: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ========== GALLERY SLIDER WITH DOTS ==========
    var gallery = document.getElementById('shomartGallery');
    var dots = document.querySelectorAll('#galleryDots .dot');
    
    if (gallery && dots.length > 0) {
        gallery.addEventListener('scroll', function() {
            var slideWidth = gallery.offsetWidth;
            var currentIndex = Math.round(gallery.scrollLeft / slideWidth);
            
            dots.forEach(function(d, i) {
                d.classList.toggle('active', i === currentIndex);
            });
        });
        
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                var index = parseInt(this.dataset.index);
                gallery.scrollTo({
                    left: index * gallery.offsetWidth,
                    behavior: 'smooth'
                });
            });
        });
    }

    // ========== HELPER: Update Variation ID ==========
    function updateVariationId() {
        var form = document.querySelector('.variations_form');
        if (!form) return;
        
        try {
            var variations = JSON.parse(form.getAttribute('data-product_variations'));
            
            // Get all selected attributes
            var selectedAttrs = {};
            var allSelected = true;
            
            form.querySelectorAll('select.hidden-variation-select').forEach(function(select) {
                var name = select.name; // e.g., "attribute_pa_color"
                var value = select.value;
                
                if (!value) {
                    allSelected = false;
                }
                selectedAttrs[name] = value;
            });
            
            if (!allSelected) {
                form.querySelector('.variation_id').value = '0';
                return;
            }
            
            // Find matching variation
            var matchedVariation = null;
            for (var i = 0; i < variations.length; i++) {
                var variation = variations[i];
                var isMatch = true;
                
                for (var attrName in selectedAttrs) {
                    var varValue = variation.attributes[attrName];
                    var selValue = selectedAttrs[attrName];
                    
                    // Empty in variation means "any" - matches anything
                    if (varValue !== '' && 
                        varValue.toLowerCase() !== selValue.toLowerCase() &&
                        varValue !== selValue) {
                        isMatch = false;
                        break;
                    }
                }
                
                if (isMatch) {
                    matchedVariation = variation;
                    break;
                }
            }
            
            if (matchedVariation) {
                form.querySelector('.variation_id').value = matchedVariation.variation_id;
                console.log('✅ Variation found:', matchedVariation.variation_id);
                
                // Update price display if needed
                if (matchedVariation.price_html) {
                    var priceEl = document.querySelector('.product-price');
                    if (priceEl) {
                        priceEl.innerHTML = matchedVariation.price_html;
                    }
                }
            } else {
                form.querySelector('.variation_id').value = '0';
                console.log('❌ No matching variation');
            }
            
        } catch(e) {
            console.error('Error updating variation:', e);
        }
    }

    // ========== COLOR VARIATIONS (with image swap!) ==========
    var colorOptions = document.querySelectorAll('.color-option:not(.out-of-stock)');
    var galleryContainer = document.getElementById('shomartGallery');
    var galleryDots = document.getElementById('galleryDots');
    
    colorOptions.forEach(function(opt) {
        opt.addEventListener('click', function() {
            var attribute = this.dataset.attribute;
            var value = this.dataset.value;
            var colorImage = this.querySelector('img');
            
            // Remove active from siblings
            this.parentNode.querySelectorAll('.color-option').forEach(function(o) {
                o.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update label
            var label = document.getElementById('selected-' + attribute);
            if (label) label.textContent = value;
            
            // Update hidden select
            var select = document.getElementById(attribute);
            if (select) {
                select.value = value;
            }
            
            // Update variation ID
            updateVariationId();
            
            // ========== CHANGE MAIN GALLERY IMAGE ==========
            if (galleryContainer && colorImage) {
                var newImageSrc = colorImage.src;
                
                // Try to get full size image from variations data
                var form = document.querySelector('.variations_form');
                if (form) {
                    try {
                        var variations = JSON.parse(form.getAttribute('data-product_variations'));
                        for (var i = 0; i < variations.length; i++) {
                            var varAttrs = variations[i].attributes;
                            var attrKey = 'attribute_' + attribute;
                            if (varAttrs[attrKey] && 
                                (varAttrs[attrKey].toLowerCase() === value.toLowerCase() || 
                                 varAttrs[attrKey] === value)) {
                                if (variations[i].image && variations[i].image.src) {
                                    newImageSrc = variations[i].image.src;
                                }
                                break;
                            }
                        }
                    } catch(e) {}
                }
                
                // Animate the image change
                galleryContainer.style.opacity = '0.3';
                galleryContainer.style.transition = 'opacity 0.2s';
                
                setTimeout(function() {
                    galleryContainer.innerHTML = '<div class="gallery-slide"><img src="' + newImageSrc + '" alt="' + value + '"></div>';
                    
                    if (galleryDots) {
                        galleryDots.innerHTML = '<span class="dot active" data-index="0"></span>';
                    }
                    
                    galleryContainer.style.opacity = '1';
                    galleryContainer.scrollLeft = 0;
                }, 200);
            }
        });
    });

    // ========== SIZE VARIATIONS ==========
    var sizeOptions = document.querySelectorAll('.size-option:not(.out-of-stock)');
    sizeOptions.forEach(function(opt) {
        opt.addEventListener('click', function() {
            var attribute = this.dataset.attribute;
            var value = this.dataset.value;
            
            this.parentNode.querySelectorAll('.size-option').forEach(function(o) {
                o.classList.remove('active');
            });
            this.classList.add('active');
            
            var label = document.getElementById('selected-' + attribute);
            if (label) label.textContent = value;
            
            var select = document.getElementById(attribute);
            if (select) {
                select.value = value;
            }
            
            // Update variation ID
            updateVariationId();
        });
    });

});

// Toggle product details dropdown (global function)
function toggleProductDetails() {
    var content = document.getElementById('detailsContent');
    var arrow = document.getElementById('toggleArrow');
    
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        arrow.classList.add('rotated');
    } else {
        content.style.display = 'none';
        arrow.classList.remove('rotated');
    }
}
</script>