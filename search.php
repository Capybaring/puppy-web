<?php
get_header();

global $wp_query;

$puppy_search_term = get_search_query();
$puppy_search_count = isset($wp_query->found_posts) ? absint($wp_query->found_posts) : 0;
$puppy_search_count_label = sprintf(
    _n('%s product found', '%s products found', $puppy_search_count, 'puppy-market'),
    number_format_i18n($puppy_search_count)
);
?>
<main id="main-content" class="content-shell puppy-search-results">
  <div class="container">
    <header class="puppy-search-header">
      <p class="eyebrow">Product search</p>
      <h1>Search results for “<?php echo esc_html($puppy_search_term); ?>”</h1>
      <p class="puppy-search-count"><?php echo esc_html($puppy_search_count_label); ?></p>

      <form class="puppy-search-refine" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="hidden" name="post_type" value="product">
        <label class="screen-reader-text" for="puppy-search-refine-input">Search products</label>
        <input id="puppy-search-refine-input" type="search" name="s" value="<?php echo esc_attr($puppy_search_term); ?>" placeholder="Search products, categories, brands or SKU" required>
        <button type="submit">Search</button>
      </form>
    </header>

    <?php if (have_posts()) : ?>
      <div class="search-product-grid" aria-label="Product search results">
        <?php while (have_posts()) : the_post(); ?>
          <?php
          $puppy_search_product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
          if (!$puppy_search_product) continue;

          get_template_part('template-parts/product-card', null, array(
              'product'         => $puppy_search_product,
              'card_class'      => 'search-product-card',
              'show_details'    => false,
              'show_sale_label' => false,
          ));
          ?>
        <?php endwhile; ?>
      </div>

      <nav class="puppy-search-pagination" aria-label="Search result pages">
        <?php
        the_posts_pagination(array(
            'mid_size'           => 1,
            'prev_text'          => '← Previous',
            'next_text'          => 'Next →',
            'screen_reader_text' => 'Search result pages',
        ));
        ?>
      </nav>
    <?php else : ?>
      <?php
      $puppy_search_categories = function_exists('puppy_market_top_categories')
          ? puppy_market_top_categories(6, true)
          : array();
      ?>
      <section class="empty-state puppy-search-empty">
        <span aria-hidden="true">🔎</span>
        <h2>No matching products</h2>
        <p>Try another product name, category, brand or SKU.</p>
        <a class="button" href="<?php echo esc_url(puppy_market_catalog_url()); ?>">View all products</a>

        <?php if (!empty($puppy_search_categories)) : ?>
          <div class="puppy-search-suggestions">
            <h3>Browse popular categories</h3>
            <div>
              <?php foreach ($puppy_search_categories as $puppy_search_category) : ?>
                <?php
                $puppy_search_category_url = get_term_link($puppy_search_category);
                if (is_wp_error($puppy_search_category_url)) continue;
                ?>
                <a href="<?php echo esc_url($puppy_search_category_url); ?>"><?php echo esc_html($puppy_search_category->name); ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>

