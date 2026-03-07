<?php
/**
 * Usage Examples.
 *
 * @package Mansa\Core
 */

namespace Mansa\Core;

use Mansa\Relationships\ProductBrandRelation;
use Mansa\Relationships\ArticleRelations;
use Mansa\PostTypes\ProductPostType;
use Mansa\PostTypes\BrandPostType;
use Mansa\PostTypes\ArticlePostType;

/**
 * Usage examples class.
 */
class UsageExamples {

	/**
	 * Example: Create a new product and assign a brand.
	 *
	 * @param string $product_title Product title.
	 * @param string $product_content Product content.
	 * @param int    $brand_id Brand ID.
	 * @return int Product ID.
	 */
	public static function create_product_with_brand( $product_title, $product_content, $brand_id ) {
		$product_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( $product_title ),
				'post_content' => wp_kses_post( $product_content ),
				'post_type'    => ProductPostType::POST_TYPE,
				'post_status'  => 'publish',
			)
		);

		if ( $product_id && ! is_wp_error( $product_id ) ) {
			$relation = new ProductBrandRelation();
			$relation->set_brand( $product_id, $brand_id );
		}

		return $product_id;
	}

	/**
	 * Example: Create an article with related products and brands.
	 *
	 * @param string $article_title Article title.
	 * @param string $article_content Article content.
	 * @param array  $product_ids Array of product IDs.
	 * @param array  $brand_ids Array of brand IDs.
	 * @return int Article ID.
	 */
	public static function create_article_with_relations( $article_title, $article_content, $product_ids, $brand_ids ) {
		$article_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( $article_title ),
				'post_content' => wp_kses_post( $article_content ),
				'post_type'    => ArticlePostType::POST_TYPE,
				'post_status'  => 'publish',
			)
		);

		if ( $article_id && ! is_wp_error( $article_id ) ) {
			$relations = new ArticleRelations();
			$relations->set_related_products( $article_id, $product_ids );
			$relations->set_related_brands( $article_id, $brand_ids );
		}

		return $article_id;
	}

	/**
	 * Example: Query products by brand.
	 *
	 * @param int $brand_id Brand ID.
	 * @return array Array of product posts.
	 */
	public static function get_brand_products( $brand_id ) {
		$query = QueryHelpers::get_products_by_brand( $brand_id );

		return $query->posts;
	}

	/**
	 * Example: Display product with brand information.
	 *
	 * @param int $product_id Product ID.
	 * @return void
	 */
	public static function display_product_with_brand( $product_id ) {
		$product = get_post( $product_id );
		$brand   = QueryHelpers::get_product_brand( $product_id );

		if ( $product ) {
			echo '<h2>' . esc_html( $product->post_title ) . '</h2>';
			echo '<div>' . wp_kses_post( $product->post_content ) . '</div>';

			if ( $brand ) {
				echo '<p>Brand: <a href="' . esc_url( get_permalink( $brand->ID ) ) . '">' . esc_html( $brand->post_title ) . '</a></p>';
			}
		}
	}

	/**
	 * Example: Display article with related content.
	 *
	 * @param int $article_id Article ID.
	 * @return void
	 */
	public static function display_article_with_relations( $article_id ) {
		$article = get_post( $article_id );
		$related = QueryHelpers::get_article_related_content( $article_id );

		if ( $article ) {
			echo '<h1>' . esc_html( $article->post_title ) . '</h1>';
			echo '<div>' . wp_kses_post( $article->post_content ) . '</div>';

			if ( ! empty( $related['products'] ) ) {
				echo '<h3>Related Products:</h3><ul>';
				foreach ( $related['products'] as $product_id ) {
					$product = get_post( $product_id );
					if ( $product ) {
						echo '<li><a href="' . esc_url( get_permalink( $product->ID ) ) . '">' . esc_html( $product->post_title ) . '</a></li>';
					}
				}
				echo '</ul>';
			}

			if ( ! empty( $related['brands'] ) ) {
				echo '<h3>Related Brands:</h3><ul>';
				foreach ( $related['brands'] as $brand_id ) {
					$brand = get_post( $brand_id );
					if ( $brand ) {
						echo '<li><a href="' . esc_url( get_permalink( $brand->ID ) ) . '">' . esc_html( $brand->post_title ) . '</a></li>';
					}
				}
				echo '</ul>';
			}
		}
	}
}