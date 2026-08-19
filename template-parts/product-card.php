<?php
/**
 * Reusable product card, shared by the homepage rails and the shop/category grid.
 *
 * Expected $args:
 * - product          (WC_Product) required
 * - badge             (string)  overlay ribbon text, e.g. "Best seller" / "Sale"
 * - badges            (array)   extra overlay ribbon texts stacked below badge
 * - show_description  (bool)    show a trimmed short description line
 * - show_details      (bool)    show brand, rating, saving and stock details
 * - show_sale_label   (bool)    show the inline Sale label when no Sale badge exists
 * - card_class        (string)  extra class(es) added to the wrapper (ignored when bare)
 * - bare              (bool)    when true, output only the inner markup with no
 *                                <article> wrapper — use this inside a WooCommerce
 *                                <li class="product"> that already provides the card box.
 *
 * Badges stack instead of a single ribbon (like Chewy's cards, which often show
 * "Sale" + another tag at once). Autoship was dropped from the theme, so cards
 * no longer auto-add an "Autoship & Save" badge or a discounted Autoship price
 * line.
 */
defined('ABSPATH') || exit;

$puppy_card_product = isset($args['product']) ? $args['product'] : null;
if (!$puppy_card_product) return;

$puppy_card_badge = isset($args['badge']) ? $args['badge'] : '';
$puppy_card_show_description = !empty($args['show_description']);
$puppy_card_show_details = !array_key_exists('show_details', $args) || !empty($args['show_details']);
$puppy_card_show_sale_label = !array_key_exists('show_sale_label', $args) || !empty($args['show_sale_label']);
$puppy_card_class = isset($args['card_class']) ? ' ' . sanitize_html_class($args['card_class']) : '';
$puppy_card_bare = !empty($args['bare']);

$puppy_card_url = get_permalink($puppy_card_product->get_id());
$puppy_card_cart_url = $puppy_card_product->add_to_cart_url();
$puppy_card_ajax_endpoint = class_exists('WC_AJAX') ? WC_AJAX::get_endpoint('add_to_cart') : $puppy_card_cart_url;

$puppy_card_badges = array();
if ($puppy_card_badge) $puppy_card_badges[] = $puppy_card_badge;
if (!empty($args['badges']) && is_array($args['badges'])) {
    foreach ($args['badges'] as $puppy_card_extra_badge) if ($puppy_card_extra_badge) $puppy_card_badges[] = $puppy_card_extra_badge;
}
$puppy_card_badges = array_slice(array_unique($puppy_card_badges), 0, 3);

$puppy_card_brand_name = '';
$puppy_card_brand_taxonomy = function_exists('puppy_market_brand_taxonomy') ? puppy_market_brand_taxonomy() : 'product_tag';
$puppy_card_brand_terms = get_the_terms($puppy_card_product->get_id(), $puppy_card_brand_taxonomy);
if (!is_wp_error($puppy_card_brand_terms) && !empty($puppy_card_brand_terms)) {
    $puppy_card_brand_name = $puppy_card_brand_terms[0]->name;
}

$puppy_card_rating = (float) $puppy_card_product->get_average_rating();
$puppy_card_review_count = (int) $puppy_card_product->get_review_count();
$puppy_card_saving_percent = 0;
$puppy_card_regular_price = (float) $puppy_card_product->get_regular_price();
$puppy_card_current_price = (float) $puppy_card_product->get_price();
if ($puppy_card_product->is_type('simple') && $puppy_card_product->is_on_sale() && $puppy_card_regular_price > 0 && $puppy_card_current_price >= 0 && $puppy_card_current_price < $puppy_card_regular_price) {
    $puppy_card_saving_percent = (int) round((1 - ($puppy_card_current_price / $puppy_card_regular_price)) * 100);
}
?>
<?php if (!$puppy_card_bare) : ?><article class="product<?php echo esc_attr($puppy_card_class); ?>"><?php endif; ?>
  <div class="product-card-media">
    <a href="<?php echo esc_url($puppy_card_url); ?>"><div class="product-image"><?php echo wp_kses_post($puppy_card_product->get_image('woocommerce_thumbnail', array('loading' => 'lazy'))); ?></div></a>
    <?php if (!empty($puppy_card_badges)) : ?>
    <div class="product-card-badges">
      <?php foreach ($puppy_card_badges as $puppy_card_badge_text) : ?>
        <span class="product-card-badge"><?php echo esc_html($puppy_card_badge_text); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if ($puppy_card_show_details && $puppy_card_brand_name) : ?><p class="product-card-brand"><?php echo esc_html($puppy_card_brand_name); ?></p><?php endif; ?>
  <h3><a href="<?php echo esc_url($puppy_card_url); ?>"><?php echo esc_html($puppy_card_product->get_name()); ?></a></h3>
  <?php if ($puppy_card_show_description) : ?><p><?php echo esc_html(wp_trim_words($puppy_card_product->get_short_description() ?: $puppy_card_product->get_description(), 10)); ?></p><?php endif; ?>
  <?php if ($puppy_card_show_details && $puppy_card_review_count > 0 && function_exists('wc_get_rating_html')) : ?>
    <div class="product-card-rating" aria-label="<?php echo esc_attr(sprintf('%1$s out of 5 stars from %2$d reviews', number_format_i18n($puppy_card_rating, 1), $puppy_card_review_count)); ?>">
      <strong><?php echo esc_html(number_format_i18n($puppy_card_rating, 1)); ?></strong>
      <?php echo wp_kses_post(wc_get_rating_html($puppy_card_rating, $puppy_card_review_count)); ?>
      <span><?php echo esc_html(number_format_i18n($puppy_card_review_count)); ?></span>
    </div>
  <?php elseif ($puppy_card_show_details) : ?>
    <div class="product-card-rating is-empty"><span class="product-card-empty-stars" aria-hidden="true">★★★★★</span><span>No reviews yet</span></div>
  <?php endif; ?>
  <div class="product-price-row">
    <span class="price"><?php echo wp_kses_post($puppy_card_product->get_price_html()); ?></span>
    <?php if ($puppy_card_show_sale_label && $puppy_card_product->is_on_sale() && !in_array('Sale', $puppy_card_badges, true)) : ?><span class="deal-label">Sale</span><?php endif; ?>
  </div>
  <?php if ($puppy_card_show_details && $puppy_card_saving_percent > 0) : ?><p class="product-card-saving">Save <?php echo absint($puppy_card_saving_percent); ?>% today</p><?php endif; ?>
  <?php if ($puppy_card_show_details) : ?>
    <p class="product-card-availability <?php echo $puppy_card_product->is_in_stock() ? 'is-available' : 'is-unavailable'; ?>">
      <span aria-hidden="true"></span><?php echo esc_html($puppy_card_product->is_in_stock() ? ($puppy_card_product->is_on_backorder(1) ? 'Available on backorder' : 'In stock') : 'Out of stock'); ?>
    </p>
  <?php endif; ?>
  <?php if ($puppy_card_product->is_purchasable() && $puppy_card_product->is_in_stock()) : ?>
    <?php if ($puppy_card_product->is_type('simple')) : ?>
      <a class="button product-cart-button product-ajax-cart" data-product-id="<?php echo absint($puppy_card_product->get_id()); ?>" data-add-to-cart-url="<?php echo esc_url($puppy_card_ajax_endpoint); ?>" href="<?php echo esc_url($puppy_card_cart_url); ?>">Add to cart</a>
    <?php else : ?>
      <a class="button product-cart-button" href="<?php echo esc_url($puppy_card_url); ?>">Choose options</a>
    <?php endif; ?>
  <?php elseif (!$puppy_card_product->is_in_stock()) : ?>
    <span class="stock-label">Out of stock</span>
  <?php endif; ?>
<?php if (!$puppy_card_bare) : ?></article><?php endif; ?>
