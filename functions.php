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
        'primary'      => __('Primary Menu', 'puppy-market'),
        'footer_shop'  => __('Footer Shop Menu', 'puppy-market'),
        'footer_help'  => __('Footer Help Menu', 'puppy-market'),
        'footer_about' => __('Footer About Menu', 'puppy-market'),
    ));
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'puppy_market_setup');

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

    if (function_exists('is_account_page') && is_account_page() && is_user_logged_in()) {
        $account_script_path = get_template_directory() . '/assets/account.js';
        wp_enqueue_script(
            'puppy-market-account',
            get_template_directory_uri() . '/assets/account.js',
            array(),
            file_exists($account_script_path) ? filemtime($account_script_path) : $style_version,
            true
        );
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
                    var curMain = document.querySelector(".puppy-cart-main");
                    var curSide = document.querySelector(".puppy-cart-sidebar");
                    if (main && curMain) curMain.outerHTML = main.outerHTML;
                    if (side && curSide) curSide.outerHTML = side.outerHTML;
                }
                function submitCart(formData, doneMsg) {
                    var form = document.querySelector("form.woocommerce-cart-form");
                    if (!form) return;
                    var loading = document.createElement("span");
                    loading.className = "puppy-cart-updating";
                    loading.textContent = "Updating…";
                    document.body.appendChild(loading);
                    fetch(form.getAttribute("action"), { method: "POST", body: formData, credentials: "same-origin" })
                        .then(function (r) { return r.text(); })
                        .then(function (html) { swap(html); if (doneMsg) toast(doneMsg); })
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
                    submitCart(fd, code.trim() ? "Coupon applied" : "");
                });

                document.addEventListener("click", function (e) {
                    var wrap = e.target.closest ? e.target.closest("[data-promo-wrap]") : null;
                    if (wrap) {
                        var toggle = e.target.closest("[data-promo-toggle]");
                        if (toggle) { wrap.classList.toggle("is-open"); return; }
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
                            .then(function (html) { swap(html); toast("Removed from cart"); })
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

/** Data-driven PDP details. Reviews stay disabled for this storefront. */
function puppy_market_product_about_item() {
    global $product;
    if (!$product) return;

    $description = $product->get_description() ?: $product->get_short_description();
    $ingredients = $product->get_meta('_ingredients', true) ?: $product->get_meta('ingredients', true);
    $instructions = $product->get_meta('_feeding_instructions', true) ?: $product->get_meta('feeding_instructions', true);
    $specifications = array();

    foreach ($product->get_attributes() as $attribute) {
        if (!$attribute->get_visible()) continue;
        $values = $attribute->is_taxonomy()
            ? wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'))
            : $attribute->get_options();
        if (!empty($values)) {
            $attribute_label = wc_attribute_label($attribute->get_name());
            $attribute_value = implode(', ', $values);
            $specifications[$attribute_label] = $attribute_value;
            if (!$ingredients && stripos($attribute_label, 'ingredient') !== false) $ingredients = $attribute_value;
            if (!$instructions && (stripos($attribute_label, 'feeding') !== false || stripos($attribute_label, 'instruction') !== false)) $instructions = $attribute_value;
        }
    }
    if ($product->get_sku()) $specifications['SKU'] = $product->get_sku();
    if ($product->has_weight()) $specifications['Weight'] = wc_format_weight($product->get_weight());
    if ($product->has_dimensions()) $specifications['Dimensions'] = wc_format_dimensions($product->get_dimensions(false));

    $sections = array();
    if ($description) $sections[] = array('title' => 'Details', 'content' => wp_kses_post(wpautop($description)), 'open' => true);
    if ($ingredients) $sections[] = array('title' => 'Ingredient Information', 'content' => wp_kses_post(wpautop($ingredients)), 'open' => false);
    if ($instructions) $sections[] = array('title' => 'Feeding Instructions', 'content' => wp_kses_post(wpautop($instructions)), 'open' => false);
    if (!empty($specifications)) {
        $spec_html = '<dl class="ipet-spec-list">';
        foreach ($specifications as $label => $value) $spec_html .= '<div><dt>' . esc_html($label) . '</dt><dd>' . esc_html($value) . '</dd></div>';
        $spec_html .= '</dl>';
        $sections[] = array('title' => 'Specifications', 'content' => $spec_html, 'open' => false);
    }
    $sections[] = array('title' => 'Shipping & Returns', 'content' => '<p>Free shipping is available on qualifying orders. Eligible unused items can be returned within 30 days of delivery.</p>', 'open' => false);
    if (!empty($sections) && !array_filter($sections, function ($section) { return $section['open']; })) $sections[0]['open'] = true;

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

/** Return only global attributes actually assigned to products in this category. */
function puppy_market_category_filter_attributes($category) {
    if (!$category || empty($category->term_id) || !function_exists('wc_get_attribute_taxonomies')) return array();
    $product_ids = get_posts(array(
        'post_type' => 'product', 'post_status' => 'publish', 'fields' => 'ids',
        'posts_per_page' => -1, 'no_found_rows' => true,
        'tax_query' => array(array(
            'taxonomy' => 'product_cat', 'field' => 'term_id',
            'terms' => array((int) $category->term_id), 'include_children' => true,
        )),
    ));
    if (empty($product_ids)) return array();
    $filters = array();
    foreach (wc_get_attribute_taxonomies() as $attribute) {
        $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
        if (!taxonomy_exists($taxonomy)) continue;
        $terms = get_terms(array(
            'taxonomy' => $taxonomy, 'hide_empty' => true, 'object_ids' => $product_ids,
            'number' => 12, 'orderby' => 'count', 'order' => 'DESC',
        ));
        if (!is_wp_error($terms) && !empty($terms)) {
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
    $selected_categories = isset($_GET['puppy_category']) ? array_filter(array_map('sanitize_title', (array) wp_unslash($_GET['puppy_category']))) : array();
    if (!empty($selected_categories)) {
        $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $selected_categories, 'operator' => 'IN');
    }
    $selected_brands = isset($_GET['puppy_brand']) ? array_filter(array_map('sanitize_title', (array) wp_unslash($_GET['puppy_brand']))) : array();
    if (!empty($selected_brands)) {
        $brand_taxonomy = puppy_market_brand_taxonomy();
        $brand_terms = get_terms(array('taxonomy' => $brand_taxonomy, 'hide_empty' => false, 'slug' => $selected_brands));
        if (!is_wp_error($brand_terms) && !empty($brand_terms)) {
            $tax_query[] = array('taxonomy' => $brand_taxonomy, 'field' => 'slug', 'terms' => $selected_brands, 'operator' => 'IN');
        } else {
            $query->set('s', str_replace('-', ' ', sanitize_title(reset($selected_brands))));
        }
    }
    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $attribute) {
            $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
            $key = 'puppy_attr_' . $taxonomy;
            $selected_terms = isset($_GET[$key]) ? array_filter(array_map('sanitize_title', (array) wp_unslash($_GET[$key]))) : array();
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

function puppy_market_search_products($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) return;
    $query->set('post_type', array('post', 'page', 'product'));
}
add_action('pre_get_posts', 'puppy_market_search_products');

function puppy_market_no_products_message() {
    echo '<div class="empty-state catalog-empty"><span>🐾</span><h2>No products in this category yet</h2><p>We are adding more essentials. Explore another category for now.</p><a class="button" href="' . esc_url(puppy_market_catalog_url()) . '">View all products</a></div>';
}
add_action('woocommerce_no_products_found', 'puppy_market_no_products_message');
