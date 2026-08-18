<!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?><a class="skip-link" href="#main-content">Skip to content</a><div class="ipet-toast" role="status" aria-live="polite"></div>
<header class="site-header">
  <?php
  $puppy_account_url = puppy_market_account_url();
  $puppy_account_action_url = is_user_logged_in() ? wp_logout_url(home_url('/')) : $puppy_account_url;
  $puppy_cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
  $puppy_checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : $puppy_cart_url;
  $puppy_shop_url = puppy_market_catalog_url();
  // Mini-cart preview data for the hover dropdown below — Chewy always lets you
  // peek at what's in the cart without leaving the page, we previously just had
  // a plain link straight to /cart/.
  $puppy_cart_count = 0;
  $puppy_cart_subtotal_html = '';
  $puppy_cart_free_shipping_gap = 75;
  $puppy_cart_preview_items = array();
  if (function_exists('WC') && WC()->cart) {
      $puppy_cart_count = absint(WC()->cart->get_cart_contents_count());
      $puppy_cart_subtotal_html = WC()->cart->get_cart_subtotal();
      $puppy_cart_free_shipping_gap = 75 - (float) WC()->cart->get_subtotal();
      $puppy_cart_preview_items = array_slice(WC()->cart->get_cart(), 0, 3);
  }
  ?>
  <div class="topbar">
    <div class="container topbar-inner">
      <div class="header-promises"><a href="<?php echo esc_url(home_url('/shipping/')); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M3 6.5h11v10H3z"></path><path d="M14 10h3.5l3.5 3.5v3H14z"></path><circle cx="7" cy="18" r="1.8"></circle><circle cx="18" cy="18" r="1.8"></circle></svg></span><strong>Free Shipping</strong></a><a href="<?php echo esc_url(home_url('/contact/')); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M4.5 5.5h15v10h-9l-4.5 3v-3h-1.5z"></path><path d="M8 10h8M8 13h5"></path></svg></span><strong>24/7 Support</strong></a><a href="<?php echo esc_url(home_url('/returns/')); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M8 7H4l3-3"></path><path d="M4 7a8 8 0 1 1-1 8"></path><path d="M12 9v4l2.5 1.5"></path></svg></span><strong>Easy Returns</strong></a></div>
      <div class="header-actions">
        <div class="header-menu">
          <a class="header-menu-trigger" href="<?php echo esc_url($puppy_account_action_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="8" r="3.2"></circle><path d="M5.5 19c.7-3.1 3.1-4.8 6.5-4.8s5.8 1.7 6.5 4.8"></path></svg></span><strong><?php echo is_user_logged_in() ? 'Account' : 'Sign In'; ?></strong></a>
          <div class="header-dropdown account-dropdown">
            <?php if (is_user_logged_in()) : ?>
              <a class="dropdown-primary-button" href="<?php echo esc_url($puppy_account_url); ?>">My Account</a>
              <a href="<?php echo esc_url($puppy_account_url); ?>">Order history</a>
              <a href="<?php echo esc_url($puppy_account_action_url); ?>">Sign out</a>
            <?php else : ?>
              <a class="dropdown-primary-button" href="<?php echo esc_url($puppy_account_url); ?>">Sign In</a>
              <p class="dropdown-secondary-link">New to iPet? <a href="<?php echo esc_url($puppy_account_url); ?>">Create an account</a></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="header-menu">
          <a class="header-menu-trigger" href="<?php echo esc_url($puppy_cart_url); ?>"><span class="header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M3.5 5h2l1.5 10.2a2 2 0 0 0 2 1.8h7.8a2 2 0 0 0 1.9-1.5L20.5 8H6.2"></path><circle cx="9.5" cy="19.3" r="1"></circle><circle cx="17" cy="19.3" r="1"></circle></svg></span><strong>Cart</strong><span class="cart-count"><?php echo absint($puppy_cart_count); ?></span></a>
          <div class="header-dropdown cart-dropdown">
            <?php if ($puppy_cart_count > 0) : ?>
              <div class="cart-dropdown-items">
                <?php foreach ($puppy_cart_preview_items as $puppy_cart_preview_item) :
                  $puppy_cart_preview_product = isset($puppy_cart_preview_item['data']) ? $puppy_cart_preview_item['data'] : null;
                  if (!$puppy_cart_preview_product) continue;
                ?>
                  <a class="cart-dropdown-item" href="<?php echo esc_url(get_permalink($puppy_cart_preview_product->get_id())); ?>">
                    <span class="cart-dropdown-item-image"><?php echo wp_kses_post($puppy_cart_preview_product->get_image('thumbnail')); ?></span>
                    <span class="cart-dropdown-item-info"><strong><?php echo esc_html($puppy_cart_preview_product->get_name()); ?></strong><span><?php echo absint($puppy_cart_preview_item['quantity']); ?> × <?php echo wp_kses_post(wc_price($puppy_cart_preview_product->get_price())); ?></span></span>
                  </a>
                <?php endforeach; ?>
                <?php if ($puppy_cart_count > count($puppy_cart_preview_items)) : ?><p class="cart-dropdown-more">+<?php echo absint($puppy_cart_count - count($puppy_cart_preview_items)); ?> more in cart</p><?php endif; ?>
              </div>
              <div class="cart-dropdown-summary"><span><?php echo absint($puppy_cart_count); ?> item<?php echo $puppy_cart_count === 1 ? '' : 's'; ?></span><strong><?php echo wp_kses_post($puppy_cart_subtotal_html); ?></strong></div>
              <p class="cart-dropdown-shipping"><?php if ($puppy_cart_free_shipping_gap > 0) : ?>Add <?php echo wp_kses_post(wc_price($puppy_cart_free_shipping_gap)); ?> more for <strong>free shipping</strong><?php else : ?>You've unlocked <strong>free shipping</strong><?php endif; ?></p>
              <a class="dropdown-button dropdown-button-outline" href="<?php echo esc_url($puppy_cart_url); ?>">View cart</a>
              <a class="dropdown-button dropdown-button-primary" href="<?php echo esc_url($puppy_checkout_url); ?>">Checkout</a>
            <?php else : ?>
              <p class="cart-dropdown-empty">Your cart is empty.</p>
              <a class="dropdown-button dropdown-button-primary" href="<?php echo esc_url($puppy_shop_url); ?>">Start shopping</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container header-main">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><img class="brand-logo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/ipet-logo.png'); ?>" alt="iPet"></a>
    <form class="search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search dogs, cats and pet essentials…" value="<?php echo get_search_query(); ?>"><button type="submit">Search</button></form>
  </div>
  <nav class="container nav" aria-label="Primary navigation">
  <?php $puppy_primary_menu = wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-menu', 'fallback_cb' => false, 'echo' => false)); if ($puppy_primary_menu) : echo $puppy_primary_menu; else : ?>
    <div class="nav-item"><a class="nav-trigger" href="<?php echo esc_url(puppy_market_category_link('dog')); ?>">Dogs <span class="nav-chevron" aria-hidden="true"></span></a><div class="mega-menu"><div><strong>Food &amp; Treats</strong><a href="<?php echo esc_url(puppy_market_category_link('dog-food')); ?>">Dog Food</a><a href="<?php echo esc_url(puppy_market_category_link('puppy-food')); ?>">Puppy Food</a><a href="<?php echo esc_url(puppy_market_category_link('dog-treats')); ?>">Dog Treats</a></div><div><strong>Gear &amp; Toys</strong><a href="<?php echo esc_url(puppy_market_category_link('dog-toys')); ?>">Toys</a><a href="<?php echo esc_url(puppy_market_category_link('dog-walk')); ?>">Walking Gear</a><a href="<?php echo esc_url(puppy_market_category_link('dog-beds')); ?>">Beds &amp; Crates</a><a href="<?php echo esc_url(puppy_market_category_link('dog-grooming')); ?>">Dog Grooming</a></div><a class="menu-feature" href="<?php echo esc_url(puppy_market_category_link('dog')); ?>"><b>DOG</b><span>Shop the dog collection →</span></a></div></div>
    <div class="nav-item"><a class="nav-trigger" href="<?php echo esc_url(puppy_market_category_link('cat')); ?>">Cats <span class="nav-chevron" aria-hidden="true"></span></a><div class="mega-menu"><div><strong>Food &amp; Litter</strong><a href="<?php echo esc_url(puppy_market_category_link('cat-food')); ?>">Cat Food</a><a href="<?php echo esc_url(puppy_market_category_link('kitten-food')); ?>">Kitten Food</a><a href="<?php echo esc_url(puppy_market_category_link('cat-litter')); ?>">Cat Litter</a></div><div><strong>Gear &amp; Toys</strong><a href="<?php echo esc_url(puppy_market_category_link('cat-toys')); ?>">Cat Toys</a><a href="<?php echo esc_url(puppy_market_category_link('cat-beds')); ?>">Beds &amp; Scratchers</a><a href="<?php echo esc_url(puppy_market_category_link('cat-scratchers')); ?>">Cat Scratchers</a><a href="<?php echo esc_url(puppy_market_category_link('pet-care')); ?>">Grooming &amp; Care</a></div><a class="menu-feature" href="<?php echo esc_url(puppy_market_category_link('cat')); ?>"><b>CAT</b><span>Shop the cat collection →</span></a></div></div><a href="<?php echo esc_url(puppy_market_category_link('birds')); ?>">Birds</a>
    <div class="nav-item"><a class="nav-trigger" href="<?php echo esc_url(puppy_market_category_link('small-pets')); ?>">More Pets <span class="nav-chevron" aria-hidden="true"></span></a><div class="mega-menu compact"><div><strong>Small Pets &amp; Birds</strong><a href="<?php echo esc_url(puppy_market_category_link('small-pets')); ?>">Rabbits &amp; Hamsters</a><a href="<?php echo esc_url(puppy_market_category_link('small-pet-food')); ?>">Small Pet Food</a><a href="<?php echo esc_url(puppy_market_category_link('birds')); ?>">Pet Birds</a><a href="<?php echo esc_url(puppy_market_category_link('bird-food')); ?>">Bird Food</a></div><div><strong>Aquatic &amp; Reptiles</strong><a href="<?php echo esc_url(puppy_market_category_link('aquarium')); ?>">Aquarium Supplies</a><a href="<?php echo esc_url(puppy_market_category_link('aquarium-food')); ?>">Aquarium Food</a><a href="<?php echo esc_url(puppy_market_category_link('reptiles')); ?>">Reptile Supplies</a><a href="<?php echo esc_url(puppy_market_category_link('reptile-food')); ?>">Reptile Food</a><a href="<?php echo esc_url(puppy_market_category_link('reptile-habitat')); ?>">Habitats &amp; Environments</a></div></div></div>
    <a href="<?php echo esc_url(puppy_market_category_link('pet-toys')); ?>">Pet Toys</a><?php /* Chewy has a top-level Pharmacy nav item; this site substitutes real grooming/wellness content in that slot instead, per project scope. */ ?><a href="<?php echo esc_url(puppy_market_category_link('pet-care')); ?>">Grooming &amp; Wellness</a><a href="<?php echo esc_url($puppy_shop_url); ?>">Shop All</a><a href="<?php echo esc_url(puppy_market_catalog_url('new')); ?>">New Arrivals</a><a class="nav-sale" href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Deals</a>
  <?php endif; ?>
  </nav>
</header>
