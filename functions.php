<?php
/** Puppy Market theme functions. */

require_once get_template_directory() . '/inc/content-settings.php';

function puppy_market_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 120,
        'width'       => 420,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    register_nav_menus(array(
        'primary'      => __('Header Category Menu', 'puppy-market'),
        'footer_shop'  => __('Footer Shop Menu', 'puppy-market'),
        'footer_help'  => __('Footer Help Menu', 'puppy-market'),
        'footer_about' => __('Footer About Menu', 'puppy-market'),
    ));
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'puppy_market_setup');

/** Mark primary-menu branches so two-level dropdowns do not inherit mega-menu styling. */
function puppy_market_primary_menu_depth_classes($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') return $items;

    $children_by_parent = array();
    $items_by_id = array();

    foreach ($items as $item) {
        $item_id = absint($item->ID);
        $parent_id = absint($item->menu_item_parent);
        $items_by_id[$item_id] = $item;
        if ($parent_id) $children_by_parent[$parent_id][] = $item_id;
    }

    foreach ($items as $item) {
        if (absint($item->menu_item_parent) !== 0) continue;

        $item_id = absint($item->ID);
        $second_level_ids = isset($children_by_parent[$item_id])
            ? $children_by_parent[$item_id]
            : array();
        if (empty($second_level_ids)) continue;

        $has_third_level = false;
        foreach ($second_level_ids as $second_level_id) {
            if (!empty($children_by_parent[$second_level_id])) {
                $has_third_level = true;
                break;
            }
        }

        $depth_class = $has_third_level
            ? 'puppy-menu-has-third-level'
            : 'puppy-menu-two-level-only';
        $items_by_id[$item_id]->classes[] = $depth_class;
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'puppy_market_primary_menu_depth_classes', 10, 2);

function puppy_market_image_sizes() {
    add_image_size('puppy-cart-400', 400, 400, true);
}
add_action('after_setup_theme', 'puppy_market_image_sizes');

function puppy_market_assets() {
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_version = file_exists($style_path) ? filemtime($style_path) : '0.5.0';
    wp_enqueue_style('puppy-market-style', get_stylesheet_uri(), array(), $style_version);

    $storefront_style_path = get_template_directory() . '/assets/storefront-v2.css';
    wp_enqueue_style(
        'puppy-market-storefront-v2',
        get_template_directory_uri() . '/assets/storefront-v2.css',
        array('puppy-market-style'),
        file_exists($storefront_style_path) ? filemtime($storefront_style_path) : $style_version
    );

    $cart_drawer_script_path = get_template_directory() . '/assets/cart-drawer.js';
    wp_enqueue_script(
        'puppy-market-cart-drawer',
        get_template_directory_uri() . '/assets/cart-drawer.js',
        array('jquery'),
        file_exists($cart_drawer_script_path) ? filemtime($cart_drawer_script_path) : $style_version,
        true
    );
    wp_localize_script('puppy-market-cart-drawer', 'puppyCartDrawerData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'action'  => 'puppy_market_cart_drawer',
    ));

    if (function_exists('is_product') && is_product()) {
        $pdp_script_path = get_template_directory() . '/assets/pdp.js';
        wp_enqueue_script(
            'puppy-market-pdp',
            get_template_directory_uri() . '/assets/pdp.js',
            array('jquery'),
            file_exists($pdp_script_path) ? filemtime($pdp_script_path) : $style_version,
            true
        );
    }

    $search_script_path = get_template_directory() . '/assets/search.js';
    wp_enqueue_script(
        'puppy-market-product-search',
        get_template_directory_uri() . '/assets/search.js',
        array(),
        file_exists($search_script_path) ? filemtime($search_script_path) : $style_version,
        true
    );
    wp_localize_script('puppy-market-product-search', 'puppySearchData', array(
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'action'   => 'puppy_market_search_suggestions',
        'minChars' => 2,
    ));

    // Catalog-only interactions. Do not load this script on the homepage,
    // product detail, account, cart, checkout, header or footer-only pages.
    if (function_exists('is_shop') && (is_shop() || is_product_category())) {
        $catalog_style_path = get_template_directory() . '/assets/catalog.css';
        wp_enqueue_style(
            'puppy-market-catalog',
            get_template_directory_uri() . '/assets/catalog.css',
            array('puppy-market-storefront-v2'),
            file_exists($catalog_style_path) ? filemtime($catalog_style_path) : $style_version
        );
        $catalog_script_path = get_template_directory() . '/assets/catalog.js';
        wp_enqueue_script(
            'puppy-market-catalog',
            get_template_directory_uri() . '/assets/catalog.js',
            array('jquery', 'wc-add-to-cart'),
            file_exists($catalog_script_path) ? filemtime($catalog_script_path) : $style_version,
            true
        );
    }

    if (function_exists('is_checkout') && is_checkout() && !is_wc_endpoint_url('order-received')) {
        $checkout_script_path = get_template_directory() . '/assets/checkout.js';
        wp_enqueue_script(
            'puppy-market-checkout',
            get_template_directory_uri() . '/assets/checkout.js',
            array(),
            file_exists($checkout_script_path) ? filemtime($checkout_script_path) : $style_version,
            true
        );

        $current_user = wp_get_current_user();
        wp_localize_script('puppy-market-checkout', 'ipetCheckout', array(
            'isLoggedIn' => is_user_logged_in(),
            'email'       => is_user_logged_in() ? $current_user->user_email : '',
            'accountUrl'  => puppy_market_account_url(),
            'loginUrl'    => wp_login_url(wc_get_checkout_url()),
            'privacyUrl'  => get_privacy_policy_url() ?: puppy_market_page_url('privacy-policy'),
            'termsUrl'    => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('terms') : puppy_market_page_url('terms'),
        ));
    }

    if (function_exists('is_account_page') && is_account_page()) {
        $account_style_path = get_template_directory() . '/assets/account.css';
        wp_enqueue_style(
            'puppy-market-account',
            get_template_directory_uri() . '/assets/account.css',
            array('puppy-market-storefront-v2'),
            file_exists($account_style_path) ? filemtime($account_style_path) : $style_version
        );

        if (is_user_logged_in()) {
            $account_script_path = get_template_directory() . '/assets/account.js';
            wp_enqueue_script(
                'puppy-market-account',
                get_template_directory_uri() . '/assets/account.js',
                array(),
                file_exists($account_script_path) ? filemtime($account_script_path) : $style_version,
                true
            );
        }
    }

    if (function_exists('is_cart') && is_cart()) {
        wp_register_script('puppy-market-cart-nav', '', array(), $style_version, true);
        wp_enqueue_script('puppy-market-cart-nav');
        wp_add_inline_script('puppy-market-cart-nav', '
            (function () {
                var rail = document.querySelector("[data-rec-rail]");
                var prev = document.querySelector(".puppy-cart-rec-prev");
                var next = document.querySelector(".puppy-cart-rec-next");
                if (rail && next && prev) {
                    var step = function () { return rail.querySelector(".product-card") ? rail.querySelector(".product-card").getBoundingClientRect().width + 20 : 320; };
                    var scrollBy = function (dir) { rail.scrollBy({ left: dir * step(), behavior: "smooth" }); };
                    next.addEventListener("click", function () { scrollBy(1); });
                    prev.addEventListener("click", function () { scrollBy(-1); });
                    var update = function () {
                        prev.disabled = rail.scrollLeft <= 2;
                        next.disabled = rail.scrollLeft >= rail.scrollWidth - rail.clientWidth - 2;
                    };
                    rail.addEventListener("scroll", update, { passive: true });
                    window.addEventListener("resize", update);
                    update();
                }

                function toast(msg) {
                    var t = document.getElementById("puppy-cart-toast");
                    if (!t) {
                        t = document.createElement("div");
                        t.id = "puppy-cart-toast";
                        document.body.appendChild(t);
                    }
                    t.textContent = msg;
                    t.classList.add("show");
                    clearTimeout(t._h);
                    t._h = setTimeout(function () { t.classList.remove("show"); }, 2400);
                }
                function swap(html) {
                    var doc = new DOMParser().parseFromString(html, "text/html");
                    var main = doc.querySelector(".puppy-cart-main");
                    var side = doc.querySelector(".puppy-cart-sidebar");
                    var empty = doc.querySelector(".puppy-cart-empty-page, .puppy-cart-empty, .cart-empty");
                    var curMain = document.querySelector(".puppy-cart-main");
                    var curSide = document.querySelector(".puppy-cart-sidebar");

                    if (empty || !main || !curMain) {
                        window.location.reload();
                        return false;
                    }

                    curMain.outerHTML = main.outerHTML;
                    if (side && curSide) {
                        curSide.outerHTML = side.outerHTML;
                    } else if (!side && curSide) {
                        curSide.remove();
                    }
                    return doc;
                }
                function showPromoResult(doc, fallbackMessage) {
                    var notice = doc.querySelector(".woocommerce-error, .woocommerce-message, .woocommerce-info");
                    var isError = !!(notice && notice.classList.contains("woocommerce-error"));
                    var message = notice ? notice.textContent.replace(/\\s+/g, " ").trim() : fallbackMessage;
                    var wrap = document.querySelector("[data-promo-wrap]");
                    var toggle = document.querySelector("[data-promo-toggle]");
                    var chevron = toggle ? toggle.querySelector(".puppy-cart-promo-chevron") : null;
                    var msg = document.querySelector("[data-promo-msg]");

                    if (wrap) wrap.classList.add("is-open");
                    if (toggle) toggle.setAttribute("aria-expanded", "true");
                    if (chevron) chevron.textContent = "−";
                    if (msg) {
                        msg.textContent = message || (isError ? "This promo code could not be applied." : "Promo code applied.");
                        msg.className = "puppy-cart-promo-msg " + (isError ? "is-error" : "is-success");
                    }
                    if (!isError) toast(message || "Promo code applied");
                }
                function submitCart(formData, doneMsg, showCouponFeedback) {
                    var form = document.querySelector("form.woocommerce-cart-form");
                    if (!form) return;
                    var loading = document.createElement("span");
                    loading.className = "puppy-cart-updating";
                    loading.textContent = "Updating…";
                    document.body.appendChild(loading);
                    fetch(form.getAttribute("action"), { method: "POST", body: formData, credentials: "same-origin" })
                        .then(function (r) { return r.text(); })
                        .then(function (html) {
                            var doc = swap(html);
                            if (!doc) return;
                            if (showCouponFeedback) {
                                showPromoResult(doc, doneMsg);
                            } else if (doneMsg) {
                                toast(doneMsg);
                            }
                        })
                        .catch(function () { toast("Something went wrong. Please try again."); })
                        .then(function () { if (loading && loading.parentNode) loading.parentNode.removeChild(loading); });
                }

                document.addEventListener("change", function (e) {
                    var input = e.target;
                    if (!input || !input.name) return;
                    if (input.name.indexOf("cart[") === 0 && input.name.indexOf("][qty]") !== -1) {
                        var form = document.querySelector("form.woocommerce-cart-form");
                        if (!form) return;
                        var fd = new FormData(form);
                        fd.set("update_cart", "Update cart");
                        fd.delete("apply_coupon");
                        submitCart(fd);
                    }
                });

                document.addEventListener("submit", function (e) {
                    var submitter = e.submitter;
                    if (!submitter || submitter.name !== "apply_coupon") return;
                    e.preventDefault();
                    var form = document.querySelector("form.woocommerce-cart-form");
                    if (!form) return;
                    var code = (document.getElementById("puppy_coupon_code") || {}).value || "";
                    var msg = document.querySelector("[data-promo-msg]");
                    if (!code.trim()) { if (msg) { msg.textContent = "Please enter a valid promo code."; msg.className = "puppy-cart-promo-msg is-error"; } return; }
                    var fd = new FormData(form);
                    fd.set("apply_coupon", "Apply coupon");
                    fd.delete("update_cart");
                    submitCart(fd, "Coupon applied", true);
                });

                document.addEventListener("click", function (e) {
                    var detailsToggle = e.target.closest ? e.target.closest("[data-cart-details-toggle]") : null;
                    if (detailsToggle) {
                        var detailsPanel = document.getElementById(detailsToggle.getAttribute("aria-controls"));
                        var detailsOpen = detailsToggle.getAttribute("aria-expanded") === "true";
                        var detailsIcon = detailsToggle.querySelector("[aria-hidden=\"true\"]");
                        detailsToggle.setAttribute("aria-expanded", detailsOpen ? "false" : "true");
                        if (detailsPanel) detailsPanel.hidden = detailsOpen;
                        if (detailsIcon) detailsIcon.textContent = detailsOpen ? "+" : "−";
                        return;
                    }

                    var wrap = e.target.closest ? e.target.closest("[data-promo-wrap]") : null;
                    if (wrap) {
                        var toggle = e.target.closest("[data-promo-toggle]");
                        if (toggle) {
                            var promoOpen = wrap.classList.toggle("is-open");
                            var promoIcon = toggle.querySelector(".puppy-cart-promo-chevron");
                            toggle.setAttribute("aria-expanded", promoOpen ? "true" : "false");
                            if (promoIcon) promoIcon.textContent = promoOpen ? "−" : "+";
                            return;
                        }
                    }
                    if (e.target.closest && e.target.closest(".puppy-cart-remove-button")) {
                        e.preventDefault();
                        var href = e.target.closest(".puppy-cart-remove-button").getAttribute("href");
                        if (!href) return;
                        var loading = document.createElement("span");
                        loading.className = "puppy-cart-updating";
                        loading.textContent = "Updating…";
                        document.body.appendChild(loading);
                        fetch(href, { method: "GET", credentials: "same-origin" })
                            .then(function (r) { return r.text(); })
                            .then(function (html) { if (swap(html)) toast("Removed from cart"); })
                            .catch(function () { toast("Something went wrong. Please try again."); })
                            .then(function () { if (loading && loading.parentNode) loading.parentNode.removeChild(loading); });
                    }
                });
            })();
        ');
        add_action('wp_footer', function () {
            if (function_exists('is_cart') && is_cart()) {
                echo '<style id="puppy-cart-toast-css">#puppy-cart-toast{position:fixed;left:50%;bottom:24px;transform:translateX(-50%) translateY(20px);z-index:200;padding:12px 20px;border-radius:8px;background:#17191b;color:#fff;font-size:14px;font-weight:600;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s}#puppy-cart-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}.puppy-cart-updating{position:fixed;top:0;left:0;right:0;z-index:200;height:3px;background:linear-gradient(90deg,#1757c8,#3ec26a);box-shadow:0 0 8px rgba(23,87,200,.5)}</style>';
            }
        });
    }
}
add_action('wp_enqueue_scripts', 'puppy_market_assets');

/**
 * Empty the cart from the cart-page header. Run before WooCommerce processes
 * the form's regular update_cart field so the clear action remains atomic.
 */
function puppy_market_handle_clear_cart_request() {
    if (empty($_SERVER['REQUEST_METHOD']) || 'POST' !== strtoupper(sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD']))) || empty($_POST['puppy_clear_cart'])) {
        return;
    }

    if (!function_exists('WC') || empty($_POST['puppy-clear-cart-nonce'])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST['puppy-clear-cart-nonce']));
    if (!wp_verify_nonce($nonce, 'puppy-clear-cart')) {
        return;
    }

    if (!WC()->cart && function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    if (WC()->cart) {
        WC()->cart->empty_cart();
    }

    wp_safe_redirect(wc_get_cart_url());
    exit;
}
add_action('wp_loaded', 'puppy_market_handle_clear_cart_request', 5);

/**
 * Keep WooCommerce extension hooks on the empty-cart screen while replacing
 * only its default notice with the theme's custom empty state.
 */
function puppy_market_remove_default_empty_cart_message() {
    if (function_exists('wc_empty_cart_message')) {
        remove_action('woocommerce_cart_is_empty', 'wc_empty_cart_message', 10);
    }
}
add_action('wp', 'puppy_market_remove_default_empty_cart_message', 5);

/**
 * Return the current cart state used by the site-wide add-to-cart drawer.
 */
function puppy_market_cart_drawer_data() {
    if (!function_exists('WC')) {
        wp_send_json_error(array('message' => 'WooCommerce is unavailable.'), 503);
    }

    if (!WC()->cart && function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    $cart = WC()->cart;
    if (!$cart) {
        wp_send_json_error(array('message' => 'The cart could not be loaded.'), 503);
    }

    $requested_product_id = isset($_GET['product_id']) ? absint(wp_unslash($_GET['product_id'])) : 0;
    $selected_item = null;
    $cart_items = array_reverse($cart->get_cart(), true);

    foreach ($cart_items as $cart_item) {
        $cart_product_id = !empty($cart_item['variation_id']) ? absint($cart_item['variation_id']) : absint($cart_item['product_id']);
        if (!$requested_product_id || $requested_product_id === $cart_product_id || $requested_product_id === absint($cart_item['product_id'])) {
            $selected_item = $cart_item;
            break;
        }
    }

    $item_data = null;
    if ($selected_item && !empty($selected_item['data'])) {
        $product = $selected_item['data'];
        $parent_product = !empty($selected_item['product_id']) ? wc_get_product($selected_item['product_id']) : null;
        $image_id = $product->get_image_id();
        if (!$image_id && $parent_product) $image_id = $parent_product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src('woocommerce_thumbnail');
        $quantity = max(1, absint($selected_item['quantity']));
        $product_page_id = !empty($selected_item['product_id']) ? absint($selected_item['product_id']) : $product->get_id();

        $item_data = array(
            'name'       => $product->get_name(),
            'image_url'  => $image_url,
            'url'        => get_permalink($product_page_id),
            'meta_html'  => sprintf(
                'Quantity: %1$d · %2$s',
                $quantity,
                $cart->get_product_subtotal($product, $quantity)
            ),
        );
    }

    $free_shipping_threshold = 49.0;
    $subtotal_value = (float) $cart->get_subtotal();
    $remaining = max(0, $free_shipping_threshold - $subtotal_value);
    $progress = $free_shipping_threshold > 0 ? min(100, max(0, ($subtotal_value / $free_shipping_threshold) * 100)) : 100;

    wp_send_json_success(array(
        'count'         => absint($cart->get_cart_contents_count()),
        'subtotal_html' => $cart->get_cart_subtotal(),
        'shipping'      => array(
            'qualified'     => $remaining <= 0,
            'remaining_html'=> wc_price($remaining),
            'progress'      => round($progress, 2),
        ),
        'item'          => $item_data,
    ));
}
add_action('wp_ajax_puppy_market_cart_drawer', 'puppy_market_cart_drawer_data');
add_action('wp_ajax_nopriv_puppy_market_cart_drawer', 'puppy_market_cart_drawer_data');

/**
 * The account area has a complete, page-scoped design system in account.css.
 * Keep WooCommerce's stock CSS away from account routes so its floats, tables,
 * form spacing and button rules cannot override that design.
 */
function puppy_market_account_disable_woocommerce_styles($styles) {
    if (function_exists('is_account_page') && is_account_page()) {
        return array();
    }

    return $styles;
}
add_filter('woocommerce_enqueue_styles', 'puppy_market_account_disable_woocommerce_styles', 20);

function puppy_market_account_dequeue_block_styles() {
    if (!function_exists('is_account_page') || !is_account_page()) {
        return;
    }

    $account_default_style_handles = array(
        'woocommerce-layout',
        'woocommerce-smallscreen',
        'woocommerce-general',
        'woocommerce-blocktheme',
        'wc-blocks-style',
        'wc-blocks-vendors-style',
        'wc-blocks-packages-style',
    );

    foreach ($account_default_style_handles as $style_handle) {
        wp_dequeue_style($style_handle);
    }
}
add_action('wp_enqueue_scripts', 'puppy_market_account_dequeue_block_styles', 100);

/** A small, consistent SVG icon set for the WooCommerce account center. */
function puppy_market_account_icon($name) {
    $icons = array(
        'dashboard'       => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10.5V20h13v-9.5"/><path d="M9.5 20v-6h5v6"/>',
        'orders'          => '<path d="M3.5 7.5 12 3l8.5 4.5L12 12 3.5 7.5Z"/><path d="M3.5 7.5V17L12 21l8.5-4V7.5"/><path d="M12 12v9"/>',
        'downloads'       => '<path d="M12 3v12"/><path d="m7.5 10.5 4.5 4.5 4.5-4.5"/><path d="M4 20h16"/>',
        'edit-address'    => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'payment-methods' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/><path d="M7 15h3"/>',
        'edit-account'    => '<circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0"/>',
        'customer-logout' => '<path d="M10 4H5v16h5"/><path d="M14 8l4 4-4 4"/><path d="M8 12h10"/>',
        'shop'            => '<path d="M4 9h16l-1 12H5L4 9Z"/><path d="M8 9a4 4 0 0 1 8 0"/>',
    );
    $paths = isset($icons[$name]) ? $icons[$name] : $icons['dashboard'];
    return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}

function puppy_market_woocommerce_labels() {
    if (!class_exists('WooCommerce')) return;
    add_filter('woocommerce_product_add_to_cart_text', function () { return 'Add to cart'; });
    add_filter('woocommerce_product_single_add_to_cart_text', function () { return 'Add to cart'; });
add_filter('woocommerce_return_to_shop_text', function () { return 'Continue shopping'; });
    add_filter('loop_shop_per_page', function () { return 12; });
    add_filter('gettext', function ($translated, $text) {
        if ($text === 'Proceed to checkout') return 'Proceed to Checkout';
        if ($text === 'Add to cart') return 'Add to Cart';
        if ($text === 'Remove item') return 'Remove';
        return $translated;
    }, 10, 2);
}
add_action('after_setup_theme', 'puppy_market_woocommerce_labels');

function puppy_market_product_kicker() {
    echo '<p class="ipet-product-kicker">iPet everyday essential</p>';
}
add_action('woocommerce_single_product_summary', 'puppy_market_product_kicker', 4);

function puppy_market_product_byline() {
    echo '<p class="ipet-product-byline">By <a href="' . esc_url(home_url('/')) . '">iPet</a></p>';
}
add_action('woocommerce_single_product_summary', 'puppy_market_product_byline', 6);

// Keep category/SKU metadata out of the compact product hero.
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
// Product reviews are disabled for this storefront, so do not render the default rating.
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);

/**
 * Delivery/stock estimate line under the price — Chewy always shows a concrete
 * "get it by <date>" or out-of-stock line next to the buy box, we had nothing here.
 */
function puppy_market_product_delivery_estimate() {
    global $product;
    if (!$product) return;
    echo '<p class="ipet-delivery-estimate">';
    if ($product->is_in_stock()) {
        $eta_timestamp = strtotime('+3 weekdays', current_time('timestamp'));
        $eta = $eta_timestamp ? date_i18n('D, M j', $eta_timestamp) : '';
        echo '<span class="ipet-delivery-icon" aria-hidden="true">🚚</span> In stock — get it by <strong>' . esc_html($eta) . '</strong>';
    } else {
        echo '<span class="ipet-delivery-icon" aria-hidden="true">⏳</span> Currently out of stock';
    }
    echo '</p>';
}
add_action('woocommerce_single_product_summary', 'puppy_market_product_delivery_estimate', 22);

/** Define the four editorial sections managed on each product. */
function puppy_market_product_detail_fields() {
    return array(
        'detail' => array(
            'label'       => 'Detail',
            'description' => 'Main product information. Leave blank to use the WooCommerce product description.',
            'meta_key'    => '_puppy_pdp_detail',
        ),
        'instruction' => array(
            'label'       => 'Instruction',
            'description' => 'Usage, feeding, care or assembly instructions.',
            'meta_key'    => '_puppy_pdp_instruction',
        ),
        'size' => array(
            'label'       => 'Size',
            'description' => 'Sizing, dimensions, weight, fit or capacity information.',
            'meta_key'    => '_puppy_pdp_size',
        ),
        'faq' => array(
            'label'       => 'FAQ',
            'description' => 'Common product questions and answers.',
            'meta_key'    => '_puppy_pdp_faq',
        ),
    );
}

/** Add rich-text editors to the WooCommerce product editing screen. */
function puppy_market_add_product_detail_meta_box() {
    add_meta_box(
        'puppy-market-product-detail-sections',
        __('Product detail sections', 'puppy-market'),
        'puppy_market_render_product_detail_meta_box',
        'product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes_product', 'puppy_market_add_product_detail_meta_box');

/** Render the four product-specific detail editors. */
function puppy_market_render_product_detail_meta_box($post) {
    wp_nonce_field('puppy_market_save_product_detail_sections', 'puppy_market_product_detail_sections_nonce');

    foreach (puppy_market_product_detail_fields() as $field_id => $field) {
        $value = (string) get_post_meta($post->ID, $field['meta_key'], true);
        echo '<div class="puppy-product-detail-editor" style="margin:0 0 24px">';
        echo '<p style="margin:0 0 8px"><strong>' . esc_html($field['label']) . '</strong><br><span class="description">' . esc_html($field['description']) . '</span></p>';

        wp_editor($value, 'puppypdp' . sanitize_key($field_id), array(
            'textarea_name' => 'puppy_pdp_' . sanitize_key($field_id),
            'textarea_rows' => 7,
            'media_buttons'  => false,
            'teeny'          => true,
            'quicktags'      => true,
        ));

        echo '</div>';
    }
}

/** Save product detail editors without affecting quick edits or autosaves. */
function puppy_market_save_product_detail_sections($post_id) {
    if (!isset($_POST['puppy_market_product_detail_sections_nonce'])) return;
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['puppy_market_product_detail_sections_nonce'])), 'puppy_market_save_product_detail_sections')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (puppy_market_product_detail_fields() as $field_id => $field) {
        $input_name = 'puppy_pdp_' . sanitize_key($field_id);
        if (!isset($_POST[$input_name])) continue;
        update_post_meta($post_id, $field['meta_key'], wp_kses_post(wp_unslash($_POST[$input_name])));
    }
}
add_action('save_post_product', 'puppy_market_save_product_detail_sections');

/** Render Detail, Instruction, Size and FAQ below the product hero. */
function puppy_market_product_about_item() {
    global $product;
    if (!$product) return;

    $detail = (string) $product->get_meta('_puppy_pdp_detail', true);
    if ($detail === '') $detail = $product->get_description() ?: $product->get_short_description();

    $instruction = (string) $product->get_meta('_puppy_pdp_instruction', true);
    if ($instruction === '') {
        $instruction = (string) ($product->get_meta('_feeding_instructions', true) ?: $product->get_meta('feeding_instructions', true));
    }

    $size = (string) $product->get_meta('_puppy_pdp_size', true);
    $size_rows = array();

    if ($size === '') {
        foreach ($product->get_attributes() as $attribute) {
            if (!$attribute->get_visible()) continue;
            $label = wc_attribute_label($attribute->get_name());
            if (!preg_match('/size|dimension|weight|width|height|length|capacity|volume/i', $label)) continue;

            $values = $attribute->is_taxonomy()
                ? wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'))
                : $attribute->get_options();
            if (!empty($values)) $size_rows[$label] = implode(', ', $values);
        }

        if ($product->has_weight()) $size_rows['Weight'] = wc_format_weight($product->get_weight());
        if ($product->has_dimensions()) $size_rows['Dimensions'] = wc_format_dimensions($product->get_dimensions(false));
    }

    $faq = (string) $product->get_meta('_puppy_pdp_faq', true);

    $detail_content = $detail !== ''
        ? wp_kses_post(wpautop($detail))
        : '<p>Product details will be added soon.</p>';

    $instruction_content = $instruction !== ''
        ? wp_kses_post(wpautop($instruction))
        : '<p>Please follow the directions supplied with the product. Contact customer care if you need product-specific guidance.</p>';

    if ($size !== '') {
        $size_content = wp_kses_post(wpautop($size));
    } elseif (!empty($size_rows)) {
        $size_content = '<dl class="ipet-spec-list">';
        foreach ($size_rows as $label => $value) {
            $size_content .= '<div><dt>' . esc_html($label) . '</dt><dd>' . esc_html($value) . '</dd></div>';
        }
        $size_content .= '</dl>';
    } else {
        $size_content = '<p>Size information will be added soon.</p>';
    }

    $faq_content = $faq !== ''
        ? wp_kses_post(wpautop($faq))
        : '<p>Have a question about this item? <a href="' . esc_url(puppy_market_page_url('contact')) . '">Contact customer care</a> before ordering.</p>';

    $sections = array(
        array('title' => 'Detail', 'content' => $detail_content, 'open' => true),
        array('title' => 'Instruction', 'content' => $instruction_content, 'open' => false),
        array('title' => 'Size', 'content' => $size_content, 'open' => false),
        array('title' => 'FAQ', 'content' => $faq_content, 'open' => false),
    );

    echo '<section class="ipet-about-item" aria-labelledby="ipet-about-title"><h2 id="ipet-about-title">About This Item</h2>';
    foreach ($sections as $index => $section) {
        $panel_id = 'ipet-product-detail-' . absint($index);
        echo '<div class="ipet-accordion' . ($section['open'] ? ' is-open' : '') . '"><button type="button" aria-expanded="' . ($section['open'] ? 'true' : 'false') . '" aria-controls="' . esc_attr($panel_id) . '"><strong>' . esc_html($section['title']) . '</strong><span aria-hidden="true">' . ($section['open'] ? '−' : '+') . '</span></button><div id="' . esc_attr($panel_id) . '" class="ipet-accordion-panel">' . $section['content'] . '</div></div>';
    }
    echo '</section>';
}
add_filter('woocommerce_product_tabs', function ($tabs) { return array(); }, 99);


/** The taxonomy actually used for brand filtering — a real product_brand taxonomy if a plugin registered one, product_tag otherwise. */
function puppy_market_brand_taxonomy() {
    return taxonomy_exists('product_brand') ? 'product_brand' : 'product_tag';
}

function puppy_market_category_icon($name) {
    if (strpos($name, 'dog') !== false || strpos($name, 'puppy') !== false) return '🐶';
    if (strpos($name, 'cat') !== false || strpos($name, 'kitten') !== false) return '🐱';
    if (strpos($name, 'toy') !== false) return '🧸';
    if (strpos($name, 'care') !== false || strpos($name, 'groom') !== false) return '🧴';
    if (strpos($name, 'food') !== false || strpos($name, 'treat') !== false) return '🥣';
    return '🐾';
}

/**
 * The shop page URL, optionally with a `catalog_view` filter appended
 * (e.g. puppy_market_catalog_url('new'), puppy_market_catalog_url('sale')).
 * Call with no argument for the plain shop URL — this is the single source
 * of truth for it; don't recompute wc_get_page_permalink('shop') elsewhere.
 */
function puppy_market_catalog_url($view = '') {
    $url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/');
    return $view ? add_query_arg('catalog_view', sanitize_key($view), $url) : $url;
}

/** The My Account page URL — single source of truth, don't recompute wc_get_page_permalink('myaccount') elsewhere. */
function puppy_market_account_url() {
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/');
}

/** Product IDs belonging to a category archive, including its descendants. */
function puppy_market_category_product_ids($category) {
    static $category_product_cache = array();

    if (!$category || empty($category->term_id)) return array();

    $category_id = (int) $category->term_id;
    if (isset($category_product_cache[$category_id])) {
        return $category_product_cache[$category_id];
    }

    $category_product_cache[$category_id] = array_map('absint', get_posts(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'fields' => 'ids',
        'posts_per_page' => -1,
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query' => array(array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => array($category_id),
            'include_children' => true,
        )),
    )));

    return $category_product_cache[$category_id];
}

/**
 * Return terms assigned to a known product set and replace WordPress's global
 * term count with the count inside that set.
 */
function puppy_market_contextual_filter_terms($taxonomy, $product_ids, $limit = 0) {
    if (!taxonomy_exists($taxonomy) || empty($product_ids)) return array();

    $term_rows = wp_get_object_terms($product_ids, $taxonomy, array(
        'fields' => 'all_with_object_id',
        'orderby' => 'none',
    ));
    if (is_wp_error($term_rows) || empty($term_rows)) return array();

    $terms = array();
    foreach ($term_rows as $term_row) {
        $term_id = (int) $term_row->term_id;
        if (!isset($terms[$term_id])) {
            $terms[$term_id] = clone $term_row;
            $terms[$term_id]->count = 0;
        }
        $terms[$term_id]->count++;
    }

    $terms = array_values($terms);
    usort($terms, function ($left, $right) {
        if ((int) $left->count === (int) $right->count) {
            return strnatcasecmp($left->name, $right->name);
        }
        return (int) $right->count - (int) $left->count;
    });

    return $limit > 0 ? array_slice($terms, 0, $limit) : $terms;
}

/** Return only global attributes actually assigned to products in this category. */
function puppy_market_category_filter_attributes($category) {
    if (!$category || empty($category->term_id) || !function_exists('wc_get_attribute_taxonomies')) return array();

    $product_ids = puppy_market_category_product_ids($category);
    if (empty($product_ids)) return array();

    $filters = array();
    foreach (wc_get_attribute_taxonomies() as $attribute) {
        $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
        $terms = puppy_market_contextual_filter_terms($taxonomy, $product_ids, 12);
        if (!empty($terms)) {
            $filters[] = array(
                'taxonomy' => $taxonomy,
                'label' => $attribute->attribute_label ?: wc_attribute_label($taxonomy),
                'terms' => $terms,
            );
        }
    }
    return $filters;
}

function puppy_market_catalog_query($query) {
    if (is_admin() || !$query->is_main_query() || !function_exists('is_shop') || !(is_shop() || is_product_category())) return;
    $view = isset($_GET['catalog_view']) ? sanitize_key(wp_unslash($_GET['catalog_view'])) : '';
    if ($view === 'new') {
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    } elseif ($view === 'sale' && function_exists('wc_get_product_ids_on_sale')) {
        $sale_ids = wc_get_product_ids_on_sale();
        $query->set('post__in', !empty($sale_ids) ? $sale_ids : array(0));
        $query->set('orderby', 'post__in');
    }
    $tax_query = (array) $query->get('tax_query');
    $current_category = is_product_category() ? get_queried_object() : null;
    $current_category_product_ids = $current_category && !empty($current_category->term_id)
        ? puppy_market_category_product_ids($current_category)
        : array();
    $selected_categories = isset($_GET['puppy_category']) ? array_filter(array_map('sanitize_title', (array) wp_unslash($_GET['puppy_category']))) : array();

    // A category archive already constrains the catalog to its current term.
    // Only direct children shown by the sidebar are valid additional filters;
    // sibling categories would otherwise be combined with the archive using AND
    // and always produce an empty result.
    if (!empty($selected_categories) && $current_category) {
        $allowed_category_slugs = !empty($current_category->term_id)
            ? get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'parent' => (int) $current_category->term_id,
                'fields' => 'slugs',
            ))
            : array();
        $allowed_category_slugs = is_wp_error($allowed_category_slugs) ? array() : $allowed_category_slugs;
        $selected_categories = array_values(array_intersect($selected_categories, $allowed_category_slugs));
    }

    if (!empty($selected_categories)) {
        $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $selected_categories, 'operator' => 'IN');
    }
    $selected_brands = isset($_GET['puppy_brand']) ? array_filter(array_map('sanitize_title', (array) wp_unslash($_GET['puppy_brand']))) : array();
    if (!empty($selected_brands)) {
        $brand_taxonomy = puppy_market_brand_taxonomy();

        if ($current_category) {
            $allowed_brand_terms = puppy_market_contextual_filter_terms($brand_taxonomy, $current_category_product_ids);
            $allowed_brand_slugs = wp_list_pluck($allowed_brand_terms, 'slug');
            $selected_brands = array_values(array_intersect($selected_brands, $allowed_brand_slugs));
        }

        if (!empty($selected_brands)) {
            $brand_terms = get_terms(array('taxonomy' => $brand_taxonomy, 'hide_empty' => false, 'slug' => $selected_brands));
            if (!is_wp_error($brand_terms) && !empty($brand_terms)) {
                $tax_query[] = array('taxonomy' => $brand_taxonomy, 'field' => 'slug', 'terms' => $selected_brands, 'operator' => 'IN');
            } elseif (!$current_category) {
                $query->set('s', str_replace('-', ' ', sanitize_title(reset($selected_brands))));
            }
        }
    }
    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $attribute) {
            $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
            $key = 'puppy_attr_' . $taxonomy;
            $selected_terms = isset($_GET[$key]) ? array_filter(array_map('sanitize_title', (array) wp_unslash($_GET[$key]))) : array();

            if (!empty($selected_terms) && $current_category && taxonomy_exists($taxonomy)) {
                $allowed_attribute_terms = puppy_market_contextual_filter_terms($taxonomy, $current_category_product_ids);
                $allowed_attribute_slugs = wp_list_pluck($allowed_attribute_terms, 'slug');
                $selected_terms = array_values(array_intersect($selected_terms, $allowed_attribute_slugs));
            }

            if (!empty($selected_terms) && taxonomy_exists($taxonomy)) {
                $tax_query[] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $selected_terms, 'operator' => 'IN');
            }
        }
    }
    if (count($tax_query) > 1) $tax_query['relation'] = 'AND';
    if (!empty($tax_query)) $query->set('tax_query', $tax_query);

    $meta_query = (array) $query->get('meta_query');
    $puppy_min_price = isset($_GET['puppy_min_price']) ? absint($_GET['puppy_min_price']) : 0;
    $puppy_max_price = isset($_GET['puppy_max_price']) ? absint($_GET['puppy_max_price']) : 0;
    if ($puppy_min_price > 0 || $puppy_max_price > 0) {
        $price_query = array('key' => '_price', 'type' => 'NUMERIC');
        if ($puppy_min_price > 0 && $puppy_max_price > 0) {
            $price_query['value'] = array($puppy_min_price, $puppy_max_price);
            $price_query['compare'] = 'BETWEEN';
        } elseif ($puppy_min_price > 0) {
            $price_query['value'] = $puppy_min_price;
            $price_query['compare'] = '>=';
        } else {
            $price_query['value'] = $puppy_max_price;
            $price_query['compare'] = '<=';
        }
        $meta_query[] = $price_query;
    }
    if (!empty($meta_query)) $query->set('meta_query', $meta_query);

    if (!empty($_GET['puppy_on_sale']) && function_exists('wc_get_product_ids_on_sale')) {
        $sale_ids = wc_get_product_ids_on_sale();
        $existing_in = (array) $query->get('post__in');
        $query->set('post__in', !empty($existing_in) ? array_intersect($existing_in, $sale_ids) : (!empty($sale_ids) ? $sale_ids : array(0)));
    }
}
add_action('pre_get_posts', 'puppy_market_catalog_query');

function puppy_market_hide_catalog_add_to_cart_notice($message) {
    return function_exists('is_shop') && (is_shop() || is_product_category()) ? '' : $message;
}
add_filter('wc_add_to_cart_message_html', 'puppy_market_hide_catalog_add_to_cart_notice', 99);

function puppy_market_catalog_title($title) {
    if (!function_exists('is_shop') || !is_shop() || empty($_GET['catalog_view'])) return $title;
    $view = sanitize_key(wp_unslash($_GET['catalog_view']));
    if ($view === 'new') return 'New arrivals';
    if ($view === 'sale') return 'Deals';
    return $title;
}
add_filter('woocommerce_page_title', 'puppy_market_catalog_title');

/**
 * Taxonomies whose terms should be searchable from the storefront search box.
 * WooCommerce categories, tags, brands and all registered global attributes
 * are included automatically.
 */
function puppy_market_product_search_taxonomies() {
    $taxonomies = array('product_cat', 'product_tag');

    if (taxonomy_exists('product_brand')) {
        $taxonomies[] = 'product_brand';
    }

    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $attribute) {
            $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
            if (taxonomy_exists($taxonomy)) $taxonomies[] = $taxonomy;
        }
    }

    return array_values(array_unique(array_filter(array_map('sanitize_key', $taxonomies))));
}

/**
 * Resolve matching product IDs across names, descriptions, parent/variation
 * SKUs, product categories, brands and global attribute terms.
 */
function puppy_market_product_search_ids($search_term) {
    global $wpdb;

    $search_term = trim(wp_strip_all_tags((string) $search_term));
    if ($search_term === '') return array();

    $taxonomies = puppy_market_product_search_taxonomies();
    $taxonomy_placeholders = implode(', ', array_fill(0, count($taxonomies), '%s'));
    $like = '%' . $wpdb->esc_like($search_term) . '%';
    $prefix = $wpdb->esc_like($search_term) . '%';
    $slug_like = '%' . $wpdb->esc_like(sanitize_title($search_term)) . '%';

    $sql = "
        SELECT products.ID,
               MIN(
                   CASE
                       WHEN products.post_title = %s THEN 0
                       WHEN products.post_title LIKE %s THEN 1
                       WHEN sku.meta_value = %s THEN 2
                       WHEN products.post_title LIKE %s THEN 3
                       ELSE 4
                   END
               ) AS puppy_relevance
        FROM {$wpdb->posts} AS products
        LEFT JOIN {$wpdb->postmeta} AS sku
               ON sku.post_id = products.ID
              AND sku.meta_key = '_sku'
        LEFT JOIN {$wpdb->term_relationships} AS relationships
               ON relationships.object_id = products.ID
        LEFT JOIN {$wpdb->term_taxonomy} AS taxonomy
               ON taxonomy.term_taxonomy_id = relationships.term_taxonomy_id
        LEFT JOIN {$wpdb->terms} AS terms
               ON terms.term_id = taxonomy.term_id
        WHERE products.post_type = 'product'
          AND products.post_status = 'publish'
          AND (
               products.post_title LIKE %s
               OR products.post_excerpt LIKE %s
               OR products.post_content LIKE %s
               OR sku.meta_value LIKE %s
               OR EXISTS (
                   SELECT 1
                   FROM {$wpdb->posts} AS variations
                   INNER JOIN {$wpdb->postmeta} AS variation_sku
                           ON variation_sku.post_id = variations.ID
                          AND variation_sku.meta_key = '_sku'
                   WHERE variations.post_parent = products.ID
                     AND variations.post_type = 'product_variation'
                     AND variation_sku.meta_value LIKE %s
               )
               OR (
                   taxonomy.taxonomy IN ({$taxonomy_placeholders})
                   AND (terms.name LIKE %s OR terms.slug LIKE %s)
               )
          )
        GROUP BY products.ID
        ORDER BY puppy_relevance ASC, products.post_date DESC
        LIMIT 500
    ";

    $parameters = array(
        $search_term,
        $prefix,
        $search_term,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like,
    );
    $parameters = array_merge($parameters, $taxonomies, array($like, $slug_like));

    $prepared_sql = $wpdb->prepare($sql, $parameters);
    return array_values(array_filter(array_map('absint', (array) $wpdb->get_col($prepared_sql))));
}

/** Return compact product/category and article suggestions for the header search. */
function puppy_market_search_suggestions() {
    $search_term = isset($_GET['term'])
        ? trim(sanitize_text_field(wp_unslash($_GET['term'])))
        : '';
    $search_length = function_exists('mb_strlen')
        ? mb_strlen($search_term)
        : strlen($search_term);

    if ($search_length < 2) {
        wp_send_json_success(array('suggestions' => array(), 'articles' => array()));
    }

    $search_term = function_exists('mb_substr')
        ? mb_substr($search_term, 0, 80)
        : substr($search_term, 0, 80);
    $suggestions = array();
    $seen_labels = array();

    foreach (array_slice(puppy_market_product_search_ids($search_term), 0, 5) as $product_id) {
        $label = trim(wp_strip_all_tags(get_the_title($product_id)));
        $url = get_permalink($product_id);
        if ($label === '' || !$url) continue;

        $label_key = strtolower($label);
        if (isset($seen_labels[$label_key])) continue;

        $suggestions[] = array('label' => $label, 'url' => esc_url_raw($url));
        $seen_labels[$label_key] = true;
    }

    if (count($suggestions) < 5 && taxonomy_exists('product_cat')) {
        $category_terms = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'search'     => $search_term,
            'number'     => 5 - count($suggestions),
            'orderby'    => 'count',
            'order'      => 'DESC',
        ));

        if (!is_wp_error($category_terms)) {
            foreach ($category_terms as $category_term) {
                $label = trim(wp_strip_all_tags($category_term->name));
                $url = get_term_link($category_term);
                $label_key = strtolower($label);
                if ($label === '' || is_wp_error($url) || isset($seen_labels[$label_key])) continue;

                $suggestions[] = array('label' => $label, 'url' => esc_url_raw($url));
                $seen_labels[$label_key] = true;
                if (count($suggestions) >= 5) break;
            }
        }
    }

    $articles = array();
    $article_query = new WP_Query(array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        's'                   => $search_term,
        'posts_per_page'      => 3,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ));

    foreach ($article_query->posts as $article) {
        $label = trim(wp_strip_all_tags(get_the_title($article)));
        $url = get_permalink($article);
        if ($label === '' || !$url) continue;
        $articles[] = array('label' => $label, 'url' => esc_url_raw($url));
    }

    wp_reset_postdata();
    nocache_headers();
    wp_send_json_success(array(
        'suggestions' => $suggestions,
        'articles'    => $articles,
    ));
}
add_action('wp_ajax_nopriv_puppy_market_search_suggestions', 'puppy_market_search_suggestions');
add_action('wp_ajax_puppy_market_search_suggestions', 'puppy_market_search_suggestions');

/** Restrict the public storefront search to published WooCommerce products. */
function puppy_market_search_products($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) return;

    $product_ids = puppy_market_product_search_ids($query->get('s'));

    $query->set('post_type', 'product');
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', 12);
    $query->set('ignore_sticky_posts', true);
    $query->set('has_password', false);
    $query->set('post__in', !empty($product_ids) ? $product_ids : array(0));
    $query->set('orderby', 'post__in');
    $query->set('puppy_market_product_search', true);
}
add_action('pre_get_posts', 'puppy_market_search_products');

/**
 * Matching has already been resolved to post__in above. Remove WordPress's
 * title/content-only search fragment while keeping the original search term
 * available to the template and pagination links.
 */
function puppy_market_product_search_sql($search, $query) {
    if (!is_admin() && $query->get('puppy_market_product_search')) return '';
    return $search;
}
add_filter('posts_search', 'puppy_market_product_search_sql', 99, 2);

function puppy_market_catalog_has_active_filters() {
    foreach ($_GET as $filter_key => $filter_value) {
        $filter_key = sanitize_key($filter_key);
        if ($filter_key !== 'catalog_view' && strpos($filter_key, 'puppy_') !== 0) {
            continue;
        }

        foreach ((array) $filter_value as $value) {
            if (trim(sanitize_text_field(wp_unslash($value))) !== '') {
                return true;
            }
        }
    }
    return false;
}

function puppy_market_no_products_message() {
    $has_filters = puppy_market_catalog_has_active_filters();
    $clear_url = puppy_market_catalog_url();

    if (function_exists('is_product_category') && is_product_category()) {
        $current_category = get_queried_object();
        $category_url = $current_category && !empty($current_category->term_id)
            ? get_term_link($current_category, 'product_cat')
            : '';
        if (!is_wp_error($category_url) && $category_url) {
            $clear_url = $category_url;
        }
    }

    $title = $has_filters ? 'No products match these filters' : 'No products in this category yet';
    $description = $has_filters
        ? 'Try clearing the filters to see everything available in this category.'
        : 'We are adding more essentials. Explore another category for now.';
    $button_label = $has_filters ? 'Clear filters' : 'View all products';

    echo '<div class="catalog-empty" role="status">'
        . '<span class="catalog-empty-icon" aria-hidden="true">🐾</span>'
        . '<h2>' . esc_html($title) . '</h2>'
        . '<p>' . esc_html($description) . '</p>'
        . '<a class="button" href="' . esc_url($clear_url) . '">' . esc_html($button_label) . '</a>'
        . '</div>';
}

// WooCommerce attaches wc_no_products_found() to this hook by default.
// Remove it so the catalog never renders the plugin's default info notice.
remove_action('woocommerce_no_products_found', 'wc_no_products_found', 10);
add_action('woocommerce_no_products_found', 'puppy_market_no_products_message', 10);
/** Redirect the Contact Us form back to its page with a safe status value. */
function puppy_market_contact_form_redirect($status) {
    $redirect_url = wp_get_referer();
    if (!$redirect_url) $redirect_url = puppy_market_page_url('contact');

    $redirect_url = remove_query_arg('contact_status', $redirect_url);
    $redirect_url = add_query_arg('contact_status', sanitize_key($status), $redirect_url) . '#contact-form';
    wp_safe_redirect($redirect_url);
    exit;
}

/** Process the Contact Us page form and deliver it to the configured address. */
function puppy_market_handle_contact_form() {
    $nonce = isset($_POST['puppy_contact_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['puppy_contact_nonce']))
        : '';

    if (!$nonce || !wp_verify_nonce($nonce, 'puppy_market_contact_form')) {
        puppy_market_contact_form_redirect('invalid');
    }

    // Quietly accept honeypot submissions without sending mail.
    $honeypot = isset($_POST['company_website'])
        ? trim((string) wp_unslash($_POST['company_website']))
        : '';
    if ($honeypot !== '') puppy_market_contact_form_redirect('success');

    $name = isset($_POST['contact_name'])
        ? sanitize_text_field(wp_unslash($_POST['contact_name']))
        : '';
    $email = isset($_POST['contact_email'])
        ? sanitize_email(wp_unslash($_POST['contact_email']))
        : '';
    $order_number = isset($_POST['order_number'])
        ? sanitize_text_field(wp_unslash($_POST['order_number']))
        : '';
    $topic = isset($_POST['contact_topic'])
        ? sanitize_key(wp_unslash($_POST['contact_topic']))
        : 'other';
    $message = isset($_POST['contact_message'])
        ? sanitize_textarea_field(wp_unslash($_POST['contact_message']))
        : '';

    $topic_labels = array(
        'order'    => 'Order help',
        'shipping' => 'Shipping',
        'returns'  => 'Returns',
        'product'  => 'Product question',
        'business' => 'Business & wholesale',
        'other'    => 'Other',
    );

    if (!isset($topic_labels[$topic])) $topic = 'other';
    if ($name === '' || !is_email($email) || $message === '') {
        puppy_market_contact_form_redirect('invalid');
    }

    $support_email = sanitize_email(get_theme_mod('puppy_market_contact_email', get_option('admin_email')));
    $business_email = sanitize_email(get_theme_mod('puppy_market_business_email', ''));
    $recipient = $topic === 'business' && is_email($business_email) ? $business_email : $support_email;

    if (!is_email($recipient)) puppy_market_contact_form_redirect('error');

    $subject = sprintf('[%s] %s request from %s', wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES), $topic_labels[$topic], $name);
    $mail_body = implode("\n", array(
        'Name: ' . $name,
        'Email: ' . $email,
        'Topic: ' . $topic_labels[$topic],
        'Order number: ' . ($order_number !== '' ? $order_number : 'Not provided'),
        '',
        'Message:',
        substr($message, 0, 5000),
    ));
    $headers = array('Reply-To: ' . $name . ' <' . $email . '>');

    $sent = wp_mail($recipient, $subject, $mail_body, $headers);
    puppy_market_contact_form_redirect($sent ? 'success' : 'error');
}
add_action('admin_post_nopriv_puppy_market_contact', 'puppy_market_handle_contact_form');
add_action('admin_post_puppy_market_contact', 'puppy_market_handle_contact_form');
