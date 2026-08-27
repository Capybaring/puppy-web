<?php
/**
 * Template Name: About Us
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$shop_url = puppy_market_catalog_url();
$contact_url = puppy_market_page_url('contact');
$site_name = get_bloginfo('name');
$custom_page_content = '';

if (have_posts()) {
    the_post();
    $custom_page_content = trim((string) get_the_content());
}
?>
<main id="main-content" class="ipet-policy-page ipet-about-page">
  <section class="ipet-policy-hero">
    <div class="container ipet-policy-hero-grid">
      <div class="ipet-policy-hero-copy">
        <p class="eyebrow">About <?php echo esc_html($site_name); ?></p>
        <h1>Better pet shopping starts with clearer choices.</h1>
        <p>We bring everyday pet essentials, straightforward information and helpful support together in one simple store experience.</p>
        <div class="ipet-policy-actions">
          <a class="button" href="<?php echo esc_url($shop_url); ?>">Explore the store</a>
          <a class="ipet-policy-text-link" href="<?php echo esc_url($contact_url); ?>">Contact our team →</a>
        </div>
      </div>

      <aside class="ipet-policy-highlight" aria-label="About our store">
        <span aria-hidden="true"><?php echo puppy_market_service_icon('shield'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
        <p class="eyebrow">Our approach</p>
        <strong>Practical products for everyday life with pets.</strong>
        <p>Our goal is to make it easier to browse, compare and get help before and after an order.</p>
      </aside>
    </div>
  </section>

  <?php if ($custom_page_content !== '') : ?>
    <section class="ipet-policy-details">
      <div class="container">
        <article class="ipet-policy-editor-content">
          <?php echo apply_filters('the_content', $custom_page_content); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered WordPress page content. */ ?>
        </article>
      </div>
    </section>
  <?php else : ?>
    <section class="ipet-policy-summary">
      <div class="container">
        <div class="ipet-policy-heading">
          <p class="eyebrow">What matters to us</p>
          <h2>A store designed around useful decisions</h2>
        </div>
        <div class="ipet-policy-card-grid">
          <article><span>01</span><h3>Everyday usefulness</h3><p>We focus on food, play, grooming, comfort and care products that fit naturally into daily routines.</p></article>
          <article><span>02</span><h3>Clear information</h3><p>Product details, delivery guidance and return information should be easy to find before you order.</p></article>
          <article><span>03</span><h3>Helpful support</h3><p>When a product or order needs attention, our contact routes help you reach the appropriate next step.</p></article>
        </div>
      </div>
    </section>

    <section class="ipet-policy-process">
      <div class="container">
        <div class="ipet-policy-heading">
          <p class="eyebrow">How we help</p>
          <h2>From browsing to after-order support</h2>
        </div>
        <ol class="ipet-policy-steps">
          <li><span>1</span><div><h3>Browse by need</h3><p>Use categories and product details to narrow the store to the essentials that fit your pet and routine.</p></div></li>
          <li><span>2</span><div><h3>Order with clarity</h3><p>Review pricing, availability, delivery information and store policies before completing checkout.</p></div></li>
          <li><span>3</span><div><h3>Get the right help</h3><p>Use Contact Us for product, order, shipping, return or business questions whenever more information is needed.</p></div></li>
        </ol>
      </div>
    </section>

    <section class="ipet-policy-details">
      <div class="container ipet-policy-details-grid">
        <article>
          <p class="eyebrow">Our storefront</p>
          <h2>Simple to explore</h2>
          <ul>
            <li>Organized product categories for different pets and care needs.</li>
            <li>Consistent product cards with pricing, availability and purchase actions.</li>
            <li>Clear access to account, cart, delivery and return information.</li>
          </ul>
        </article>
        <article>
          <p class="eyebrow">Our responsibility</p>
          <h2>Support without guesswork</h2>
          <ul>
            <li>We explain store and product information as clearly as possible.</li>
            <li>Health, nutrition and behavior concerns should be discussed with a qualified veterinarian.</li>
            <li>Store policies and contact information remain available throughout the shopping journey.</li>
          </ul>
        </article>
      </div>
    </section>
  <?php endif; ?>

  <section class="ipet-policy-cta">
    <div class="container">
      <div><p class="eyebrow">Questions?</p><h2>We are here to help you find the next step.</h2><p>Tell us whether your question is about a product, an order, delivery, a return or business purchasing.</p></div>
      <a class="button" href="<?php echo esc_url($contact_url); ?>">Contact us</a>
    </div>
  </section>
</main>
<?php get_footer(); ?>
