<?php
$footer_tagline = get_theme_mod('puppy_market_footer_tagline', '');
if (!$footer_tagline) $footer_tagline = get_bloginfo('description');
$footer_shop_url = puppy_market_catalog_url();
$footer_account_url = puppy_market_account_url();
$footer_contact_url = puppy_market_page_url('contact');
$footer_about_url = puppy_market_page_url('about');
$footer_returns_url = puppy_market_page_url('returns');
$footer_privacy_url = get_privacy_policy_url();
if (!$footer_privacy_url) $footer_privacy_url = puppy_market_page_url('privacy-policy');
$footer_cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : puppy_market_page_url('cart');
$footer_checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : puppy_market_page_url('checkout');
$footer_social_title = get_theme_mod('puppy_market_footer_social_title', 'Stay connected');
$footer_social_description = get_theme_mod(
    'puppy_market_footer_social_description',
    'Follow along for pet care tips, new arrivals and everyday favorites.'
);
$footer_social_items = array(
    'facebook' => array(
        'label' => 'Facebook',
        'url'   => puppy_market_setting_url('puppy_market_footer_facebook_url', ''),
        'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4.2c-.5-.1-1.8-.2-3.4-.2C10.4 4 8 6 8 9.7V13H4v4h4v7h5v-7h3.7l.6-4H13V10c0-1.2.3-2 1-2Z"></path></svg>',
    ),
    'youtube' => array(
        'label' => 'YouTube',
        'url'   => puppy_market_setting_url('puppy_market_footer_youtube_url', ''),
        'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-3-.4-5-1-6-.8-1-2-1.2-9-1.2S3.8 5 3 6c-.6 1-1 3-1 6s.4 5 1 6c.8 1 2 1.2 9 1.2s8.2-.2 9-1.2c.6-1 1-3 1-6Z"></path><path class="footer-social-cutout" d="m10 9 5 3-5 3V9Z"></path></svg>',
    ),
    'instagram' => array(
        'label' => 'Instagram',
        'url'   => puppy_market_setting_url('puppy_market_footer_instagram_url', ''),
        'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="5"></rect><circle class="footer-social-cutout" cx="12" cy="12" r="3.5"></circle><circle class="footer-social-cutout" cx="17.5" cy="6.8" r="1"></circle></svg>',
    ),
    'tiktok' => array(
        'label' => 'TikTok',
        'url'   => puppy_market_setting_url('puppy_market_footer_tiktok_url', ''),
        'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h3c.3 2 1.5 3.4 4 4v3c-1.6 0-3-.5-4-1.2V16a6 6 0 1 1-6-6h1v3h-1a3 3 0 1 0 3 3V3Z"></path></svg>',
    ),
);
$footer_copyright_template = get_theme_mod(
    'puppy_market_footer_copyright_text',
    '© {year} {site_name}. All rights reserved.'
);
$footer_copyright_text = strtr((string) $footer_copyright_template, array(
    '{year}'      => date_i18n('Y'),
    '{site_name}' => get_bloginfo('name'),
));
$footer_credit_text = (string) get_theme_mod(
    'puppy_market_footer_credit_text',
    'Powered by WordPress and WooCommerce'
);
?>
<footer class="site-footer">
  <section class="footer-support" aria-label="Customer support">
    <div class="container footer-support-inner">
      <p class="footer-support-title"><strong>Our pet care team is here 24/7.</strong><span>Questions about an order or choosing the right product? We are happy to help.</span></p>
      <a class="footer-support-link" href="<?php echo esc_url($footer_contact_url); ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.7 3.8 9.4 8 7.6 9.8c1.3 2.8 3.7 5.2 6.5 6.5l1.8-1.8 4.2 2.7-.7 3.1c-.2.8-.9 1.3-1.7 1.3C9.3 21.6 2.4 14.7 2.4 6.3c0-.8.5-1.5 1.3-1.7z"></path></svg>
        <span><small>Need help?</small><strong>Contact support</strong></span>
      </a>
      <a class="footer-support-link" href="<?php echo esc_url($footer_contact_url); ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5h16v11H9l-5 3v-14Z"></path><path d="M8 10h8M8 13h5"></path></svg>
        <span><small>Quick question?</small><strong>Chat with us</strong></span>
      </a>
      <a class="footer-back-top" href="#page-top"><span aria-hidden="true">↑</span> Back to top</a>
    </div>
  </section>

  <div class="footer-main"><div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo wp_kses_post(puppy_market_brand_markup()); ?></a>
        <?php if ($footer_tagline) : ?><p><?php echo esc_html($footer_tagline); ?></p><?php endif; ?>
      </div>
      <div><h3>Shop</h3>
        <?php if (has_nav_menu('footer_shop')) : ?>
          <?php wp_nav_menu(array('theme_location' => 'footer_shop', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false)); ?>
        <?php else : ?>
          <ul class="footer-menu"><li><a href="<?php echo esc_url($footer_shop_url); ?>">Shop all</a></li><li><a href="<?php echo esc_url(puppy_market_category_link('dog-food')); ?>">Dog essentials</a></li><li><a href="<?php echo esc_url(puppy_market_category_link('cat-food')); ?>">Cat essentials</a></li><li><a href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Sale</a></li></ul>
        <?php endif; ?>
      </div>
      <div><h3>Help</h3>
        <?php if (has_nav_menu('footer_help')) : ?>
          <?php wp_nav_menu(array('theme_location' => 'footer_help', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false)); ?>
        <?php else : ?>
          <ul class="footer-menu"><li><a href="<?php echo esc_url($footer_returns_url); ?>">Returns &amp; support</a></li><li><a href="<?php echo esc_url($footer_account_url); ?>">My account</a></li></ul>
        <?php endif; ?>
      </div>
      <div><h3>About</h3>
        <?php if (has_nav_menu('footer_about')) : ?>
          <?php wp_nav_menu(array('theme_location' => 'footer_about', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false)); ?>
        <?php else : ?>
          <ul class="footer-menu"><li><a href="<?php echo esc_url($footer_about_url); ?>">About us</a></li><li><a href="<?php echo esc_url($footer_privacy_url); ?>">Privacy</a></li><li><a href="<?php echo esc_url($footer_contact_url); ?>">Customer care</a></li></ul>
        <?php endif; ?>
      </div>
      <div class="footer-connect"><h3><?php echo esc_html($footer_social_title); ?></h3><p><?php echo esc_html($footer_social_description); ?></p>
        <div class="footer-socials" aria-label="Social media">
          <?php foreach ($footer_social_items as $footer_social_network => $footer_social_item) : ?>
            <?php if ($footer_social_item['url'] !== '') : ?>
              <a class="footer-social footer-social-<?php echo esc_attr($footer_social_network); ?>" href="<?php echo esc_url($footer_social_item['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($footer_social_item['label']); ?>"><?php echo $footer_social_item['icon']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted fixed theme SVG. */ ?></a>
            <?php else : ?>
              <span class="footer-social footer-social-<?php echo esc_attr($footer_social_network); ?> is-disabled" role="img" aria-label="<?php echo esc_attr($footer_social_item['label'] . ' link not configured'); ?>"><?php echo $footer_social_item['icon']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted fixed theme SVG. */ ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <nav class="footer-quick-links" aria-label="Footer links">
      <span aria-label="Country">🌐 United States</span><a href="<?php echo esc_url($footer_about_url); ?>">About</a><a href="<?php echo esc_url($footer_returns_url); ?>">Returns &amp; support</a><a href="<?php echo esc_url($footer_privacy_url); ?>">Privacy</a><a href="<?php echo esc_url($footer_shop_url); ?>">Shop all</a>
    </nav>

    <div class="footer-reassurance" aria-label="Shopping reassurance">
      <div><span aria-hidden="true">✓</span><strong>Secure checkout</strong><small>Protected payments</small></div>
      <div><span aria-hidden="true">↗</span><strong>Fast shipping</strong><small>Free over $75</small></div>
      <div><span aria-hidden="true">↩</span><strong>Easy returns</strong><small>Simple and convenient</small></div>
      <?php $footer_payment_methods = puppy_market_payment_methods(); ?>
      <?php if (!empty($footer_payment_methods)) : ?>
        <div class="footer-payments"><strong>Ways to pay</strong><?php foreach ($footer_payment_methods as $footer_payment_method) : ?><span><?php echo esc_html($footer_payment_method); ?></span><?php endforeach; ?></div>
      <?php endif; ?>
    </div>

    <?php if ($footer_copyright_text !== '' || $footer_credit_text !== '') : ?>
      <div class="footer-bottom">
        <?php if ($footer_copyright_text !== '') : ?><span><?php echo esc_html($footer_copyright_text); ?></span><?php endif; ?>
        <?php if ($footer_credit_text !== '') : ?><span><?php echo esc_html($footer_credit_text); ?></span><?php endif; ?>
      </div>
    <?php endif; ?>
  </div></div>
</footer>
<?php if (class_exists('WooCommerce')) : ?>
  <div class="ipet-cart-drawer-shell" data-cart-drawer aria-hidden="true">
    <button class="ipet-cart-drawer-overlay" type="button" data-cart-drawer-close tabindex="-1" aria-label="Close cart panel"></button>
    <aside class="ipet-cart-drawer" role="dialog" aria-modal="true" aria-labelledby="ipet-cart-drawer-title" aria-busy="false">
      <header class="ipet-cart-drawer-header">
        <h2 id="ipet-cart-drawer-title"><span aria-hidden="true">✓</span> Added to Cart</h2>
        <button class="ipet-cart-drawer-close" type="button" data-cart-drawer-close data-cart-drawer-close-button aria-label="Close cart panel"><span aria-hidden="true">×</span></button>
      </header>
      <div class="ipet-cart-drawer-body">
        <a class="ipet-cart-drawer-item" data-cart-drawer-item-link href="<?php echo esc_url($footer_cart_url); ?>">
          <span class="ipet-cart-drawer-image"><img data-cart-drawer-image src="<?php echo esc_url(function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_thumbnail') : ''); ?>" alt=""></span>
          <span class="ipet-cart-drawer-item-copy"><strong data-cart-drawer-name>Item added to your cart</strong><span data-cart-drawer-meta>Updating cart details…</span></span>
        </a>
        <section class="ipet-cart-drawer-summary" aria-label="Cart summary">
          <p class="ipet-cart-drawer-shipping"><strong data-cart-drawer-shipping-amount>Checking your cart…</strong><span data-cart-drawer-shipping-copy></span></p>
          <div class="ipet-cart-drawer-progress" data-cart-drawer-progress role="progressbar" aria-label="Free shipping progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span></div>
          <div class="ipet-cart-drawer-subtotal"><strong data-cart-drawer-subtotal-label>Subtotal:</strong><span data-cart-drawer-subtotal>—</span></div>
        </section>
        <div class="ipet-cart-drawer-actions">
          <a class="ipet-cart-drawer-view" href="<?php echo esc_url($footer_cart_url); ?>">View Cart</a>
          <a class="ipet-cart-drawer-checkout" href="<?php echo esc_url($footer_checkout_url); ?>">Proceed to Checkout</a>
        </div>
      </div>
    </aside>
  </div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Account/cart hover dropdowns only work with a mouse; on touch screens
  // ":hover" never fires, so give the first tap a toggle instead of only
  // following the link straight through.
  document.querySelectorAll('.header-menu').forEach(function (menu) {
    var trigger = menu.querySelector('.header-menu-trigger');
    if (!trigger) return;
    trigger.addEventListener('click', function (event) {
      if (window.matchMedia('(hover: hover)').matches) return;
      if (!menu.classList.contains('is-open')) {
        event.preventDefault();
        document.querySelectorAll('.header-menu.is-open').forEach(function (open) { if (open !== menu) open.classList.remove('is-open'); });
        menu.classList.add('is-open');
      }
    });
  });
  document.addEventListener('click', function (event) {
    document.querySelectorAll('.header-menu.is-open').forEach(function (menu) { if (!menu.contains(event.target)) menu.classList.remove('is-open'); });
  });
  if (!document.body.classList.contains('single-product')) return;
  var form = document.querySelector('form.cart');
  var toast = document.querySelector('.ipet-toast');
  var cartCount = document.querySelector('.header-actions .cart-count');
  if (!form || !toast) return;
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (form.classList.contains('is-loading')) return;
    form.classList.add('is-loading');
    var button = form.querySelector('.single_add_to_cart_button');
    if (button) { button.disabled = true; button.textContent = 'Adding…'; }
    var payload = new URLSearchParams(new FormData(form));
    // WooCommerce stores the product ID on the submit button, but
    // FormData(form) does not include submit-button values.
    var productId = button && (button.value || button.getAttribute('value'));
    if (!productId) {
      var addToCartField = form.querySelector('[name="add-to-cart"]');
      productId = addToCartField && addToCartField.value;
    }
    if (productId) {
      // wc-ajax only needs product_id. Keeping add-to-cart here also
      // activates WooCommerce's classic form handler, which can add the
      // item twice and queue its default success notice.
      payload.set('product_id', productId);
      payload.delete('add-to-cart');
    }
    var endpoint = window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url
      ? window.wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
      : window.location.origin + '/?wc-ajax=add_to_cart';
    function showAddToCartError() {
      toast.textContent = 'Could not add item. Please try again.';
      toast.classList.add('is-error', 'is-visible');
      window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600);
    }
    var addToCartConfirmed = false;
    fetch(endpoint, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: payload.toString() })
      .then(function (response) {
        if (!response.ok) throw new Error('add-to-cart-request-failed');
        return response.json();
      })
      .then(function (data) {
        if (!data || data.error) {
          showAddToCartError();
          return;
        }
        addToCartConfirmed = true;
        toast.classList.remove('is-error', 'is-visible');
        try {
          var quantity = parseInt(payload.get('quantity') || '1', 10);
          if (cartCount) { var current = parseInt(cartCount.textContent, 10) || 0; cartCount.textContent = current + quantity; }
          // Reuse the same success drawer opened by homepage product cards.
          if (window.jQuery) window.jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, button]);
        } catch (interfaceError) {
          if (window.console && window.console.error) window.console.error('Cart updated, but the confirmation interface failed.', interfaceError);
        }
      })
      .catch(function () {
        if (!addToCartConfirmed) showAddToCartError();
      })
      .finally(function () {
        form.classList.remove('is-loading');
        if (button) { button.disabled = false; button.textContent = 'Add to cart'; }
      });
  });
});
</script>
<?php wp_footer(); ?>
</body>
</html>
