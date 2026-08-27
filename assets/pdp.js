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

    var gallery = root.querySelector('[data-pdp-gallery]');
    var galleryStage = root.querySelector('[data-pdp-gallery-stage]');
    var mainImage = root.querySelector('[data-pdp-main-image]');
    var lightbox = document.querySelector('[data-pdp-lightbox]');
    var lightboxImage = lightbox && lightbox.querySelector('[data-pdp-lightbox-image]');
    var galleryThumbs = Array.prototype.slice.call(root.querySelectorAll('.ipet-gallery-thumb'));
    var galleryPrev = root.querySelector('[data-pdp-gallery-prev]');
    var galleryNext = root.querySelector('[data-pdp-gallery-next]');
    var galleryCount = root.querySelector('[data-pdp-gallery-count]');
    var currentImageIndex = Math.max(0, galleryThumbs.findIndex(function (thumb) { return thumb.classList.contains('is-active'); }));
    var suppressZoomUntil = 0;

    function showGalleryImage(index, focusThumb) {
      if (!galleryThumbs.length || !mainImage) return;
      currentImageIndex = (index + galleryThumbs.length) % galleryThumbs.length;
      var thumb = galleryThumbs[currentImageIndex];
      mainImage.classList.add('is-switching');
      mainImage.src = thumb.getAttribute('data-display');
      mainImage.removeAttribute('srcset');
      mainImage.removeAttribute('sizes');
      mainImage.alt = thumb.getAttribute('data-alt') || mainImage.alt;
      mainImage.setAttribute('data-full', thumb.getAttribute('data-full'));
      window.requestAnimationFrame(function () { mainImage.classList.remove('is-switching'); });
      galleryThumbs.forEach(function (item, itemIndex) {
        var selected = itemIndex === currentImageIndex;
        item.classList.toggle('is-active', selected);
        item.setAttribute('aria-selected', selected ? 'true' : 'false');
        item.tabIndex = selected ? 0 : -1;
      });
      if (galleryCount) galleryCount.textContent = (currentImageIndex + 1) + ' / ' + galleryThumbs.length;
      thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      if (focusThumb) thumb.focus({ preventScroll: true });
    }

    galleryThumbs.forEach(function (thumb, thumbIndex) {
      thumb.addEventListener('click', function () { showGalleryImage(thumbIndex, false); });
    });
    if (galleryPrev) galleryPrev.addEventListener('click', function () { showGalleryImage(currentImageIndex - 1, false); });
    if (galleryNext) galleryNext.addEventListener('click', function () { showGalleryImage(currentImageIndex + 1, false); });
    if (gallery && galleryThumbs.length > 1) {
      gallery.addEventListener('keydown', function (event) {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        event.preventDefault();
        showGalleryImage(currentImageIndex + (event.key === 'ArrowRight' ? 1 : -1), true);
      });
    }
    if (galleryStage && galleryThumbs.length > 1) {
      var pointerStart = null;
      galleryStage.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse' || event.target.closest('.ipet-gallery-arrow')) return;
        pointerStart = { x: event.clientX, y: event.clientY };
      });
      galleryStage.addEventListener('pointerup', function (event) {
        if (!pointerStart) return;
        var deltaX = event.clientX - pointerStart.x;
        var deltaY = event.clientY - pointerStart.y;
        pointerStart = null;
        if (Math.abs(deltaX) < 45 || Math.abs(deltaX) <= Math.abs(deltaY) * 1.2) return;
        suppressZoomUntil = Date.now() + 300;
        showGalleryImage(currentImageIndex + (deltaX < 0 ? 1 : -1), false);
      });
      galleryStage.addEventListener('pointercancel', function () { pointerStart = null; });
    }

    var zoomButton = root.querySelector('[data-pdp-zoom]');
    if (zoomButton && lightbox) {
      zoomButton.addEventListener('click', function (event) {
        if (Date.now() < suppressZoomUntil) { event.preventDefault(); return; }
        if (lightboxImage && mainImage) lightboxImage.src = mainImage.getAttribute('data-full') || mainImage.src;
        if (typeof lightbox.showModal === 'function') lightbox.showModal();
      });
      lightbox.querySelector('[data-pdp-lightbox-close]').addEventListener('click', function () { lightbox.close(); });
      lightbox.addEventListener('click', function (event) { if (event.target === lightbox) lightbox.close(); });
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
