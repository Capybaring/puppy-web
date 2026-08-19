(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.shop-shell .product-ajax-cart').forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        if (button.classList.contains('is-loading')) return;

        var originalLabel = button.textContent;
        var payload = new URLSearchParams({
          product_id: button.getAttribute('data-product-id'),
          quantity: '1'
        });
        button.classList.add('is-loading');
        button.setAttribute('aria-busy', 'true');
        button.textContent = 'Adding…';

        fetch(button.getAttribute('data-add-to-cart-url'), {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: payload.toString()
        })
          .then(function (response) { return response.json(); })
          .then(function (data) {
            if (!data || data.error) throw new Error('add-to-cart-failed');
            button.textContent = 'Added to cart';
            if (window.jQuery) {
              window.jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, button]);
            }
          })
          .catch(function () { button.textContent = 'Could not add'; })
          .finally(function () {
            window.setTimeout(function () {
              button.textContent = originalLabel;
              button.classList.remove('is-loading');
              button.removeAttribute('aria-busy');
            }, 1400);
          });
      });
    });
  });
}());
