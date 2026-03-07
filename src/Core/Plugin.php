<?php
/**
 * Main Plugin Class.
 *
 * @package Mansa\Core
 */

namespace Mansa\Core;

use Mansa\Providers\PostTypeServiceProvider;
use Mansa\Providers\TaxonomyServiceProvider;
use Mansa\Providers\RelationshipServiceProvider;
use Mansa\Providers\AdminServiceProvider;

/**
 * Main plugin class.
 */
class Plugin {

	/**
	 * Container instance.
	 *
	 * @var Container
	 */
	protected static $container;

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public static function init() {
		self::$container = new Container();

		self::register_providers();
		self::boot_providers();
	}

	/**
	 * Register service providers.
	 *
	 * @return void
	 */
	protected static function register_providers() {
		$providers = array(
			PostTypeServiceProvider::class,
			TaxonomyServiceProvider::class,
			RelationshipServiceProvider::class,
			AdminServiceProvider::class,
		);

		foreach ( $providers as $provider_class ) {
			$provider = new $provider_class();
			$provider->register( self::$container );
		}
	}

	/**
	 * Boot service providers.
	 *
	 * @return void
	 */
	protected static function boot_providers() {
		$providers = array(
			PostTypeServiceProvider::class,
			TaxonomyServiceProvider::class,
			RelationshipServiceProvider::class,
			AdminServiceProvider::class,
		);

		foreach ( $providers as $provider_class ) {
			$provider = new $provider_class();
			$provider->boot( self::$container );
		}
	}

	/**
	 * Get the container instance.
	 *
	 * @return Container
	 */
	public static function get_container() {
		return self::$container;
	}

	/**
	 * Plugin activation hook.
	 *
	 * @return void
	 */
	public static function activate() {
		// Flush rewrite rules on activation.
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation hook.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Flush rewrite rules on deactivation.
		flush_rewrite_rules();
	}
}
