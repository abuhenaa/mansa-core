<?php
/**
 * Product Post Type.
 *
 * @package Mansa\PostTypes
 */

namespace Mansa\PostTypes;

/**
 * Product post type.
 */
class ProductPostType {

	/**
	 * Post type slug.
	 */
	const POST_TYPE = 'mansa_product';

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register() {
		$labels = array(
			'name'               => _x( 'Products', 'post type general name', 'mansa-core' ),
			'singular_name'      => _x( 'Product', 'post type singular name', 'mansa-core' ),
			'menu_name'          => _x( 'Products', 'admin menu', 'mansa-core' ),
			'name_admin_bar'     => _x( 'Product', 'add new on admin bar', 'mansa-core' ),
			'add_new'            => _x( 'Add New', 'product', 'mansa-core' ),
			'add_new_item'       => __( 'Add New Product', 'mansa-core' ),
			'new_item'           => __( 'New Product', 'mansa-core' ),
			'edit_item'          => __( 'Edit Product', 'mansa-core' ),
			'view_item'          => __( 'View Product', 'mansa-core' ),
			'all_items'          => __( 'All Products', 'mansa-core' ),
			'search_items'       => __( 'Search Products', 'mansa-core' ),
			'parent_item_colon'  => __( 'Parent Products:', 'mansa-core' ),
			'not_found'          => __( 'No products found.', 'mansa-core' ),
			'not_found_in_trash' => __( 'No products found in Trash.', 'mansa-core' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'products' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'products',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
		);

		register_post_type( self::POST_TYPE, $args );
	}
}