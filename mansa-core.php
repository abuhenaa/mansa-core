<?php
/**
 * Plugin Name: Mansa Core
 * Plugin URI: https://mansa.local
 * Description: Core CMS architecture for Mansa Market content discovery platform
 * Version: 1.0.0
 * Author: Abu Hena
 * Author URI: https://github.com/abuhenaa
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mansa-core
 * Domain Path: /languages
 * Requires at least: 5.9
 * Requires PHP: 7.4
 *
 * @package Mansa\Core
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'MANSA_CORE_VERSION', '1.0.0' );
define( 'MANSA_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'MANSA_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'MANSA_CORE_FILE', __FILE__ );

/**
 * Load the autoloader.
 */
$autoloader = MANSA_CORE_PATH . 'vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

// Plugin activation hook.
register_activation_hook( __FILE__, array( 'Mansa\\Core\\Plugin', 'activate' ) );

// Plugin deactivation hook.
register_deactivation_hook( __FILE__, array( 'Mansa\\Core\\Plugin', 'deactivate' ) );

/**
 * Initialize the plugin.
 */
add_action(
	'plugins_loaded',
	function () {
		\Mansa\Core\Plugin::init();
	},
	0
);

/**
 * Register all Gutenberg blocks
 */
function mansa_core_register_blocks() {
    // Register Featured Discovery block
    register_block_type( MANSA_CORE_PATH . 'build/featured-discovery' );
    
    // Register Category Grid block
    register_block_type( MANSA_CORE_PATH . 'build/category-grid' );
    
    // Register Featured Brand block
    register_block_type( MANSA_CORE_PATH . 'build/featured-brand' );
    
    // Register Community Newsletter block
    register_block_type( MANSA_CORE_PATH . 'build/community-newsletter' );
}
add_action( 'init', 'mansa_core_register_blocks' );

/**
 * Initialize assets handler
 */
function mansa_core_init_assets() {
    require_once MANSA_CORE_PATH . 'includes/class-assets.php';
    new \Mansa\Core\Assets();
}
add_action( 'plugins_loaded', 'mansa_core_init_assets' );
