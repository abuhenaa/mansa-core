<?php
/**
 * Article Post Type.
 *
 * @package Mansa\PostTypes
 */

namespace Mansa\PostTypes;

/**
 * Article post type.
 */
class ArticlePostType {

	/**
	 * Post type slug.
	 */
	const POST_TYPE = 'mansa_article';

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register() {
		$labels = array(
			'name'               => _x( 'Articles', 'post type general name', 'mansa-core' ),
			'singular_name'      => _x( 'Article', 'post type singular name', 'mansa-core' ),
			'menu_name'          => _x( 'Articles', 'admin menu', 'mansa-core' ),
			'name_admin_bar'     => _x( 'Article', 'add new on admin bar', 'mansa-core' ),
			'add_new'            => _x( 'Add New', 'article', 'mansa-core' ),
			'add_new_item'       => __( 'Add New Article', 'mansa-core' ),
			'new_item'           => __( 'New Article', 'mansa-core' ),
			'edit_item'          => __( 'Edit Article', 'mansa-core' ),
			'view_item'          => __( 'View Article', 'mansa-core' ),
			'all_items'          => __( 'All Articles', 'mansa-core' ),
			'search_items'       => __( 'Search Articles', 'mansa-core' ),
			'parent_item_colon'  => __( 'Parent Articles:', 'mansa-core' ),
			'not_found'          => __( 'No articles found.', 'mansa-core' ),
			'not_found_in_trash' => __( 'No articles found in Trash.', 'mansa-core' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'articles' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'articles',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
			'menu_icon'          => 'dashicons-media-document',
		);

		register_post_type( self::POST_TYPE, $args );
	}
}