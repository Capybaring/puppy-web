<?php
/**
 * Template Name: Shipping
 * Template Post Type: page
 */

defined('ABSPATH') || exit;

get_header();

$contact_url = add_query_arg('contact_topic', 'shipping', puppy_market_page_url('contact')) . '#contact-form';
$orders_url = function_exists('wc_get_account_endpoint_url')
    ? wc_get_account_endpoint_url('orders')
    : puppy_market_account_url();
?>
<main id="main-content" class="ipet-policy-page ipet-shipping-page">
  <section class="ipet-policy-hero">
    <div class="container ipet-policy-hero-grid">
      <div class="ipet-policy-hero-copy">
        <p class="eyebrow">Shipping</p>
        <h1>Clear delivery from checkout to your door.</h1>
        <p>Review shipping eligibility, follow your order and know where to go when a delivery needs attention.</p>
        <div class="ipet-policy-actions">
          <a class="button" href="<?php echo esc_url($orders_url); ?>">Track an order</a>
          <a class="ipet-policy-text-link" href="<?php echo esc_url($contact_url); ?>">Ask about shipping →</a>
        </div>
      </div>

      <aside class="ipet-policy-highlight" aria-label="Free shipping summary">
        <span aria-hidden="true"><?php echo puppy_market_service_icon('shipping'); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme SVG. */ ?></span>
        <p class="eyebrow">Delivery benefit</p>
        <strong>Free shipping on eligible orders of $49 or more.</strong>
        <p>Eligibility, available delivery methods and the final estimate are confirmed during checkout.</p>
      </aside>
    </div>
  </section>

  <section class="ipet-policy-summary">
    <div class="container">
      <div class="ipet-policy-heading">
        <p class="eyebrow">At a glance</p>
        <h2>What to expect</h2>
      </div>
      <div class="ipet-policy-card-grid">
        <article><span>01</span><h3>Checkout estimate</h3><p>Available methods and delivery estimates are shown before you place the order.</p></article>
        <article><span>02</span><h3>Order updates</h3><p>We use your order details to share confirmation, dispatch and tracking information.</p></article>
        <article><span>03</span><h3>Delivery support</h3><p>If tracking is unclear or a parcel needs attention, send support your order number.</p></article>
      </div>
    </div>
  </section>

  <section class="ipet-policy-process">
    <div class="container">
      <div class="ipet-policy-heading">
        <p class="eyebrow">Delivery journey</p>
        <h2>How shipping works</h2>
      </div>
      <ol class="ipet-policy-steps">
        <li><span>1</span><div><h3>Place your order</h3><p>Confirm the delivery address and choose from the methods available at checkout.</p></div></li>
        <li><span>2</span><div><h3>Watch for dispatch</h3><p>Once the parcel leaves the fulfilment location, tracking is added to your order when available.</p></div></li>
        <li><span>3</span><div><h3>Follow delivery</h3><p>Use your account order history or the carrier update to follow the parcel to its destination.</p></div></li>
      </ol>
    </div>
  </section>

  <section class="ipet-policy-details">
    <div class="container ipet-policy-details-grid">
      <article>
        <p class="eyebrow">Before dispatch</p>
        <h2>Check the important details</h2>
        <ul>
          <li>Review the recipient name, address and contact information before payment.</li>
          <li>Shipping eligibility can vary by item, destination and final basket value.</li>
          <li>Contact support quickly if an address needs correcting after checkout.</li>
        </ul>
      </article>
      <article>
        <p class="eyebrow">After dispatch</p>
        <h2>Keep track of the parcel</h2>
        <ul>
          <li>Open My Account → Orders to review the latest information available to the store.</li>
          <li>Items may arrive separately when an order is fulfilled in more than one parcel.</li>
          <li>For delayed, damaged or missing deliveries, include the order number in your message.</li>
        </ul>
      </article>
    </div>
  </section>

  <section class="ipet-policy-faq">
    <div class="container">
      <div class="ipet-policy-heading">
        <p class="eyebrow">Quick answers</p>
        <h2>Shipping questions</h2>
      </div>
      <div class="ipet-policy-faq-list">
        <details><summary>How do I know when my order will arrive?</summary><p>The best current estimate appears at checkout. After dispatch, use the tracking information attached to your order when available.</p></details>
        <details><summary>Does every order qualify for free shipping?</summary><p>Free shipping applies to eligible orders of $49 or more. The checkout total and destination determine the final available methods.</p></details>
        <details><summary>What should I do if tracking has not updated?</summary><p>Carrier updates can take time to appear. If the status remains unclear, contact support with your order number so the team can review it.</p></details>
      </div>
    </div>
  </section>

  <section class="ipet-policy-cta">
    <div class="container">
      <div><p class="eyebrow">Need delivery help?</p><h2>Send us your order details.</h2><p>Include the order number and a short description so support can identify the right next step.</p></div>
      <a class="button" href="<?php echo esc_url($contact_url); ?>">Contact shipping support</a>
    </div>
  </section>
</main>
<?php get_footer(); ?>
