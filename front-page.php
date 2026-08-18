<?php
/** Homepage template. Editorial media is selected in Appearance → Customize. */

get_header();

$shop_url = puppy_market_catalog_url();
$contact_url = puppy_market_page_url('contact');
$shipping_url = puppy_market_page_url('shipping');
$returns_url = puppy_market_page_url('returns');

$carousel_slides = array(
    array(
        'image'    => puppy_market_image_url('puppy_market_home_slide_1_image'),
        'eyebrow' => 'Everyday favorites',
        'title'    => 'Just the right kind of company, every day.',
        'copy'     => 'Find food, toys and care essentials for every pet and every routine.',
        'label'    => 'Start shopping',
        'url'      => $shop_url,
    ),
    array(
        'image'    => puppy_market_image_url('puppy_market_home_slide_2_image'),
        'eyebrow' => 'For dogs',
        'title'    => 'Good days start with a good meal.',
        'copy'     => 'Explore dog food, rewarding treats and everyday walking essentials.',
        'label'    => 'Shop dog favorites',
        'url'      => puppy_market_category_link('dog-food'),
    ),
    array(
        'image'    => puppy_market_image_url('puppy_market_home_slide_3_image'),
        'eyebrow' => 'For cats',
        'title'    => 'Save the curiosity for more playful moments.',
        'copy'     => 'Discover cat toys, interactive gear and cozy everyday comforts.',
        'label'    => 'Shop cat favorites',
        'url'      => puppy_market_category_link('cat-toys'),
    ),
);

$shop_by_categories = puppy_market_top_categories(7, false);
$popular_categories = puppy_market_popular_categories(6);
$quick_categories = array_slice($popular_categories, 0, 5);

$brand_taxonomy = puppy_market_brand_taxonomy();
$brand_terms = get_terms(array(
    'taxonomy'   => $brand_taxonomy,
    'hide_empty' => true,
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
));
if (is_wp_error($brand_terms)) $brand_terms = array();

$video_url = puppy_market_media_url('puppy_market_home_video');
$video_type = puppy_market_media_mime_type('puppy_market_home_video');
$video_poster_id = puppy_market_media_id('puppy_market_home_video_poster');
$video_poster_url = puppy_market_image_url('puppy_market_home_video_poster');
$care_image_id = puppy_market_media_id('puppy_market_home_care_image');
?>

<main id="main-content">
    <section class="section sequence-promo sequence-promo-1">
        <div class="container"><div class="sequence-card"><h2>Free shipping on orders over $75.</h2></div></div>
    </section>

    <section class="home-carousel" aria-label="iPet featured picks">
        <div class="container">
            <div class="carousel-frame">
                <?php foreach ($carousel_slides as $index => $slide) :
                    $slide_style = $slide['image'] ? '--puppy-slide-image:url("' . esc_url_raw($slide['image']) . '");' : '';
                ?>
                    <article class="carousel-slide<?php echo $index === 0 ? ' is-active' : ''; ?><?php echo $slide['image'] ? ' has-media' : ' no-media'; ?>"<?php echo $slide_style ? ' style="' . esc_attr($slide_style) . '"' : ''; ?>>
                        <div>
                            <p class="eyebrow"><?php echo esc_html($slide['eyebrow']); ?></p>
                            <h2><?php echo esc_html($slide['title']); ?></h2>
                            <p><?php echo esc_html($slide['copy']); ?></p>
                            <a class="button" href="<?php echo esc_url($slide['url']); ?>"><?php echo esc_html($slide['label']); ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
                <button class="carousel-arrow carousel-prev" type="button" aria-label="Previous slide">‹</button>
                <button class="carousel-arrow carousel-next" type="button" aria-label="Next slide">›</button>
                <div class="carousel-dots">
                    <?php foreach ($carousel_slides as $index => $slide) : ?>
                        <button type="button" class="<?php echo $index === 0 ? 'is-active' : ''; ?>" aria-label="Slide <?php echo absint($index + 1); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="section value-strip" aria-label="iPet member perks and services">
        <div class="container"><div class="value-strip-inner">
            <div class="value-strip-membership"><strong>iPet member perks</strong><p>Free shipping on every order, plus rewards on repeat purchases.</p><a class="button button-light" href="<?php echo esc_url($shop_url); ?>">Learn more</a></div>
            <div class="value-strip-services">
                <a href="<?php echo esc_url($contact_url); ?>"><span aria-hidden="true">☎</span><strong>24/7 customer care</strong></a>
                <a href="<?php echo esc_url($contact_url); ?>"><span aria-hidden="true">💬</span><strong>Chat with our pet care team</strong></a>
                <a href="<?php echo esc_url($shipping_url); ?>"><span aria-hidden="true">🚚</span><strong>Fast, reliable shipping</strong></a>
                <a href="<?php echo esc_url($returns_url); ?>"><span aria-hidden="true">↩</span><strong>365-day easy returns</strong></a>
            </div>
        </div></div>
    </section>

    <?php if (!empty($shop_by_categories)) : ?>
        <section class="section shop-by-pet" id="categories">
            <div class="container">
                <div class="section-heading"><h2>Who are you shopping for?</h2></div>
                <div class="pet-strip">
                    <?php foreach ($shop_by_categories as $category) :
                        $category_url = get_term_link($category);
                        if (is_wp_error($category_url)) continue;
                    ?>
                        <a href="<?php echo esc_url($category_url); ?>">
                            <span class="pet-image"><?php echo wp_kses_post(puppy_market_category_thumbnail($category, 'medium')); ?></span>
                            <strong><?php echo esc_html($category->name); ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($quick_categories)) : ?>
        <section class="section quick-links" aria-label="Popular searches">
            <div class="container"><div class="quick-links-row">
                <?php foreach ($quick_categories as $category) :
                    $category_url = get_term_link($category);
                    if (is_wp_error($category_url)) continue;
                ?>
                    <a href="<?php echo esc_url($category_url); ?>"><?php echo esc_html($category->name); ?></a>
                <?php endforeach; ?>
                <a href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Deals</a>
            </div></div>
        </section>
    <?php endif; ?>

    <?php if (!empty($brand_terms)) : ?>
        <section class="section brand-wall" aria-label="Shop by brand">
            <div class="container"><div class="section-heading"><h2>Shop by brand</h2></div><div class="brand-wall-row">
                <?php foreach ($brand_terms as $brand_term) : ?>
                    <a href="<?php echo esc_url(add_query_arg('puppy_brand%5B%5D', $brand_term->slug, $shop_url)); ?>"><?php echo esc_html($brand_term->name); ?></a>
                <?php endforeach; ?>
            </div></div>
        </section>
    <?php endif; ?>

    <section class="trust-strip" aria-label="Shopping reassurance">
        <div class="container"><div class="trust-strip-grid">
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">↗</span><div><strong>Free shipping</strong><p>On orders over $75</p></div></div>
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">↩</span><div><strong>365-day returns</strong><p>Easy returns on eligible items</p></div></div>
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">✓</span><div><strong>Secure checkout</strong><p>Your payment is protected</p></div></div>
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">✦</span><div><strong>Pet support</strong><p>Here when you need us</p></div></div>
        </div></div>
    </section>

    <section class="section best-sellers">
        <div class="container"><div class="section-heading"><h2>Best sellers</h2><a href="<?php echo esc_url($shop_url); ?>">Shop all →</a></div><div class="best-sellers-grid">
            <?php $best_sellers = function_exists('wc_get_products') ? wc_get_products(array('status' => 'publish', 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC')) : array(); ?>
            <?php if (!empty($best_sellers)) : foreach ($best_sellers as $best_seller) : ?>
                <?php get_template_part('template-parts/product-card', null, array('product' => $best_seller, 'badge' => $best_seller->is_on_sale() ? 'Sale' : 'Best seller', 'card_class' => 'best-seller-card')); ?>
            <?php endforeach; else : ?>
                <article class="best-sellers-empty"><strong>Customer favorites are coming soon.</strong><p>Popular pet essentials will appear here as products are added.</p></article>
            <?php endif; ?>
        </div></div>
    </section>

    <section class="section seasonal-section">
        <div class="container"><div class="seasonal-banner"><div><p class="eyebrow">A little more joy</p><h2>Spend your energy on the fun stuff.</h2><p>Find toys, cleaning essentials and daily care for happier routines.</p><a class="button" href="<?php echo esc_url($shop_url); ?>">Shop pet essentials →</a></div><span>summer</span></div></div>
    </section>

    <?php if (!empty($popular_categories)) : ?>
        <section class="section popular-links">
            <div class="container"><div class="section-heading"><h2>Shop popular categories</h2></div><div class="popular-link-list">
                <?php foreach ($popular_categories as $category) :
                    $category_url = get_term_link($category);
                    if (is_wp_error($category_url)) continue;
                ?>
                    <a href="<?php echo esc_url($category_url); ?>">
                        <span class="popular-image"><?php echo wp_kses_post(puppy_market_category_thumbnail($category, 'medium')); ?></span>
                        <strong><?php echo esc_html($category->name); ?></strong>
                    </a>
                <?php endforeach; ?>
            </div></div>
        </section>
    <?php endif; ?>

    <?php if ($video_url || $video_poster_id) : ?>
        <section class="section sequence-promo sequence-promo-3">
            <div class="container"><div class="sequence-card">
                <div class="sequence-video">
                    <?php if ($video_poster_id) : ?>
                        <?php echo wp_get_attachment_image($video_poster_id, 'large', false, array('class' => 'sequence-video-fallback', 'alt' => 'Pet care in motion')); ?>
                    <?php endif; ?>
                    <?php if ($video_url) : ?>
                        <video autoplay muted loop playsinline preload="metadata"<?php echo $video_poster_url ? ' poster="' . esc_url($video_poster_url) . '"' : ''; ?> aria-label="Pet care in motion">
                            <source src="<?php echo esc_url($video_url); ?>"<?php echo $video_type ? ' type="' . esc_attr($video_type) . '"' : ''; ?>>
                        </video>
                    <?php endif; ?>
                </div>
                <div class="sequence-copy"><p class="eyebrow">Everyday care</p><h2>Small routines, happier days.</h2><p>Simple essentials that make pet care feel easier.</p></div>
            </div></div>
        </section>
    <?php endif; ?>

    <section class="section product-showcase" id="deals">
        <div class="container"><div class="product-showcase-layout">
            <aside class="summer-promo-card"><div><p class="eyebrow">Everyday essentials</p><h2>Make every moment together more fun.</h2><p>From daily food to interactive toys, make room for easy, happy routines.</p></div><a class="button" href="<?php echo esc_url(puppy_market_catalog_url('sale')); ?>">Shop more →</a></aside>
            <div class="product-carousel-shell"><div class="product-carousel-viewport"><div class="product-carousel-track">
                <?php $sale_products = function_exists('wc_get_products') ? wc_get_products(array('status' => 'publish', 'limit' => 8, 'on_sale' => true, 'orderby' => 'date', 'order' => 'DESC')) : array(); ?>
                <?php if (!empty($sale_products)) : foreach ($sale_products as $sale_product) : ?>
                    <?php get_template_part('template-parts/product-card', null, array('product' => $sale_product, 'show_description' => true)); ?>
                <?php endforeach; else : ?>
                    <article class="product product-empty-card"><div class="product-image product-image-empty" aria-hidden="true"></div><h3>No sale items yet</h3><p>Sale products will appear here when promotional pricing is added.</p><a class="button" href="<?php echo esc_url($shop_url); ?>">View all products</a></article>
                <?php endif; ?>
            </div></div><button type="button" class="product-carousel-prev" aria-label="Previous products">‹</button><button type="button" class="product-carousel-next" aria-label="Next products">›</button></div>
        </div></div>
    </section>

    <section class="section care-feature">
        <div class="container"><div class="care-feature-card<?php echo $care_image_id ? ' has-media' : ' no-media'; ?>">
            <?php if ($care_image_id) : ?><div class="care-feature-image"><?php echo wp_get_attachment_image($care_image_id, 'large', false, array('alt' => 'Pet care essentials')); ?></div><?php endif; ?>
            <div class="care-feature-copy"><p class="eyebrow">A happier routine</p><h2>Small care moments add up.</h2><p>Make everyday pet care easier with simple food, play and grooming essentials chosen for the way you live together.</p><a class="button" href="<?php echo esc_url($shop_url); ?>">Explore pet care →</a></div>
        </div></div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var petVideo = document.querySelector('.sequence-video video');
  if (petVideo) petVideo.addEventListener('canplay', function () { petVideo.classList.add('is-ready'); });

  var frame = document.querySelector('.carousel-frame');
  if (frame) {
    var slides = frame.querySelectorAll('.carousel-slide');
    var dots = frame.querySelectorAll('.carousel-dots button');
    var previous = frame.querySelector('.carousel-prev');
    var next = frame.querySelector('.carousel-next');
    var current = 0;
    function show(index) {
      if (!slides.length) return;
      current = (index + slides.length) % slides.length;
      slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === current); });
      dots.forEach(function (dot, i) { dot.classList.toggle('is-active', i === current); });
    }
    if (previous) previous.addEventListener('click', function () { show(current - 1); });
    if (next) next.addEventListener('click', function () { show(current + 1); });
    dots.forEach(function (dot, i) { dot.addEventListener('click', function () { show(i); }); });
    if (slides.length > 1) window.setInterval(function () { show(current + 1); }, 5000);
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
    productViewport.scrollBy({ left: distance, behavior: 'smooth' });
    window.setTimeout(updateProductControls, 350);
  }
  if (productPrev) productPrev.addEventListener('click', function () { moveProducts(-1); });
  if (productNext) productNext.addEventListener('click', function () { moveProducts(1); });
  if (productViewport) {
    productViewport.addEventListener('scroll', updateProductControls);
    window.addEventListener('resize', function () { sizeProductCards(); updateProductControls(); });
    sizeProductCards();
    updateProductControls();
  }

  var toast = document.querySelector('.ipet-toast');
  var cartLink = document.querySelector('.header-actions .cart-count');
  function showToast(message, error) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.toggle('is-error', !!error);
    toast.classList.add('is-visible');
    window.clearTimeout(window.ipetToastTimer);
    window.ipetToastTimer = window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600);
  }
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
          if (cartLink) cartLink.textContent = (parseInt(cartLink.textContent, 10) || 0) + 1;
          window.setTimeout(function () { button.textContent = 'Add to cart'; button.classList.remove('is-loading'); }, 1500);
        })
        .catch(function () {
          button.textContent = 'Could not add';
          button.classList.remove('is-loading');
          showToast('Could not add item. Please try again.', true);
          window.setTimeout(function () { button.textContent = 'Add to cart'; }, 1500);
        });
    });
  });
});
</script>

<?php get_footer(); ?>
