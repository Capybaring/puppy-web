<?php get_header(); ?>
<main id="main-content" class="content-shell"><div class="container">
    <p class="eyebrow">Search results</p>
    <h1>Looking for: <?php echo esc_html(get_search_query()); ?></h1>
    <?php if (have_posts()) : ?><div class="post-grid">
        <?php while (have_posts()) : the_post(); ?>
            <article class="post-card"><p class="eyebrow"><?php echo esc_html(get_post_type() === 'product' ? 'Product' : 'Journal'); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p><a class="text-link" href="<?php the_permalink(); ?>">View details →</a></article>
        <?php endwhile; ?>
    </div><?php else : ?><div class="empty-state"><span>🔎</span><h2>No matching results</h2><p>Try searching for “dog food,” “cat litter” or “toys.”</p><a class="button" href="<?php echo esc_url(home_url('/')); ?>">Back to home</a></div><?php endif; ?>
</div></main>
<?php get_footer(); ?>
