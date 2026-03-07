<?php
/**
 * Relationship Service Provider.
 *
 * @package Mansa\Providers
 */

namespace Mansa\Providers;

use Mansa\Core\Container;
use Mansa\Core\ServiceProvider;
use Mansa\Relationships\ProductBrandRelation;
use Mansa\Relationships\ArticleRelations;

/**
 * Relationship Service Provider.
 */
class RelationshipServiceProvider extends ServiceProvider {

	/**
	 * Register services.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function register( Container $container ) {
		$container->register(
			ProductBrandRelation::class,
			function ( $c ) {
				return new ProductBrandRelation();
			}
		);

		$container->register(
			ArticleRelations::class,
			function ( $c ) {
				return new ArticleRelations();
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
		// Relationships are handled through meta boxes and queries.
		// No specific boot actions needed.
	}
}