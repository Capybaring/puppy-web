<?php
/**
 * Backend-managed content and media helpers.
 *
 * The theme stores attachment IDs and text settings in the WordPress database.
 * Images and videos are uploaded through the Media Library; no editorial media
 * is copied from the theme directory.
 */

defined('ABSPATH') || exit;

/** Register the native Customizer controls used by the storefront. */
function puppy_market_customize_register($wp_customize) {
    $wp_customize->add_section('puppy_market_home_media', array(
        'title'       => __('Homepage media', 'puppy-market'),
        'description' => __('Upload or select homepage images and video from the WordPress Media Library.', 'puppy-market'),
        'priority'    => 35,
    ));

    $media_controls = array(
        'puppy_market_home_slide_1_image' => array('label' => __('Carousel image 1', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_slide_2_image' => array('label' => __('Carousel image 2', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_slide_3_image' => array('label' => __('Carousel image 3', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_video'         => array('label' => __('Homepage promotional video', 'puppy-market'), 'mime_type' => 'video'),
        'puppy_market_home_video_poster'  => array('label' => __('Promotional video poster', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_care_image'    => array('label' => __('Care feature image', 'puppy-market'), 'mime_type' => 'image'),
    );

    foreach ($media_controls as $setting_id => $control) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, $setting_id, array(
            'label'     => $control['label'],
            'section'   => 'puppy_market_home_media',
            'mime_type' => $control['mime_type'],
        )));
    }

    $wp_customize->add_section('puppy_market_store_text', array(
        'title'    => __('Store text', 'puppy-market'),
        'priority' => 36,
    ));

    $wp_customize->add_setting('puppy_market_pdp_promotion_text', array(
        'default'           => __('Free shipping on orders $49+', 'puppy-market'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('puppy_market_pdp_promotion_text', array(
        'label'   => __('Product page promotion', 'puppy-market'),
        'section' => 'puppy_market_store_text',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('puppy_market_footer_tagline', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('puppy_market_footer_tagline', array(
        'label'       => __('Footer description', 'puppy-market'),
        'description' => __('Leave blank to use the site tagline from Settings → General.', 'puppy-market'),
        'section'     => 'puppy_market_store_text',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'puppy_market_customize_register');

/** Return an attachment ID stored in a theme setting. */
function puppy_market_media_id($setting_id) {
    $attachment_id = absint(get_theme_mod($setting_id, 0));
    return $attachment_id && get_post_type($attachment_id) === 'attachment' ? $attachment_id : 0;
}

/** Return the selected image URL, or an empty string when no image is selected. */
function puppy_market_image_url($setting_id, $size = 'full') {
    $attachment_id = puppy_market_media_id($setting_id);
    if (!$attachment_id || !wp_attachment_is_image($attachment_id)) return '';
    $url = wp_get_attachment_image_url($attachment_id, $size);
    return $url ?: '';
}

/** Return the selected media URL, or an empty string when nothing is selected. */
function puppy_market_media_url($setting_id) {
    $attachment_id = puppy_market_media_id($setting_id);
    if (!$attachment_id) return '';
    $url = wp_get_attachment_url($attachment_id);
    return $url ?: '';
}

/** Return the MIME type of a selected attachment. */
function puppy_market_media_mime_type($setting_id) {
    $attachment_id = puppy_market_media_id($setting_id);
    return $attachment_id ? (string) get_post_mime_type($attachment_id) : '';
}

/** Render the backend-managed custom logo, with the site name as a text fallback. */
function puppy_market_brand_markup() {
    $logo_id = absint(get_theme_mod('custom_logo', 0));
    if ($logo_id) {
        $logo = wp_get_attachment_image($logo_id, 'full', false, array(
            'class' => 'brand-logo',
            'alt'   => get_bloginfo('name'),
        ));
        if ($logo) return $logo;
    }

    return '<span class="brand-text">' . esc_html(get_bloginfo('name')) . '</span>';
}

/** Resolve a real WordPress page by slug; never synthesize a page in theme code. */
function puppy_market_page_url($slug) {
    $page = get_page_by_path(sanitize_title($slug), OBJECT, 'page');
    return $page instanceof WP_Post ? get_permalink($page) : home_url('/');
}

/** Resolve a WooCommerce category managed in Products → Categories. */
function puppy_market_product_category($slug) {
    if (!taxonomy_exists('product_cat')) return null;
    $term = get_term_by('slug', sanitize_title($slug), 'product_cat');
    return $term && !is_wp_error($term) ? $term : null;
}

/** Resolve a category URL without creating or renaming any categories. */
function puppy_market_category_link($slug) {
    $term = puppy_market_product_category($slug);
    if ($term) {
        $url = get_term_link($term);
        if (!is_wp_error($url)) return $url;
    }
    return puppy_market_catalog_url();
}

/** Render the thumbnail uploaded for a WooCommerce category. */
function puppy_market_category_thumbnail($term, $size = 'medium', $class = '') {
    if (is_string($term)) $term = puppy_market_product_category($term);
    if (!$term || is_wp_error($term)) return '';

    $thumbnail_id = absint(get_term_meta($term->term_id, 'thumbnail_id', true));
    if (!$thumbnail_id) return '';

    $image = wp_get_attachment_image($thumbnail_id, $size, false, array(
        'class'   => trim('category-image ' . $class),
        'loading' => 'lazy',
        'alt'     => $term->name,
    ));

    return $image ?: '';
}

/** Get top-level categories in the order maintained by WooCommerce. */
function puppy_market_top_categories($limit = 7, $hide_empty = false) {
    if (!taxonomy_exists('product_cat')) return array();
    $terms = get_terms(array(
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => (bool) $hide_empty,
        'number'     => absint($limit),
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'exclude'    => array(absint(get_option('default_product_cat'))),
    ));
    return is_wp_error($terms) ? array() : $terms;
}

/** Get popular categories from real product counts. */
function puppy_market_popular_categories($limit = 6) {
    if (!taxonomy_exists('product_cat')) return array();
    $terms = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => absint($limit),
        'orderby'    => 'count',
        'order'      => 'DESC',
        'exclude'    => array(absint(get_option('default_product_cat'))),
    ));
    return is_wp_error($terms) ? array() : $terms;
}

/** Minimal dynamic menu used only until an administrator assigns a real menu. */
function puppy_market_primary_menu_fallback($args = array()) {
    $categories = puppy_market_top_categories(6, false);
    echo '<ul class="nav-menu">';
    foreach ($categories as $category) {
        $url = get_term_link($category);
        if (is_wp_error($url)) continue;
        echo '<li><a href="' . esc_url($url) . '">' . esc_html($category->name) . '</a></li>';
    }
    echo '<li><a href="' . esc_url(puppy_market_catalog_url()) . '">' . esc_html__('Shop all', 'puppy-market') . '</a></li>';
    echo '</ul>';
}
