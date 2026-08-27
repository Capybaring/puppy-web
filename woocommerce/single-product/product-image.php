<?php
/** Product gallery for the custom PDP. */
defined('ABSPATH') || exit;

global $product;
$puppy_gallery_ids = $product ? array_values(array_unique(array_filter(array_merge(array($product->get_image_id()), $product->get_gallery_image_ids())))) : array();
$puppy_main_id = !empty($puppy_gallery_ids) ? $puppy_gallery_ids[0] : 0;
$puppy_main_full = $puppy_main_id ? wp_get_attachment_image_url($puppy_main_id, 'full') : '';
$puppy_gallery_count = count($puppy_gallery_ids);
$puppy_main_image_id = 'ipet-pdp-main-image-' . ($product ? absint($product->get_id()) : 'product');
?>
<div class="woocommerce-product-gallery images ipet-product-gallery" data-columns="1" data-pdp-gallery style="opacity:1;">
    <div class="ipet-pdp-gallery-stage" data-pdp-gallery-stage>
        <?php if ($product && $product->is_on_sale()) : ?><span class="ipet-pdp-badge">Deal</span><?php endif; ?>
        <?php if ($puppy_gallery_count > 1) : ?>
            <button type="button" class="ipet-gallery-arrow ipet-gallery-prev" data-pdp-gallery-prev aria-label="Previous product image"><span aria-hidden="true">‹</span></button>
            <button type="button" class="ipet-gallery-arrow ipet-gallery-next" data-pdp-gallery-next aria-label="Next product image"><span aria-hidden="true">›</span></button>
        <?php endif; ?>
        <button type="button" class="ipet-detail-image" data-pdp-zoom aria-label="Open larger product image">
            <?php if ($puppy_main_id) : ?>
                <?php echo wp_get_attachment_image($puppy_main_id, 'woocommerce_single', false, array('id' => $puppy_main_image_id, 'loading' => 'eager', 'fetchpriority' => 'high', 'data-pdp-main-image' => '', 'data-full' => esc_url($puppy_main_full))); ?>
            <?php else : ?>
                <img class="woocommerce-placeholder wp-post-image" src="<?php echo esc_url(wc_placeholder_img_src('woocommerce_single')); ?>" alt="<?php echo esc_attr($product ? $product->get_name() : 'Product image'); ?>" loading="eager">
            <?php endif; ?>
        </button>
        <?php if ($puppy_gallery_count > 1) : ?><span class="ipet-gallery-count" data-pdp-gallery-count aria-live="polite">1 / <?php echo absint($puppy_gallery_count); ?></span><?php endif; ?>
    </div>

    <?php if ($puppy_gallery_count > 1) : ?>
        <div class="ipet-gallery-thumbs" role="tablist" aria-label="Product images">
            <?php foreach ($puppy_gallery_ids as $puppy_thumb_index => $puppy_thumb_id) :
                $puppy_thumb_src = wp_get_attachment_image_url($puppy_thumb_id, 'woocommerce_gallery_thumbnail');
                $puppy_display_src = wp_get_attachment_image_url($puppy_thumb_id, 'woocommerce_single');
                $puppy_full_src = wp_get_attachment_image_url($puppy_thumb_id, 'full');
                $puppy_thumb_alt = get_post_meta($puppy_thumb_id, '_wp_attachment_image_alt', true);
                if (!$puppy_thumb_alt && $product) $puppy_thumb_alt = $product->get_name() . ' image ' . ($puppy_thumb_index + 1);
                if (!$puppy_thumb_src || !$puppy_display_src) continue;
            ?>
                <button type="button" class="ipet-gallery-thumb<?php echo $puppy_thumb_index === 0 ? ' is-active' : ''; ?>" data-pdp-gallery-index="<?php echo absint($puppy_thumb_index); ?>" data-display="<?php echo esc_url($puppy_display_src); ?>" data-full="<?php echo esc_url($puppy_full_src ?: $puppy_display_src); ?>" data-alt="<?php echo esc_attr($puppy_thumb_alt); ?>" aria-controls="<?php echo esc_attr($puppy_main_image_id); ?>" aria-label="View product image <?php echo absint($puppy_thumb_index + 1); ?> of <?php echo absint($puppy_gallery_count); ?>" aria-selected="<?php echo $puppy_thumb_index === 0 ? 'true' : 'false'; ?>" tabindex="<?php echo $puppy_thumb_index === 0 ? '0' : '-1'; ?>" role="tab">
                    <img src="<?php echo esc_url($puppy_thumb_src); ?>" alt="" loading="lazy">
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($puppy_main_full) : ?>
    <dialog class="ipet-pdp-lightbox" data-pdp-lightbox aria-label="Product image preview">
        <button type="button" class="ipet-pdp-lightbox-close" data-pdp-lightbox-close aria-label="Close image preview">×</button>
        <img src="<?php echo esc_url($puppy_main_full); ?>" alt="<?php echo esc_attr($product ? $product->get_name() : 'Product image'); ?>" data-pdp-lightbox-image>
    </dialog>
<?php endif; ?>
