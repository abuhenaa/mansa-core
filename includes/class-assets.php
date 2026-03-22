<?php
/**
 * Assets Handler Class
 * 
 * Handles enqueueing of CSS and JavaScript files for the Mansa Core plugin.
 * 
 * @package Mansa\Core
 * @since 1.0.0
 */

namespace Mansa\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles' ] );
        add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        // Main plugin styles with CSS variables
        wp_enqueue_style(
            'mansa-core-variables',
            MANSA_CORE_URL . 'assets/style.css',
            [],
            MANSA_CORE_VERSION
        );
    }

    /**
     * Enqueue block assets (both frontend and editor)
     */
    public function enqueue_block_assets() {
        // Ensure CSS variables are available for blocks on both frontend and editor
        wp_enqueue_style(
            'mansa-core-variables',
            MANSA_CORE_URL . 'assets/style.css',
            [],
            MANSA_CORE_VERSION
        );
        
        // Manually enqueue block styles if auto-loading isn't working
        $blocks = ['featured-discovery', 'category-grid', 'featured-brand', 'community-newsletter'];
        
        foreach ($blocks as $block) {
            // Frontend styles
            if (file_exists(MANSA_CORE_PATH . "build/{$block}/style-index.css")) {
                wp_enqueue_style(
                    "mansa-core-{$block}-style",
                    MANSA_CORE_URL . "build/{$block}/style-index.css",
                    ['mansa-core-variables'],
                    MANSA_CORE_VERSION
                );
            }
            
            // Editor styles
            if (is_admin() && file_exists(MANSA_CORE_PATH . "build/{$block}/index.css")) {
                wp_enqueue_style(
                    "mansa-core-{$block}-editor",
                    MANSA_CORE_URL . "build/{$block}/index.css",
                    ['mansa-core-variables'],
                    MANSA_CORE_VERSION
                );
            }
        }
    }

    /**
     * Enqueue editor-only assets
     */
    public function enqueue_editor_assets() {
        // Editor-specific styles can be loaded here if needed
    }
}
