<?php
/**
 * Footer + Bottom Nav (Oval/Pill) + Scroll to Top + Product Action Bar
 */

$home_url = esc_url(home_url('/'));
$shop_url = function_exists('wc_get_page_id') ? esc_url(get_permalink(wc_get_page_id('shop'))) : '#';
$cart_url = function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#';
$account_url = function_exists('wc_get_page_id') ? esc_url(get_permalink(wc_get_page_id('myaccount'))) : '#';
?>

</div><!-- .shomart-content -->


<!-- ============ SCROLL TO TOP ============ -->
<button class="scroll-top" id="shomart-scroll-top" aria-label="Scroll to top">&uarr;</button>


<!-- ============ PRODUCT ACTION BAR (single product only) ============ -->
<div class="product-action-bar" id="shomart-action-bar">
    <button class="action-btn action-btn-cart" id="shomart-add-cart">Add to Cart</button>
    <button class="action-btn action-btn-buy" id="shomart-buy-now">Buy Now</button>
</div>


<!-- ============ BOTTOM NAV (OVAL, GREY, SVG ICONS) ============ -->
<nav class="shomart-bottom-nav">
    <a href="<?php echo $home_url; ?>" class="nav-home <?php echo is_front_page() ? 'active' : ''; ?>" title="Home">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" />
        </svg>
    </a>
    <a href="<?php echo $shop_url; ?>"
        class="<?php echo (function_exists('is_shop') && is_shop()) ? 'active' : ''; ?>" title="Categories">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
        </svg>
    </a>
    <a href="<?php echo $cart_url; ?>"
        class="<?php echo (function_exists('is_cart') && is_cart()) ? 'active' : ''; ?>" title="Cart">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
        </svg>
    </a>
    <a href="<?php echo $account_url; ?>"
        class="<?php echo (function_exists('is_account_page') && is_account_page()) ? 'active' : ''; ?>"
        title="Account">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
        </svg>
    </a>
</nav>


<?php wp_footer(); ?>


<!-- ============ ALL JAVASCRIPT INLINE ============ -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== 2. SCROLL TO TOP BUTTON =====
    var scrollBtn = document.getElementById('shomart-scroll-top');
    if (scrollBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        });
        scrollBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===== 3. CATEGORIES POPUP =====
    var catBtn = document.getElementById('shomart-cat-btn');
    var catOverlay = document.getElementById('cat-popup-overlay');
    var catClose = document.getElementById('cat-popup-close');

    if (catBtn && catOverlay) {
        catBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            catOverlay.classList.add('show');
            document.body.classList.add('cat-popup-open');
        });
        
        if (catClose) {
            catClose.addEventListener('click', function (e) {
                e.stopPropagation();
                catOverlay.classList.remove('show');
                document.body.classList.remove('cat-popup-open');
            });
        }
        
        catOverlay.addEventListener('click', function (e) {
            if (e.target === catOverlay) {
                catOverlay.classList.remove('show');
                document.body.classList.remove('cat-popup-open');
            }
        });
        
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && catOverlay.classList.contains('show')) {
                catOverlay.classList.remove('show');
                document.body.classList.remove('cat-popup-open');
            }
        });
    }

    // ===== 4. DYNAMIC CATEGORY TABS =====
    var catItems = document.querySelectorAll('.cat-item');
    if (catItems.length > 0) {
        catItems.forEach(function (item) {
            item.addEventListener('click', function () {
                var targetId = item.getAttribute('data-target');
                catItems.forEach(function (c) { c.classList.remove('active'); });
                item.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(function (tab) { tab.classList.remove('active'); });
                var target = document.getElementById(targetId);
                if (target) { target.classList.add('active'); }
            });
        });
    }

    // ===== 5. HIDE CATEGORY IMAGES ON SCROLL =====
    var catBar = document.querySelector('.cat-bar-wrapper');
    if (catBar) {
        var lastScrollY = 0;
        var scrollTimeout;
        var SCROLL_DOWN_THRESHOLD = 150;
        var SCROLL_UP_THRESHOLD = 80;

        window.addEventListener('scroll', function () {
            if (scrollTimeout) return;

            scrollTimeout = setTimeout(function () {
                var currentScroll = window.scrollY;

                if (currentScroll > SCROLL_DOWN_THRESHOLD && !catBar.classList.contains('scrolled')) {
                    catBar.classList.add('scrolled');
                }
                else if (currentScroll < SCROLL_UP_THRESHOLD && catBar.classList.contains('scrolled')) {
                    catBar.classList.remove('scrolled');
                }

                lastScrollY = currentScroll;
                scrollTimeout = null;
            }, 50);
        }, { passive: true });
    }

    // ===== 6. ADD TO CART (Fly Animation + Submit) =====
    var addToCartBtn = document.getElementById('shomart-add-cart');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            
            var hiddenForm = document.querySelector('form.cart');
            if (!hiddenForm) {
                console.log('No cart form found on page');
                return;
            }
            
            var isVariable = hiddenForm.classList.contains('variations_form');
            
            if (isVariable) {
                var selects = hiddenForm.querySelectorAll('select.hidden-variation-select');
                
                if (selects.length > 0) {
                    var allSelected = true;
                    var unselectedAttr = '';
                    
                    selects.forEach(function(select) {
                        if (!select.value) {
                            allSelected = false;
                            unselectedAttr = select.name.replace('attribute_', '').replace('pa_', '');
                        }
                    });
                    
                    if (!allSelected) {
                        alert('⚠️ Please select ' + unselectedAttr + ' before adding to cart!');
                        var variations = document.querySelector('.variation-section');
                        if (variations) {
                            variations.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return;
                    }
                }
            }
            
            // Fly animation
            var mainImg = document.querySelector('.shomart-gallery-slider img') || document.querySelector('.woocommerce-product-gallery__image img');
            if (mainImg) {
                var flyImg = mainImg.cloneNode(true);
                flyImg.classList.add('fly-img');
                flyImg.style.width = '60px';
                flyImg.style.height = '60px';
                var imgRect = mainImg.getBoundingClientRect();
                flyImg.style.left = imgRect.left + 'px';
                flyImg.style.top = imgRect.top + 'px';
                document.body.appendChild(flyImg);
                var cartIcon = document.querySelector('.cart-icon-link');
                var cartRect = cartIcon ? cartIcon.getBoundingClientRect() : null;
                if (cartRect) {
                    flyImg.style.transition = 'all 0.8s cubic-bezier(0.5, -0.5, 1, 1)';
                    requestAnimationFrame(function () {
                        flyImg.style.left = cartRect.left + 'px';
                        flyImg.style.top = cartRect.top + 'px';
                        flyImg.style.width = '20px';
                        flyImg.style.height = '20px';
                        flyImg.style.opacity = '0.3';
                    });
                    setTimeout(function () { flyImg.remove(); }, 800);
                }
            }
            
            // Submit form
            var submitBtn = hiddenForm.querySelector('button[type="submit"]');
            if (submitBtn) { 
                submitBtn.click(); 
            } else {
                hiddenForm.submit();
            }
        });
    }
// ===== BUY NOW BUTTON (FINAL VERSION) =====
var buyNowBtn = document.getElementById('shomart-buy-now');

if (buyNowBtn) {
    buyNowBtn.addEventListener('click', function (e) {
        e.preventDefault();

        var hiddenForm = document.querySelector('form.cart');
        if (!hiddenForm) return;

        var productId = hiddenForm.querySelector('input[name="add-to-cart"]').value;
        var variationField = hiddenForm.querySelector('input[name="variation_id"]');
        var variationId = variationField ? variationField.value : '';

        // If variable product, ensure variation selected
        if (hiddenForm.classList.contains('variations_form')) {
            if (!variationId || variationId === '0') {
                alert('⚠️ Please select all options before buying!');
                return;
            }
        }

        // Redirect with buynow parameter
        var url = '<?php echo home_url(); ?>/?add-to-cart=' + productId + '&buynow=1';

        if (variationId && variationId !== '0') {
            url += '&variation_id=' + variationId;
        }

        window.location.href = url;
    });
}
    

}); // ← THIS CLOSING BRACKET WAS MISSING!
</script>

</body>
</html>