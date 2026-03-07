<?php
/**
 * Admin Service Provider.
 *
 * @package Mansa\Providers
 */

namespace Mansa\Providers;

use Mansa\Core\Container;
use Mansa\Core\ServiceProvider;
use Mansa\Admin\MetaBoxes;

/**
 * Admin Service Provider.
 */
class AdminServiceProvider extends ServiceProvider {

	/**
	 * Register services.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function register( Container $container ) {
		$container->register(
			MetaBoxes::class,
			function ( $c ) {
				return new MetaBoxes();
			}
		);
	}

	/**
	 * Boot the provider.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function boot( Container $container ) {
		if ( is_admin() ) {
			$meta_boxes = $container->get( MetaBoxes::class );
			add_action( 'add_meta_boxes', array( $meta_boxes, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $meta_boxes, 'save_meta_boxes' ) );
		}
	}
}