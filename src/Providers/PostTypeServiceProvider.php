<?php
/**
 * Post Type Service Provider.
 *
 * @package Mansa\Providers
 */

namespace Mansa\Providers;

use Mansa\Core\Container;
use Mansa\Core\ServiceProvider;
use Mansa\PostTypes\ProductPostType;
use Mansa\PostTypes\BrandPostType;
use Mansa\PostTypes\ArticlePostType;

/**
 * Post Type Service Provider.
 */
class PostTypeServiceProvider extends ServiceProvider {

	/**
	 * Register services.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function register( Container $container ) {
		$container->register(
			ProductPostType::class,
			function ( $c ) {
				return new ProductPostType();
			}
		);

		$container->register(
			BrandPostType::class,
			function ( $c ) {
				return new BrandPostType();
			}
		);

		$container->register(
			ArticlePostType::class,
			function ( $c ) {
				return new ArticlePostType();
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
		add_action( 'init', array( $this, 'register_post_types' ) );
	}

	/**
	 * Register post types.
	 *
	 * @return void
	 */
	public function register_post_types() {
		$container = \Mansa\Core\Plugin::get_container();

		$container->get( ProductPostType::class )->register();
		$container->get( BrandPostType::class )->register();
		$container->get( ArticlePostType::class )->register();
	}
}