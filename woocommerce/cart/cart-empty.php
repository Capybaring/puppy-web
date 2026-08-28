<?php
/**
 * Empty cart page.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.8.0
 */

defined( 'ABSPATH' ) || exit;

$puppy_empty_cart_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
if ( ! $puppy_empty_cart_shop_url ) {
	$puppy_empty_cart_shop_url = home_url( '/' );
}

do_action( 'woocommerce_cart_is_empty' );
?>

<section class="puppy-cart-empty-page" aria-labelledby="puppy-cart-empty-title">
	<div class="puppy-cart-empty-content">
		<span class="puppy-cart-empty-page-icon" aria-hidden="true">
			<svg viewBox="0 0 32 32" width="34" height="34" fill="none">
				<path d="M5 7h3l2.1 12.2a2 2 0 0 0 2 1.7h10.4a2 2 0 0 0 1.9-1.4L27 11H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<circle cx="13" cy="25.5" r="1.5" fill="currentColor"/>
				<circle cx="23" cy="25.5" r="1.5" fill="currentColor"/>
			</svg>
		</span>
		<p class="puppy-cart-empty-eyebrow"><?php esc_html_e( 'Shopping cart', 'hero-theme' ); ?></p>
		<h1 id="puppy-cart-empty-title"><?php esc_html_e( 'Your cart is empty', 'hero-theme' ); ?></h1>
		<p class="puppy-cart-empty-copy"><?php esc_html_e( 'Browse food, toys and everyday essentials to find something your pet will love.', 'hero-theme' ); ?></p>
		<div class="puppy-cart-empty-actions">
			<a class="puppy-cart-empty-primary" href="<?php echo esc_url( $puppy_empty_cart_shop_url ); ?>"><?php esc_html_e( 'Shop products', 'hero-theme' ); ?></a>
			<a class="puppy-cart-empty-secondary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'hero-theme' ); ?></a>
		</div>
	</div>

	<div class="puppy-cart-empty-benefits" aria-label="<?php esc_attr_e( 'Shopping benefits', 'hero-theme' ); ?>">
		<div><span aria-hidden="true">✓</span><p><strong><?php esc_html_e( 'Fast delivery', 'hero-theme' ); ?></strong><small><?php esc_html_e( 'Reliable delivery to your door', 'hero-theme' ); ?></small></p></div>
		<div><span aria-hidden="true">✓</span><p><strong><?php esc_html_e( 'Easy returns', 'hero-theme' ); ?></strong><small><?php esc_html_e( 'Hassle-free eligible returns', 'hero-theme' ); ?></small></p></div>
		<div><span aria-hidden="true">✓</span><p><strong><?php esc_html_e( 'Secure checkout', 'hero-theme' ); ?></strong><small><?php esc_html_e( 'Protected payment information', 'hero-theme' ); ?></small></p></div>
	</div>
</section>
