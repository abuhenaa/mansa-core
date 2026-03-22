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

$section_title = $attributes['sectionTitle'] ?? 'Stay Ahead of What\'s Next';
$section_description = $attributes['sectionDescription'] ?? 'Receive curated discoveries, product highlights, and emerging brands.';
$placeholder_text = $attributes['placeholderText'] ?? 'Your email address';
$button_text = $attributes['buttonText'] ?? 'Subscribe';
$whatsapp_text = $attributes['whatsappText'] ?? 'Join our WhatsApp Community — Get weekly curated product discoveries';
$whatsapp_url = $attributes['whatsappUrl'] ?? '#';
$action_url = $attributes['actionUrl'] ?? '';

// Generate a unique form ID
$form_id = 'mansa-newsletter-' . uniqid();
?>
<section <?php echo get_block_wrapper_attributes(); ?> class="section section-alt">
    <div class="container">
        <div class="community">
            <h2 style="font-size:clamp(1.75rem,4vw,2.25rem);font-weight:600;margin-bottom:.75rem"><?php echo esc_html($section_title); ?></h2>
            <p style="color:var(--muted);font-size:1.1rem;margin-bottom:2.5rem;line-height:1.6"><?php echo esc_html($section_description); ?></p>
            
            <?php if (!empty($action_url)) : ?>
                <form class="subscribe-form" action="<?php echo esc_url($action_url); ?>" method="post" target="_blank">
            <?php else : ?>
                <form class="subscribe-form" onsubmit="event.preventDefault(); alert('Newsletter subscription form - configure action URL in block settings')">
            <?php endif; ?>
                <input 
                    type="email" 
                    name="email"
                    placeholder="<?php echo esc_attr($placeholder_text); ?>"
                    required
                />
                <button type="submit" class="btn btn-primary"><?php echo esc_html($button_text); ?></button>
            </form>
            
            <a href="<?php echo esc_url($whatsapp_url); ?>" class="wa-link" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                </svg>
                <?php echo esc_html($whatsapp_text); ?>
            </a>
        </div>
    </div>
</section>
