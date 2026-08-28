<?php
/**
 * Template Name: Returns & Support
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$page_id = get_queried_object_id();
$care_content = trim((string) get_post_field('post_content', $page_id));
if ($care_content === '' && function_exists('puppy_market_default_returns_page_content')) {
    $care_content = puppy_market_default_returns_page_content();
}

$hero_title = get_theme_mod('puppy_market_returns_hero_title', 'How can we help?');
$hero_text = get_theme_mod(
    'puppy_market_returns_hero_text',
    'Choose a help topic to find the most useful answer, or contact the customer care team in the way that works best for you.'
);
$response_text = get_theme_mod(
    'puppy_market_contact_response_text',
    'Our customer care team will follow up as soon as possible.'
);
$contact_email = sanitize_email(get_theme_mod('puppy_market_contact_email', get_option('admin_email')));
$contact_phone = trim((string) get_theme_mod('puppy_market_contact_phone', ''));
$chat_url = esc_url_raw(get_theme_mod('puppy_market_contact_chat_url', ''));
?>
<main id="main-content" class="ipet-care-page">
  <section class="ipet-care-hero">
    <div class="container ipet-care-hero-inner">
      <p class="eyebrow">Customer Care</p>
      <h1><?php echo esc_html($hero_title); ?></h1>
      <p><?php echo esc_html($hero_text); ?></p>
      <div class="ipet-care-hero-actions">
        <a class="button" href="#help-center">Browse help topics</a>
      </div>
    </div>
  </section>

  <section class="ipet-care-help" id="help-center" aria-label="Help center">
    <div class="container ipet-care-help-layout">
      <aside class="ipet-care-sidebar">
        <p class="eyebrow">Help</p>
        <h2>Browse by topic</h2>
        <div class="ipet-care-topic-selector" role="radiogroup" aria-label="Customer care topics">
          <input class="ipet-care-topic-control ipet-care-topic-default" type="radio" name="care-help-topic" id="care-topic-common" checked tabindex="-1" autocomplete="off" aria-label="Common questions">
          <label class="ipet-care-topic-option">
            <input class="ipet-care-topic-control" type="radio" name="care-help-topic" id="care-topic-orders" autocomplete="off" aria-controls="orders-help">
            <span>Orders &amp; payments</span>
          </label>
          <label class="ipet-care-topic-option">
            <input class="ipet-care-topic-control" type="radio" name="care-help-topic" id="care-topic-shipping" autocomplete="off" aria-controls="shipping-help">
            <span>Shipping &amp; delivery</span>
          </label>
          <label class="ipet-care-topic-option">
            <input class="ipet-care-topic-control" type="radio" name="care-help-topic" id="care-topic-returns" autocomplete="off" aria-controls="returns-help">
            <span>Returns &amp; refunds</span>
          </label>
          <label class="ipet-care-topic-option">
            <input class="ipet-care-topic-control" type="radio" name="care-help-topic" id="care-topic-products" autocomplete="off" aria-controls="products-help">
            <span>Product questions</span>
          </label>
        </div>
      </aside>

      <div class="ipet-care-help-main">
        <label class="ipet-care-common-return" for="care-topic-common">← Back to common questions</label>
        <?php echo apply_filters('the_content', $care_content); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress page content. */ ?>
      </div>
    </div>
  </section>

  <section class="ipet-care-channels" id="contact-options" aria-labelledby="care-channels-title">
    <div class="container">
      <div class="ipet-care-section-heading">
        <p class="eyebrow">Still need help?</p>
        <h2 id="care-channels-title">Choose how you want to reach us</h2>
        <p><?php echo esc_html($response_text); ?></p>
      </div>
      <div class="ipet-care-channel-grid">
        <?php if ($chat_url !== '') : ?>
          <a class="ipet-care-channel" href="<?php echo esc_url($chat_url); ?>">
            <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5.5h16v11H9l-5 3v-14Z"></path><path d="M8 10h8M8 13h5"></path></svg></span>
            <strong>Chat</strong>
            <p>Start a conversation with customer care.</p>
            <small>Start chat →</small>
          </a>
        <?php else : ?>
          <div class="ipet-care-channel is-disabled" aria-disabled="true">
            <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5.5h16v11H9l-5 3v-14Z"></path><path d="M8 10h8M8 13h5"></path></svg></span>
            <strong>Chat</strong>
            <p>Live chat is not available right now.</p>
            <small>Unavailable</small>
          </div>
        <?php endif; ?>

        <?php if ($contact_phone !== '') : ?>
          <a class="ipet-care-channel" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>">
            <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6.7 3.8 9.4 8 7.6 9.8c1.3 2.8 3.7 5.2 6.5 6.5l1.8-1.8 4.2 2.7-.7 3.1c-.2.8-.9 1.3-1.7 1.3C9.3 21.6 2.4 14.7 2.4 6.3c0-.8.5-1.5 1.3-1.7Z"></path></svg></span>
            <strong>Phone</strong>
            <p><?php echo esc_html($contact_phone); ?></p>
            <small>Call us →</small>
          </a>
        <?php else : ?>
          <div class="ipet-care-channel is-disabled" aria-disabled="true">
            <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6.7 3.8 9.4 8 7.6 9.8c1.3 2.8 3.7 5.2 6.5 6.5l1.8-1.8 4.2 2.7-.7 3.1c-.2.8-.9 1.3-1.7 1.3C9.3 21.6 2.4 14.7 2.4 6.3c0-.8.5-1.5 1.3-1.7Z"></path></svg></span>
            <strong>Phone</strong>
            <p>Phone support is not configured yet.</p>
            <small>Unavailable</small>
          </div>
        <?php endif; ?>

        <?php if ($contact_email) : ?>
          <a class="ipet-care-channel" href="mailto:<?php echo esc_attr(antispambot($contact_email)); ?>">
            <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="14" rx="2"></rect><path d="m5 7 7 6 7-6"></path></svg></span>
            <strong>Email</strong>
            <p>Open your email app and write to customer care.</p>
            <small>Send email →</small>
          </a>
        <?php else : ?>
          <div class="ipet-care-channel is-disabled" aria-disabled="true">
            <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="14" rx="2"></rect><path d="m5 7 7 6 7-6"></path></svg></span>
            <strong>Email</strong>
            <p>Email support is not configured yet.</p>
            <small>Unavailable</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
