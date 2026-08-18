<?php get_header(); ?>
<main id="main-content" class="content-shell"><div class="container">
    <div class="section-heading"><h1><?php the_archive_title(); ?></h1></div>
    <?php if (have_posts()) : ?><div class="post-grid">
        <?php while (have_posts()) : the_post(); ?>
            <article class="post-card"><p class="eyebrow">Journal</p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Read more →</a></article>
        <?php endwhile; ?>
    </div><?php else : ?><div class="empty-state"><span>🐾</span><h2>No stories yet</h2><p>We are preparing more pet life content.</p></div><?php endif; ?>
</div></main>
<?php get_footer(); ?>
