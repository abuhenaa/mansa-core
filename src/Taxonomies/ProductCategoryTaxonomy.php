<?php
/**
 * Product Category Taxonomy.
 *
 * @package Mansa\Taxonomies
 */

namespace Mansa\Taxonomies;

use Mansa\PostTypes\ProductPostType;
use Mansa\PostTypes\BrandPostType;

/**
 * Product Category taxonomy.
 */
class ProductCategoryTaxonomy {

	/**
	 * Taxonomy slug.
	 */
	const TAXONOMY = 'mansa_product_category';

	/**
	 * Register the taxonomy.
	 *
	 * @return void
	 */
	public function register() {
		$labels = array(
			'name'              => _x( 'Product Categories', 'taxonomy general name', 'mansa-core' ),
			'singular_name'     => _x( 'Product Category', 'taxonomy singular name', 'mansa-core' ),
			'search_items'      => __( 'Search Product Categories', 'mansa-core' ),
			'all_items'         => __( 'All Product Categories', 'mansa-core' ),
			'parent_item'       => __( 'Parent Product Category', 'mansa-core' ),
			'parent_item_colon' => __( 'Parent Product Category:', 'mansa-core' ),
			'edit_item'         => __( 'Edit Product Category', 'mansa-core' ),
			'update_item'       => __( 'Update Product Category', 'mansa-core' ),
			'add_new_item'      => __( 'Add New Product Category', 'mansa-core' ),
			'new_item_name'     => __( 'New Product Category Name', 'mansa-core' ),
			'menu_name'         => __( 'Product Categories', 'mansa-core' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'product-category' ),
			'show_in_rest'      => true,
			'rest_base'         => 'product-categories',
		);

		register_taxonomy(
			self::TAXONOMY,
			array( ProductPostType::POST_TYPE, BrandPostType::POST_TYPE ),
			$args
		);
	}
}