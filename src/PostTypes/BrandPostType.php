<?php
/**
 * Brand Post Type.
 *
 * @package Mansa\PostTypes
 */

namespace Mansa\PostTypes;

/**
 * Brand post type.
 */
class BrandPostType {

	/**
	 * Post type slug.
	 */
	const POST_TYPE = 'mansa_brand';

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register() {
		$labels = array(
			'name'               => _x( 'Brands', 'post type general name', 'mansa-core' ),
			'singular_name'      => _x( 'Brand', 'post type singular name', 'mansa-core' ),
			'menu_name'          => _x( 'Brands', 'admin menu', 'mansa-core' ),
			'name_admin_bar'     => _x( 'Brand', 'add new on admin bar', 'mansa-core' ),
			'add_new'            => _x( 'Add New', 'brand', 'mansa-core' ),
			'add_new_item'       => __( 'Add New Brand', 'mansa-core' ),
			'new_item'           => __( 'New Brand', 'mansa-core' ),
			'edit_item'          => __( 'Edit Brand', 'mansa-core' ),
			'view_item'          => __( 'View Brand', 'mansa-core' ),
			'all_items'          => __( 'All Brands', 'mansa-core' ),
			'search_items'       => __( 'Search Brands', 'mansa-core' ),
			'parent_item_colon'  => __( 'Parent Brands:', 'mansa-core' ),
			'not_found'          => __( 'No brands found.', 'mansa-core' ),
			'not_found_in_trash' => __( 'No brands found in Trash.', 'mansa-core' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'brands' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'brands',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
		);

		register_post_type( self::POST_TYPE, $args );
	}
}