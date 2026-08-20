<?php
/**
 * Account navigation.
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

$customer    = wp_get_current_user();
$name        = $customer->first_name ?: $customer->display_name;
$menu_items  = wc_get_account_menu_items();
$active_label = __('Account menu', 'puppy-market');

foreach ($menu_items as $endpoint => $label) {
    if (wc_is_current_account_menu_item($endpoint)) {
        $active_label = $label;
        break;
    }
}

do_action('woocommerce_before_account_navigation');
?>
<nav class="woocommerce-MyAccount-navigation ipet-account-navigation" aria-label="<?php esc_attr_e('Account pages', 'woocommerce'); ?>">
    <div class="ipet-account-profile">
        <div class="ipet-account-avatar">
            <?php echo get_avatar($customer->ID, 64, '', $name); ?>
        </div>
        <div class="ipet-account-profile-copy">
            <span class="ipet-account-profile-name"><?php echo esc_html(sprintf(__('Hi, %s', 'puppy-market'), $name)); ?></span>
            <span class="ipet-account-profile-email"><?php echo esc_html($customer->user_email); ?></span>
            <span class="ipet-account-profile-note"><?php esc_html_e('iPet customer', 'puppy-market'); ?></span>
        </div>
    </div>

    <button class="ipet-account-menu-toggle" type="button" aria-expanded="false" aria-controls="ipet-account-menu">
        <span><?php echo esc_html($active_label); ?></span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <ul id="ipet-account-menu">
        <?php foreach ($menu_items as $endpoint => $label) : ?>
            <li class="<?php echo esc_attr(wc_get_account_menu_item_classes($endpoint)); ?>">
                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" <?php echo wc_is_current_account_menu_item($endpoint) ? 'aria-current="page"' : ''; ?>>
                    <span class="ipet-account-menu-icon">
                        <?php echo puppy_market_account_icon($endpoint); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </span>
                    <span><?php echo esc_html($label); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php do_action('woocommerce_after_account_navigation'); ?>
