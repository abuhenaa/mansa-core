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

$section_title = $attributes['sectionTitle'] ?? 'Explore Categories';
$categories = $attributes['categories'] ?? [];

// Default categories if none are set
if (empty($categories)) {
    $categories = [
        ['name' => 'Food & Drink', 'icon' => 'food', 'link' => '#'],
        ['name' => 'Beauty & Personal Care', 'icon' => 'beauty', 'link' => '#'],
        ['name' => 'Home & Lifestyle', 'icon' => 'home', 'link' => '#'],
        ['name' => 'Fashion & Textiles', 'icon' => 'fashion', 'link' => '#'],
    ];
}
?>
<section <?php echo get_block_wrapper_attributes(); ?> class="section section-alt" id="products">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html($section_title); ?></h2>
        </div>
        <div class="grid-4">
            <?php foreach ($categories as $category) : ?>
                <a href="<?php echo esc_url($category['link']); ?>" class="cat-card">
                    <div class="cat-icon">
                        <svg viewBox="0 0 24 24">
                            <?php
                            // Simple icon SVGs based on category name
                            switch (strtolower($category['name'])) {
                                case 'food & drink':
                                case 'food':
                                    echo '<path d="M3 2h3a2 2 0 012 2v2a2 2 0 01-2 2H5l-2 4v8h18v-8l-2-4h-1a2 2 0 01-2-2V4a2 2 0 012-2h3"/><path d="M3 18h18"/>';
                                    break;
                                case 'beauty & personal care':
                                case 'beauty':
                                    echo '<path d="M12 3l1.5 3.2 3.5.5-2.5 2.5.6 3.5L12 11l-3.1 1.7.6-3.5L7 6.7l3.5-.5z"/><path d="M9 18c0 2 1.5 3 3 3s3-1 3-3"/><path d="M12 14v4"/>';
                                    break;
                                case 'home & lifestyle':
                                case 'home':
                                    echo '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>';
                                    break;
                                case 'fashion & textiles':
                                case 'fashion':
                                    echo '<path d="M20.38 3.46L16 2 12 3.46 8 2 3.62 3.46a1 1 0 00-.62.94V20a1 1 0 001 1h16a1 1 0 001-1V4.4a1 1 0 00-.62-.94z"/><path d="M8 2v20M16 2v20"/>';
                                    break;
                                default:
                                    echo '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>';
                            }
                            ?>
                        </svg>
                    </div>
                    <h3><?php echo esc_html($category['name']); ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
