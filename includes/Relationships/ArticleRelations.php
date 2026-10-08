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
	 * Get related articles for a post (product or brand).
	 *
	 * @param int $post_id Post ID.
	 * @return array Array of article IDs.
	 */
	public function get_related_articles( $post_id ) {
		$article_ids = get_post_meta( $post_id, self::META_KEY_ARTICLES, true );
		return is_array( $article_ids ) ? array_values( array_map( 'absint', array_filter( $article_ids ) ) ) : array();
	}

	/**
	 * Set related articles for a post (product or brand).
	 *
	 * @param int   $post_id             Post ID.
	 * @param array $related_article_ids Array of article IDs.
	 * @return bool|int
	 */
	public function set_related_articles( $post_id, $related_article_ids ) {
		$sanitized_ids = array_map( 'absint', array_filter( (array) $related_article_ids ) );
		return update_post_meta( $post_id, self::META_KEY_ARTICLES, array_values( array_unique( $sanitized_ids ) ) );
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
	 * Get articles that reference or are related to a given product.
	 *
	 * @param int $product_id Product ID.
	 * @return int[]
	 */
	public function get_articles_by_product( $product_id ) {
		$product_id  = absint( $product_id );
		$article_ids = $this->get_related_articles( $product_id );

		global $wpdb;
		if ( $wpdb ) {
			$reverse_article_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta}
					WHERE meta_key = %s
					AND (meta_value = %d OR meta_value LIKE %s OR meta_value LIKE %s)",
					self::META_KEY_PRODUCTS,
					$product_id,
					'%' . $wpdb->esc_like( 'i:' . $product_id . ';' ) . '%',
					'%' . $wpdb->esc_like( '"' . $product_id . '"' ) . '%'
				)
			);

			if ( ! empty( $reverse_article_ids ) ) {
				$article_ids = array_merge( $article_ids, array_map( 'absint', $reverse_article_ids ) );
			}
		}

		return array_values( array_unique( array_filter( $article_ids ) ) );
	}

	/**
	 * Get articles that reference or are related to a given brand.
	 *
	 * @param int $brand_id Brand ID.
	 * @return int[]
	 */
	public function get_articles_by_brand( $brand_id ) {
		$brand_id    = absint( $brand_id );
		$article_ids = $this->get_related_articles( $brand_id );

		global $wpdb;
		if ( $wpdb ) {
			$reverse_article_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta}
					WHERE meta_key = %s
					AND (meta_value = %d OR meta_value LIKE %s OR meta_value LIKE %s)",
					self::META_KEY_BRANDS,
					$brand_id,
					'%' . $wpdb->esc_like( 'i:' . $brand_id . ';' ) . '%',
					'%' . $wpdb->esc_like( '"' . $brand_id . '"' ) . '%'
				)
			);

			if ( ! empty( $reverse_article_ids ) ) {
				$article_ids = array_merge( $article_ids, array_map( 'absint', $reverse_article_ids ) );
			}
		}

		return array_values( array_unique( array_filter( $article_ids ) ) );
	}

	/**
	 * Query articles by related products.
	 *
	 * @param array $product_ids Array of product IDs.
	 * @param array $args        Additional WP_Query args.
	 * @return \WP_Query
	 */
	public function query_articles_by_products( $product_ids, $args = array() ) {
		$all_article_ids = array();
		foreach ( (array) $product_ids as $pid ) {
			$all_article_ids = array_merge( $all_article_ids, $this->get_articles_by_product( $pid ) );
		}
		$all_article_ids = array_values( array_unique( array_filter( $all_article_ids ) ) );

		if ( empty( $all_article_ids ) ) {
			return new \WP_Query(
				array(
					'post_type'      => ArticlePostType::POST_TYPE,
					'post__in'       => array( 0 ),
					'posts_per_page' => isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : -1,
				)
			);
		}

		$defaults = array(
			'post_type'      => ArticlePostType::POST_TYPE,
			'post__in'       => $all_article_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => -1,
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
		$all_article_ids = array();
		foreach ( (array) $brand_ids as $bid ) {
			$all_article_ids = array_merge( $all_article_ids, $this->get_articles_by_brand( $bid ) );
		}
		$all_article_ids = array_values( array_unique( array_filter( $all_article_ids ) ) );

		if ( empty( $all_article_ids ) ) {
			return new \WP_Query(
				array(
					'post_type'      => ArticlePostType::POST_TYPE,
					'post__in'       => array( 0 ),
					'posts_per_page' => isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : -1,
				)
			);
		}

		$defaults = array(
			'post_type'      => ArticlePostType::POST_TYPE,
			'post__in'       => $all_article_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => -1,
		);

		$query_args = wp_parse_args( $args, $defaults );

		return new \WP_Query( $query_args );
	}
}