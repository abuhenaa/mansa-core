<?php
/**
 * Taxonomy Service Provider.
 *
 * @package Mansa\Providers
 */

namespace Mansa\Providers;

use Mansa\Core\Container;
use Mansa\Core\ServiceProvider;
use Mansa\Taxonomies\ProductCategoryTaxonomy;
use Mansa\Taxonomies\OriginTaxonomy;
use Mansa\Taxonomies\ArticleTopicTaxonomy;

/**
 * Taxonomy Service Provider.
 */
class TaxonomyServiceProvider extends ServiceProvider {

	/**
	 * Register services.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function register( Container $container ) {
		$container->register(
			ProductCategoryTaxonomy::class,
			function ( $c ) {
				return new ProductCategoryTaxonomy();
			}
		);

		$container->register(
			OriginTaxonomy::class,
			function ( $c ) {
				return new OriginTaxonomy();
			}
		);

		$container->register(
			ArticleTopicTaxonomy::class,
			function ( $c ) {
				return new ArticleTopicTaxonomy();
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
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Register taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		$container = \Mansa\Core\Plugin::get_container();

		$container->get( ProductCategoryTaxonomy::class )->register();
		$container->get( OriginTaxonomy::class )->register();
		$container->get( ArticleTopicTaxonomy::class )->register();
	}
}