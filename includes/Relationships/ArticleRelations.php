<?php
/**
 * Article Relations.
 *
 * @package Mansa\Relationships
 */

namespace Mansa\Relationships;

use Mansa\PostTypes\ProductPostType;
use Mansa\PostTypes\BrandPostType;
use Mansa\PostTypes\ArticlePostType;

/**
 * Article relationships handler.
 */
class ArticleRelations {

	/**
	 * Meta key for related products.
	 */
	const META_KEY_PRODUCTS = '_mansa_related_products';

	/**
	 * Meta key for related articles.
	 */
	const META_KEY_ARTICLES = '_mansa_related_articles';

	/**
	 * Meta key for related brands.
	 */
	const META_KEY_BRANDS = '_mansa_related_brands';

	/**
	 * Get related products for an article.
	 *
	 * @param int $article_id Article ID.
	 * @return array Array of product IDs.
	 */
	public function get_related_products( $article_id ) {
		$product_ids = get_post_meta( $article_id, self::META_KEY_PRODUCTS, true );
		return is_array( $product_ids ) ? array_map( 'absint', $product_ids ) : array();
	}

	/**
	 * set related articles for product
	 */
	public function set_related_articles( $article_id, $related_article_ids ) {
		$sanitized_ids = array_map( 'absint', (array) $related_article_ids );
		return update_post_meta( $article_id, self::META_KEY_ARTICLES, $sanitized_ids );
	}

	/**
	 * Set related products for an article.
	 *
	 * @param int   $article_id  Article ID.
	 * @param array $product_ids Array of product IDs.
	 * @return bool True on success, false on failure.
	 */
	public function set_related_products( $article_id, $product_ids ) {
		$sanitized_ids = array_map( 'absint', (array) $product_ids );
		return update_post_meta( $article_id, self::META_KEY_PRODUCTS, $sanitized_ids );
	}

	/**
	 * Get related brands for an article.
	 *
	 * @param int $article_id Article ID.
	 * @return array Array of brand IDs.
	 */
	public function get_related_brands( $article_id ) {
		$brand_ids = get_post_meta( $article_id, self::META_KEY_BRANDS, true );
		return is_array( $brand_ids ) ? array_map( 'absint', $brand_ids ) : array();
	}

	/**
	 * Set related brands for an article.
	 *
	 * @param int   $article_id Article ID.
	 * @param array $brand_ids  Array of brand IDs.
	 * @return bool True on success, false on failure.
	 */
	public function set_related_brands( $article_id, $brand_ids ) {
		$sanitized_ids = array_map( 'absint', (array) $brand_ids );
		return update_post_meta( $article_id, self::META_KEY_BRANDS, $sanitized_ids );
	}

	/**
	 * Get articles that reference a given product.
	 *
	 * @param int $product_id Product ID.
	 * @return int[]
	 */
	public function get_articles_by_product( $product_id ) {
		$query = $this->query_articles_by_products( array( $product_id ), array( 'fields' => 'ids', 'posts_per_page' => -1 ) );
		return $query->have_posts() ? $query->posts : array();
	}

	/**
	 * Query articles by related products.
	 *
	 * @param array $product_ids Array of product IDs.
	 * @param array $args        Additional WP_Query args.
	 * @return \WP_Query
	 */
	public function query_articles_by_products( $product_ids, $args = array() ) {
		$defaults = array(
			'post_type'      => ArticlePostType::POST_TYPE,
			'posts_per_page' => -1
			);

		$query_args = wp_parse_args( $args, $defaults );

		return new \WP_Query( $query_args );
	}

	/**
	 * Query articles by related brands.
	 *
	 * @param array $brand_ids Array of brand IDs.
	 * @param array $args      Additional WP_Query args.
	 * @return \WP_Query
	 */
	public function query_articles_by_brands( $brand_ids, $args = array() ) {
		$defaults = array(
			'post_type'      => ArticlePostType::POST_TYPE,
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => self::META_KEY_BRANDS,
					'value'   => $brand_ids,
					'compare' => 'IN',
				),
			),
		);

		$query_args = wp_parse_args( $args, $defaults );

		return new \WP_Query( $query_args );
	}
}