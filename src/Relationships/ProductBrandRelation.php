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