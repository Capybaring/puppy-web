<?php
/**
 * Shop/category grid card — reuses the same product-card partial as the
 * homepage rails so the catalog matches the merchandising density everywhere.
 */
defined('ABSPATH') || exit;

global $product;
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('', $product); ?>>
    <?php
    get_template_part('template-parts/product-card', null, array(
        'product' => $product,
        'badge' => $product->is_on_sale() ? 'Sale' : '',
        'bare' => true,
    ));
    ?>
</li>
