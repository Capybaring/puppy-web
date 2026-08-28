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
    'Find a quick answer, manage your order or reach the customer care team in the way that works best for you.'
);
$form_title = get_theme_mod('puppy_market_returns_form_title', 'Send us a message');
$form_text = get_theme_mod(
    'puppy_market_returns_form_text',
    'Share the details below and choose the topic that best matches your question.'
);
$success_text = get_theme_mod(
    'puppy_market_returns_success_text',
    'Thanks — your message has been sent.'
);
$response_text = get_theme_mod(
    'puppy_market_contact_response_text',
    'Our customer care team will follow up as soon as possible.'
);
$contact_phone = trim((string) get_theme_mod('puppy_market_contact_phone', ''));
$chat_url = esc_url_raw(get_theme_mod('puppy_market_contact_chat_url', ''));

$contact_status = isset($_GET['contact_status'])
    ? sanitize_key(wp_unslash($_GET['contact_status']))
    : '';
$contact_topic = isset($_GET['contact_topic'])
    ? sanitize_key(wp_unslash($_GET['contact_topic']))
    : 'order';
$contact_topics = array(
    'order'    => 'Order help',
    'shipping' => 'Shipping or delivery',
    'returns'  => 'Returns',
    'product'  => 'Product question',
    'business' => 'Business & wholesale',
    'other'    => 'Other',
);
if (!isset($contact_topics[$contact_topic])) $contact_topic = 'order';

$orders_url = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('orders')
    : puppy_market_account_url();
$account_url = puppy_market_account_url();
$product_support_url = add_query_arg('contact_topic', 'product', puppy_market_page_url('returns')) . '#contact-form';
$business_support_url = add_query_arg('contact_topic', 'business', puppy_market_page_url('returns')) . '#contact-form';
?>
<main id="main-content" class="ipet-care-page">
  <section class="ipet-care-hero">
    <div class="container ipet-care-hero-inner">
      <p class="eyebrow">Customer Care</p>
      <h1><?php echo esc_html($hero_title); ?></h1>
      <p><?php echo esc_html($hero_text); ?></p>
      <div class="ipet-care-hero-actions">
        <a class="button" href="<?php echo esc_url($orders_url); ?>">View your orders</a>
        <a href="#common-questions">Browse common questions →</a>
      </div>
    </div>
  </section>

  <section class="ipet-care-topics" aria-labelledby="care-topics-title">
    <div class="container">
      <div class="ipet-care-section-heading">
        <p class="eyebrow">Start here</p>
        <h2 id="care-topics-title">What do you need help with?</h2>
      </div>
      <div class="ipet-care-topic-grid">
        <a href="<?php echo esc_url($orders_url); ?>">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('shield'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <div><strong>Orders & payments</strong><p>Review purchases, payment details and order status.</p></div><b aria-hidden="true">→</b>
        </a>
        <a href="#shipping-question">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('shipping'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <div><strong>Shipping & delivery</strong><p>Find delivery estimates, tracking and address guidance.</p></div><b aria-hidden="true">→</b>
        </a>
        <a href="#returns-question">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('returns'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <div><strong>Returns & refunds</strong><p>Check return requirements and the next steps.</p></div><b aria-hidden="true">→</b>
        </a>
        <a href="<?php echo esc_url($product_support_url); ?>">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('support'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <div><strong>Product questions</strong><p>Ask about an item before or after purchasing.</p></div><b aria-hidden="true">→</b>
        </a>
        <a href="<?php echo esc_url($account_url); ?>">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('shield'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <div><strong>Account help</strong><p>Manage your details and access your order history.</p></div><b aria-hidden="true">→</b>
        </a>
        <a href="<?php echo esc_url($business_support_url); ?>">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('business'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
          <div><strong>Business & wholesale</strong><p>Contact the team about professional purchasing.</p></div><b aria-hidden="true">→</b>
        </a>
      </div>
    </div>
  </section>

  <div class="ipet-care-managed-content">
    <?php echo apply_filters('the_content', $care_content); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress page content. */ ?>
  </div>

  <section class="ipet-care-channels" aria-labelledby="care-channels-title">
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

        <a class="ipet-care-channel" href="#contact-form">
          <span class="ipet-care-channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="14" rx="2"></rect><path d="m5 7 7 6 7-6"></path></svg></span>
          <strong>Email</strong>
          <p>Send your question through the secure form.</p>
          <small>Write a message →</small>
        </a>
      </div>
    </div>
  </section>

  <section class="ipet-care-form-section" id="contact-form">
    <div class="container">
      <div class="ipet-care-form-card">
        <div class="ipet-contact-section-heading">
          <p class="eyebrow">Email customer care</p>
          <h2><?php echo esc_html($form_title); ?></h2>
          <p><?php echo esc_html($form_text); ?></p>
        </div>

        <?php if ($contact_status === 'success') : ?>
          <div class="ipet-contact-notice is-success" role="status"><?php echo esc_html($success_text); ?></div>
        <?php elseif ($contact_status === 'invalid') : ?>
          <div class="ipet-contact-notice is-error" role="alert">Please check the required fields and try again.</div>
        <?php elseif ($contact_status === 'error') : ?>
          <div class="ipet-contact-notice is-error" role="alert">Your message could not be sent. Please use another contact option.</div>
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
            <textarea id="contact-message" name="contact_message" rows="6" maxlength="5000" required></textarea>
          </div>
          <div class="ipet-contact-submit is-full">
            <button type="submit">Send message</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
