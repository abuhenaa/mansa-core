<?php
/**
 * Product Brand Relation.
 *
 * @package Mansa\Relationships
 */

namespace Mansa\Relationships;

use Mansa\PostTypes\ProductPostType;

/**
 * Product Brand relationship handler.
 */
class ProductBrandRelation {

	/**
	 * Meta key for brand relationship.
	 */
	const META_KEY = '_mansa_brand_id';

	/**
	 * Meta key for manually selected products on brand.
	 */
	const META_KEY_BRAND_PRODUCTS = '_mansa_brand_products';

	/**
	 * Get brand for a product.
	 *
	 * @param int $product_id Product ID.
	 * @return int|null Brand ID or null.
	 */
	public function get_brand( $product_id ) {
		$brand_id = get_post_meta( $product_id, self::META_KEY, true );
		return $brand_id ? absint( $brand_id ) : null;
	}

	/**
	 * Set brand for a product.
	 *
	 * @param int $product_id Product ID.
	 * @param int $brand_id   Brand ID.
	 * @return bool True on success, false on failure.
	 */
	public function set_brand( $product_id, $brand_id ) {
		return update_post_meta( $product_id, self::META_KEY, absint( $brand_id ) );
	}

	/**
	 * Get products for a brand.
	 *
	 * @param int $brand_id Brand ID.
	 * @return array Array of product IDs.
	 */
	public function get_products( $brand_id ) {
		global $wpdb;

		$product_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta}
				WHERE meta_key = %s AND meta_value = %d",
				self::META_KEY,
				$brand_id
			)
		);

		return array_map( 'absint', $product_ids );
	}

	/**
	 * Get manually selected products for a brand.
	 *
	 * @param int $brand_id Brand ID.
	 * @return array Array of product IDs.
	 */
	public function get_selected_products( $brand_id ) {
		$product_ids = get_post_meta( $brand_id, self::META_KEY_BRAND_PRODUCTS, true );
		if ( is_string( $product_ids ) && ! empty( $product_ids ) ) {
			return array_values( array_filter( array_map( 'absint', explode( ',', $product_ids ) ) ) );
		}
		if ( is_array( $product_ids ) ) {
			return array_values( array_filter( array_map( 'absint', $product_ids ) ) );
		}
		return array();
	}

	/**
	 * Set manually selected products for a brand.
	 *
	 * @param int   $brand_id    Brand ID.
	 * @param array $product_ids Array of product IDs.
	 * @return bool True on success, false on failure.
	 */
	public function set_selected_products( $brand_id, $product_ids ) {
		$sanitized_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $product_ids ) ) ) );
		$result        = update_post_meta( $brand_id, self::META_KEY_BRAND_PRODUCTS, $sanitized_ids );

		// Sync: ensure selected products have this brand assigned if none exists yet.
		foreach ( $sanitized_ids as $pid ) {
			$existing_brand = $this->get_brand( $pid );
			if ( empty( $existing_brand ) ) {
				$this->set_brand( $pid, $brand_id );
			}
		}

		return (bool) $result;
	}

	/**
	 * Query manually selected products by brand.
	 *
	 * @param int   $brand_id Brand ID.
	 * @param array $args     Additional WP_Query args.
	 * @return \WP_Query
	 */
	public function query_selected_products( $brand_id, $args = array() ) {
		$product_ids = $this->get_selected_products( $brand_id );
		if ( empty( $product_ids ) ) {
			return new \WP_Query( array( 'post__in' => array( 0 ) ) );
		}

		$defaults = array(
			'post_type'      => ProductPostType::POST_TYPE,
			'post__in'       => $product_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $product_ids ),
			'post_status'    => 'publish',
		);

		$query_args = wp_parse_args( $args, $defaults );

		return new \WP_Query( $query_args );
	}

	/**
	 * Query products by brand.
	 *
	 * @param int   $brand_id Brand ID.
	 * @param array $args     Additional WP_Query args.
	 * @return \WP_Query
	 */
	public function query_products_by_brand( $brand_id, $args = array() ) {
		$defaults = array(
			'post_type'      => ProductPostType::POST_TYPE,
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => self::META_KEY,
					'value' => $brand_id,
					'type'  => 'NUMERIC',
				),
			),
		);

		$query_args = wp_parse_args( $args, $defaults );

		return new \WP_Query( $query_args );
	}
}