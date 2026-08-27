(function ($) {
  'use strict';

  var config = window.puppyCartDrawerData || {};
  var shell;
  var panel;
  var closeButton;
  var lastFocus;
  var activeRequest;

  function normalizeTrigger(trigger) {
    if (!trigger) return null;
    if (trigger.jquery) return trigger.get(0);
    return trigger.nodeType === 1 ? trigger : null;
  }

  function getProductId(trigger) {
    if (!trigger) return '';

    var form = trigger.closest && trigger.closest('form.cart');
    var variation = form && form.querySelector('input[name="variation_id"]');
    if (variation && parseInt(variation.value, 10) > 0) return variation.value;

    return trigger.getAttribute('data-product-id') ||
      trigger.value ||
      (form && form.querySelector('[name="add-to-cart"]') && form.querySelector('[name="add-to-cart"]').value) ||
      '';
  }

  function setHtml(selector, value) {
    var element = shell.querySelector(selector);
    if (element) element.innerHTML = value || '';
  }

  function setText(selector, value) {
    var element = shell.querySelector(selector);
    if (element) element.textContent = value || '';
  }

  function primeItem(trigger) {
    var card = trigger && trigger.closest && trigger.closest('.product, .ipet-pdp');
    var image = card && card.querySelector('.product-image img, [data-pdp-main-image]');
    var title = card && card.querySelector('h3 a, [data-pdp-title], h1');
    var drawerImage = shell.querySelector('[data-cart-drawer-image]');

    if (image && drawerImage) {
      drawerImage.src = image.currentSrc || image.src;
      drawerImage.alt = image.alt || '';
    }
    if (title) setText('[data-cart-drawer-name]', title.textContent.trim());
    setText('[data-cart-drawer-meta]', 'Updating cart details…');
  }

  function updateCartCounts(count) {
    document.querySelectorAll('.cart-count').forEach(function (element) {
      element.textContent = String(count);
    });
  }

  function removeInjectedViewCart(trigger) {
    var card = trigger && trigger.closest && trigger.closest('.product, li.product');
    var scope = card || document;
    scope.querySelectorAll('.added_to_cart.wc-forward').forEach(function (link) {
      link.remove();
    });
  }

  function render(data) {
    if (!data) throw new Error('missing-cart-data');

    var item = data.item || {};
    var shipping = data.shipping || {};
    var itemLink = shell.querySelector('[data-cart-drawer-item-link]');
    var drawerImage = shell.querySelector('[data-cart-drawer-image]');
    var progress = shell.querySelector('[data-cart-drawer-progress]');
    var progressBar = progress && progress.querySelector('span');
    var count = parseInt(data.count, 10) || 0;

    if (itemLink && item.url) itemLink.href = item.url;
    if (drawerImage && item.image_url) {
      drawerImage.src = item.image_url;
      drawerImage.alt = item.name || '';
    }
    setText('[data-cart-drawer-name]', item.name || 'Item added to your cart');
    setHtml('[data-cart-drawer-meta]', item.meta_html || '');

    if (shipping.qualified) {
      setText('[data-cart-drawer-shipping-amount]', 'FREE shipping unlocked');
      setText('[data-cart-drawer-shipping-copy]', '');
    } else {
      setHtml('[data-cart-drawer-shipping-amount]', shipping.remaining_html || '');
      setText('[data-cart-drawer-shipping-copy]', ' until FREE shipping');
    }

    if (progress && progressBar) {
      var percentage = Math.max(0, Math.min(100, parseFloat(shipping.progress) || 0));
      progressBar.style.width = percentage + '%';
      progress.setAttribute('aria-valuenow', String(Math.round(percentage)));
    }

    setText('[data-cart-drawer-subtotal-label]', 'Subtotal (' + count + ' ' + (count === 1 ? 'item' : 'items') + '):');
    setHtml('[data-cart-drawer-subtotal]', data.subtotal_html || '');
    updateCartCounts(count);
  }

  function openDrawer(trigger) {
    if (!shell) return;

    lastFocus = trigger || document.activeElement;
    removeInjectedViewCart(trigger);
    window.setTimeout(function () { removeInjectedViewCart(trigger); }, 0);
    window.setTimeout(function () { removeInjectedViewCart(trigger); }, 200);
    primeItem(trigger);
    shell.classList.add('is-open', 'is-loading');
    shell.setAttribute('aria-hidden', 'false');
    panel.setAttribute('aria-busy', 'true');
    document.body.classList.add('ipet-cart-drawer-open');
    window.setTimeout(function () { closeButton.focus(); }, 80);

    if (activeRequest) activeRequest.abort();
    activeRequest = typeof AbortController !== 'undefined' ? new AbortController() : null;

    var url = new URL(config.ajaxUrl || '/wp-admin/admin-ajax.php', window.location.href);
    url.searchParams.set('action', config.action || 'puppy_market_cart_drawer');
    var productId = getProductId(trigger);
    if (productId) url.searchParams.set('product_id', productId);

    fetch(url.toString(), {
      credentials: 'same-origin',
      signal: activeRequest ? activeRequest.signal : undefined
    })
      .then(function (response) {
        if (!response.ok) throw new Error('cart-drawer-request-failed');
        return response.json();
      })
      .then(function (response) {
        if (!response || !response.success) throw new Error('cart-drawer-data-failed');
        render(response.data);
      })
      .catch(function (error) {
        if (error && error.name === 'AbortError') return;
        setText('[data-cart-drawer-meta]', 'Your cart was updated. View your cart for full details.');
      })
      .finally(function () {
        shell.classList.remove('is-loading');
        panel.setAttribute('aria-busy', 'false');
      });
  }

  function closeDrawer() {
    if (!shell || !shell.classList.contains('is-open')) return;
    if (activeRequest) activeRequest.abort();
    shell.classList.remove('is-open', 'is-loading');
    shell.setAttribute('aria-hidden', 'true');
    panel.setAttribute('aria-busy', 'false');
    document.body.classList.remove('ipet-cart-drawer-open');
    if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus({ preventScroll: true });
  }

  function handleKeyboard(event) {
    if (!shell || !shell.classList.contains('is-open')) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      closeDrawer();
      return;
    }
    if (event.key !== 'Tab') return;

    var focusable = Array.prototype.slice.call(panel.querySelectorAll('button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])'));
    if (!focusable.length) return;
    var first = focusable[0];
    var last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  function init() {
    shell = document.querySelector('[data-cart-drawer]');
    if (!shell) return;
    panel = shell.querySelector('.ipet-cart-drawer');
    closeButton = shell.querySelector('[data-cart-drawer-close-button]');

    shell.querySelectorAll('[data-cart-drawer-close]').forEach(function (button) {
      button.addEventListener('click', closeDrawer);
    });
    document.addEventListener('keydown', handleKeyboard);

    $(document.body).on('added_to_cart.ipetCartDrawer', function (event, fragments, cartHash, trigger) {
      openDrawer(normalizeTrigger(trigger));
    });
  }

  if (document.readyState !== 'loading') init();
  else document.addEventListener('DOMContentLoaded', init);
}(jQuery));
