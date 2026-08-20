(function () {
  'use strict';

  function showToast(message, isError) {
    var toast = document.querySelector('.ipet-toast');
    if (!toast) return;

    toast.textContent = message;
    toast.classList.toggle('is-error', Boolean(isError));
    toast.classList.add('is-visible');

    window.clearTimeout(window.puppySearchToastTimer);
    window.puppySearchToastTimer = window.setTimeout(function () {
      toast.classList.remove('is-visible');
      toast.classList.remove('is-error');
    }, 2600);
  }

  function updateCartCount() {
    document.querySelectorAll('.cart-count').forEach(function (count) {
      var current = parseInt(count.textContent, 10);
      count.textContent = String((Number.isFinite(current) ? current : 0) + 1);
    });
  }

  document.addEventListener('click', function (event) {
    var button = event.target.closest('.puppy-search-results .product-ajax-cart');
    if (!button) return;

    event.preventDefault();
    if (button.classList.contains('is-loading')) return;

    var endpoint = button.getAttribute('data-add-to-cart-url');
    var productId = button.getAttribute('data-product-id');
    if (!endpoint || !productId) {
      window.location.href = button.href;
      return;
    }

    var originalText = button.textContent;
    button.classList.add('is-loading');
    button.setAttribute('aria-disabled', 'true');
    button.textContent = 'Adding…';

    var body = new URLSearchParams({
      product_id: productId,
      quantity: '1'
    });

    fetch(endpoint, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString()
    })
      .then(function (response) {
        if (!response.ok) throw new Error('request-failed');
        return response.json();
      })
      .then(function (data) {
        if (!data || data.error) throw new Error('add-to-cart-failed');

        button.textContent = 'Added to cart';
        updateCartCount();
        showToast('Added to cart', false);

        if (window.jQuery) {
          window.jQuery(document.body).trigger('added_to_cart', [
            data.fragments || {},
            data.cart_hash || '',
            window.jQuery(button)
          ]);
        }
      })
      .catch(function () {
        button.textContent = 'Could not add';
        showToast('Could not add item. Please try again.', true);
      })
      .finally(function () {
        window.setTimeout(function () {
          button.textContent = originalText;
          button.classList.remove('is-loading');
          button.removeAttribute('aria-disabled');
        }, 1600);
      });
  });
}());

