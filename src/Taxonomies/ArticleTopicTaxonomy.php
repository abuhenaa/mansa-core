<?php
/**
 * Article Topic Taxonomy.
 *
 * @package Mansa\Taxonomies
 */

namespace Mansa\Taxonomies;

use Mansa\PostTypes\ArticlePostType;

/**
 * Article Topic taxonomy.
 */
class ArticleTopicTaxonomy {

	/**
	 * Taxonomy slug.
	 */
	const TAXONOMY = 'mansa_article_topic';

	/**
	 * Register the taxonomy.
	 *
	 * @return void
	 */
	public function register() {
		$labels = array(
			'name'              => _x( 'Article Topics', 'taxonomy general name', 'mansa-core' ),
			'singular_name'     => _x( 'Article Topic', 'taxonomy singular name', 'mansa-core' ),
			'search_items'      => __( 'Search Article Topics', 'mansa-core' ),
			'all_items'         => __( 'All Article Topics', 'mansa-core' ),
			'parent_item'       => __( 'Parent Article Topic', 'mansa-core' ),
			'parent_item_colon' => __( 'Parent Article Topic:', 'mansa-core' ),
			'edit_item'         => __( 'Edit Article Topic', 'mansa-core' ),
			'update_item'       => __( 'Update Article Topic', 'mansa-core' ),
			'add_new_item'      => __( 'Add New Article Topic', 'mansa-core' ),
			'new_item_name'     => __( 'New Article Topic Name', 'mansa-core' ),
			'menu_name'         => __( 'Article Topics', 'mansa-core' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'article-topic' ),
			'show_in_rest'      => true,
			'rest_base'         => 'article-topics',
		);

		register_taxonomy(
			self::TAXONOMY,
			array( ArticlePostType::POST_TYPE ),
			$args
		);
	}
}