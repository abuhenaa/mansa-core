<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$section_title = $attributes['sectionTitle'] ?? 'Featured Discovery';
$section_description = $attributes['sectionDescription'] ?? 'Curated insights from our latest guides.';
$post_type = $attributes['postType'] ?? 'post';
$posts_per_page = $attributes['postsPerPage'] ?? 3;

$args = array(
    'post_type'      => $post_type,
    'posts_per_page' => $posts_per_page,
    'post_status'    => 'publish',
);

$query = new WP_Query($args);
?>
<section <?php echo get_block_wrapper_attributes(); ?> class="section" id="guides">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html($section_title); ?></h2>
            <p><?php echo esc_html($section_description); ?></p>
        </div>
        <div class="grid-3">
            <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
                <article class="card">
                    <div class="card-img">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large'); ?>
                        <?php else : ?>
                            <img src="<?php echo MANSA_CORE_URL; ?>assets/placeholder.jpg" alt="<?php echo esc_attr(get_the_title()); ?>" />
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3><?php the_title(); ?></h3>
                        <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        <a href="<?php the_permalink(); ?>" class="card-link">Read Guide →</a>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <p><?php _e('No posts found.', 'mansa-core'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
