<?php
/**
 * Template Name: Front Page
 * Description: The template for displaying the site's front page.
 */

get_header(); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1><?php bloginfo('name'); ?></h1>
        <p><?php bloginfo('description'); ?></p>
        <a href="#featured-content" class="btn">Explore More</a>
    </div>
</section>

<!-- Featured Content Section -->
<section id="featured-content" class="featured-content">
    <div class="container">
        <h2>Featured Content</h2>
        <?php
        // Custom query for featured content (adjust category or tags as needed)
        $featured_query = new WP_Query(array(
            'posts_per_page' => 3,
            'category_name' => 'featured' // Change 'featured' to your desired category
        ));

        if ($featured_query->have_posts()) : while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
            <div class="featured-post">
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p><?php the_excerpt(); ?></p>
            </div>
        <?php endwhile; endif; wp_reset_postdata(); ?>
    </div>
</section>

<!-- Recent Posts Section -->
<section class="recent-posts">
    <div class="container">
        <h2>Latest Posts</h2>
        <div class="post-grid">
            <?php
            if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article class="post-item">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php the_excerpt(); ?></p>
                </article>
            <?php endwhile; else : ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
