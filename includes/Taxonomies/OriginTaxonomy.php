<?php
/**
 * Origin Taxonomy.
 *
 * @package Mansa\Taxonomies
 */

namespace Mansa\Taxonomies;

use Mansa\PostTypes\BrandPostType;
use Mansa\PostTypes\ProductPostType;

/**
 * Origin taxonomy.
 */
class OriginTaxonomy {

	/**
	 * Taxonomy slug.
	 */
	const TAXONOMY = 'mansa_origin';

	/**
	 * Register the taxonomy.
	 *
	 * @return void
	 */
	public function register() {
		$labels = array(
			'name'              => _x( 'Origins', 'taxonomy general name', 'mansa-core' ),
			'singular_name'     => _x( 'Origin', 'taxonomy singular name', 'mansa-core' ),
			'search_items'      => __( 'Search Origins', 'mansa-core' ),
			'all_items'         => __( 'All Origins', 'mansa-core' ),
			'parent_item'       => __( 'Parent Origin', 'mansa-core' ),
			'parent_item_colon' => __( 'Parent Origin:', 'mansa-core' ),
			'edit_item'         => __( 'Edit Origin', 'mansa-core' ),
			'update_item'       => __( 'Update Origin', 'mansa-core' ),
			'add_new_item'      => __( 'Add New Origin', 'mansa-core' ),
			'new_item_name'     => __( 'New Origin Name', 'mansa-core' ),
			'menu_name'         => __( 'Origins', 'mansa-core' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'origin' ),
			'show_in_rest'      => true,
			'rest_base'         => 'origins',
		);

		register_taxonomy(
			self::TAXONOMY,
			array( ProductPostType::POST_TYPE, BrandPostType::POST_TYPE ),
			$args
		);
	}
}