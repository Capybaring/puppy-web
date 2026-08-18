<?php get_header(); ?>
<main id="main-content" class="content-shell"><div class="container content-card">
<?php while (have_posts()) : the_post(); ?>
    <?php if (function_exists('is_cart') && is_cart()) : ?>
        <div class="puppy-cart-banner">Free $20 eGift card with your first $49+ order — <a href="<?php echo esc_url(puppy_market_catalog_url()); ?>">Shop now</a></div>
        <div class="entry-content cart-entry"><?php the_content(); ?></div>
    <?php else : ?>
        <p class="eyebrow">iPet · Pet life</p>
        <h1><?php the_title(); ?></h1>
        <div class="entry-content"><?php the_content(); ?></div>
    <?php endif; ?>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
