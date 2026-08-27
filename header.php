<!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body id="page-top" <?php body_class(); ?>><?php wp_body_open(); ?><a class="skip-link" href="#main-content">Skip to content</a><div class="ipet-toast" role="status" aria-live="polite"></div>
<header class="site-header">
  <?php
  $puppy_account_url = puppy_market_account_url();
  $puppy_account_action_url = is_user_logged_in() ? wp_logout_url(home_url('/')) : $puppy_account_url;
  $puppy_cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
  $puppy_checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : $puppy_cart_url;
  $puppy_shop_url = puppy_market_catalog_url();
  $puppy_shipping_url = puppy_market_page_url('shipping');
  $puppy_contact_url = puppy_market_page_url('contact');
  $puppy_returns_url = puppy_market_page_url('returns');
  $puppy_current_user = is_user_logged_in() ? wp_get_current_user() : null;
  $puppy_account_greeting = $puppy_current_user ? ($puppy_current_user->first_name ?: $puppy_current_user->display_name) : '';
  $puppy_orders_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('orders') : $puppy_account_url;
  $puppy_account_menu_links = array(
      'Account'            => $puppy_account_url,
      'Orders'             => $puppy_orders_url,
      'Manage Autoship'    => $puppy_account_url,
      'Favorites'          => $puppy_account_url,
      'Buy Again'          => $puppy_shop_url,
      'iPet+'              => $puppy_account_url,
      'Prescriptions'      => $puppy_shop_url,
      'My Vet Clinics'     => $puppy_account_url,
      'Pet Portal'         => $puppy_account_url,
      'Connect with a Vet' => $puppy_contact_url,
      'iPet Pet Insurance' => $puppy_contact_url,
  );
  // Mini-cart preview data for the hover dropdown below — Chewy always lets you
  // peek at what's in the cart without leaving the page, we previously just had
  // a plain link straight to /cart/.
  $puppy_cart_count = 0;
  $puppy_cart_subtotal_html = '';
  $puppy_cart_free_shipping_threshold = 49;
  $puppy_cart_free_shipping_gap = $puppy_cart_free_shipping_threshold;
  $puppy_cart_shipping_progress = 0;
  $puppy_cart_preview_items = array();
  if (function_exists('WC') && WC()->cart) {
      $puppy_cart_count = absint(WC()->cart->get_cart_contents_count());
      $puppy_cart_subtotal_html = WC()->cart->get_cart_subtotal();
      $puppy_cart_subtotal = (float) WC()->cart->get_subtotal();
      $puppy_cart_free_shipping_gap = $puppy_cart_free_shipping_threshold - $puppy_cart_subtotal;
      $puppy_cart_shipping_progress = min(100, max(0, ($puppy_cart_subtotal / $puppy_cart_free_shipping_threshold) * 100));
      $puppy_cart_preview_items = array_slice(WC()->cart->get_cart(), 0, 3);
  }
  ?>
  <div class="container header-main">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo wp_kses_post(puppy_market_brand_markup()); ?></a>
    <form class="search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" data-puppy-search autocomplete="off">
      <input type="hidden" name="post_type" value="product">
      <input type="search" name="s" placeholder="Search" value="<?php echo esc_attr(get_search_query()); ?>" aria-label="Search products" aria-autocomplete="list" aria-controls="puppy-search-suggestions" aria-expanded="false" autocomplete="off" autocapitalize="off" spellcheck="false" required>
      <button type="submit" aria-label="Submit search">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <circle cx="10.8" cy="10.8" r="6.8"></circle>
          <path d="m15.9 15.9 4.4 4.4"></path>
        </svg>
      </button>
      <div id="puppy-search-suggestions" class="puppy-search-suggest-panel" hidden>
        <div class="puppy-search-suggest-list" role="listbox" aria-label="Search suggestions" data-search-suggestion-list></div>
      </div>
    </form>
    <div class="header-utilities" aria-label="Store services and account">
      <div class="header-promises"><a href="<?php echo esc_url($puppy_shipping_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M3 6.5h11v10H3z"></path><path d="M14 10h3.5l3.5 3.5v3H14z"></path><circle cx="7" cy="18" r="1.8"></circle><circle cx="18" cy="18" r="1.8"></circle></svg></span><strong>Free Shipping</strong></a><a href="<?php echo esc_url($puppy_contact_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M4.5 5.5h15v10h-9l-4.5 3v-3h-1.5z"></path><path d="M8 10h8M8 13h5"></path></svg></span><strong>24/7 Support</strong></a><a href="<?php echo esc_url($puppy_returns_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M8 7H4l3-3"></path><path d="M4 7a8 8 0 1 1-1 8"></path><path d="M12 9v4l2.5 1.5"></path></svg></span><strong>Easy Returns</strong></a></div>
      <div class="header-actions">
        <div class="header-menu">
          <a class="header-menu-trigger" href="<?php echo esc_url(is_user_logged_in() ? $puppy_account_url : $puppy_account_action_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="8" r="3.2"></circle><path d="M5.5 19c.7-3.1 3.1-4.8 6.5-4.8s5.8 1.7 6.5 4.8"></path></svg></span><span class="header-account-copy"><?php if (is_user_logged_in()) : ?><small>Hi, <?php echo esc_html($puppy_account_greeting); ?>!</small><?php endif; ?><strong><?php echo is_user_logged_in() ? 'Account' : 'Sign In'; ?></strong></span><span class="header-dropdown-chevron" aria-hidden="true"></span></a>
          <div class="header-dropdown account-dropdown <?php echo is_user_logged_in() ? 'is-customer' : 'is-guest'; ?>">
            <?php if (is_user_logged_in()) : ?>
              <?php foreach ($puppy_account_menu_links as $puppy_account_menu_label => $puppy_account_menu_url) : ?>
                <a href="<?php echo esc_url($puppy_account_menu_url); ?>"><?php echo esc_html($puppy_account_menu_label); ?></a>
              <?php endforeach; ?>
            <?php else : ?>
              <a class="dropdown-primary-button" href="<?php echo esc_url($puppy_account_url); ?>">Sign In or Create an Account</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="header-menu">
          <a class="header-menu-trigger" href="<?php echo esc_url($puppy_cart_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M3.5 5h2l1.5 10.2a2 2 0 0 0 2 1.8h7.8a2 2 0 0 0 1.9-1.5L20.5 8H6.2"></path><circle cx="9.5" cy="19.3" r="1"></circle><circle cx="17" cy="19.3" r="1"></circle></svg></span><strong>Cart</strong><span class="cart-count"><?php echo absint($puppy_cart_count); ?></span><span class="header-dropdown-chevron" aria-hidden="true"></span></a>
          <div class="header-dropdown cart-dropdown">
            <?php if ($puppy_cart_count > 0) : ?>
              <div class="cart-dropdown-summary"><span>Cart Subtotal:</span><strong><?php echo wp_kses_post($puppy_cart_subtotal_html); ?></strong></div>
              <div class="cart-dropdown-shipping-wrap">
                <p class="cart-dropdown-shipping">
                  <?php if ($puppy_cart_free_shipping_gap > 0) : ?>Add <?php echo wp_kses_post(wc_price($puppy_cart_free_shipping_gap)); ?> more for <strong>FREE shipping</strong><?php else : ?>Your order ships <strong>FREE!</strong><?php endif; ?>
                  <svg class="cart-dropdown-truck" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6.5h11v9H3z"></path><path d="M14 10h3.5l3.5 3.5v2H14z"></path><circle cx="7" cy="18" r="1.8"></circle><circle cx="18" cy="18" r="1.8"></circle></svg>
                </p>
                <div class="cart-dropdown-progress" aria-hidden="true"><span style="width: <?php echo esc_attr($puppy_cart_shipping_progress); ?>%"></span></div>
              </div>
              <div class="cart-dropdown-actions">
                <a class="dropdown-button dropdown-button-outline" href="<?php echo esc_url($puppy_cart_url); ?>">View Cart</a>
                <a class="dropdown-button dropdown-button-primary" href="<?php echo esc_url($puppy_checkout_url); ?>">Proceed to Checkout</a>
              </div>
              <div class="cart-dropdown-items">
                <?php foreach ($puppy_cart_preview_items as $puppy_cart_preview_item) :
                  $puppy_cart_preview_product = isset($puppy_cart_preview_item['data']) ? $puppy_cart_preview_item['data'] : null;
                  if (!$puppy_cart_preview_product) continue;
                  $puppy_cart_preview_quantity = absint($puppy_cart_preview_item['quantity']);
                  $puppy_cart_preview_line_total = isset($puppy_cart_preview_item['line_total']) ? (float) $puppy_cart_preview_item['line_total'] : (float) $puppy_cart_preview_product->get_price() * $puppy_cart_preview_quantity;
                ?>
                  <a class="cart-dropdown-item" href="<?php echo esc_url(get_permalink($puppy_cart_preview_product->get_id())); ?>">
                    <span class="cart-dropdown-item-image"><?php echo wp_kses_post($puppy_cart_preview_product->get_image('thumbnail')); ?></span>
                    <span class="cart-dropdown-item-info"><strong><?php echo esc_html($puppy_cart_preview_product->get_name()); ?></strong><span><?php echo wp_kses_post(wc_price($puppy_cart_preview_line_total)); ?> (Qty: <?php echo absint($puppy_cart_preview_quantity); ?>)</span></span>
                  </a>
                <?php endforeach; ?>
                <?php if ($puppy_cart_count > count($puppy_cart_preview_items)) : ?><p class="cart-dropdown-more">+<?php echo absint($puppy_cart_count - count($puppy_cart_preview_items)); ?> more in cart</p><?php endif; ?>
              </div>
            <?php else : ?>
              <p class="cart-dropdown-empty">Your cart is empty.</p>
              <a class="dropdown-button dropdown-button-primary" href="<?php echo esc_url($puppy_shop_url); ?>">Start shopping</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if (has_nav_menu('primary')) : ?>
    <nav class="container nav" aria-label="Primary navigation">
      <?php wp_nav_menu(array(
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'nav-menu',
          'fallback_cb'    => false,
          'depth'          => 3,
      )); ?>
    </nav>
  <?php endif; ?>
</header>

