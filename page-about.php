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

$hero_title = get_theme_mod('puppy_market_about_hero_title', 'For every day with pets.');
$hero_text = get_theme_mod(
    'puppy_market_about_hero_text',
    'We make everyday pet shopping feel clearer, more useful and easier to trust.'
);
$mission_title = get_theme_mod('puppy_market_about_mission_title', 'Pet care should feel simple and personal.');
$mission_text = get_theme_mod(
    'puppy_market_about_mission_text',
    'From everyday essentials to support after an order, we bring the things pet parents need into one straightforward experience.'
);
$essentials_title = get_theme_mod('puppy_market_about_essentials_title', 'Everything pets need');
$essentials_text = get_theme_mod(
    'puppy_market_about_essentials_text',
    'Food, play, grooming, comfort and care products selected for real routines with pets.'
);
$trust_title = get_theme_mod('puppy_market_about_trust_title', 'Clarity you can trust');
$trust_text = get_theme_mod(
    'puppy_market_about_trust_text',
    'Useful product information, visible policies and a clear route to support before and after checkout.'
);
$services_title = get_theme_mod('puppy_market_about_services_title', 'A little more ease for every pet parent');
$cta_title = get_theme_mod('puppy_market_about_cta_title', 'Ready to find something your pet will love?');
$cta_text = get_theme_mod(
    'puppy_market_about_cta_text',
    'Explore the store or contact our team when you need help choosing the next step.'
);
$cta_button_text = get_theme_mod('puppy_market_about_cta_button_text', 'Explore the store');

$hero_image_id = puppy_market_media_id('puppy_market_about_hero_image');
$story_image_id = puppy_market_media_id('puppy_market_about_story_image');
$care_image_id = puppy_market_media_id('puppy_market_about_care_image');
$hero_image = $hero_image_id
    ? wp_get_attachment_image($hero_image_id, 'full', false, array('class' => 'ipet-about-image', 'loading' => 'eager'))
    : '';
$story_image = $story_image_id
    ? wp_get_attachment_image($story_image_id, 'full', false, array('class' => 'ipet-about-image', 'loading' => 'lazy'))
    : '';
$care_image = $care_image_id
    ? wp_get_attachment_image($care_image_id, 'full', false, array('class' => 'ipet-about-image', 'loading' => 'lazy'))
    : '';
?>
<main id="main-content" class="ipet-about-page">
  <section class="ipet-about-hero">
    <div class="container ipet-about-hero-grid">
      <div class="ipet-about-hero-copy">
        <p class="eyebrow">About <?php echo esc_html($site_name); ?></p>
        <h1><?php echo esc_html($hero_title); ?></h1>
        <p><?php echo esc_html($hero_text); ?></p>
        <div class="ipet-about-actions">
          <a class="button" href="<?php echo esc_url($shop_url); ?>">Shop now</a>
          <a href="#our-story">Our story →</a>
        </div>
      </div>
      <div class="ipet-about-hero-media<?php echo $hero_image ? ' has-media' : ' no-media'; ?>">
        <?php if ($hero_image) echo wp_kses_post($hero_image); ?>
      </div>
    </div>
  </section>

  <section class="ipet-about-mission" id="our-story">
    <div class="container">
      <p class="eyebrow">Why we are here</p>
      <h2><?php echo esc_html($mission_title); ?></h2>
      <p><?php echo esc_html($mission_text); ?></p>
    </div>
  </section>

  <section class="ipet-about-mosaic">
    <div class="container ipet-about-mosaic-grid">
      <div class="ipet-about-mosaic-media<?php echo $story_image ? ' has-media' : ' no-media'; ?>">
        <?php if ($story_image) echo wp_kses_post($story_image); ?>
      </div>
      <article class="ipet-about-mosaic-copy is-blue">
        <p class="eyebrow">Made for real routines</p>
        <h2><?php echo esc_html($essentials_title); ?></h2>
        <p><?php echo esc_html($essentials_text); ?></p>
        <a href="<?php echo esc_url($shop_url); ?>">Explore products →</a>
      </article>
      <article class="ipet-about-mosaic-copy is-lime">
        <p class="eyebrow">Helpful by design</p>
        <h2><?php echo esc_html($trust_title); ?></h2>
        <p><?php echo esc_html($trust_text); ?></p>
        <a href="<?php echo esc_url($contact_url); ?>">Get support →</a>
      </article>
      <div class="ipet-about-mosaic-media<?php echo $care_image ? ' has-media' : ' no-media'; ?>">
        <?php if ($care_image) echo wp_kses_post($care_image); ?>
      </div>
    </div>
  </section>

  <?php if ($custom_page_content !== '') : ?>
    <section class="ipet-about-editor-section">
      <div class="container ipet-about-editor-content">
        <?php echo apply_filters('the_content', $custom_page_content); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered WordPress page content. */ ?>
      </div>
    </section>
  <?php endif; ?>

  <section class="ipet-about-services">
    <div class="container">
      <div class="ipet-about-section-heading">
        <p class="eyebrow">By your side</p>
        <h2><?php echo esc_html($services_title); ?></h2>
      </div>
      <div class="ipet-about-service-grid">
        <article>
          <span aria-hidden="true"><?php echo puppy_market_service_icon('support'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <h3>Human support</h3>
          <p>Clear answers for products, orders, delivery and returns.</p>
        </article>
        <article>
          <span aria-hidden="true"><?php echo puppy_market_service_icon('shipping'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <h3>Everyday convenience</h3>
          <p>A simple path from browsing and checkout to delivery updates.</p>
        </article>
        <article>
          <span aria-hidden="true"><?php echo puppy_market_service_icon('returns'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <h3>Straightforward care</h3>
          <p>Visible policies and an easy way to ask for help when needed.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="ipet-about-cta">
    <div class="container ipet-about-cta-inner">
      <div>
        <p class="eyebrow">For life with pets</p>
        <h2><?php echo esc_html($cta_title); ?></h2>
        <p><?php echo esc_html($cta_text); ?></p>
      </div>
      <a class="button" href="<?php echo esc_url($shop_url); ?>"><?php echo esc_html($cta_button_text); ?></a>
    </div>
  </section>
</main>
<?php get_footer(); ?>
