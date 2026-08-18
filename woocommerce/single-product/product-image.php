<?php
/** Product gallery for the custom PDP. */
defined('ABSPATH') || exit;

global $product;
$puppy_fallback_index = absint(($product ? $product->get_id() : 1) % 4) + 1;
$puppy_gallery_ids = $product ? array_values(array_unique(array_filter(array_merge(array($product->get_image_id()), $product->get_gallery_image_ids())))) : array();
$puppy_main_id = !empty($puppy_gallery_ids) ? $puppy_gallery_ids[0] : 0;
$puppy_main_full = $puppy_main_id ? wp_get_attachment_image_url($puppy_main_id, 'full') : '';
?>
<div class="woocommerce-product-gallery images ipet-product-gallery" data-columns="1" style="opacity:1;">
    <div class="ipet-pdp-gallery-stage">
        <?php if ($product && $product->is_on_sale()) : ?><span class="ipet-pdp-badge">Deal</span><?php endif; ?>
        <button type="button" class="ipet-pdp-wishlist" data-pdp-wishlist aria-pressed="false" aria-label="Add to wishlist">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.7a5.5 5.5 0 0 0-7.8 0L12 5.8l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21.4l7.8-7.8 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>
        </button>
        <button type="button" class="ipet-detail-image" data-pdp-zoom aria-label="Open larger product image">
            <?php if ($puppy_main_id) : ?>
                <?php echo wp_get_attachment_image($puppy_main_id, 'woocommerce_single', false, array('loading' => 'eager', 'fetchpriority' => 'high', 'data-pdp-main-image' => '', 'data-full' => esc_url($puppy_main_full))); ?>
            <?php else : ?>
                <span class="product-fallback product-fallback-<?php echo esc_attr($puppy_fallback_index); ?>" aria-label="<?php echo esc_attr($product ? $product->get_name() : 'Product image'); ?>"></span>
            <?php endif; ?>
        </button>
    </div>

    <?php if (count($puppy_gallery_ids) > 1) : ?>
        <div class="ipet-gallery-thumbs" role="tablist" aria-label="Product images">
            <?php foreach ($puppy_gallery_ids as $puppy_thumb_index => $puppy_thumb_id) :
                $puppy_thumb_src = wp_get_attachment_image_url($puppy_thumb_id, 'woocommerce_gallery_thumbnail');
                $puppy_display_src = wp_get_attachment_image_url($puppy_thumb_id, 'woocommerce_single');
                $puppy_full_src = wp_get_attachment_image_url($puppy_thumb_id, 'full');
                if (!$puppy_thumb_src || !$puppy_display_src) continue;
            ?>
                <button type="button" class="ipet-gallery-thumb<?php echo $puppy_thumb_index === 0 ? ' is-active' : ''; ?>" data-display="<?php echo esc_url($puppy_display_src); ?>" data-full="<?php echo esc_url($puppy_full_src ?: $puppy_display_src); ?>" aria-selected="<?php echo $puppy_thumb_index === 0 ? 'true' : 'false'; ?>" role="tab">
                    <img src="<?php echo esc_url($puppy_thumb_src); ?>" alt="" loading="lazy">
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<dialog class="ipet-pdp-lightbox" data-pdp-lightbox aria-label="Product image preview">
    <button type="button" class="ipet-pdp-lightbox-close" data-pdp-lightbox-close aria-label="Close image preview">×</button>
    <img src="<?php echo esc_url($puppy_main_full); ?>" alt="<?php echo esc_attr($product ? $product->get_name() : 'Product image'); ?>" data-pdp-lightbox-image>
</dialog>
