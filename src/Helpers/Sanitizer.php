<?php
/**
 * Sanitizer Helper.
 *
 * @package Mansa\Core\Helpers
 */

namespace Mansa\Core\Helpers;

/**
 * Sanitizer helper class.
 */
class Sanitizer {

	/**
	 * Sanitize array of IDs.
	 *
	 * @param array $ids Array of IDs.
	 * @return array Sanitized array of integers.
	 */
	public static function sanitize_ids( $ids ) {
		if ( ! is_array( $ids ) ) {
			return array();
		}

		return array_map( 'absint', $ids );
	}

	/**
	 * Sanitize post type.
	 *
	 * @param string $post_type Post type.
	 * @return string Sanitized post type.
	 */
	public static function sanitize_post_type( $post_type ) {
		return sanitize_key( $post_type );
	}

	/**
	 * Sanitize taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @return string Sanitized taxonomy.
	 */
	public static function sanitize_taxonomy( $taxonomy ) {
		return sanitize_key( $taxonomy );
	}

	/**
	 * Sanitize meta key.
	 *
	 * @param string $meta_key Meta key.
	 * @return string Sanitized meta key.
	 */
	public static function sanitize_meta_key( $meta_key ) {
		return sanitize_key( $meta_key );
	}
}