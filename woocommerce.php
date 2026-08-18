<?php if (function_exists('is_product') && is_product()) : ?>
    <?php wc_get_template('single-product.php'); ?>
<?php else : ?>
<?php get_header(); ?>
<main id="main-content" class="shop-shell">
    <div class="container">
        <?php if (function_exists('woocommerce_breadcrumb')) : ?>
            <?php woocommerce_breadcrumb(array('delimiter' => ' › ', 'home' => 'Home')); ?>
        <?php endif; ?>
        <?php $puppy_categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array(get_option('default_product_cat')))); ?>
        <?php if (function_exists('is_product_category') && is_product_category()) : $puppy_term = get_queried_object(); ?>
            <section class="category-hero"><div><p class="eyebrow">iPet featured category</p><h1><?php echo esc_html($puppy_term->name); ?></h1><p>Thoughtfully chosen essentials your pets will love using every day.</p></div><span><?php echo esc_html(puppy_market_category_icon($puppy_term->name)); ?></span></section>
        <?php elseif (function_exists('is_shop') && is_shop()) : ?>
            <section class="category-hero shop-hero"><div><p class="eyebrow">iPet shop</p><h1>Find something great for today</h1><p>Take your time browsing food, toys and everyday essentials by pet and need.</p></div><span>iP</span></section>
        <?php endif; ?>
        <?php if (function_exists('is_shop') && (is_shop() || is_product_category())) : ?>
            <div class="shop-layout">
                <?php
                $puppy_selected_categories = isset($_GET['puppy_category']) ? array_map('sanitize_title', (array) wp_unslash($_GET['puppy_category'])) : array();
                $puppy_selected_brands = isset($_GET['puppy_brand']) ? array_map('sanitize_title', (array) wp_unslash($_GET['puppy_brand'])) : array();
                $puppy_brand_taxonomy = puppy_market_brand_taxonomy();
                $puppy_brand_terms = get_terms(array('taxonomy' => $puppy_brand_taxonomy, 'hide_empty' => true, 'number' => 8, 'orderby' => 'count', 'order' => 'DESC'));
                $puppy_min_price = isset($_GET['puppy_min_price']) ? absint($_GET['puppy_min_price']) : '';
                $puppy_max_price = isset($_GET['puppy_max_price']) ? absint($_GET['puppy_max_price']) : '';
                $puppy_on_sale_only = !empty($_GET['puppy_on_sale']);
                ?>
                <aside class="shop-sidebar" aria-label="Product filters">
                    <form class="puppy-filter-form" method="get" action="<?php echo esc_url(puppy_market_catalog_url()); ?>">
                        <div class="sidebar-section sidebar-filter-heading"><h2>Filter products</h2><a class="sidebar-reset" href="<?php echo esc_url(puppy_market_catalog_url()); ?>">Clear filters</a></div>
                        <fieldset class="sidebar-section puppy-filter-group"><legend>Pet type</legend>
                            <?php foreach ($puppy_categories as $sidebar_category) : if ((int) $sidebar_category->parent !== 0) continue; ?>
                                <label class="puppy-filter-option"><input type="checkbox" name="puppy_category[]" value="<?php echo esc_attr($sidebar_category->slug); ?>" <?php checked(in_array($sidebar_category->slug, $puppy_selected_categories, true)); ?>><span><?php echo esc_html($sidebar_category->name); ?></span><small><?php echo absint($sidebar_category->count); ?></small></label>
                            <?php endforeach; ?>
                        </fieldset>
                        <fieldset class="sidebar-section puppy-filter-group"><legend>Brand</legend>
                            <?php if (!is_wp_error($puppy_brand_terms) && !empty($puppy_brand_terms)) : foreach ($puppy_brand_terms as $puppy_brand_term) : ?>
                                <label class="puppy-filter-option"><input type="checkbox" name="puppy_brand[]" value="<?php echo esc_attr($puppy_brand_term->slug); ?>" <?php checked(in_array($puppy_brand_term->slug, $puppy_selected_brands, true)); ?>><span><?php echo esc_html($puppy_brand_term->name); ?></span><small><?php echo absint($puppy_brand_term->count); ?></small></label>
                            <?php endforeach; else : ?><p class="puppy-filter-empty">Add product brands or tags in the WordPress dashboard.</p><?php endif; ?>
                        </fieldset>
                        <fieldset class="sidebar-section puppy-filter-group"><legend>Price</legend>
                            <div class="puppy-filter-price-row"><input type="number" min="0" inputmode="numeric" name="puppy_min_price" placeholder="Min" value="<?php echo esc_attr($puppy_min_price); ?>"><span>–</span><input type="number" min="0" inputmode="numeric" name="puppy_max_price" placeholder="Max" value="<?php echo esc_attr($puppy_max_price); ?>"></div>
                        </fieldset>
                        <fieldset class="sidebar-section puppy-filter-group"><legend>Availability</legend>
                            <label class="puppy-filter-option"><input type="checkbox" name="puppy_on_sale" value="1" <?php checked($puppy_on_sale_only); ?>><span>On sale only</span></label>
                        </fieldset>
                        <button class="puppy-filter-submit" type="submit">Apply filters</button>
                    </form>
                    <div class="sidebar-tip"><span>iP</span><strong>iPet standards</strong><p>Simple, useful and made for life with pets.</p></div>
                </aside>
                <section class="shop-results"><?php woocommerce_content(); ?></section>
            </div>
        <?php else : woocommerce_content(); endif; ?>
    </div>
</main>
<?php get_footer(); ?>
<?php endif; ?>
