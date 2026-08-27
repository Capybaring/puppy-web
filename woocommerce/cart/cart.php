<?php
/**
 * Cart page — theme override of woocommerce/templates/cart/cart.php.
 *
 * European-marketplace style: a wide two-column layout with the cart items on
 * the left (~72%) and a sticky Order Summary on the right (~28%), plus a
 * "Inspired by your cart" recommendation rail below. Built as a theme override
 * of the classic (shortcode) cart so quantity updates, coupons and checkout
 * still work through the standard WooCommerce hooks.
 *
 * The site header and footer are rendered normally (page.php); only the main
 * content area is replaced by this template.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.8.0
 */

defined( 'ABSPATH' ) || exit;

$puppy_cart_total      = WC()->cart->get_displayed_subtotal();
$puppy_cart_item_count = WC()->cart->get_cart_contents_count();
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="puppy-cart-layout">

		<!-- LEFT: Shopping Cart -->
		<div class="puppy-cart-main">
			<header class="puppy-cart-heading">
				<h1>Shopping Cart</h1>
				<span class="puppy-cart-item-count"><?php echo esc_html( $puppy_cart_item_count ); ?> item<?php echo absint( $puppy_cart_item_count ) === 1 ? '' : 's'; ?></span>
			</header>

			<?php if ( WC()->cart->is_empty() ) : ?>
				<div class="puppy-cart-empty">
					<span class="puppy-cart-empty-icon" aria-hidden="true">🛒</span>
					<h2>Your cart is currently empty</h2>
					<p>Find something great for your pets today.</p>
					<a class="puppy-cart-empty-shop" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">Continue shopping</a>
				</div>
			<?php else : ?>
				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
					<thead>
						<tr>
							<th class="product-remove"><span class="screen-reader-text">Remove item</span></th>
							<th class="product-thumbnail"><span class="screen-reader-text">Thumbnail image</span></th>
							<th scope="col" class="product-name">Product</th>
							<th scope="col" class="product-price">Price</th>
							<th scope="col" class="product-quantity">Quantity</th>
							<th scope="col" class="product-subtotal">Subtotal</th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

							$visible = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

							if ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
								$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
								$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
								?>
								<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

									<td class="product-remove" data-title="<?php esc_attr_e( 'Remove item', 'woocommerce' ); ?>">
										<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												/* translators: %s is the product name */
												esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() )
											),
											$cart_item_key
										);
										?>
									</td>

									<td class="product-thumbnail">
										<div class="puppy-cart-thumb">
											<?php
											$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'puppy-cart-400' ), $cart_item, $cart_item_key );
											if ( ! $product_permalink ) {
												echo $thumbnail; // PHPCS: XSS ok.
											} else {
												printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
											}
											?>
										</div>
									</td>

									<td scope="row" role="rowheader" class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
										<span class="puppy-cart-brand"><?php echo esc_html( $_product->get_meta( '_brand', true ) ? $_product->get_meta( '_brand', true ) : 'iPet' ); ?></span>
										<?php
										if ( ! $product_permalink ) {
											echo wp_kses_post( $product_name . '&nbsp;' );
										} else {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a class="puppy-cart-product-title" href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
										}
										?>

										<div class="puppy-cart-unit-price"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok. ?></div>

										<?php
										do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
										echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

										if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
										}

										if ( $_product->is_on_sale() ) {
											printf(
												'<div class="puppy-cart-promo"><span>%s</span> <a href="%s">Details</a> %s</div>',
												esc_html__( 'Save an extra 10% with your first order', 'hero-theme' ),
												esc_url( $product_permalink ),
												'<span class="puppy-cart-promo-plus">+1 <u>deal</u></span>'
											);
										}
										?>
									</td>

									<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
										<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok. ?>
									</td>

									<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
										<div class="puppy-cart-actions-inner">
											<span class="puppy-cart-qty-label"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></span>
											<?php
											if ( $_product->is_sold_individually() ) {
												$min_quantity = 1;
												$max_quantity = 1;
											} else {
												$min_quantity = 0;
												$max_quantity = $_product->get_max_purchase_quantity();
											}
											$product_quantity = woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $max_quantity,
													'min_value'    => $min_quantity,
													'product_name' => $product_name,
												),
												$_product,
												false
											);
											echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
											?>
											<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="puppy-cart-remove-button" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ); ?>">
												<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="none"><path d="M2 4h12M6.5 4V2.5h3V4M4 4l.7 9h6.6l.7-9M6.7 6.5v4M9.3 6.5v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
												<span><?php esc_html_e( 'Remove', 'woocommerce' ); ?></span>
											</a>
										</div>
									</td>

									<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
										<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok. ?>
									</td>
								</tr>
								<?php
							}
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>
						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				<input type="hidden" name="update_cart" value="Update cart" />
			<?php endif; ?>
		</div>

		<!-- RIGHT: Order Summary -->
		<?php if ( ! WC()->cart->is_empty() ) : ?>
			<aside class="puppy-cart-sidebar">
					<div class="puppy-cart-order-summary">
						<h2>Order Summary</h2>

						<div class="puppy-cart-subtotal-row">
							<span class="puppy-cart-subtotal-label">Subtotal</span>
							<span class="puppy-cart-subtotal-value"><?php echo wp_kses_post( wc_price( $puppy_cart_total ) ); ?></span>
						</div>
						<div class="puppy-cart-meta-row">
							<span><?php echo esc_html( $puppy_cart_item_count ); ?> item<?php echo absint( $puppy_cart_item_count ) === 1 ? '' : 's'; ?></span>
							<span class="puppy-cart-see-details">See details <span aria-hidden="true">+</span></span>
						</div>

						<?php if ( WC()->cart->get_coupons() ) : foreach ( WC()->cart->get_coupons() as $puppy_coupon ) : ?>
							<div class="puppy-cart-order-row puppy-cart-discount"><span>Discount (<?php echo esc_html( $puppy_coupon->get_code() ); ?>)</span><strong>-<?php echo wp_kses_post( wc_price( WC()->cart->get_coupon_discount_amount( $puppy_coupon->get_code(), WC()->cart->display_cart_ex_tax ) ) ); ?></strong></div>
						<?php endforeach; endif; ?>
						<?php foreach ( WC()->cart->get_fees() as $puppy_fee ) : ?>
							<div class="puppy-cart-order-row"><span><?php echo esc_html( $puppy_fee->name ); ?></span><strong><?php echo wp_kses_post( wc_price( $puppy_fee->amount ) ); ?></strong></div>
						<?php endforeach; ?>

						<div class="puppy-cart-shipping-note">
							<?php
							$puppy_free_ship = 49;
							$puppy_remaining = max( 0, $puppy_free_ship - $puppy_cart_total );
							if ( $puppy_remaining > 0 ) {
								printf( 'You are <strong>%s</strong> away from <strong class="puppy-cart-free-ship">FREE shipping</strong>', wp_kses_post( wc_price( $puppy_remaining ) ) );
							} else {
								echo 'Your order ships <strong class="puppy-cart-free-ship">FREE</strong>!';
							}
							?>
							<div class="puppy-cart-ship-progress" aria-hidden="true"><span style="width: <?php echo esc_attr( min( 100, ( $puppy_cart_total / $puppy_free_ship ) * 100 ) ); ?>%"></span></div>
						</div>

						<?php if ( wc_coupons_enabled() ) : ?>
							<div class="puppy-cart-promo" data-promo-wrap>
								<div class="puppy-cart-promo-toggle" data-promo-toggle>
									<span>Promo code</span>
									<span class="puppy-cart-promo-chevron" aria-hidden="true">+</span>
								</div>
								<div class="puppy-cart-promo-panel" data-promo-panel>
									<div class="puppy-cart-promo-field">
										<input type="text" name="coupon_code" class="puppy-cart-promo-input" id="puppy_coupon_code" value="" placeholder="Enter promo code" />
										<button type="submit" class="puppy-cart-promo-apply" name="apply_coupon" value="Apply coupon">Apply</button>
									</div>
									<div class="puppy-cart-promo-msg" data-promo-msg></div>
								</div>
							</div>
						<?php endif; ?>

						<div class="puppy-cart-order-total">
							<span>Estimated total</span>
							<strong><?php echo wp_kses_post( WC()->cart->get_total() ); ?></strong>
						</div>

						<div class="wc-proceed-to-checkout">
							<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
						</div>

						<p class="puppy-cart-secure-note">🔒 Secure checkout</p>
					</div>

				<?php
				/**
				 * Cart collaterals hook — keep for compatibility, but our main
				 * cards below are the reassurance + payment cards.
				 */
				do_action( 'woocommerce_cart_collaterals' );
				?>

				<div class="puppy-cart-trust-card">
					<div class="puppy-cart-trust-row"><span aria-hidden="true">✓</span><div><strong>Fast delivery</strong><p>Reliable delivery to your door</p></div></div>
					<div class="puppy-cart-trust-row"><span aria-hidden="true">✓</span><div><strong>Easy returns</strong><p>Hassle-free returns on eligible items</p></div></div>
					<div class="puppy-cart-trust-row"><span aria-hidden="true">✓</span><div><strong>Secure checkout</strong><p>Your payment information is protected</p></div></div>
				</div>

				<?php $puppy_cart_payment_methods = puppy_market_payment_methods(); ?>
				<?php if ( ! empty( $puppy_cart_payment_methods ) ) : ?>
					<div class="puppy-cart-pay-card">
						<span class="puppy-cart-pay-label">Secure payment</span>
						<div class="puppy-cart-pay-badges"><?php foreach ( $puppy_cart_payment_methods as $puppy_cart_payment_method ) : ?><span><?php echo esc_html( $puppy_cart_payment_method ); ?></span><?php endforeach; ?></div>
					</div>
				<?php endif; ?>
			</aside>
		<?php endif; ?>

	</div>

	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php if ( ! WC()->cart->is_empty() && function_exists( 'wc_get_products' ) ) : ?>
	<section class="puppy-cart-recommendations">
		<header class="puppy-cart-recommendations-header">
			<h2>Inspired by your cart</h2>
			<div class="puppy-cart-rec-controls">
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?><a class="puppy-cart-rec-shopall" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Shop all</a><?php endif; ?>
				<button type="button" class="puppy-cart-rec-arrow puppy-cart-rec-prev" aria-label="Previous recommendations"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
				<button type="button" class="puppy-cart-rec-arrow puppy-cart-rec-next" aria-label="Next recommendations"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
			</div>
		</header>
		<div class="puppy-cart-recommendations-rail" data-rec-rail>
			<?php
			$puppy_recent = wc_get_products( array( 'status' => 'publish', 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC' ) );
			foreach ( $puppy_recent as $puppy_rec_product ) :
				get_template_part( 'template-parts/product-card', null, array(
					'product'         => $puppy_rec_product,
					'card_class'      => 'puppy-cart-recommendation',
					'show_sale_label' => false,
					'show_reviews'    => false,
				) );
			endforeach;
			?>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! WC()->cart->is_empty() && function_exists( 'wc_get_page_permalink' ) ) : ?>
	<div class="puppy-cart-mobile-bar">
		<div class="puppy-cart-mobile-bar-total">
			<span>Total</span>
			<strong><?php echo wp_kses_post( WC()->cart->get_total() ); ?></strong>
		</div>
		<a class="puppy-cart-mobile-bar-checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">Checkout</a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_cart' ); ?>
