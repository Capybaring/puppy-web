<?php get_header(); ?>
<main id="main-content" class="content-shell"><div class="container content-card">
<?php while (have_posts()) : the_post(); ?>
    <p class="eyebrow">Journal</p>
    <h1><?php the_title(); ?></h1>
    <p class="post-meta">Published <?php echo esc_html(get_the_date()); ?></p>
    <div class="entry-content"><?php the_content(); ?></div>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
