<?php
/**
 * Three-column product detail page.
 *
 * @package WooCommerce\Templates
 * @version 1.6.4
 */

defined('ABSPATH') || exit;

get_header();

$puppy_promotion_text = get_theme_mod('puppy_market_pdp_promotion_text', 'Free shipping on orders $49+');
?>
<main id="main-content" class="ipet-pdp-shell">
    <?php if ($puppy_promotion_text) : ?>
        <div class="ipet-pdp-promotion"><span><?php echo esc_html($puppy_promotion_text); ?></span></div>
    <?php endif; ?>

    <div class="ipet-pdp-container">
        <?php if (function_exists('woocommerce_breadcrumb')) : ?>
            <nav class="ipet-pdp-breadcrumb" aria-label="Breadcrumb">
                <?php woocommerce_breadcrumb(array('delimiter' => '<span aria-hidden="true">/</span>', 'home' => 'Home')); ?>
            </nav>
        <?php endif; ?>

        <?php while (have_posts()) : the_post(); ?>
            <?php do_action('woocommerce_before_single_product'); ?>

            <?php if (post_password_required()) : ?>
                <?php echo get_the_password_form(); ?>
            <?php else :
                global $product;
                $product = wc_get_product();
                if (!$product) continue;
                if (function_exists('WC') && isset(WC()->structured_data) && method_exists(WC()->structured_data, 'generate_product_data')) {
                    WC()->structured_data->generate_product_data($product);
                }

                $puppy_brand_name = 'iPet';
                $puppy_brand_url = home_url('/');
                $puppy_brand_taxonomy = function_exists('puppy_market_brand_taxonomy') ? puppy_market_brand_taxonomy() : 'product_tag';
                $puppy_brand_terms = get_the_terms($product->get_id(), $puppy_brand_taxonomy);
                if (!is_wp_error($puppy_brand_terms) && !empty($puppy_brand_terms)) {
                    $puppy_brand_name = $puppy_brand_terms[0]->name;
                    $puppy_brand_link = get_term_link($puppy_brand_terms[0]);
                    if (!is_wp_error($puppy_brand_link)) $puppy_brand_url = $puppy_brand_link;
                }

                $puppy_attributes = array_filter($product->get_attributes(), function ($attribute) {
                    return $attribute->get_visible() || $attribute->get_variation();
                });
                $puppy_stock_text = $product->is_in_stock() ? ($product->is_on_backorder(1) ? 'Available on backorder' : 'In stock') : 'Out of stock';
                $puppy_eta_timestamp = strtotime('+3 weekdays', current_time('timestamp'));
                $puppy_eta = $puppy_eta_timestamp ? date_i18n('D, M j', $puppy_eta_timestamp) : '';
                $puppy_rating = (float) $product->get_average_rating();
                $puppy_review_count = (int) $product->get_review_count();
                $puppy_show_rating = function_exists('wc_review_ratings_enabled')
                    && wc_review_ratings_enabled()
                    && method_exists($product, 'get_reviews_allowed')
                    && $product->get_reviews_allowed()
                    && $puppy_review_count > 0
                    && function_exists('wc_get_rating_html');
            ?>
                <article id="product-<?php the_ID(); ?>" <?php wc_product_class('ipet-pdp', $product); ?>>
                    <section class="ipet-pdp-main" aria-label="Product purchase information">
                        <div class="ipet-pdp-mobile-heading">
                            <p class="ipet-pdp-kicker">iPet everyday essential</p>
                            <p class="ipet-pdp-mobile-title" role="heading" aria-level="1"><?php the_title(); ?></p>
                            <p class="ipet-pdp-brand">By <a href="<?php echo esc_url($puppy_brand_url); ?>"><?php echo esc_html($puppy_brand_name); ?></a></p>
                            <?php if ($puppy_show_rating) : ?>
                                <div class="ipet-pdp-rating" aria-label="<?php echo esc_attr(sprintf('%1$s out of 5 stars from %2$d reviews', number_format_i18n($puppy_rating, 1), $puppy_review_count)); ?>"><strong><?php echo esc_html(number_format_i18n($puppy_rating, 1)); ?></strong><?php echo wp_kses_post(wc_get_rating_html($puppy_rating, $puppy_review_count)); ?><span><?php echo esc_html(number_format_i18n($puppy_review_count)); ?> reviews</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="ipet-pdp-gallery-column">
                            <?php woocommerce_show_product_images(); ?>
                        </div>

                        <section class="ipet-pdp-info-column" aria-labelledby="ipet-pdp-title">
                            <p class="ipet-pdp-kicker">iPet everyday essential</p>
                            <h1 id="ipet-pdp-title" class="product_title entry-title"><?php the_title(); ?></h1>
                            <p class="ipet-pdp-brand">By <a href="<?php echo esc_url($puppy_brand_url); ?>"><?php echo esc_html($puppy_brand_name); ?></a></p>
                            <?php if ($puppy_show_rating) : ?>
                                <div class="ipet-pdp-rating" aria-label="<?php echo esc_attr(sprintf('%1$s out of 5 stars from %2$d reviews', number_format_i18n($puppy_rating, 1), $puppy_review_count)); ?>"><strong><?php echo esc_html(number_format_i18n($puppy_rating, 1)); ?></strong><?php echo wp_kses_post(wc_get_rating_html($puppy_rating, $puppy_review_count)); ?><span><?php echo esc_html(number_format_i18n($puppy_review_count)); ?> reviews</span></div>
                            <?php endif; ?>

                            <?php if ($product->get_short_description()) : ?>
                                <div class="ipet-pdp-summary-copy"><?php echo wp_kses_post(wpautop($product->get_short_description())); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($puppy_attributes)) : ?>
                                <div class="ipet-pdp-options" aria-label="Product options">
                                    <?php foreach ($puppy_attributes as $puppy_attribute) :
                                        $puppy_attribute_name = wc_attribute_label($puppy_attribute->get_name());
                                        $puppy_option_values = array();
                                        if ($puppy_attribute->is_taxonomy()) {
                                            $puppy_terms = wc_get_product_terms($product->get_id(), $puppy_attribute->get_name(), array('fields' => 'all'));
                                            foreach ($puppy_terms as $puppy_term) $puppy_option_values[$puppy_term->slug] = $puppy_term->name;
                                        } else {
                                            foreach ($puppy_attribute->get_options() as $puppy_option) $puppy_option_values[sanitize_title($puppy_option)] = $puppy_option;
                                        }
                                        if (empty($puppy_option_values)) continue;
                                        $puppy_select_name = 'attribute_' . sanitize_title($puppy_attribute->get_name());
                                    ?>
                                        <fieldset class="ipet-pdp-option-group" data-pdp-option-group="<?php echo esc_attr($puppy_select_name); ?>">
                                            <legend><?php echo esc_html($puppy_attribute_name); ?></legend>
                                            <div class="ipet-pdp-option-values">
                                                <?php foreach ($puppy_option_values as $puppy_option_slug => $puppy_option_label) : ?>
                                                    <button type="button" class="ipet-pdp-option" data-pdp-attribute="<?php echo esc_attr($puppy_select_name); ?>" data-pdp-value="<?php echo esc_attr($puppy_option_slug); ?>" aria-pressed="false">
                                                        <span><?php echo esc_html($puppy_option_label); ?></span>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </fieldset>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            $puppy_benefits = array();
                            foreach ($puppy_attributes as $puppy_attribute) {
                                $puppy_values = $puppy_attribute->is_taxonomy()
                                    ? wc_get_product_terms($product->get_id(), $puppy_attribute->get_name(), array('fields' => 'names'))
                                    : $puppy_attribute->get_options();
                                foreach ($puppy_values as $puppy_value) {
                                    if (count($puppy_benefits) >= 9) break 2;
                                    $puppy_benefits[] = array('label' => wc_attribute_label($puppy_attribute->get_name()), 'value' => $puppy_value);
                                }
                            }
                            ?>
                            <?php if (!empty($puppy_benefits)) : ?>
                                <section class="ipet-pdp-benefits" aria-labelledby="ipet-benefits-title">
                                    <h2 id="ipet-benefits-title">At a Glance</h2>
                                    <div class="ipet-pdp-benefit-grid">
                                        <?php foreach ($puppy_benefits as $puppy_benefit) : ?>
                                            <div class="ipet-pdp-benefit"><span aria-hidden="true">✓</span><div><small><?php echo esc_html($puppy_benefit['label']); ?></small><strong><?php echo esc_html($puppy_benefit['value']); ?></strong></div></div>
                                        <?php endforeach; ?>
                                    </div>
                                </section>
                            <?php endif; ?>
                        </section>

                        <aside class="ipet-pdp-purchase-column" aria-label="Purchase options">
                            <div class="ipet-pdp-purchase-panel">
                                <div class="ipet-pdp-purchase-heading">
                                    <div class="ipet-pdp-price" data-pdp-price><?php echo wp_kses_post($product->get_price_html()); ?></div>
                                    <?php if ($product->is_on_sale()) : ?><span class="ipet-pdp-sale-note">Sale price applied</span><?php endif; ?>
                                </div>

                                <div class="ipet-pdp-offers">
                                    <?php if ($product->is_on_sale()) : ?>
                                        <div class="ipet-pdp-offer"><span aria-hidden="true">%</span><p><strong>Limited-time deal</strong><br>Sale pricing is already applied to this item.</p></div>
                                    <?php endif; ?>
                                    <div class="ipet-pdp-offer"><span aria-hidden="true">⌁</span><p><strong>Free shipping over $49</strong><br>Qualifying orders ship free.</p></div>
                                </div>

                                <div class="ipet-pdp-stock-row">
                                    <div><small>Availability</small><strong class="<?php echo $product->is_in_stock() ? 'is-in-stock' : 'is-out-of-stock'; ?>" data-pdp-stock><?php echo esc_html($puppy_stock_text); ?></strong></div>
                                    <div><small>Delivery</small><strong><?php echo $product->is_in_stock() ? 'Get it by ' . esc_html($puppy_eta) : 'Currently unavailable'; ?></strong></div>
                                </div>

                                <div class="ipet-pdp-cart-form">
                                    <?php woocommerce_template_single_add_to_cart(); ?>
                                </div>

                                <div class="ipet-pdp-reassurance">
                                    <p><span aria-hidden="true">↻</span><strong>30-day easy returns</strong><a href="<?php echo esc_url(puppy_market_page_url('returns')); ?>">Details</a></p>
                                    <p><span aria-hidden="true">✓</span><strong>Secure checkout</strong><small>Protected payment processing</small></p>
                                </div>

                                <div class="ipet-pdp-account-benefit">
                                    <span aria-hidden="true">iP</span><div><strong>Make checkout easier</strong><p>Sign in to keep your order history and manage purchases in one place.</p><a href="<?php echo esc_url(puppy_market_account_url()); ?>">Sign in or create an account</a></div>
                                </div>

                                <div class="ipet-pdp-payments" aria-label="Accepted payment methods"><span>Visa</span><span>Mastercard</span><span>PayPal</span><span>Apple Pay</span></div>
                            </div>

                            <?php
                            $puppy_related_ids = function_exists('wc_get_related_products') ? wc_get_related_products($product->get_id(), 1) : array();
                            if (!empty($puppy_related_ids)) :
                                $puppy_related = wc_get_product($puppy_related_ids[0]);
                                if ($puppy_related) :
                            ?>
                                <a class="ipet-pdp-mini-product" href="<?php echo esc_url($puppy_related->get_permalink()); ?>">
                                    <span class="ipet-pdp-mini-image"><?php echo wp_kses_post($puppy_related->get_image('woocommerce_thumbnail')); ?></span>
                                    <span><small>You may also like</small><strong><?php echo esc_html($puppy_related->get_name()); ?></strong><b><?php echo wp_kses_post($puppy_related->get_price_html()); ?></b></span>
                                </a>
                            <?php endif; endif; ?>
                        </aside>
                    </section>

                    <?php if (function_exists('puppy_market_product_about_item')) puppy_market_product_about_item(); ?>

                    <?php do_action('woocommerce_after_single_product_summary'); ?>

                    <div class="ipet-pdp-mobile-cart" aria-hidden="true">
                        <span data-pdp-mobile-price><?php echo wp_kses_post($product->get_price_html()); ?></span>
                        <button type="button" data-pdp-mobile-add>Add to cart</button>
                    </div>
                </article>
            <?php endif; ?>

            <?php do_action('woocommerce_after_single_product'); ?>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer();

/* Omit closing PHP tag to avoid accidental output. */
