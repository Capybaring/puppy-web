<?php
/**
 * Template Name: Contact Us
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$contact_email = sanitize_email(get_theme_mod('puppy_market_contact_email', get_option('admin_email')));
$business_email = sanitize_email(get_theme_mod('puppy_market_business_email', ''));
$contact_phone = trim((string) get_theme_mod('puppy_market_contact_phone', ''));
$response_text = get_theme_mod(
    'puppy_market_contact_response_text',
    'We will review your request and follow up as soon as possible.'
);
$contact_status = isset($_GET['contact_status'])
    ? sanitize_key(wp_unslash($_GET['contact_status']))
    : '';
$contact_topic = isset($_GET['contact_topic'])
    ? sanitize_key(wp_unslash($_GET['contact_topic']))
    : 'order';
$contact_topics = array('order', 'shipping', 'returns', 'product', 'business', 'other');
if (!in_array($contact_topic, $contact_topics, true)) $contact_topic = 'order';

$shipping_url = puppy_market_page_url('shipping');
$returns_url = puppy_market_page_url('returns');
$orders_url = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('orders')
    : puppy_market_account_url();
?>
<main id="main-content" class="ipet-contact-page">
  <section class="ipet-contact-hero">
    <div class="container ipet-contact-hero-grid">
      <div class="ipet-contact-hero-copy">
        <p class="eyebrow">Customer care</p>
        <h1>How can we help?</h1>
        <p>Questions about an order, delivery, return or product? Send us the details and we will help you find the next step.</p>
        <div class="ipet-contact-hero-actions">
          <a class="button" href="#contact-form">Send a message</a>
          <a class="ipet-contact-text-link" href="<?php echo esc_url($orders_url); ?>">View your orders →</a>
        </div>
      </div>

      <aside class="ipet-contact-promise" aria-label="Service assurance">
        <span class="ipet-contact-promise-icon" aria-hidden="true"><?php echo puppy_market_service_icon('shield'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted, fixed theme SVG. */ ?></span>
        <p class="eyebrow">Service assurance</p>
        <h2>Support before and after your order.</h2>
        <p><?php echo esc_html($response_text); ?></p>
        <ul>
          <li>Secure handling of your contact details</li>
          <li>Clear guidance for orders and returns</li>
          <li>Dedicated business and wholesale support</li>
        </ul>
      </aside>
    </div>
  </section>

  <section class="ipet-contact-methods">
    <div class="container">
      <div class="ipet-contact-section-heading">
        <p class="eyebrow">Choose the right route</p>
        <h2>Contact options</h2>
      </div>
      <div class="ipet-contact-method-grid">
        <a href="#contact-form">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('support'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted, fixed theme SVG. */ ?></span>
          <div><strong>Customer support</strong><p>Orders, shipping, returns and product questions.</p></div>
          <b aria-hidden="true">→</b>
        </a>

        <?php if ($contact_email) : ?>
          <a href="mailto:<?php echo esc_attr(antispambot($contact_email)); ?>">
            <span aria-hidden="true"><?php echo puppy_market_service_icon('mail'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted, fixed theme SVG. */ ?></span>
            <div><strong>Email us</strong><p><?php echo wp_kses_post(antispambot($contact_email)); ?></p></div>
            <b aria-hidden="true">→</b>
          </a>
        <?php endif; ?>

        <a id="business-support" href="#contact-form">
          <span aria-hidden="true"><?php echo puppy_market_service_icon('business'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted, fixed theme SVG. */ ?></span>
          <div><strong>Business & wholesale</strong><p>For stores, clinics and professional purchasing.</p></div>
          <b aria-hidden="true">→</b>
        </a>

        <?php if ($contact_phone !== '') : ?>
          <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>">
            <span aria-hidden="true"><?php echo puppy_market_service_icon('phone'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted, fixed theme SVG. */ ?></span>
            <div><strong>Call support</strong><p><?php echo esc_html($contact_phone); ?></p></div>
            <b aria-hidden="true">→</b>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="ipet-contact-workspace" id="contact-form">
    <div class="container ipet-contact-workspace-grid">
      <div class="ipet-contact-form-card">
        <div class="ipet-contact-section-heading">
          <p class="eyebrow">Send a message</p>
          <h2>Tell us what you need</h2>
          <p>Fields marked with an asterisk are required.</p>
        </div>

        <?php if ($contact_status === 'success') : ?>
          <div class="ipet-contact-notice is-success" role="status">Thanks — your message has been sent.</div>
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
              <option value="order" <?php selected($contact_topic, 'order'); ?>>Order help</option>
              <option value="shipping" <?php selected($contact_topic, 'shipping'); ?>>Shipping</option>
              <option value="returns" <?php selected($contact_topic, 'returns'); ?>>Returns</option>
              <option value="product" <?php selected($contact_topic, 'product'); ?>>Product question</option>
              <option value="business" <?php selected($contact_topic, 'business'); ?>>Business & wholesale</option>
              <option value="other" <?php selected($contact_topic, 'other'); ?>>Other</option>
            </select>
          </div>

          <div class="ipet-contact-field is-full">
            <label for="contact-message">How can we help? <span>*</span></label>
            <textarea id="contact-message" name="contact_message" rows="7" maxlength="5000" required></textarea>
          </div>

          <div class="ipet-contact-submit is-full">
            <button type="submit">Send message</button>
            <small>Your message is sent securely to the store support address.</small>
          </div>
        </form>
      </div>

      <aside class="ipet-contact-help-card">
        <p class="eyebrow">Quick answers</p>
        <h2>Helpful links</h2>
        <a href="<?php echo esc_url($orders_url); ?>">
          <span aria-hidden="true">01</span>
          <div><strong>Orders</strong><p>Review purchases and order status.</p></div>
          <b aria-hidden="true">→</b>
        </a>
        <a href="<?php echo esc_url($shipping_url); ?>">
          <span aria-hidden="true">02</span>
          <div><strong>Shipping</strong><p>Delivery information and tracking guidance.</p></div>
          <b aria-hidden="true">→</b>
        </a>
        <a href="<?php echo esc_url($returns_url); ?>">
          <span aria-hidden="true">03</span>
          <div><strong>Returns</strong><p>Eligibility and return instructions.</p></div>
          <b aria-hidden="true">→</b>
        </a>
        <?php if ($business_email) : ?>
          <div class="ipet-contact-business-email">
            <strong>Business email</strong>
            <a href="mailto:<?php echo esc_attr(antispambot($business_email)); ?>"><?php echo wp_kses_post(antispambot($business_email)); ?></a>
          </div>
        <?php endif; ?>
      </aside>
    </div>
  </section>
</main>
<?php get_footer(); ?>
