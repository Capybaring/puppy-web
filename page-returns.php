<?php
/**
 * Template Name: Returns & Support
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$page_id = get_queried_object_id();
$policy_content = trim((string) get_post_field('post_content', $page_id));
if ($policy_content === '' && function_exists('puppy_market_default_returns_page_content')) {
    $policy_content = puppy_market_default_returns_page_content();
}

$hero_title = get_theme_mod(
    'puppy_market_returns_hero_title',
    'A straightforward path when an item is not right.'
);
$hero_text = get_theme_mod(
    'puppy_market_returns_hero_text',
    'Review the return guidance or send the support team your order details from one place.'
);
$highlight_label = get_theme_mod('puppy_market_returns_highlight_label', 'Return window');
$highlight_title = get_theme_mod(
    'puppy_market_returns_highlight_title',
    'Eligible unused items can be requested for return within 30 days of delivery.'
);
$highlight_text = get_theme_mod(
    'puppy_market_returns_highlight_text',
    'Support confirms eligibility and provides the correct instructions for the product and order.'
);
$form_title = get_theme_mod('puppy_market_returns_form_title', 'Tell us what you need');
$form_text = get_theme_mod(
    'puppy_market_returns_form_text',
    'Use this form for returns, delivery questions, order help, products or business enquiries.'
);
$success_text = get_theme_mod(
    'puppy_market_returns_success_text',
    'Thanks — your message has been sent.'
);
$response_text = get_theme_mod(
    'puppy_market_contact_response_text',
    'We will review your request and follow up as soon as possible.'
);
$contact_email = sanitize_email(get_theme_mod('puppy_market_contact_email', get_option('admin_email')));
$business_email = sanitize_email(get_theme_mod('puppy_market_business_email', ''));
$contact_phone = trim((string) get_theme_mod('puppy_market_contact_phone', ''));

$contact_status = isset($_GET['contact_status'])
    ? sanitize_key(wp_unslash($_GET['contact_status']))
    : '';
$contact_topic = isset($_GET['contact_topic'])
    ? sanitize_key(wp_unslash($_GET['contact_topic']))
    : 'returns';
$contact_topics = array(
    'returns'  => 'Returns',
    'order'    => 'Order help',
    'shipping' => 'Shipping or delivery',
    'product'  => 'Product question',
    'business' => 'Business & wholesale',
    'other'    => 'Other',
);
if (!isset($contact_topics[$contact_topic])) $contact_topic = 'returns';

$orders_url = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('orders')
    : puppy_market_account_url();
?>
<main id="main-content" class="ipet-policy-page ipet-returns-page">
  <section class="ipet-policy-hero">
    <div class="container ipet-policy-hero-grid">
      <div class="ipet-policy-hero-copy">
        <p class="eyebrow"><?php echo esc_html(get_the_title($page_id)); ?></p>
        <h1><?php echo esc_html($hero_title); ?></h1>
        <p><?php echo esc_html($hero_text); ?></p>
        <div class="ipet-policy-actions">
          <a class="button" href="#contact-form">Contact support</a>
          <a class="ipet-policy-text-link" href="<?php echo esc_url($orders_url); ?>">Find your order →</a>
        </div>
      </div>

      <aside class="ipet-policy-highlight" aria-label="<?php echo esc_attr($highlight_label); ?>">
        <span aria-hidden="true"><?php echo puppy_market_service_icon('returns'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
        <p class="eyebrow"><?php echo esc_html($highlight_label); ?></p>
        <strong><?php echo esc_html($highlight_title); ?></strong>
        <p><?php echo esc_html($highlight_text); ?></p>
      </aside>
    </div>
  </section>

  <div class="ipet-returns-managed-content">
    <?php echo apply_filters('the_content', $policy_content); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress page content. */ ?>
  </div>

  <section class="ipet-contact-workspace" id="contact-form">
    <div class="container ipet-contact-workspace-grid">
      <div class="ipet-contact-form-card">
        <div class="ipet-contact-section-heading">
          <p class="eyebrow">Returns & support</p>
          <h2><?php echo esc_html($form_title); ?></h2>
          <p><?php echo esc_html($form_text); ?></p>
        </div>

        <?php if ($contact_status === 'success') : ?>
          <div class="ipet-contact-notice is-success" role="status"><?php echo esc_html($success_text); ?></div>
        <?php elseif ($contact_status === 'invalid') : ?>
          <div class="ipet-contact-notice is-error" role="alert">Please check the required fields and try again.</div>
        <?php elseif ($contact_status === 'error') : ?>
          <div class="ipet-contact-notice is-error" role="alert">Your message could not be sent. Please use the email option instead.</div>
        <?php endif; ?>

        <form class="ipet-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
          <input type="hidden" name="action" value="puppy_market_contact">
          <?php wp_nonce_field('puppy_market_contact_form', 'puppy_contact_nonce'); ?>

          <p class="ipet-contact-honeypot" aria-hidden="true">
            <label>Website<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>
          </p>

          <div class="ipet-contact-field">
            <label for="contact-name">Name <span>*</span></label>
            <input id="contact-name" type="text" name="contact_name" autocomplete="name" required>
          </div>

          <div class="ipet-contact-field">
            <label for="contact-email">Email <span>*</span></label>
            <input id="contact-email" type="email" name="contact_email" autocomplete="email" required>
          </div>

          <div class="ipet-contact-field">
            <label for="contact-order">Order number</label>
            <input id="contact-order" type="text" name="order_number" autocomplete="off">
          </div>

          <div class="ipet-contact-field">
            <label for="contact-topic">Topic <span>*</span></label>
            <select id="contact-topic" name="contact_topic" required>
              <?php foreach ($contact_topics as $topic_value => $topic_label) : ?>
                <option value="<?php echo esc_attr($topic_value); ?>" <?php selected($contact_topic, $topic_value); ?>><?php echo esc_html($topic_label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="ipet-contact-field is-full">
            <label for="contact-message">How can we help? <span>*</span></label>
            <textarea id="contact-message" name="contact_message" rows="7" maxlength="5000" required></textarea>
          </div>

          <div class="ipet-contact-submit is-full">
            <button type="submit">Send message</button>
            <small>Your message is sent securely to the configured store support address.</small>
          </div>
        </form>
      </div>

      <aside class="ipet-contact-help-card">
        <p class="eyebrow">Support details</p>
        <h2>Choose the right route</h2>
        <p><?php echo esc_html($response_text); ?></p>

        <a href="<?php echo esc_url($orders_url); ?>">
          <span aria-hidden="true">01</span>
          <div><strong>Orders</strong><p>Review purchases and order status.</p></div>
          <b aria-hidden="true">→</b>
        </a>

        <?php if ($contact_email) : ?>
          <a href="mailto:<?php echo esc_attr(antispambot($contact_email)); ?>">
            <span aria-hidden="true">02</span>
            <div><strong>Email support</strong><p><?php echo wp_kses_post(antispambot($contact_email)); ?></p></div>
            <b aria-hidden="true">→</b>
          </a>
        <?php endif; ?>

        <?php if ($contact_phone !== '') : ?>
          <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>">
            <span aria-hidden="true">03</span>
            <div><strong>Call support</strong><p><?php echo esc_html($contact_phone); ?></p></div>
            <b aria-hidden="true">→</b>
          </a>
        <?php endif; ?>

        <?php if ($business_email) : ?>
          <div class="ipet-contact-business-email">
            <strong>Business & wholesale</strong>
            <a href="mailto:<?php echo esc_attr(antispambot($business_email)); ?>"><?php echo wp_kses_post(antispambot($business_email)); ?></a>
          </div>
        <?php endif; ?>
      </aside>
    </div>
  </section>
</main>
<?php get_footer(); ?>
