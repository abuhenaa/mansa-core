<?php
/**
 * Query Helpers.
 *
 * @package Mansa\Core
 */

namespace Mansa\Core;

use Mansa\Relationships\ProductBrandRelation;
use Mansa\Relationships\ArticleRelations;
use Mansa\PostTypes\ProductPostType;
use Mansa\PostTypes\BrandPostType;
use Mansa\PostTypes\ArticlePostType;

/**
 * Query helper methods.
 */
class QueryHelpers {

	/**
	 * Get products for a specific brand.
	 *
	 * @param int $brand_id Brand ID.
	 * @return \WP_Query
	 */
	public static function get_products_by_brand( $brand_id ) {
		$relation = new ProductBrandRelation();

		return $relation->query_products_by_brand( $brand_id );
	}

	/**
	 * Get articles related to specific products.
	 *
	 * @param array $product_ids Array of product IDs.
	 * @return \WP_Query
	 */
	public static function get_articles_by_products( $product_ids ) {
		$relations = new ArticleRelations();

		return $relations->query_articles_by_products( $product_ids );
	}

	/**
	 * Get articles related to specific brands.
	 *
	 * @param array $brand_ids Array of brand IDs.
	 * @return \WP_Query
	 */
	public static function get_articles_by_brands( $brand_ids ) {
		$relations = new ArticleRelations();

		return $relations->query_articles_by_brands( $brand_ids );
	}

	/**
	 * Get all products with their brands.
	 *
	 * @param array $args Additional query arguments.
	 * @return \WP_Query
	 */
	public static function get_products_with_brands( $args = array() ) {
		$defaults = array(
			'post_type'      => ProductPostType::POST_TYPE,
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => ProductBrandRelation::META_KEY,
					'compare' => 'EXISTS',
				),
			),
		);

		return new \WP_Query( wp_parse_args( $args, $defaults ) );
	}

	/**
	 * Get brand information for a product.
	 *
	 * @param int $product_id Product ID.
	 * @return \WP_Post|null Brand post object or null.
	 */
	public static function get_product_brand( $product_id ) {
		$relation = new ProductBrandRelation();
		$brand_id = $relation->get_brand( $product_id );

		if ( $brand_id ) {
			return get_post( $brand_id );
		}

		return null;
	}

	/**
	 * Get related content for an article.
	 *
	 * @param int $article_id Article ID.
	 * @return array Array with 'products' and 'brands' keys.
	 */
	public static function get_article_related_content( $article_id ) {
		$relations = new ArticleRelations();

		return array(
			'products' => $relations->get_related_products( $article_id ),
			'brands'   => $relations->get_related_brands( $article_id ),
		);
	}
}