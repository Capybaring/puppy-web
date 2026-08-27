<?php
/**
 * Template Name: Privacy Policy
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$site_name = get_bloginfo('name');
$contact_url = puppy_market_page_url('contact');
$privacy_email = sanitize_email(get_theme_mod('puppy_market_contact_email', get_option('admin_email')));
$custom_page_content = '';

if (have_posts()) {
    the_post();
    $custom_page_content = trim((string) get_the_content());
}
?>
<main id="main-content" class="ipet-policy-page ipet-privacy-page">
  <section class="ipet-policy-hero">
    <div class="container ipet-policy-hero-grid">
      <div class="ipet-policy-hero-copy">
        <p class="eyebrow">Privacy policy</p>
        <h1>How <?php echo esc_html($site_name); ?> handles personal information.</h1>
        <p>This notice explains the information involved when you browse the store, create an account, place an order or contact support.</p>
        <div class="ipet-policy-actions">
          <a class="button" href="<?php echo esc_url($contact_url); ?>">Contact us about privacy</a>
        </div>
      </div>

      <aside class="ipet-policy-highlight" aria-label="Privacy summary">
        <span aria-hidden="true"><?php echo puppy_market_service_icon('shield'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
        <p class="eyebrow">In summary</p>
        <strong>Information is used to operate the store and support your requests.</strong>
        <p>Review this page before launch and update it whenever your business, providers or data practices change.</p>
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
          <p class="eyebrow">Information involved</p>
          <h2>What the store may collect</h2>
        </div>
        <div class="ipet-policy-card-grid">
          <article><span>01</span><h3>Account and order details</h3><p>Name, contact information, delivery and billing details, account credentials and order history.</p></article>
          <article><span>02</span><h3>Store activity</h3><p>Device, browser, cookie, page-view and interaction data used to operate, protect and improve the website.</p></article>
          <article><span>03</span><h3>Messages and preferences</h3><p>Support messages, product questions, communication choices and information you submit through forms.</p></article>
        </div>
      </div>
    </section>

    <section class="ipet-policy-process">
      <div class="container">
        <div class="ipet-policy-heading">
          <p class="eyebrow">How information is used</p>
          <h2>Operating and supporting the store</h2>
        </div>
        <ol class="ipet-policy-steps">
          <li><span>1</span><div><h3>Complete transactions</h3><p>Process orders, payments, delivery, returns, refunds and messages related to a purchase.</p></div></li>
          <li><span>2</span><div><h3>Maintain the service</h3><p>Run accounts, remember preferences, prevent misuse, troubleshoot issues and understand website performance.</p></div></li>
          <li><span>3</span><div><h3>Communicate</h3><p>Respond to requests and send store or marketing messages when permitted and consistent with your choices.</p></div></li>
        </ol>
      </div>
    </section>

    <section class="ipet-policy-details">
      <div class="container ipet-policy-details-grid">
        <article>
          <p class="eyebrow">Service providers</p>
          <h2>When information may be shared</h2>
          <ul>
            <li>Hosting, analytics, security and technical service providers that help operate the website.</li>
            <li>Payment, fulfilment, delivery and customer-support providers involved in an order or request.</li>
            <li>Authorities, advisers or transaction parties when required by law or necessary to protect legitimate interests.</li>
          </ul>
        </article>
        <article>
          <p class="eyebrow">Cookies and payments</p>
          <h2>Services with their own practices</h2>
          <ul>
            <li>Cookies may support the cart, account sessions, preferences, security and site measurement.</li>
            <li>Payment providers process payment details under their own terms and privacy notices.</li>
            <li>Links to third-party websites are governed by the privacy practices of those websites.</li>
          </ul>
        </article>
      </div>
    </section>

    <section class="ipet-policy-faq">
      <div class="container">
        <div class="ipet-policy-heading">
          <p class="eyebrow">Your information</p>
          <h2>Retention, choices and requests</h2>
        </div>
        <div class="ipet-policy-faq-list">
          <details><summary>How long is information kept?</summary><p>Information is kept for as long as reasonably needed for the purpose collected, store operations, dispute resolution, security and applicable legal or accounting requirements.</p></details>
          <details><summary>What choices may be available?</summary><p>Depending on your location, you may be able to request access, correction, deletion or restriction, object to certain uses, or withdraw consent where consent is used.</p></details>
          <details><summary>How are privacy requests submitted?</summary><p>Use the Contact Us page and explain the request. Identity or account information may be needed before a request can be completed securely.</p></details>
          <details><summary>Can this policy change?</summary><p>Yes. The page may be updated when store features, service providers, legal requirements or information practices change.</p></details>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="ipet-policy-cta">
    <div class="container">
      <div><p class="eyebrow">Privacy contact</p><h2>Questions or requests about your information?</h2><p><?php echo $privacy_email ? 'Contact ' . esc_html($privacy_email) . ' or use the contact form.' : 'Use the contact form and describe your privacy question or request.'; ?></p></div>
      <a class="button" href="<?php echo esc_url($contact_url); ?>">Contact us</a>
    </div>
  </section>
</main>
<?php get_footer(); ?>
