<?php
/**
 * Backend-managed content and media helpers.
 *
 * The theme stores attachment IDs and text settings in the WordPress database.
 * Images and videos are uploaded through the Media Library; no editorial media
 * is copied from the theme directory.
 */

defined('ABSPATH') || exit;

/** Default payment badges used until the store owner saves a custom list. */
function puppy_market_default_payment_methods() {
    return array('Visa', 'Mastercard', 'PayPal', 'Apple Pay');
}

/** Sanitize a comma- or line-separated list of payment method labels. */
function puppy_market_sanitize_payment_methods($value) {
    $methods = preg_split('/[\r\n,]+/', (string) $value);
    $sanitized = array();

    foreach ((array) $methods as $method) {
        $method = sanitize_text_field(trim($method));
        if ($method === '' || in_array($method, $sanitized, true)) continue;
        $sanitized[] = $method;
        if (count($sanitized) >= 12) break;
    }

    return implode("\n", $sanitized);
}

/** Return the payment badges configured in the WordPress Customizer. */
function puppy_market_payment_methods() {
    $default = implode("\n", puppy_market_default_payment_methods());
    $stored = puppy_market_sanitize_payment_methods(get_theme_mod('puppy_market_payment_methods', $default));
    return $stored === '' ? array() : explode("\n", $stored);
}

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

    $wp_customize->add_section('puppy_market_payment_methods', array(
        'title'       => __('Payment methods', 'puppy-market'),
        'description' => __('Manage the payment badges shown on product pages, the cart and the footer.', 'puppy-market'),
        'priority'    => 37,
    ));

    $wp_customize->add_setting('puppy_market_payment_methods', array(
        'default'           => implode("\n", puppy_market_default_payment_methods()),
        'sanitize_callback' => 'puppy_market_sanitize_payment_methods',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('puppy_market_payment_methods', array(
        'label'       => __('Accepted payment methods', 'puppy-market'),
        'description' => __('Enter one name per line, or separate names with commas. Leave blank to hide the payment badges.', 'puppy-market'),
        'section'     => 'puppy_market_payment_methods',
        'type'        => 'textarea',
    ));

    $wp_customize->add_section('puppy_market_home_assurance', array(
        'title'       => __('Homepage service assurance', 'puppy-market'),
        'description' => __('Edit the assurance card and the four fixed service controls. Icons and layout stay theme-managed.', 'puppy-market'),
        'priority'    => 37,
    ));

    $assurance_fields = array(
        'puppy_market_assurance_title' => array(
            'label'    => __('Assurance card title', 'puppy-market'),
            'default'  => __('Shop with confidence', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_assurance_text' => array(
            'label'    => __('Assurance card description', 'puppy-market'),
            'default'  => __('Clear support, protected checkout and straightforward help before and after every order.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_assurance_button_text' => array(
            'label'    => __('Assurance button text', 'puppy-market'),
            'default'  => __('Contact us', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_assurance_button_url' => array(
            'label'       => __('Assurance button URL', 'puppy-market'),
            'description' => __('Leave blank to use the Contact page.', 'puppy-market'),
            'default'     => '',
            'type'        => 'url',
            'sanitize'    => 'esc_url_raw',
        ),
    );

    foreach ($assurance_fields as $setting_id => $field) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => $field['default'],
            'sanitize_callback' => $field['sanitize'],
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $field['label'],
            'description' => isset($field['description']) ? $field['description'] : '',
            'section'     => 'puppy_market_home_assurance',
            'type'        => $field['type'],
        ));
    }

    $service_defaults = array(
        1 => array('Customer support', 'Helpful answers before and after every order.'),
        2 => array('Business & wholesale', 'Support for stores, clinics and professional buyers.'),
        3 => array('Tracked delivery', 'Clear shipping updates from checkout to your door.'),
        4 => array('Simple returns', 'Straightforward help for eligible returns.'),
    );

    foreach ($service_defaults as $service_index => $service_default) {
        $title_setting = 'puppy_market_service_' . $service_index . '_title';
        $description_setting = 'puppy_market_service_' . $service_index . '_description';
        $url_setting = 'puppy_market_service_' . $service_index . '_url';

        $wp_customize->add_setting($title_setting, array(
            'default'           => $service_default[0],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($title_setting, array(
            'label'   => sprintf(__('Service %d title', 'puppy-market'), $service_index),
            'section' => 'puppy_market_home_assurance',
            'type'    => 'text',
        ));

        $wp_customize->add_setting($description_setting, array(
            'default'           => $service_default[1],
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($description_setting, array(
            'label'   => sprintf(__('Service %d description', 'puppy-market'), $service_index),
            'section' => 'puppy_market_home_assurance',
            'type'    => 'textarea',
        ));

        $wp_customize->add_setting($url_setting, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($url_setting, array(
            'label'       => sprintf(__('Service %d URL', 'puppy-market'), $service_index),
            'description' => __('Leave blank to use the theme default page.', 'puppy-market'),
            'section'     => 'puppy_market_home_assurance',
            'type'        => 'url',
        ));
    }

    $wp_customize->add_section('puppy_market_home_product_sections', array(
        'title'       => __('Homepage product sections', 'puppy-market'),
        'description' => __('Choose items in the exact order they should appear. Leave every position in a group empty to keep its automatic selection. Once one position is selected, empty positions in that group are skipped.', 'puppy-market'),
        'priority'    => 38,
    ));

    $empty_choice = array(0 => __('— Empty / automatic when all are empty —', 'puppy-market'));
    $top_category_choices = $empty_choice;
    $all_category_choices = $empty_choice;

    if (taxonomy_exists('product_cat')) {
        $customizer_categories = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'exclude'    => array(absint(get_option('default_product_cat'))),
        ));

        if (!is_wp_error($customizer_categories)) {
            foreach ($customizer_categories as $customizer_category) {
                $category_id = absint($customizer_category->term_id);
                $depth = count(get_ancestors($category_id, 'product_cat', 'taxonomy'));
                $category_label = str_repeat('— ', $depth) . $customizer_category->name;
                $all_category_choices[$category_id] = $category_label;
                if (absint($customizer_category->parent) === 0) {
                    $top_category_choices[$category_id] = $customizer_category->name;
                }
            }
        }
    }

    $product_choices = $empty_choice;
    if (post_type_exists('product')) {
        $customizer_product_ids = get_posts(array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ));

        foreach ($customizer_product_ids as $customizer_product_id) {
            $product_choices[absint($customizer_product_id)] = sprintf(
                '%1$s (#%2$d)',
                get_the_title($customizer_product_id),
                absint($customizer_product_id)
            );
        }
    }

    $homepage_position_groups = array(
        array(
            'prefix'  => 'puppy_market_home_shop_for_',
            'label'   => __('Shop for', 'puppy-market'),
            'count'   => 7,
            'choices' => $top_category_choices,
        ),
        array(
            'prefix'  => 'puppy_market_home_best_seller_',
            'label'   => __('Best sellers', 'puppy-market'),
            'count'   => 5,
            'choices' => $product_choices,
        ),
        array(
            'prefix'  => 'puppy_market_home_popular_category_',
            'label'   => __('Popular categories', 'puppy-market'),
            'count'   => 6,
            'choices' => $all_category_choices,
        ),
    );

    foreach ($homepage_position_groups as $homepage_position_group) {
        for ($position = 1; $position <= $homepage_position_group['count']; $position++) {
            $setting_id = $homepage_position_group['prefix'] . $position;
            $wp_customize->add_setting($setting_id, array(
                'default'           => 0,
                'sanitize_callback' => 'absint',
                'transport'         => 'refresh',
            ));
            $wp_customize->add_control($setting_id, array(
                'label'   => sprintf(__('%1$s — position %2$d', 'puppy-market'), $homepage_position_group['label'], $position),
                'section' => 'puppy_market_home_product_sections',
                'type'    => 'select',
                'choices' => $homepage_position_group['choices'],
            ));
        }
    }

    $wp_customize->add_section('puppy_market_footer_social', array(
        'title'       => __('Footer social links', 'puppy-market'),
        'description' => __('The four platform icons always remain visible. Leave a URL empty to show its icon without making it clickable.', 'puppy-market'),
        'priority'    => 40,
    ));

    $footer_social_fields = array(
        'puppy_market_footer_social_title' => array(
            'label'    => __('Section title', 'puppy-market'),
            'default'  => __('Stay connected', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_footer_social_description' => array(
            'label'    => __('Section description', 'puppy-market'),
            'default'  => __('Follow along for pet care tips, new arrivals and everyday favorites.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_footer_facebook_url' => array(
            'label'    => __('Facebook URL', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'puppy_market_footer_youtube_url' => array(
            'label'    => __('YouTube URL', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'puppy_market_footer_instagram_url' => array(
            'label'    => __('Instagram URL', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'puppy_market_footer_tiktok_url' => array(
            'label'    => __('TikTok URL', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
    );

    foreach ($footer_social_fields as $setting_id => $field) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => $field['default'],
            'sanitize_callback' => $field['sanitize'],
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $field['label'],
            'description' => $field['type'] === 'url' ? __('Leave blank to disable this icon link.', 'puppy-market') : '',
            'section'     => 'puppy_market_footer_social',
            'type'        => $field['type'],
        ));
    }

    $wp_customize->add_section('puppy_market_footer_bottom', array(
        'title'       => __('Footer bottom text', 'puppy-market'),
        'description' => __('Customize the two small text items at the very bottom. Use {year} for the current year and {site_name} for the site title. Leave a field blank to hide it.', 'puppy-market'),
        'priority'    => 41,
    ));

    $footer_bottom_fields = array(
        'puppy_market_footer_copyright_text' => array(
            'label'       => __('Copyright text', 'puppy-market'),
            'description' => __('Available placeholders: {year} and {site_name}.', 'puppy-market'),
            'default'     => __('© {year} {site_name}. All rights reserved.', 'puppy-market'),
        ),
        'puppy_market_footer_credit_text' => array(
            'label'       => __('Right-side text', 'puppy-market'),
            'description' => __('Leave blank to remove the right-side text.', 'puppy-market'),
            'default'     => __('Powered by WordPress and WooCommerce', 'puppy-market'),
        ),
    );

    foreach ($footer_bottom_fields as $setting_id => $field) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $field['label'],
            'description' => $field['description'],
            'section'     => 'puppy_market_footer_bottom',
            'type'        => 'text',
        ));
    }

    $wp_customize->add_section('puppy_market_contact_page', array(
        'title'       => __('Returns & support page', 'puppy-market'),
        'description' => __('Manage the merged Returns and Contact page. Edit the policy body under Pages → Returns & Support.', 'puppy-market'),
        'priority'    => 39,
    ));

    $contact_fields = array(
        'puppy_market_contact_email' => array(
            'label'    => __('Customer support email', 'puppy-market'),
            'default'  => sanitize_email(get_option('admin_email')),
            'type'     => 'email',
            'sanitize' => 'sanitize_email',
        ),
        'puppy_market_business_email' => array(
            'label'       => __('Business and wholesale email', 'puppy-market'),
            'description' => __('Leave blank to use the customer support email.', 'puppy-market'),
            'default'     => '',
            'type'        => 'email',
            'sanitize'    => 'sanitize_email',
        ),
        'puppy_market_contact_phone' => array(
            'label'       => __('Support phone', 'puppy-market'),
            'description' => __('Optional. Leave blank to hide the phone contact method.', 'puppy-market'),
            'default'     => '',
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
        ),
        'puppy_market_contact_response_text' => array(
            'label'    => __('Response promise', 'puppy-market'),
            'default'  => __('We will review your request and follow up as soon as possible.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_returns_hero_title' => array(
            'label'    => __('Hero title', 'puppy-market'),
            'default'  => __('A straightforward path when an item is not right.', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_returns_hero_text' => array(
            'label'    => __('Hero description', 'puppy-market'),
            'default'  => __('Review the return guidance or send the support team your order details from one place.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_returns_highlight_label' => array(
            'label'    => __('Policy highlight label', 'puppy-market'),
            'default'  => __('Return window', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_returns_highlight_title' => array(
            'label'    => __('Policy highlight title', 'puppy-market'),
            'default'  => __('Eligible unused items can be requested for return within 30 days of delivery.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_returns_highlight_text' => array(
            'label'    => __('Policy highlight description', 'puppy-market'),
            'default'  => __('Support confirms eligibility and provides the correct instructions for the product and order.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_returns_form_title' => array(
            'label'    => __('Support form title', 'puppy-market'),
            'default'  => __('Tell us what you need', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_returns_form_text' => array(
            'label'    => __('Support form description', 'puppy-market'),
            'default'  => __('Use this form for returns, delivery questions, order help, products or business enquiries.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_returns_success_text' => array(
            'label'    => __('Successful submission message', 'puppy-market'),
            'default'  => __('Thanks — your message has been sent.', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
    );

    foreach ($contact_fields as $setting_id => $field) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => $field['default'],
            'sanitize_callback' => $field['sanitize'],
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $field['label'],
            'description' => isset($field['description']) ? $field['description'] : '',
            'section'     => 'puppy_market_contact_page',
            'type'        => $field['type'],
        ));
    }
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

/** Theme-owned informational pages used by storefront service and footer links. */
function puppy_market_service_page_definitions() {
    return array(
        'returns' => array(
            'title'    => 'Returns & Support',
            'template' => 'page-returns.php',
            'option'   => 'puppy_market_returns_page_id',
        ),
        'about' => array(
            'title'    => 'About Us',
            'template' => 'page-about.php',
            'option'   => 'puppy_market_about_page_id',
        ),
        'privacy-policy' => array(
            'title'    => 'Privacy Policy',
            'template' => 'page-privacy-policy.php',
            'option'   => 'puppy_market_privacy_page_id',
        ),
    );
}

/** Find a storefront information page by stored ID, slug, or assigned template. */
function puppy_market_service_page($slug) {
    $slug = sanitize_title($slug);
    $definitions = puppy_market_service_page_definitions();
    if (!isset($definitions[$slug])) return null;

    $definition = $definitions[$slug];

    if ($slug === 'privacy-policy') {
        $wordpress_privacy_page_id = absint(get_option('wp_page_for_privacy_policy', 0));
        $wordpress_privacy_page = $wordpress_privacy_page_id ? get_post($wordpress_privacy_page_id) : null;
        if ($wordpress_privacy_page instanceof WP_Post && $wordpress_privacy_page->post_type === 'page' && $wordpress_privacy_page->post_status !== 'trash') {
            return $wordpress_privacy_page;
        }
    }

    $stored_page_id = absint(get_option($definition['option'], 0));
    if ($stored_page_id) {
        $stored_page = get_post($stored_page_id);
        if ($stored_page instanceof WP_Post && $stored_page->post_type === 'page' && $stored_page->post_status !== 'trash') {
            return $stored_page;
        }
    }

    $page = get_page_by_path($slug, OBJECT, 'page');
    if ($page instanceof WP_Post && $page->post_status !== 'trash') return $page;

    $template_pages = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => array('publish', 'draft', 'pending', 'private', 'future'),
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => $definition['template'],
        'no_found_rows'  => true,
    ));

    return !empty($template_pages) && $template_pages[0] instanceof WP_Post
        ? $template_pages[0]
        : null;
}

/**
 * Default policy body used when the Returns & Support page is first created.
 *
 * The content is stored in the WordPress page editor so store managers can
 * change the policy without editing this theme.
 */
function puppy_market_default_returns_page_content() {
    return <<<'HTML'
<section class="ipet-policy-summary">
  <div class="container">
    <div class="ipet-policy-heading">
      <p class="eyebrow">At a glance</p>
      <h2>Before you begin</h2>
    </div>
    <div class="ipet-policy-card-grid">
      <article><span>01</span><h3>Request first</h3><p>Contact support before shipping an item back so the return can be reviewed and recorded.</p></article>
      <article><span>02</span><h3>Keep the order number</h3><p>The order number helps support identify the item, purchase date and available next step.</p></article>
      <article><span>03</span><h3>Wait for instructions</h3><p>Do not send an item to an unconfirmed address; follow the return instructions supplied by support.</p></article>
    </div>
  </div>
</section>

<section class="ipet-policy-process">
  <div class="container">
    <div class="ipet-policy-heading">
      <p class="eyebrow">Return journey</p>
      <h2>Three clear steps</h2>
    </div>
    <ol class="ipet-policy-steps">
      <li><span>1</span><div><h3>Send the request</h3><p>Tell support the order number, item and reason for the return. Add photos when an item arrived damaged or incorrect.</p></div></li>
      <li><span>2</span><div><h3>Receive confirmation</h3><p>Support reviews eligibility and sends the appropriate packaging, address and shipping guidance.</p></div></li>
      <li><span>3</span><div><h3>Complete the return</h3><p>Once the return is received and reviewed, support confirms the available refund or replacement outcome.</p></div></li>
    </ol>
  </div>
</section>

<section class="ipet-policy-details">
  <div class="container ipet-policy-details-grid">
    <article>
      <p class="eyebrow">Generally required</p>
      <h2>Keep the item return-ready</h2>
      <ul>
        <li>Request the return within 30 days of the recorded delivery date.</li>
        <li>Keep the item unused and include its original packaging and supplied parts when possible.</li>
        <li>Provide the order number and accurate information about the item's condition.</li>
      </ul>
    </article>
    <article>
      <p class="eyebrow">Contact us first</p>
      <h2>Some products need review</h2>
      <ul>
        <li>Opened consumables, hygiene-sensitive goods and regulated products may have additional restrictions.</li>
        <li>Damaged, defective or incorrect items may follow a different resolution process.</li>
        <li>Support confirms eligibility before you pay for or arrange return shipping.</li>
      </ul>
    </article>
  </div>
</section>

<section class="ipet-policy-faq">
  <div class="container">
    <div class="ipet-policy-heading">
      <p class="eyebrow">Quick answers</p>
      <h2>Return questions</h2>
    </div>
    <div class="ipet-policy-faq-list">
      <details><summary>Can I send an item back without contacting support?</summary><p>Please contact support first. The team needs to confirm eligibility and provide the correct return destination and instructions.</p></details>
      <details><summary>What if my item arrived damaged or incorrect?</summary><p>Start a return request and include the order number, a description and clear photos. Support will review the appropriate refund, replacement or return route.</p></details>
      <details><summary>When will a refund be completed?</summary><p>Timing depends on the return method and inspection. Support will confirm the outcome after the returned item or required evidence has been reviewed.</p></details>
    </div>
  </div>
</section>

<section class="ipet-policy-cta">
  <div class="container">
    <div><p class="eyebrow">Ready to start?</p><h2>Send a return request.</h2><p>Use the support form below and include your order number and item details.</p></div>
    <a class="button" href="#contact-form">Contact support</a>
  </div>
</section>
HTML;
}

/** Create and publish theme-owned information pages when they do not exist. */
function puppy_market_ensure_service_pages() {
    if (wp_installing()) return;

    foreach (puppy_market_service_page_definitions() as $slug => $definition) {
        $page = puppy_market_service_page($slug);

        if ($page instanceof WP_Post) {
            $page_id = $page->ID;
            if ($page->post_status !== 'publish') {
                $updated_page_id = wp_update_post(array(
                    'ID'          => $page_id,
                    'post_status' => 'publish',
                ), true);
                if (!is_wp_error($updated_page_id)) $page_id = absint($updated_page_id);
            }
        } else {
            $page_id = wp_insert_post(array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $definition['title'],
                'post_name'    => $slug,
                'post_content' => '',
            ), true);

            if (is_wp_error($page_id)) continue;
            $page_id = absint($page_id);
        }

        if (!$page_id) continue;

        if ($slug === 'returns' && !get_post_meta($page_id, '_puppy_market_returns_content_initialized', true)) {
            $returns_page = get_post($page_id);
            $returns_update = array('ID' => $page_id);

            if ($returns_page instanceof WP_Post && $returns_page->post_title === 'Returns') {
                $returns_update['post_title'] = 'Returns & Support';
            }

            if ($returns_page instanceof WP_Post && trim((string) $returns_page->post_content) === '') {
                $returns_update['post_content'] = puppy_market_default_returns_page_content();
            }

            if (count($returns_update) > 1) {
                wp_update_post(wp_slash($returns_update));
            }

            update_post_meta($page_id, '_puppy_market_returns_content_initialized', 1);
        }

        update_post_meta($page_id, '_wp_page_template', $definition['template']);
        update_option($definition['option'], $page_id, false);

        if ($slug === 'privacy-policy') {
            $wordpress_privacy_page_id = absint(get_option('wp_page_for_privacy_policy', 0));
            $wordpress_privacy_page = $wordpress_privacy_page_id ? get_post($wordpress_privacy_page_id) : null;
            if (!$wordpress_privacy_page instanceof WP_Post || $wordpress_privacy_page->post_status === 'trash') {
                update_option('wp_page_for_privacy_policy', $page_id, false);
            }
        }
    }
}
add_action('init', 'puppy_market_ensure_service_pages', 21);

/**
 * Retire legacy standalone service pages after Returns and Contact are merged.
 *
 * Only pages created by this theme are moved to Trash. If Trash is disabled,
 * they are changed to Draft so no content is permanently deleted.
 */
function puppy_market_retire_legacy_service_pages() {
    if (get_option('puppy_market_legacy_service_pages_retired_v1')) return;

    $legacy_pages = array(
        'contact' => array(
            'option'   => 'puppy_market_contact_page_id',
            'template' => 'page-contact.php',
            'slugs'    => array('contact-us', 'contact'),
        ),
        'shipping' => array(
            'option'   => 'puppy_market_shipping_page_id',
            'template' => 'page-shipping.php',
            'slugs'    => array('shipping'),
        ),
    );

    foreach ($legacy_pages as $definition) {
        $candidate_ids = array_filter(array(absint(get_option($definition['option'], 0))));

        foreach ($definition['slugs'] as $candidate_slug) {
            $candidate = get_page_by_path($candidate_slug, OBJECT, 'page');
            if ($candidate instanceof WP_Post) $candidate_ids[] = $candidate->ID;
        }

        $template_pages = get_posts(array(
            'post_type'      => 'page',
            'post_status'    => array('publish', 'draft', 'pending', 'private', 'future'),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $definition['template'],
            'no_found_rows'  => true,
        ));
        $candidate_ids = array_unique(array_merge($candidate_ids, array_map('absint', $template_pages)));

        foreach ($candidate_ids as $candidate_id) {
            $candidate_page = get_post($candidate_id);
            if (!$candidate_page instanceof WP_Post || $candidate_page->post_type !== 'page') continue;
            if (get_post_meta($candidate_id, '_wp_page_template', true) !== $definition['template']) continue;

            $retired = false;
            if (!defined('EMPTY_TRASH_DAYS') || EMPTY_TRASH_DAYS) {
                $retired = (bool) wp_trash_post($candidate_id);
            }
            if (!$retired) {
                wp_update_post(array(
                    'ID'          => $candidate_id,
                    'post_status' => 'draft',
                ));
            }
        }

        delete_option($definition['option']);
    }

    update_option('puppy_market_legacy_service_pages_retired_v1', 1, false);
}
add_action('init', 'puppy_market_retire_legacy_service_pages', 30);

/** Resolve a WordPress page URL, with stable theme-owned service destinations. */
function puppy_market_page_url($slug) {
    $slug = sanitize_title($slug);
    $service_page_definitions = puppy_market_service_page_definitions();
    $returns_page = isset($service_page_definitions['returns'])
        ? puppy_market_service_page('returns')
        : null;
    $returns_url = $returns_page instanceof WP_Post
        ? get_permalink($returns_page)
        : home_url('/returns/');

    if ($slug === 'contact' || $slug === 'contact-us') {
        return add_query_arg('contact_topic', 'order', $returns_url) . '#contact-form';
    }

    if ($slug === 'shipping') {
        return add_query_arg('contact_topic', 'shipping', $returns_url) . '#contact-form';
    }

    if (isset($service_page_definitions[$slug])) {
        $service_page = puppy_market_service_page($slug);
        return $service_page instanceof WP_Post
            ? get_permalink($service_page)
            : home_url('/' . $slug . '/');
    }

    $page = get_page_by_path($slug, OBJECT, 'page');
    return $page instanceof WP_Post && $page->post_status === 'publish'
        ? get_permalink($page)
        : home_url('/');
}

/** Redirect legacy standalone service routes to the merged Returns & Support page. */
function puppy_market_redirect_legacy_service_routes() {
    global $wp;
    $request = isset($wp->request) ? trim((string) $wp->request, '/') : '';

    if (!in_array($request, array('contact', 'contact-us', 'shipping'), true)) return;

    wp_safe_redirect(puppy_market_page_url($request), 301);
    exit;
}
add_action('template_redirect', 'puppy_market_redirect_legacy_service_routes', 1);

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

/** Read a numbered group of Customizer IDs while preserving slot order. */
function puppy_market_ordered_theme_mod_ids($setting_prefix, $limit) {
    $ids = array();
    for ($position = 1; $position <= absint($limit); $position++) {
        $item_id = absint(get_theme_mod($setting_prefix . $position, 0));
        if ($item_id && !in_array($item_id, $ids, true)) $ids[] = $item_id;
    }
    return $ids;
}

/** Resolve selected category IDs without allowing deleted terms into the page. */
function puppy_market_ordered_product_categories($setting_prefix, $limit) {
    $categories = array();
    foreach (puppy_market_ordered_theme_mod_ids($setting_prefix, $limit) as $category_id) {
        $category = get_term($category_id, 'product_cat');
        if ($category && !is_wp_error($category)) $categories[] = $category;
    }
    return $categories;
}
/** Return a backend-managed URL, or the supplied page URL when the field is blank. */
function puppy_market_setting_url($setting_id, $fallback = '') {
    $url = trim((string) get_theme_mod($setting_id, ''));
    return $url !== '' ? $url : $fallback;
}

/** Small fixed SVG set for service assurance and contact surfaces. */
function puppy_market_service_icon($name) {
    $icons = array(
        'shield'   => '<path d="M12 3 19 6v5c0 4.7-2.8 8-7 10-4.2-2-7-5.3-7-10V6Z"/><path d="m8.7 12.1 2.1 2.1 4.6-4.7"/>',
        'support'  => '<path d="M5 13v-2a7 7 0 0 1 14 0v2"/><path d="M5 12H3.8A1.8 1.8 0 0 0 2 13.8v2.4A1.8 1.8 0 0 0 3.8 18H6v-6Z"/><path d="M19 12h1.2a1.8 1.8 0 0 1 1.8 1.8v2.4a1.8 1.8 0 0 1-1.8 1.8H18v-6Z"/><path d="M18 18c-.7 2-2.4 3-5 3"/>',
        'business' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5h8v2"/><path d="M3 12h18"/><path d="M10 12v2h4v-2"/>',
        'shipping' => '<path d="M3 6h11v10H3Z"/><path d="M14 10h3.5l3.5 3.5V16h-7Z"/><circle cx="7" cy="18" r="1.8"/><circle cx="18" cy="18" r="1.8"/>',
        'returns'  => '<path d="M8 7H4l3-3"/><path d="M4 7a8 8 0 1 1-1 8"/><path d="M12 9v4l2.5 1.5"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'phone'    => '<path d="M7.2 3.5 10 7l-2 2.4a15.5 15.5 0 0 0 6.6 6.6l2.4-2 3.5 2.8-.8 3.2c-.2.8-1 1.4-1.8 1.3C9.8 20.2 3.8 14.2 2.7 6.1c-.1-.8.5-1.6 1.3-1.8Z"/>',
    );

    $paths = isset($icons[$name]) ? $icons[$name] : $icons['shield'];
    return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}
