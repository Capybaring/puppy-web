<?php
/** Account dashboard using real WooCommerce customer and order data. */
defined('ABSPATH') || exit;

$customer = wp_get_current_user();
$name = $customer->first_name ?: $customer->display_name;
$recent_orders = wc_get_orders(array(
    'customer_id' => get_current_user_id(),
    'limit'       => 3,
    'orderby'     => 'date',
    'order'       => 'DESC',
));
$orders_url = wc_get_account_endpoint_url('orders');
$address_url = wc_get_account_endpoint_url('edit-address');
$details_url = wc_get_account_endpoint_url('edit-account');
?>
<div class="ipet-account-dashboard">
    <header class="ipet-account-welcome">
        <p><?php esc_html_e('Account dashboard', 'puppy-market'); ?></p>
        <h2><?php echo esc_html(sprintf(__('Hi, %s!', 'puppy-market'), $name)); ?></h2>
        <span><?php esc_html_e('Manage your orders and account details in one place.', 'puppy-market'); ?></span>
    </header>

    <div class="ipet-account-quick-actions" aria-label="<?php esc_attr_e('Quick actions', 'puppy-market'); ?>">
        <a href="<?php echo esc_url($orders_url); ?>"><?php echo puppy_market_account_icon('orders'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php esc_html_e('Order history', 'puppy-market'); ?></strong><span><?php esc_html_e('Track and review orders', 'puppy-market'); ?></span></a>
        <a href="<?php echo esc_url($address_url); ?>"><?php echo puppy_market_account_icon('edit-address'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php esc_html_e('Addresses', 'puppy-market'); ?></strong><span><?php esc_html_e('Shipping and billing', 'puppy-market'); ?></span></a>
        <a href="<?php echo esc_url($details_url); ?>"><?php echo puppy_market_account_icon('edit-account'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php esc_html_e('Account settings', 'puppy-market'); ?></strong><span><?php esc_html_e('Profile and password', 'puppy-market'); ?></span></a>
        <a href="<?php echo esc_url(puppy_market_catalog_url()); ?>"><?php echo puppy_market_account_icon('shop'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php esc_html_e('Shop again', 'puppy-market'); ?></strong><span><?php esc_html_e('Pet essentials you love', 'puppy-market'); ?></span></a>
    </div>

    <div class="ipet-account-dashboard-grid">
        <section class="ipet-account-panel ipet-account-recent-orders">
            <div class="ipet-account-panel-heading"><div><p><?php esc_html_e('Activity', 'puppy-market'); ?></p><h3><?php esc_html_e('Recent orders', 'puppy-market'); ?></h3></div><a href="<?php echo esc_url($orders_url); ?>"><?php esc_html_e('View all', 'puppy-market'); ?></a></div>
            <?php if (!empty($recent_orders)) : ?>
                <div class="ipet-account-order-list">
                    <?php foreach ($recent_orders as $order) : ?>
                        <a class="ipet-account-order-row" href="<?php echo esc_url($order->get_view_order_url()); ?>">
                            <span class="ipet-account-order-icon"><?php echo puppy_market_account_icon('orders'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            <span><strong><?php echo esc_html(sprintf(__('Order #%s', 'puppy-market'), $order->get_order_number())); ?></strong><small><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></small></span>
                            <span class="ipet-order-status status-<?php echo esc_attr(sanitize_html_class($order->get_status())); ?>"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span>
                            <strong class="ipet-account-order-total"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="ipet-account-empty"><span><?php echo puppy_market_account_icon('orders'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h4><?php esc_html_e('No orders yet', 'puppy-market'); ?></h4><p><?php esc_html_e('Your future orders and delivery updates will appear here.', 'puppy-market'); ?></p><a class="button" href="<?php echo esc_url(puppy_market_catalog_url()); ?>"><?php esc_html_e('Start shopping', 'puppy-market'); ?></a></div>
            <?php endif; ?>
        </section>

        <section class="ipet-account-panel ipet-account-profile-panel">
            <div class="ipet-account-panel-heading"><div><p><?php esc_html_e('Profile', 'puppy-market'); ?></p><h3><?php esc_html_e('Your account', 'puppy-market'); ?></h3></div></div>
            <dl>
                <div><dt><?php esc_html_e('Name', 'puppy-market'); ?></dt><dd><?php echo esc_html($customer->display_name); ?></dd></div>
                <div><dt><?php esc_html_e('Email', 'puppy-market'); ?></dt><dd><?php echo esc_html($customer->user_email); ?></dd></div>
                <div><dt><?php esc_html_e('Billing phone', 'puppy-market'); ?></dt><dd><?php echo esc_html(get_user_meta($customer->ID, 'billing_phone', true) ?: __('Not added', 'puppy-market')); ?></dd></div>
            </dl>
            <a class="ipet-account-secondary-button" href="<?php echo esc_url($details_url); ?>"><?php esc_html_e('Edit account details', 'puppy-market'); ?></a>
        </section>
    </div>
</div>

<?php
do_action('woocommerce_account_dashboard');
do_action('woocommerce_before_my_account');
do_action('woocommerce_after_my_account');
