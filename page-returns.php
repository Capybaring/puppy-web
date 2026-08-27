<?php
/**
 * Template Name: Returns
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$contact_url = add_query_arg('contact_topic', 'returns', puppy_market_page_url('contact')) . '#contact-form';
$orders_url = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('orders')
    : puppy_market_account_url();
?>
<main id="main-content" class="ipet-policy-page ipet-returns-page">
  <section class="ipet-policy-hero">
    <div class="container ipet-policy-hero-grid">
      <div class="ipet-policy-hero-copy">
        <p class="eyebrow">Returns</p>
        <h1>A straightforward path when an item is not right.</h1>
        <p>Check the basic requirements, contact support before sending anything back and keep your order number ready.</p>
        <div class="ipet-policy-actions">
          <a class="button" href="<?php echo esc_url($contact_url); ?>">Start a return</a>
          <a class="ipet-policy-text-link" href="<?php echo esc_url($orders_url); ?>">Find your order →</a>
        </div>
      </div>

      <aside class="ipet-policy-highlight" aria-label="Return policy summary">
        <span aria-hidden="true"><?php echo puppy_market_service_icon('returns'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
        <p class="eyebrow">Return window</p>
        <strong>Eligible unused items can be requested for return within 30 days of delivery.</strong>
        <p>Support confirms eligibility and provides the correct instructions for the product and order.</p>
      </aside>
    </div>
  </section>

  <section class="ipet-policy-summary">
    <div class="container">
      <div class="ipet-policy-heading">
        <p class="eyebrow">At a glance</p>
        <h2>Before you begin</h2>
      </div>
      <div class="ipet-policy-card-grid">
        <article><span>01</span><h3>Request first</h3><p>Contact support before shipping an item back so the return can be reviewed and recorded.</p></article>
        <article><span>02</span><h3>Keep the order number</h3><p>The order number helps support identify the item, purchase date and available next step.</p></article>
        <article><span>03</span><h3>Wait for instructions</h3><p>Do not send an item to an unconfirmed address; follow the return instructions supplied by support.</p></article>
      </div>
    </div>
  </section>

  <section class="ipet-policy-process">
    <div class="container">
      <div class="ipet-policy-heading">
        <p class="eyebrow">Return journey</p>
        <h2>Three clear steps</h2>
      </div>
      <ol class="ipet-policy-steps">
        <li><span>1</span><div><h3>Send the request</h3><p>Tell support the order number, item and reason for the return. Add photos when an item arrived damaged or incorrect.</p></div></li>
        <li><span>2</span><div><h3>Receive confirmation</h3><p>Support reviews eligibility and sends the appropriate packaging, address and shipping guidance.</p></div></li>
        <li><span>3</span><div><h3>Complete the return</h3><p>Once the return is received and reviewed, support confirms the available refund or replacement outcome.</p></div></li>
      </ol>
    </div>
  </section>

  <section class="ipet-policy-details">
    <div class="container ipet-policy-details-grid">
      <article>
        <p class="eyebrow">Generally required</p>
        <h2>Keep the item return-ready</h2>
        <ul>
          <li>Request the return within 30 days of the recorded delivery date.</li>
          <li>Keep the item unused and include its original packaging and supplied parts when possible.</li>
          <li>Provide the order number and accurate information about the item's condition.</li>
        </ul>
      </article>
      <article>
        <p class="eyebrow">Contact us first</p>
        <h2>Some products need review</h2>
        <ul>
          <li>Opened consumables, hygiene-sensitive goods and regulated products may have additional restrictions.</li>
          <li>Damaged, defective or incorrect items may follow a different resolution process.</li>
          <li>Support confirms eligibility before you pay for or arrange return shipping.</li>
        </ul>
      </article>
    </div>
  </section>

  <section class="ipet-policy-faq">
    <div class="container">
      <div class="ipet-policy-heading">
        <p class="eyebrow">Quick answers</p>
        <h2>Return questions</h2>
      </div>
      <div class="ipet-policy-faq-list">
        <details><summary>Can I send an item back without contacting support?</summary><p>Please contact support first. The team needs to confirm eligibility and provide the correct return destination and instructions.</p></details>
        <details><summary>What if my item arrived damaged or incorrect?</summary><p>Start a return request and include the order number, a description and clear photos. Support will review the appropriate refund, replacement or return route.</p></details>
        <details><summary>When will a refund be completed?</summary><p>Timing depends on the return method and inspection. Support will confirm the outcome after the returned item or required evidence has been reviewed.</p></details>
      </div>
    </div>
  </section>

  <section class="ipet-policy-cta">
    <div class="container">
      <div><p class="eyebrow">Ready to start?</p><h2>Send a return request.</h2><p>The contact form will open with Returns already selected. Add your order number and the details of the item.</p></div>
      <a class="button" href="<?php echo esc_url($contact_url); ?>">Start a return</a>
    </div>
  </section>
</main>
<?php get_footer(); ?>
