(function ($) {
  'use strict';

  function initPdp() {
    var root = document.querySelector('.ipet-pdp');
    if (!root) return;

    root.querySelectorAll('.ipet-accordion > button').forEach(function (button) {
      button.addEventListener('click', function () {
        var item = button.parentElement;
        var open = item.classList.toggle('is-open');
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        var mark = button.querySelector('span');
        if (mark) mark.textContent = open ? '−' : '+';
      });
    });

    var mainImage = root.querySelector('[data-pdp-main-image]');
    var lightbox = document.querySelector('[data-pdp-lightbox]');
    var lightboxImage = lightbox && lightbox.querySelector('[data-pdp-lightbox-image]');
    root.querySelectorAll('.ipet-gallery-thumb').forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        if (mainImage) {
          mainImage.src = thumb.getAttribute('data-display');
          mainImage.removeAttribute('srcset');
          mainImage.setAttribute('data-full', thumb.getAttribute('data-full'));
        }
        root.querySelectorAll('.ipet-gallery-thumb').forEach(function (item) {
          item.classList.remove('is-active');
          item.setAttribute('aria-selected', 'false');
        });
        thumb.classList.add('is-active');
        thumb.setAttribute('aria-selected', 'true');
      });
    });

    var zoomButton = root.querySelector('[data-pdp-zoom]');
    if (zoomButton && lightbox) {
      zoomButton.addEventListener('click', function () {
        if (lightboxImage && mainImage) lightboxImage.src = mainImage.getAttribute('data-full') || mainImage.src;
        if (typeof lightbox.showModal === 'function') lightbox.showModal();
      });
      lightbox.querySelector('[data-pdp-lightbox-close]').addEventListener('click', function () { lightbox.close(); });
      lightbox.addEventListener('click', function (event) { if (event.target === lightbox) lightbox.close(); });
    }

    var wishlist = root.querySelector('[data-pdp-wishlist]');
    if (wishlist) {
      var wishlistKey = 'ipet-wishlist-' + (root.id || 'product');
      var saved = window.localStorage && localStorage.getItem(wishlistKey) === '1';
      wishlist.classList.toggle('is-active', saved);
      wishlist.setAttribute('aria-pressed', saved ? 'true' : 'false');
      wishlist.addEventListener('click', function () {
        saved = !saved;
        wishlist.classList.toggle('is-active', saved);
        wishlist.setAttribute('aria-pressed', saved ? 'true' : 'false');
        if (window.localStorage) localStorage.setItem(wishlistKey, saved ? '1' : '0');
      });
    }

    var variationForm = root.querySelector('form.variations_form');
    function syncOptionButtons() {
      root.querySelectorAll('[data-pdp-attribute]').forEach(function (button) {
        var select = variationForm && variationForm.querySelector('select[name="' + button.getAttribute('data-pdp-attribute') + '"]');
        if (!select) return;
        var option = Array.prototype.find.call(select.options, function (item) { return item.value === button.getAttribute('data-pdp-value'); });
        var selected = select.value === button.getAttribute('data-pdp-value');
        button.classList.toggle('is-selected', selected);
        button.classList.toggle('is-disabled', !!option && option.disabled);
        button.disabled = !!option && option.disabled;
        button.setAttribute('aria-pressed', selected ? 'true' : 'false');
      });
    }

    root.querySelectorAll('[data-pdp-attribute]').forEach(function (button) {
      button.addEventListener('click', function () {
        if (!variationForm) {
          button.parentElement.querySelectorAll('[data-pdp-attribute]').forEach(function (item) {
            item.classList.toggle('is-selected', item === button);
            item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
          });
          return;
        }
        var select = variationForm.querySelector('select[name="' + button.getAttribute('data-pdp-attribute') + '"]');
        if (!select) return;
        select.value = button.getAttribute('data-pdp-value');
        $(select).trigger('change');
        syncOptionButtons();
      });
    });

    if (variationForm) {
      $(variationForm).on('change', 'select', syncOptionButtons);
      $(variationForm).on('found_variation', function (event, variation) {
        var price = root.querySelector('[data-pdp-price]');
        var mobilePrice = root.querySelector('[data-pdp-mobile-price]');
        var stock = root.querySelector('[data-pdp-stock]');
        if (price && variation.price_html) price.innerHTML = variation.price_html;
        if (mobilePrice && variation.price_html) mobilePrice.innerHTML = variation.price_html;
        if (stock) {
          stock.textContent = variation.is_in_stock ? 'In stock' : 'Out of stock';
          stock.classList.toggle('is-in-stock', variation.is_in_stock);
          stock.classList.toggle('is-out-of-stock', !variation.is_in_stock);
        }
        syncOptionButtons();
      });
      syncOptionButtons();
    }

    var cartForm = root.querySelector('.ipet-pdp-cart-form form.cart');
    var addButton = cartForm && cartForm.querySelector('.single_add_to_cart_button');
    var mobileBar = root.querySelector('.ipet-pdp-mobile-cart');
    var mobileAdd = root.querySelector('[data-pdp-mobile-add]');
    if (mobileAdd && addButton) mobileAdd.addEventListener('click', function () { addButton.click(); });
    if (addButton && mobileBar && 'IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        mobileBar.classList.toggle('is-visible', !entries[0].isIntersecting);
        mobileBar.setAttribute('aria-hidden', entries[0].isIntersecting ? 'true' : 'false');
      }, { threshold: 0.1 }).observe(addButton);
    }
    if (cartForm && addButton) {
      cartForm.addEventListener('submit', function () {
        if (addButton.classList.contains('disabled')) return;
        addButton.dataset.originalText = addButton.textContent;
        addButton.textContent = 'Adding…';
        addButton.classList.add('is-loading');
      });
    }
  }

  if (document.readyState !== 'loading') initPdp();
  else document.addEventListener('DOMContentLoaded', initPdp);
})(jQuery);
