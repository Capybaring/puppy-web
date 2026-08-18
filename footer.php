<?php
$footer_tagline = get_theme_mod('puppy_market_footer_tagline', '');
if (!$footer_tagline) $footer_tagline = get_bloginfo('description');
?>
<footer class="site-footer"><div class="container"><div class="footer-grid">
    <div class="footer-brand"><a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo wp_kses_post(puppy_market_brand_markup()); ?></a><?php if ($footer_tagline) : ?><p><?php echo esc_html($footer_tagline); ?></p><?php endif; ?></div>
    <div><h3>Shop</h3><?php wp_nav_menu(array('theme_location' => 'footer_shop', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false)); ?></div>
    <div><h3>Help</h3><?php wp_nav_menu(array('theme_location' => 'footer_help', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false)); ?></div>
    <div><h3>About</h3><?php wp_nav_menu(array('theme_location' => 'footer_about', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false)); ?></div>
</div><div class="footer-bottom"><span>© <?php echo esc_html(date_i18n('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?></span><span>Powered by WordPress and WooCommerce</span></div></div></footer>
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
    var endpoint = window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url
      ? window.wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
      : window.location.origin + '/?wc-ajax=add_to_cart';
    fetch(endpoint, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: payload.toString() })
      .then(function (response) { return response.json(); })
      .then(function (data) {
        if (!data || data.error) throw new Error('add-to-cart-failed');
        var quantity = parseInt(payload.get('quantity') || '1', 10);
        if (cartCount) { var current = parseInt(cartCount.textContent, 10) || 0; cartCount.textContent = current + quantity; }
        toast.textContent = 'Added to cart';
        toast.classList.remove('is-error');
        toast.classList.add('is-visible');
        window.clearTimeout(window.ipetToastTimer);
        window.ipetToastTimer = window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600);
        if (window.jQuery) window.jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, button]);
      })
      .catch(function () {
        toast.textContent = 'Could not add item. Please try again.';
        toast.classList.add('is-error', 'is-visible');
        window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600);
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
