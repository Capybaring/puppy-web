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

  function createSuggestionIcon(type) {
    var namespace = 'http://www.w3.org/2000/svg';
    var svg = document.createElementNS(namespace, 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('focusable', 'false');

    if (type === 'article') {
      var leftPage = document.createElementNS(namespace, 'path');
      var rightPage = document.createElementNS(namespace, 'path');
      leftPage.setAttribute('d', 'M3.5 5.5c3.2-1.1 5.8-.5 8.5 1.4v12c-2.7-1.9-5.3-2.5-8.5-1.4z');
      rightPage.setAttribute('d', 'M20.5 5.5c-3.2-1.1-5.8-.5-8.5 1.4v12c2.7-1.9 5.3-2.5 8.5-1.4z');
      svg.appendChild(leftPage);
      svg.appendChild(rightPage);
      return svg;
    }

    var circle = document.createElementNS(namespace, 'circle');
    var handle = document.createElementNS(namespace, 'path');
    circle.setAttribute('cx', '10.5');
    circle.setAttribute('cy', '10.5');
    circle.setAttribute('r', '6.5');
    handle.setAttribute('d', 'm15.3 15.3 4.5 4.5');
    svg.appendChild(circle);
    svg.appendChild(handle);
    return svg;
  }

  function appendSuggestionLabel(container, label, query, emphasizeCompletion) {
    var normalizedLabel = label.toLocaleLowerCase();
    var normalizedQuery = query.toLocaleLowerCase();

    if (emphasizeCompletion && normalizedLabel.indexOf(normalizedQuery) === 0 && label.length > query.length) {
      container.appendChild(document.createTextNode(label.slice(0, query.length)));
      var strong = document.createElement('strong');
      strong.textContent = label.slice(query.length);
      container.appendChild(strong);
      return;
    }

    container.textContent = label;
  }

  function setupSearchSuggestions() {
    var form = document.querySelector('[data-puppy-search]');
    var config = window.puppySearchData || {};
    if (!form || !config.ajaxUrl) return;

    var input = form.querySelector('input[type="search"][name="s"]');
    var panel = form.querySelector('.puppy-search-suggest-panel');
    var list = form.querySelector('[data-search-suggestion-list]');
    if (!input || !panel || !list) return;

    var timer = 0;
    var request = null;
    var activeIndex = -1;

    function options() {
      return Array.prototype.slice.call(list.querySelectorAll('[role="option"]'));
    }

    function closePanel() {
      panel.hidden = true;
      input.setAttribute('aria-expanded', 'false');
      activeIndex = -1;
      options().forEach(function (option) {
        option.classList.remove('is-active');
        option.setAttribute('aria-selected', 'false');
      });
    }

    function setActive(index) {
      var items = options();
      if (!items.length) return;

      activeIndex = (index + items.length) % items.length;
      items.forEach(function (item, itemIndex) {
        var selected = itemIndex === activeIndex;
        item.classList.toggle('is-active', selected);
        item.setAttribute('aria-selected', selected ? 'true' : 'false');
      });
      items[activeIndex].scrollIntoView({ block: 'nearest' });
    }

    function appendGroup(items, type, query) {
      if (!Array.isArray(items) || !items.length) return;

      var group = document.createElement('div');
      group.className = 'puppy-search-suggest-group puppy-search-suggest-group-' + type;

      items.forEach(function (item) {
        if (!item || !item.label || !item.url) return;

        var link = document.createElement('a');
        var icon = document.createElement('span');
        var label = document.createElement('span');

        link.className = 'puppy-search-suggest-item';
        link.href = item.url;
        link.setAttribute('role', 'option');
        link.setAttribute('aria-selected', 'false');
        icon.className = 'puppy-search-suggest-icon';
        label.className = 'puppy-search-suggest-label';

        icon.appendChild(createSuggestionIcon(type));
        appendSuggestionLabel(label, item.label, query, type === 'search');
        link.appendChild(icon);
        link.appendChild(label);
        group.appendChild(link);
      });

      if (group.children.length) list.appendChild(group);
    }

    function render(data, query) {
      list.textContent = '';
      appendGroup(data.suggestions, 'search', query);
      appendGroup(data.articles, 'article', query);

      if (!options().length) {
        closePanel();
        return;
      }

      activeIndex = -1;
      panel.hidden = false;
      input.setAttribute('aria-expanded', 'true');
    }

    function loadSuggestions() {
      var query = input.value.trim();
      var minimum = Number(config.minChars) || 2;
      window.clearTimeout(timer);

      if (query.length < minimum) {
        if (request) request.abort();
        closePanel();
        return;
      }

      timer = window.setTimeout(function () {
        if (request) request.abort();
        request = typeof AbortController === 'function' ? new AbortController() : null;

        var parameters = new URLSearchParams({
          action: config.action || 'puppy_market_search_suggestions',
          term: query
        });

        fetch(config.ajaxUrl + '?' + parameters.toString(), {
          credentials: 'same-origin',
          signal: request ? request.signal : undefined
        })
          .then(function (response) {
            if (!response.ok) throw new Error('suggestions-failed');
            return response.json();
          })
          .then(function (response) {
            if (!response || !response.success || input.value.trim() !== query) return;
            render(response.data || {}, query);
          })
          .catch(function (error) {
            if (!error || error.name !== 'AbortError') closePanel();
          });
      }, 180);
    }

    input.addEventListener('input', loadSuggestions);
    input.addEventListener('focus', function () {
      if (input.value.trim().length >= (Number(config.minChars) || 2)) loadSuggestions();
    });
    input.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closePanel();
        return;
      }

      if (panel.hidden || (event.key !== 'ArrowDown' && event.key !== 'ArrowUp' && event.key !== 'Enter')) return;

      if (event.key === 'ArrowDown') {
        event.preventDefault();
        setActive(activeIndex + 1);
      } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        setActive(activeIndex - 1);
      } else if (event.key === 'Enter' && activeIndex >= 0) {
        event.preventDefault();
        window.location.href = options()[activeIndex].href;
      }
    });

    form.addEventListener('submit', closePanel);
    document.addEventListener('pointerdown', function (event) {
      if (!form.contains(event.target)) closePanel();
    });
  }

  setupSearchSuggestions();

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
