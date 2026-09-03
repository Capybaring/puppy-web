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
        'title'       => __('首页媒体', 'puppy-market'),
        'description' => __('从 WordPress 媒体库上传或选择首页使用的图片和视频。', 'puppy-market'),
        'priority'    => 35,
    ));

    $media_controls = array(
        'puppy_market_home_slide_1_image' => array('label' => __('轮播图片 1', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_slide_2_image' => array('label' => __('轮播图片 2', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_slide_3_image' => array('label' => __('轮播图片 3', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_video'         => array('label' => __('首页宣传视频', 'puppy-market'), 'mime_type' => 'video'),
        'puppy_market_home_video_poster'  => array('label' => __('宣传视频封面', 'puppy-market'), 'mime_type' => 'image'),
        'puppy_market_home_care_image'    => array('label' => __('关怀服务区域图片', 'puppy-market'), 'mime_type' => 'image'),
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

    $wp_customize->add_section('puppy_market_about_page', array(
        'title'       => __('关于我们页面', 'puppy-market'),
        'description' => __('管理“关于我们”页面的文字和三处大图区域。其他正文内容可在“页面 → About Us”中编辑。', 'puppy-market'),
        'priority'    => 40,
    ));

    $about_media_controls = array(
        'puppy_market_about_hero_image' => array(
            'label'       => __('首屏图片', 'puppy-market'),
            'description' => __('开场区域右侧的大图，建议比例为 4:3。', 'puppy-market'),
        ),
        'puppy_market_about_story_image' => array(
            'label'       => __('品牌故事图片', 'puppy-market'),
            'description' => __('品牌故事交错布局中的第一张大图，建议比例为 4:3。', 'puppy-market'),
        ),
        'puppy_market_about_care_image' => array(
            'label'       => __('关怀理念图片', 'puppy-market'),
            'description' => __('品牌故事交错布局中的第二张大图，建议比例为 4:3。', 'puppy-market'),
        ),
    );

    foreach ($about_media_controls as $setting_id => $control) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, $setting_id, array(
            'label'       => $control['label'],
            'description' => $control['description'],
            'section'     => 'puppy_market_about_page',
            'mime_type'   => 'image',
        )));
    }

    $about_fields = array(
        'puppy_market_about_hero_title' => array(
            'label'    => __('首屏标题', 'puppy-market'),
            'default'  => __('For every day with pets.', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_about_hero_text' => array(
            'label'    => __('首屏说明', 'puppy-market'),
            'default'  => __('We make everyday pet shopping feel clearer, more useful and easier to trust.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_about_mission_title' => array(
            'label'    => __('品牌使命标题', 'puppy-market'),
            'default'  => __('Pet care should feel simple and personal.', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_about_mission_text' => array(
            'label'    => __('品牌使命说明', 'puppy-market'),
            'default'  => __('From everyday essentials to support after an order, we bring the things pet parents need into one straightforward experience.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_about_essentials_title' => array(
            'label'    => __('商品区域标题', 'puppy-market'),
            'default'  => __('Everything pets need', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_about_essentials_text' => array(
            'label'    => __('商品区域说明', 'puppy-market'),
            'default'  => __('Food, play, grooming, comfort and care products selected for real routines with pets.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_about_trust_title' => array(
            'label'    => __('信任保障区域标题', 'puppy-market'),
            'default'  => __('Clarity you can trust', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_about_trust_text' => array(
            'label'    => __('信任保障区域说明', 'puppy-market'),
            'default'  => __('Useful product information, visible policies and a clear route to support before and after checkout.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_about_services_title' => array(
            'label'    => __('服务区域标题', 'puppy-market'),
            'default'  => __('A little more ease for every pet parent', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_about_cta_title' => array(
            'label'    => __('页面结尾标题', 'puppy-market'),
            'default'  => __('Ready to find something your pet will love?', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_about_cta_text' => array(
            'label'    => __('页面结尾说明', 'puppy-market'),
            'default'  => __('Explore the store or contact our team when you need help choosing the next step.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_about_cta_button_text' => array(
            'label'    => __('页面结尾按钮文字', 'puppy-market'),
            'default'  => __('Explore the store', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
    );

    foreach ($about_fields as $setting_id => $field) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => $field['default'],
            'sanitize_callback' => $field['sanitize'],
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'   => $field['label'],
            'section' => 'puppy_market_about_page',
            'type'    => $field['type'],
        ));
    }

    $wp_customize->add_section('puppy_market_store_text', array(
        'title'    => __('商店通用文字', 'puppy-market'),
        'priority' => 36,
    ));

    $wp_customize->add_setting('puppy_market_pdp_promotion_text', array(
        'default'           => __('Free shipping on orders $49+', 'puppy-market'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('puppy_market_pdp_promotion_text', array(
        'label'   => __('商品详情页促销文字', 'puppy-market'),
        'section' => 'puppy_market_store_text',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('puppy_market_footer_tagline', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('puppy_market_footer_tagline', array(
        'label'       => __('页脚简介', 'puppy-market'),
        'description' => __('留空时使用“设置 → 常规”中的站点副标题。', 'puppy-market'),
        'section'     => 'puppy_market_store_text',
        'type'        => 'textarea',
    ));

    $wp_customize->add_section('puppy_market_payment_methods', array(
        'title'       => __('支付方式', 'puppy-market'),
        'description' => __('管理显示在商品详情页、购物车和页脚中的支付方式标识。', 'puppy-market'),
        'priority'    => 37,
    ));

    $wp_customize->add_setting('puppy_market_payment_methods', array(
        'default'           => implode("\n", puppy_market_default_payment_methods()),
        'sanitize_callback' => 'puppy_market_sanitize_payment_methods',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('puppy_market_payment_methods', array(
        'label'       => __('支持的支付方式', 'puppy-market'),
        'description' => __('每行输入一种支付方式，也可以使用英文逗号分隔；留空则隐藏支付方式标识。', 'puppy-market'),
        'section'     => 'puppy_market_payment_methods',
        'type'        => 'textarea',
    ));

    $wp_customize->add_section('puppy_market_home_assurance', array(
        'title'       => __('首页服务保障', 'puppy-market'),
        'description' => __('编辑保障卡片和四个固定服务入口；图标与布局由主题统一管理。', 'puppy-market'),
        'priority'    => 37,
    ));

    $assurance_fields = array(
        'puppy_market_assurance_title' => array(
            'label'    => __('保障卡片标题', 'puppy-market'),
            'default'  => __('Shop with confidence', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_assurance_text' => array(
            'label'    => __('保障卡片说明', 'puppy-market'),
            'default'  => __('Clear support, protected checkout and straightforward help before and after every order.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_assurance_button_text' => array(
            'label'    => __('保障按钮文字', 'puppy-market'),
            'default'  => __('Contact us', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_assurance_button_url' => array(
            'label'       => __('保障按钮链接', 'puppy-market'),
            'description' => __('留空时使用 Customer Care 页面。', 'puppy-market'),
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
            'label'   => sprintf(__('服务 %d 标题', 'puppy-market'), $service_index),
            'section' => 'puppy_market_home_assurance',
            'type'    => 'text',
        ));

        $wp_customize->add_setting($description_setting, array(
            'default'           => $service_default[1],
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($description_setting, array(
            'label'   => sprintf(__('服务 %d 说明', 'puppy-market'), $service_index),
            'section' => 'puppy_market_home_assurance',
            'type'    => 'textarea',
        ));

        $wp_customize->add_setting($url_setting, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($url_setting, array(
            'label'       => sprintf(__('服务 %d 链接', 'puppy-market'), $service_index),
            'description' => __('留空时使用主题默认页面。', 'puppy-market'),
            'section'     => 'puppy_market_home_assurance',
            'type'        => 'url',
        ));
    }

    $wp_customize->add_section('puppy_market_home_product_sections', array(
        'title'       => __('首页商品区域', 'puppy-market'),
        'description' => __('请选择商品及其准确显示顺序。某一组的所有位置都留空时，将继续自动选择商品；一旦选择了其中一个位置，该组内留空的位置会被跳过。', 'puppy-market'),
        'priority'    => 38,
    ));

    $empty_choice = array(0 => __('— 留空 / 全部留空时自动选择 —', 'puppy-market'));
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
            'label'   => __('按宠物选购', 'puppy-market'),
            'count'   => 7,
            'choices' => $top_category_choices,
        ),
        array(
            'prefix'  => 'puppy_market_home_best_seller_',
            'label'   => __('畅销商品', 'puppy-market'),
            'count'   => 5,
            'choices' => $product_choices,
        ),
        array(
            'prefix'  => 'puppy_market_home_popular_category_',
            'label'   => __('热门分类', 'puppy-market'),
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
                'label'   => sprintf(__('%1$s — 位置 %2$d', 'puppy-market'), $homepage_position_group['label'], $position),
                'section' => 'puppy_market_home_product_sections',
                'type'    => 'select',
                'choices' => $homepage_position_group['choices'],
            ));
        }
    }

    $wp_customize->add_section('puppy_market_footer_social', array(
        'title'       => __('页脚社交媒体链接', 'puppy-market'),
        'description' => __('四个平台图标始终显示。链接留空时只显示图标，不提供点击跳转。', 'puppy-market'),
        'priority'    => 40,
    ));

    $footer_social_fields = array(
        'puppy_market_footer_social_title' => array(
            'label'    => __('区域标题', 'puppy-market'),
            'default'  => __('Stay connected', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_footer_social_description' => array(
            'label'    => __('区域说明', 'puppy-market'),
            'default'  => __('Follow along for pet care tips, new arrivals and everyday favorites.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_footer_facebook_url' => array(
            'label'    => __('Facebook 链接', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'puppy_market_footer_youtube_url' => array(
            'label'    => __('YouTube 链接', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'puppy_market_footer_instagram_url' => array(
            'label'    => __('Instagram 链接', 'puppy-market'),
            'default'  => '',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'puppy_market_footer_tiktok_url' => array(
            'label'    => __('TikTok 链接', 'puppy-market'),
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
            'description' => $field['type'] === 'url' ? __('留空时禁用该图标的跳转。', 'puppy-market') : '',
            'section'     => 'puppy_market_footer_social',
            'type'        => $field['type'],
        ));
    }

    $wp_customize->add_section('puppy_market_footer_bottom', array(
        'title'       => __('页脚底部文字', 'puppy-market'),
        'description' => __('自定义页脚最底部的两段小字。使用 {year} 表示当前年份，使用 {site_name} 表示站点标题；字段留空时隐藏对应文字。', 'puppy-market'),
        'priority'    => 41,
    ));

    $footer_bottom_fields = array(
        'puppy_market_footer_copyright_text' => array(
            'label'       => __('版权文字', 'puppy-market'),
            'description' => __('可用占位符：{year} 和 {site_name}。', 'puppy-market'),
            'default'     => __('© {year} {site_name}. All rights reserved.', 'puppy-market'),
        ),
        'puppy_market_footer_credit_text' => array(
            'label'       => __('右侧文字', 'puppy-market'),
            'description' => __('留空时隐藏右侧文字。', 'puppy-market'),
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
        'title'       => __('客户服务页面', 'puppy-market'),
        'description' => __('管理客户服务渠道和页面文字。常见问题可在“页面 → Returns & Support”中编辑。', 'puppy-market'),
        'priority'    => 39,
    ));

    $contact_fields = array(
        'puppy_market_contact_email' => array(
            'label'    => __('客户服务邮箱', 'puppy-market'),
            'default'  => sanitize_email(get_option('admin_email')),
            'type'     => 'email',
            'sanitize' => 'sanitize_email',
        ),
        'puppy_market_business_email' => array(
            'label'       => __('商务及批发邮箱', 'puppy-market'),
            'description' => __('留空时使用客户服务邮箱作为商务联系邮箱。', 'puppy-market'),
            'default'     => '',
            'type'        => 'email',
            'sanitize'    => 'sanitize_email',
        ),
        'puppy_market_contact_phone' => array(
            'label'       => __('客服电话', 'puppy-market'),
            'description' => __('留空时，电话联系渠道将显示为不可用。', 'puppy-market'),
            'default'     => '',
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
        ),
        'puppy_market_contact_chat_url' => array(
            'label'       => __('在线聊天链接', 'puppy-market'),
            'description' => __('填写在线聊天服务商提供的链接；留空时，在线聊天渠道将显示为不可用。', 'puppy-market'),
            'default'     => '',
            'type'        => 'url',
            'sanitize'    => 'esc_url_raw',
        ),
        'puppy_market_contact_response_text' => array(
            'label'    => __('服务时间或回复说明', 'puppy-market'),
            'default'  => __('Our customer care team will follow up as soon as possible.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        'puppy_market_returns_hero_title' => array(
            'label'    => __('客户服务页面标题', 'puppy-market'),
            'default'  => __('How can we help?', 'puppy-market'),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        'puppy_market_returns_hero_text' => array(
            'label'    => __('客户服务页面说明', 'puppy-market'),
            'default'  => __('Choose a help topic to find the most useful answer, or contact the customer care team in the way that works best for you.', 'puppy-market'),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
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
 * Default sidebar-linked questions used by the Customer Care page.
 *
 * The content is stored in the WordPress page editor so store managers can
 * add, remove or rewrite questions without editing this theme.
 */
function puppy_market_default_returns_page_content() {
    return <<<'HTML'
<div class="ipet-care-question-groups">
  <section class="ipet-care-question-group" id="common-questions">
    <div class="ipet-care-question-heading">
      <p class="eyebrow">Help</p>
      <h2>Common questions</h2>
      <p>Start with the information that helps customer care understand your request.</p>
    </div>
    <div class="ipet-care-faq-list">
      <details><summary>Which help topic should I choose?</summary><p>Choose the topic that most closely matches your question. If none is exact, use the contact options below and briefly explain what you need.</p></details>
      <details><summary>What information should I have ready?</summary><p>Keep your account email and order number available. For damaged or incorrect items, photos can help customer care review the issue.</p></details>
      <details><summary>How will customer care respond?</summary><p>Use Chat, Phone or Email below. The currently available channels and response guidance are shown on this page.</p></details>
    </div>
  </section>

  <section class="ipet-care-question-group" id="orders-help">
    <div class="ipet-care-question-heading">
      <p class="eyebrow">Help topic</p>
      <h2>Orders &amp; payments</h2>
    </div>
    <div class="ipet-care-faq-list">
      <details><summary>Can I change or cancel an order?</summary><p>Contact customer care as soon as possible. Address changes and cancellations cannot be guaranteed after an order begins processing.</p></details>
      <details><summary>Why did my payment fail?</summary><p>Check that the billing details match your payment method and that the method is still valid. Your payment provider may need to approve or explain a declined transaction.</p></details>
      <details><summary>Why was a promotion not applied?</summary><p>Review the promotion requirements, eligible products and dates. Some offers cannot be combined with other discounts.</p></details>
    </div>
  </section>

  <section class="ipet-care-question-group" id="shipping-help">
    <div class="ipet-care-question-heading">
      <p class="eyebrow">Help topic</p>
      <h2>Shipping &amp; delivery</h2>
    </div>
    <div class="ipet-care-faq-list">
      <details><summary>How are delivery estimates calculated?</summary><p>The available methods, cost and current delivery estimate are shown during checkout based on the items and destination.</p></details>
      <details><summary>What if tracking has not updated?</summary><p>Carrier updates can take time to appear. If the status remains unchanged or the estimated date has passed, contact customer care with the order number.</p></details>
      <details><summary>What if the delivery address is wrong?</summary><p>Contact customer care immediately. An address cannot always be changed after an order begins processing or has been dispatched.</p></details>
    </div>
  </section>

  <section class="ipet-care-question-group" id="returns-help">
    <div class="ipet-care-question-heading">
      <p class="eyebrow">Help topic</p>
      <h2>Returns &amp; refunds</h2>
    </div>
    <div class="ipet-care-faq-list">
      <details><summary>How do I start a return?</summary><p>Eligible unused items can be requested for return within 30 days of delivery. Contact customer care before sending anything back so the correct instructions can be confirmed.</p></details>
      <details><summary>When will my refund be completed?</summary><p>Refund timing depends on the return route and review. Customer care confirms the outcome after the item or required evidence has been checked.</p></details>
      <details><summary>What if an item arrived damaged or incorrect?</summary><p>Keep the packaging and take clear photos of the item and parcel. Contact customer care with the order number so the appropriate resolution can be reviewed.</p></details>
    </div>
  </section>

  <section class="ipet-care-question-group" id="products-help">
    <div class="ipet-care-question-heading">
      <p class="eyebrow">Help topic</p>
      <h2>Product questions</h2>
    </div>
    <div class="ipet-care-faq-list">
      <details><summary>Where can I find size and usage information?</summary><p>Open the product page and review its Detail, Instruction, Size and FAQ tabs. Available information can vary by product.</p></details>
      <details><summary>What does out of stock mean?</summary><p>The item is not currently available to purchase. Availability can change, so check the product page again later.</p></details>
      <details><summary>Can customer care give medical advice?</summary><p>Customer care can help with store and product information, but health, nutrition and behavior concerns should be discussed with a qualified veterinarian.</p></details>
    </div>
  </section>
</div>
HTML;
}

/**
 * Previous flat Customer Care questions, retained for safe migration.
 *
 * This exact copy prevents the migration from overwriting content edited in
 * the WordPress page editor.
 */
function puppy_market_legacy_returns_page_content_v4() {
    return <<<'HTML'
<section class="ipet-care-faq" id="common-questions">
  <div class="container">
    <div class="ipet-care-section-heading">
      <p class="eyebrow">Common questions</p>
      <h2>Find an answer before contacting us</h2>
      <p>Open a question below for the most useful next step.</p>
    </div>
    <div class="ipet-care-faq-list">
      <details id="orders-question">
        <summary>Where can I check my order status?</summary>
        <p>Open My Account and choose Orders to review the latest information available for your purchase. Tracking appears there when it has been provided.</p>
      </details>
      <details>
        <summary>Can I change or cancel an order?</summary>
        <p>Contact customer care as soon as possible. Address changes and cancellations cannot be guaranteed after an order begins processing.</p>
      </details>
      <details id="shipping-question">
        <summary>How do shipping and delivery estimates work?</summary>
        <p>The available delivery methods, cost and current estimate are shown during checkout. After dispatch, use the tracking information attached to your order.</p>
      </details>
      <details id="returns-question">
        <summary>How do I start a return?</summary>
        <p>Eligible unused items can be requested for return within 30 days of delivery. Contact customer care before sending anything back so eligibility and the correct instructions can be confirmed.</p>
      </details>
      <details>
        <summary>When will my refund be completed?</summary>
        <p>Refund timing depends on the return route and review. Customer care will confirm the outcome after the returned item or required evidence has been checked.</p>
      </details>
      <details>
        <summary>Which payment methods can I use?</summary>
        <p>The payment options currently enabled by the store are displayed at checkout. Availability can vary by payment provider and order.</p>
      </details>
    </div>
  </div>
</section>
HTML;
}

/**
 * Previous full-width returns layout, retained for safe migration.
 *
 * This exact copy prevents the migration from overwriting content edited in
 * the WordPress page editor.
 */
function puppy_market_legacy_returns_page_content_v3() {
    return <<<'HTML'
<section class="ipet-policy-compact">
  <div class="container">
    <article class="ipet-policy-compact-steps">
      <div class="ipet-policy-heading">
        <p class="eyebrow">How returns work</p>
        <h2>Three simple steps</h2>
      </div>
      <ol class="ipet-policy-steps">
        <li><span>1</span><div><h3>Send a request</h3><p>Share your order number, item and reason for the return.</p></div></li>
        <li><span>2</span><div><h3>Get instructions</h3><p>Support confirms eligibility and the correct return route.</p></div></li>
        <li><span>3</span><div><h3>Complete the return</h3><p>Follow the supplied guidance for the refund or replacement review.</p></div></li>
      </ol>
    </article>

    <aside class="ipet-policy-compact-note">
      <div class="ipet-policy-compact-note-heading">
        <p class="eyebrow">Before sending it back</p>
        <h2>Keep these details ready</h2>
      </div>
      <ul>
        <li>Request within 30 days of delivery.</li>
        <li>Keep the item unused with its packaging and supplied parts.</li>
        <li>Include photos if an item arrived damaged or incorrect.</li>
      </ul>
      <p>Contact support before arranging return shipping. Some products may have additional restrictions.</p>
    </aside>
  </div>
</section>
HTML;
}

/**
 * Previous compact policy body, retained for safe migration of unchanged pages.
 *
 * This exact copy prevents the migration from overwriting content edited in
 * the WordPress page editor.
 */
function puppy_market_legacy_returns_page_content_v2() {
    return <<<'HTML'
<section class="ipet-policy-compact">
  <div class="container ipet-policy-compact-grid">
    <article class="ipet-policy-compact-steps">
      <div class="ipet-policy-heading">
        <p class="eyebrow">How returns work</p>
        <h2>Three simple steps</h2>
      </div>
      <ol class="ipet-policy-steps">
        <li><span>1</span><div><h3>Send a request</h3><p>Share your order number, item and reason for the return.</p></div></li>
        <li><span>2</span><div><h3>Get instructions</h3><p>Support confirms eligibility and the correct return route.</p></div></li>
        <li><span>3</span><div><h3>Complete the return</h3><p>Follow the supplied guidance for the refund or replacement review.</p></div></li>
      </ol>
    </article>

    <aside class="ipet-policy-compact-note">
      <p class="eyebrow">Before sending it back</p>
      <h2>Keep these details ready</h2>
      <ul>
        <li>Request within 30 days of delivery.</li>
        <li>Keep the item unused with its packaging and supplied parts.</li>
        <li>Include photos if an item arrived damaged or incorrect.</li>
      </ul>
      <p>Contact support before arranging return shipping. Some products may have additional restrictions.</p>
    </aside>
  </div>
</section>
HTML;
}

/**
 * Previous theme-owned policy body, retained only so an unchanged generated
 * page can be safely migrated to the compact version without replacing edits.
 */
function puppy_market_legacy_returns_page_content_v1() {
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

        if ($slug === 'returns' && absint(get_post_meta($page_id, '_puppy_market_returns_content_version', true)) < 5) {
            $returns_page = get_post($page_id);
            $current_content = $returns_page instanceof WP_Post
                ? trim((string) $returns_page->post_content)
                : '';
            $generated_content_versions = array(
                trim(puppy_market_legacy_returns_page_content_v1()),
                trim(puppy_market_legacy_returns_page_content_v2()),
                trim(puppy_market_legacy_returns_page_content_v3()),
                trim(puppy_market_legacy_returns_page_content_v4()),
            );

            if ($current_content === '' || in_array($current_content, $generated_content_versions, true)) {
                wp_update_post(wp_slash(array(
                    'ID'           => $page_id,
                    'post_content' => puppy_market_default_returns_page_content(),
                )));
            }

            update_post_meta($page_id, '_puppy_market_returns_content_version', 5);
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
        return $returns_url;
    }

    if ($slug === 'shipping') {
        return $returns_url;
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
