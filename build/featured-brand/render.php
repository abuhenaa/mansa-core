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

$section_title = $attributes['sectionTitle'] ?? 'Featured Brand';
$brand_name = $attributes['brandName'] ?? 'Terra & Ember';
$brand_label = $attributes['brandLabel'] ?? 'Brand Spotlight';
$brand_description = $attributes['brandDescription'] ?? 'Born in a small workshop in Oaxaca, Terra & Ember crafts homeware from locally sourced clay and reclaimed wood. Every piece carries the warmth of hands that shaped it — honest, intentional, and built to last generations.';
$brand_image = $attributes['brandImage'] ?? null;
$button_text = $attributes['buttonText'] ?? 'Explore Brand';
$button_url = $attributes['buttonUrl'] ?? '#';

$image_url = '';
$image_alt = '';
if ($brand_image && isset($brand_image['url'])) {
    $image_url = esc_url($brand_image['url']);
    $image_alt = esc_attr($brand_image['alt'] ?? $brand_name);
}
?>
<section <?php echo get_block_wrapper_attributes(); ?> class="section" id="brands">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html($section_title); ?></h2>
        </div>
        <div class="brand-grid">
            <div class="brand-img">
                <?php if ($image_url) : ?>
                    <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" />
                <?php else : ?>
                    <img src="<?php echo MANSA_CORE_URL; ?>assets/placeholder-brand.jpg" alt="<?php echo esc_attr($brand_name); ?>" />
                <?php endif; ?>
            </div>
            <div class="brand-text">
                <span class="brand-label"><?php echo esc_html($brand_label); ?></span>
                <h3><?php echo esc_html($brand_name); ?></h3>
                <p><?php echo esc_html($brand_description); ?></p>
                <a href="<?php echo esc_url($button_url); ?>" class="btn btn-primary"><?php echo esc_html($button_text); ?></a>
            </div>
        </div>
    </div>
</section>
