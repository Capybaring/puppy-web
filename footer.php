<?php $shop_url = puppy_market_catalog_url(); ?>
<footer class="site-footer"><div class="container"><div class="footer-grid">
    <div><a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><img class="brand-logo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/ipet-logo.png'); ?>" alt="iPet"></a><p>Thoughtfully chosen essentials to make life with your pets easier and happier.</p></div>
    <div><h3>Shop</h3><p><a href="<?php echo esc_url($shop_url); ?>">Shop the store</a><br><a href="<?php echo esc_url(puppy_market_category_link('dog-food')); ?>">Dog Food</a><br><a href="<?php echo esc_url(puppy_market_category_link('cat-toys')); ?>">Cat Toys</a><br><a href="<?php echo esc_url(puppy_market_category_link('pet-toys')); ?>">Pet Toys</a><br><a href="<?php echo esc_url(puppy_market_catalog_url('new')); ?>">New Arrivals</a><br><a href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Deals</a></p></div>
    <div><h3>Help</h3><p><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact us</a><br><a href="<?php echo esc_url(home_url('/shipping/')); ?>">Shipping</a><br><a href="<?php echo esc_url(home_url('/returns/')); ?>">Returns</a><br><a href="<?php echo esc_url(puppy_market_account_url()); ?>">Track an order</a></p></div>
    <div><h3>About iPet</h3><p><a href="<?php echo esc_url(home_url('/about/')); ?>">About us</a><br><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy policy</a></p></div>
    <div><h3>Stay connected</h3><p>iPet Pet Life<br>Pet care tips every week</p></div>
</div><div class="footer-payments" aria-label="Accepted payment methods"><span>Visa</span><span>Mastercard</span><span>Amex</span><span>PayPal</span><span>Apple Pay</span></div><div class="footer-bottom"><span>© <?php echo date('Y'); ?> iPet Pet Life</span><span>A personal WooCommerce storefront in progress</span></div></div></footer>
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
