<?php get_header(); $shop_url = puppy_market_catalog_url(); ?>
<main id="main-content">
    <section class="section sequence-promo sequence-promo-1"><div class="container"><div class="sequence-card"><h2>Free shipping on orders over $75.</h2></div></div></section>
    <section class="home-carousel" aria-label="iPet featured picks"><div class="container"><div class="carousel-frame"><article class="carousel-slide is-active"><div><p class="eyebrow">Everyday favorites</p><h2>Just the right kind of company, every day.</h2><p>Find food, toys and care essentials for every pet and every routine.</p><a class="button" href="<?php echo esc_url($shop_url); ?>">Start shopping</a></div></article><article class="carousel-slide"><div><p class="eyebrow">For dogs</p><h2>Good days start with a good meal.</h2><p>Explore dog food, rewarding treats and everyday walking essentials.</p><a class="button" href="<?php echo esc_url(puppy_market_category_link('dog-food')); ?>">Shop dog favorites</a></div></article><article class="carousel-slide"><div><p class="eyebrow">For cats</p><h2>Save the curiosity for more playful moments.</h2><p>Discover cat toys, interactive gear and cozy everyday comforts.</p><a class="button" href="<?php echo esc_url(puppy_market_category_link('cat-toys')); ?>">Shop cat favorites</a></div></article><button class="carousel-arrow carousel-prev" type="button" aria-label="Previous slide">‹</button><button class="carousel-arrow carousel-next" type="button" aria-label="Next slide">›</button><div class="carousel-dots"><button type="button" class="is-active" aria-label="Slide 1"></button><button type="button" aria-label="Slide 2"></button><button type="button" aria-label="Slide 3"></button></div></div></div></section>
    <section class="section value-strip" aria-label="iPet member perks and services"><div class="container"><div class="value-strip-inner"><div class="value-strip-membership"><strong>iPet member perks</strong><p>Free shipping on every order, plus rewards on repeat purchases.</p><a class="button button-light" href="<?php echo esc_url($shop_url); ?>">Learn more</a></div><div class="value-strip-services"><a href="<?php echo esc_url(home_url('/contact/')); ?>"><span aria-hidden="true">☎</span><strong>24/7 customer care</strong></a><a href="<?php echo esc_url(home_url('/contact/')); ?>"><span aria-hidden="true">💬</span><strong>Chat with our pet care team</strong></a><a href="<?php echo esc_url(home_url('/shipping/')); ?>"><span aria-hidden="true">🚚</span><strong>Fast, reliable shipping</strong></a><a href="<?php echo esc_url(home_url('/returns/')); ?>"><span aria-hidden="true">↩</span><strong>365-day easy returns</strong></a></div></div></div></section>
    <section class="section shop-by-pet" id="categories"><div class="container"><div class="section-heading"><h2>Who are you shopping for?</h2></div><div class="pet-strip"><a href="<?php echo esc_url(puppy_market_category_link('dog')); ?>"><span class="pet-image pet-image-1" aria-hidden="true"></span><strong>Dogs</strong></a><a href="<?php echo esc_url(puppy_market_category_link('cat')); ?>"><span class="pet-image pet-image-2" aria-hidden="true"></span><strong>Cats</strong></a><a href="<?php echo esc_url(puppy_market_category_link('small-pets')); ?>"><span class="pet-image pet-image-3" aria-hidden="true"></span><strong>Small Pets</strong></a><a href="<?php echo esc_url(puppy_market_category_link('birds')); ?>"><span class="pet-image pet-image-4" aria-hidden="true"></span><strong>Birds</strong></a><a href="<?php echo esc_url(puppy_market_category_link('aquarium')); ?>"><span class="pet-image pet-image-5" aria-hidden="true"></span><strong>Fish</strong></a><a href="<?php echo esc_url(puppy_market_category_link('reptiles')); ?>"><span class="pet-image pet-image-6" aria-hidden="true"></span><strong>Reptiles</strong></a><a href="<?php echo esc_url(puppy_market_category_link('small-pets')); ?>"><span class="pet-image pet-image-7" aria-hidden="true"></span><strong>Rabbits &amp; Hamsters</strong></a></div></div></section>
    <section class="section quick-links" aria-label="Popular searches"><div class="container"><div class="quick-links-row"><a href="<?php echo esc_url(puppy_market_category_link('dog-food')); ?>">Dog Food</a><a href="<?php echo esc_url(puppy_market_category_link('dog-treats')); ?>">Dog Treats</a><a href="<?php echo esc_url(puppy_market_category_link('cat-food')); ?>">Cat Food</a><a href="<?php echo esc_url(puppy_market_category_link('cat-litter')); ?>">Cat Litter</a><a href="<?php echo esc_url(puppy_market_category_link('pet-toys')); ?>">Pet Toys</a><a href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Deals</a></div></div></section>
    <section class="section brand-wall" aria-label="Shop by brand"><div class="container"><div class="section-heading"><h2>Shop by brand</h2></div><div class="brand-wall-row">
        <?php
        $puppy_home_brand_taxonomy = puppy_market_brand_taxonomy();
        $puppy_home_brand_terms = get_terms(array('taxonomy' => $puppy_home_brand_taxonomy, 'hide_empty' => true, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC'));
        if (!is_wp_error($puppy_home_brand_terms) && !empty($puppy_home_brand_terms)) :
            foreach ($puppy_home_brand_terms as $puppy_home_brand_term) :
        ?>
            <a href="<?php echo esc_url(add_query_arg('puppy_brand%5B%5D', $puppy_home_brand_term->slug, $shop_url)); ?>"><?php echo esc_html($puppy_home_brand_term->name); ?></a>
        <?php endforeach; else : foreach (puppy_market_common_brands() as $puppy_home_brand_slug => $puppy_home_brand_name) : ?>
            <a href="<?php echo esc_url(add_query_arg('puppy_brand%5B%5D', $puppy_home_brand_slug, $shop_url)); ?>"><?php echo esc_html($puppy_home_brand_name); ?></a>
        <?php endforeach; endif; ?>
    </div></div></section>
    <section class="trust-strip" aria-label="Shopping reassurance"><div class="container"><div class="trust-strip-grid"><div class="trust-item"><span class="trust-icon" aria-hidden="true">↗</span><div><strong>Free shipping</strong><p>On orders over $75</p></div></div><div class="trust-item"><span class="trust-icon" aria-hidden="true">↩</span><div><strong>365-day returns</strong><p>Easy returns on eligible items</p></div></div><div class="trust-item"><span class="trust-icon" aria-hidden="true">✓</span><div><strong>Secure checkout</strong><p>Your payment is protected</p></div></div><div class="trust-item"><span class="trust-icon" aria-hidden="true">✦</span><div><strong>Pet support</strong><p>Here when you need us</p></div></div></div></div></section>
    <section class="section best-sellers"><div class="container"><div class="section-heading"><h2>Best sellers</h2><a href="<?php echo esc_url($shop_url); ?>">Shop all →</a></div><div class="best-sellers-grid">
        <?php /* orderby is 'date' rather than 'popularity' until there is real order history to rank by — see docs/chewy-alignment-todo.md Phase E. Switch back to 'popularity' once sales data exists. */ ?>
        <?php $puppy_best_sellers = function_exists('wc_get_products') ? wc_get_products(array('status' => 'publish', 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC')) : array(); ?>
        <?php if (!empty($puppy_best_sellers)) : foreach ($puppy_best_sellers as $puppy_best_seller) : ?>
            <?php get_template_part('template-parts/product-card', null, array('product' => $puppy_best_seller, 'badge' => $puppy_best_seller->is_on_sale() ? 'Sale' : 'Best seller', 'card_class' => 'best-seller-card', 'fallback_index' => 1)); ?>
        <?php endforeach; else : ?><article class="best-sellers-empty"><strong>Customer favorites are coming soon.</strong><p>Popular pet essentials will appear here as products are added.</p></article><?php endif; ?>
    </div></div></section>
    <section class="section seasonal-section"><div class="container"><div class="seasonal-banner"><div><p class="eyebrow">A little more joy</p><h2>Spend your energy on the fun stuff.</h2><p>Find toys, cleaning essentials and daily care for happier routines.</p><a class="button" href="<?php echo esc_url(puppy_market_category_link('pet-toys')); ?>">Shop pet toys →</a></div><span>summer</span></div></div></section>
    <section class="section popular-links"><div class="container"><div class="section-heading"><h2>Shop popular categories</h2></div><div class="popular-link-list"><a href="<?php echo esc_url(puppy_market_category_link('dog-food')); ?>"><span class="popular-image popular-image-1" aria-hidden="true"></span><strong>Dog Food</strong></a><a href="<?php echo esc_url(puppy_market_category_link('dog-treats')); ?>"><span class="popular-image popular-image-2" aria-hidden="true"></span><strong>Dog Treats</strong></a><a href="<?php echo esc_url(puppy_market_category_link('cat-toys')); ?>"><span class="popular-image popular-image-3" aria-hidden="true"></span><strong>Cat Toys</strong></a><a href="<?php echo esc_url(puppy_market_category_link('cat-litter')); ?>"><span class="popular-image popular-image-4" aria-hidden="true"></span><strong>Cat Litter &amp; Care</strong></a><a href="<?php echo esc_url(puppy_market_category_link('pet-toys')); ?>"><span class="popular-image popular-image-5" aria-hidden="true"></span><strong>Pet Toys</strong></a><a href="<?php echo esc_url(puppy_market_category_link('pet-care')); ?>"><span class="popular-image popular-image-6" aria-hidden="true"></span><strong>Pet Care</strong></a></div></div></section>
    <section class="section sequence-promo sequence-promo-3"><div class="container"><div class="sequence-card"><div class="sequence-video"><img class="sequence-video-fallback" src="<?php echo esc_url(get_template_directory_uri() . '/assets/ipet-test.gif'); ?>" alt="Pet care in motion"><video autoplay muted loop playsinline preload="metadata" poster="<?php echo esc_url(get_template_directory_uri() . '/assets/carousel-care.png'); ?>" aria-label="Pet care in motion"><source src="<?php echo esc_url(puppy_market_test_video_url()); ?>" type="video/avi"></video></div><div class="sequence-copy"><p class="eyebrow">Everyday care</p><h2>Small routines, happier days.</h2><p>Simple essentials that make pet care feel easier.</p></div></div></div></section>
    <section class="section product-showcase" id="deals"><div class="container"><div class="product-showcase-layout"><aside class="summer-promo-card"><div><p class="eyebrow">Everyday essentials</p><h2>Make every moment together more fun.</h2><p>From daily food to interactive toys, make room for easy, happy routines.</p></div><a class="button" href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Shop more →</a></aside><div class="product-carousel-shell"><div class="product-carousel-viewport"><div class="product-carousel-track">
        <?php
        $puppy_products = function_exists('wc_get_products') ? wc_get_products(array('status' => 'publish', 'limit' => 8, 'on_sale' => true, 'orderby' => 'date', 'order' => 'DESC')) : array();
        if (!empty($puppy_products)) : foreach ($puppy_products as $puppy_product) :
        ?>
            <?php get_template_part('template-parts/product-card', null, array('product' => $puppy_product, 'show_description' => true)); ?>
        <?php endforeach; else : ?>
            <article class="product product-empty-card"><div class="product-image product-fallback product-fallback-1"></div><h3>No sale items yet</h3><p>Sale products will appear here when promotional pricing is added.</p><a class="button" href="<?php echo esc_url($shop_url); ?>">View all products</a></article>
        <?php endif; ?>
    </div></div><button type="button" class="product-carousel-prev" aria-label="Previous products">‹</button><button type="button" class="product-carousel-next" aria-label="Next products">›</button></div></div></div></section>
    <section class="section care-feature"><div class="container"><div class="care-feature-card"><div class="care-feature-image"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/carousel-care.png'); ?>" alt="Pet care essentials"></div><div class="care-feature-copy"><p class="eyebrow">A happier routine</p><h2>Small care moments add up.</h2><p>Make everyday pet care easier with simple food, play and grooming essentials chosen for the way you live together.</p><a class="button" href="<?php echo esc_url(puppy_market_category_link('pet-care')); ?>">Explore pet care →</a></div></div></div></section>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var petVideo = document.querySelector('.sequence-video video');
  if (petVideo) petVideo.addEventListener('canplay', function () { petVideo.classList.add('is-ready'); });
  var frame = document.querySelector('.carousel-frame');
  if (frame) {
    var slides = frame.querySelectorAll('.carousel-slide');
    var dots = frame.querySelectorAll('.carousel-dots button');
    var current = 0;
    function show(index) { current = (index + slides.length) % slides.length; slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === current); }); dots.forEach(function (dot, i) { dot.classList.toggle('is-active', i === current); }); }
    frame.querySelector('.carousel-prev').addEventListener('click', function () { show(current - 1); });
    frame.querySelector('.carousel-next').addEventListener('click', function () { show(current + 1); });
    dots.forEach(function (dot, i) { dot.addEventListener('click', function () { show(i); }); });
    setInterval(function () { show(current + 1); }, 5000);
  }
  var productViewport = document.querySelector('.product-carousel-viewport');
  var productPrev = document.querySelector('.product-carousel-prev');
  var productNext = document.querySelector('.product-carousel-next');
  function sizeProductCards() {
    if (!productViewport) return;
    var cardWidth = Math.max(170, Math.floor((productViewport.clientWidth - 68 - 48) / 5));
    productViewport.style.setProperty('--product-card-width', cardWidth + 'px');
  }
  function updateProductControls() {
    if (!productViewport) return;
    var maxScroll = productViewport.scrollWidth - productViewport.clientWidth - 1;
    if (productPrev) productPrev.disabled = productViewport.scrollLeft <= 1;
    if (productNext) productNext.disabled = productViewport.scrollLeft >= maxScroll;
  }
  function moveProducts(direction) {
    if (!productViewport) return;
    var distance = direction * Math.max(260, productViewport.clientWidth - 68);
    if (typeof productViewport.scrollBy === 'function') productViewport.scrollBy({ left: distance, behavior: 'smooth' });
    else productViewport.scrollLeft += distance;
    window.setTimeout(updateProductControls, 350);
  }
  if (productPrev) productPrev.addEventListener('click', function () { moveProducts(-1); });
  if (productNext) productNext.addEventListener('click', function () { moveProducts(1); });
  if (productViewport) { productViewport.addEventListener('scroll', updateProductControls); window.addEventListener('resize', function () { sizeProductCards(); updateProductControls(); }); sizeProductCards(); updateProductControls(); }

  var toast = document.querySelector('.ipet-toast');
  var cartLink = document.querySelector('.header-actions .cart-count');
  function showToast(message, error) { if (!toast) return; toast.textContent = message; toast.classList.toggle('is-error', !!error); toast.classList.add('is-visible'); window.clearTimeout(window.ipetToastTimer); window.ipetToastTimer = window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600); }
  document.querySelectorAll('.product-ajax-cart').forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      if (button.classList.contains('is-loading')) return;
      button.classList.add('is-loading');
      button.textContent = 'Adding…';
      var body = new URLSearchParams({ product_id: button.getAttribute('data-product-id'), quantity: '1' });
      fetch(button.getAttribute('data-add-to-cart-url'), { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: body.toString() })
        .then(function (response) { return response.json(); })
        .then(function (data) {
          if (data && data.error) throw new Error('add-to-cart-failed');
          button.textContent = 'Added to cart';
          showToast('Added to cart', false);
          if (cartLink) { var current = parseInt(cartLink.textContent, 10) || 0; cartLink.textContent = current + 1; }
          window.setTimeout(function () { button.textContent = 'Add to cart'; button.classList.remove('is-loading'); }, 1500);
        })
        .catch(function () { button.textContent = 'Could not add'; button.classList.remove('is-loading'); showToast('Could not add item. Please try again.', true); window.setTimeout(function () { button.textContent = 'Add to cart'; }, 1500); });
    });
  });
});
</script>
<?php get_footer(); ?>
