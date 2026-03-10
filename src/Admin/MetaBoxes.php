<?php
/**
 * Admin Meta Boxes.
 *
 * @package Mansa\Admin
 */

namespace Mansa\Admin;

use Mansa\PostTypes\ProductPostType;
use Mansa\PostTypes\ArticlePostType;
use Mansa\PostTypes\BrandPostType;
use Mansa\Relationships\ProductBrandRelation;
use Mansa\Relationships\ArticleRelations;

/**
 * Admin meta boxes handler.
 */
class MetaBoxes {

	/**
	 * Product Brand Relation instance.
	 *
	 * @var ProductBrandRelation
	 */
	protected $product_brand_relation;

	/**
	 * Article Relations instance.
	 *
	 * @var ArticleRelations
	 */
	protected $article_relations;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->product_brand_relation = new ProductBrandRelation();
		$this->article_relations      = new ArticleRelations();
	}

	/**
	 * Add meta boxes.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		// Product brand selector.
		add_meta_box(
			'mansa_product_brand',
			__( 'Brand', 'mansa-core' ),
			array( $this, 'render_product_brand_meta_box' ),
			ProductPostType::POST_TYPE,
			'side',
			'default'
		);

		// Article related products.
		add_meta_box(
			'mansa_article_products',
			__( 'Related Products', 'mansa-core' ),
			array( $this, 'render_article_products_meta_box' ),
			ArticlePostType::POST_TYPE,
			'side',
			'default'
		);

		// Article related brands.
		add_meta_box(
			'mansa_article_brands',
			__( 'Related Brands', 'mansa-core' ),
			array( $this, 'render_article_brands_meta_box' ),
			ArticlePostType::POST_TYPE,
			'side',
			'default'
		);

		// Product where to buy link.
		add_meta_box(
			'mansa_product_where_to_buy',
			__( 'Where to Buy Link', 'mansa-core' ),
			array( $this, 'render_product_where_to_buy_meta_box' ),
			ProductPostType::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Save meta boxes.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta_boxes( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['mansa_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['mansa_meta_nonce'] ), 'mansa_save_meta' ) ) {
			return;
		}

		// Check capabilities.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( ProductPostType::POST_TYPE === $post_type ) {
			$this->save_product_brand( $post_id );
			$this->save_product_where_to_buy( $post_id );
		} elseif ( ArticlePostType::POST_TYPE === $post_type ) {
			$this->save_article_relations( $post_id );
		}
	}

	/**
	 * Render product brand meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_product_brand_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );

		$current_brand = $this->product_brand_relation->get_brand( $post->ID );

		$brands = get_posts(
			array(
				'post_type'      => BrandPostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		echo '<select name="mansa_brand_id" id="mansa_brand_id" style="width: 100%;">';
		echo '<option value="">' . esc_html__( 'Select a brand...', 'mansa-core' ) . '</option>';

		foreach ( $brands as $brand ) {
			$selected = selected( $current_brand, $brand->ID, false );
			echo '<option value="' . esc_attr( $brand->ID ) . '" ' . esc_attr( $selected ) . '>';
			echo esc_html( $brand->post_title );
			echo '</option>';
		}

		echo '</select>';
	}

	/**
	 * Render article products meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_article_products_meta_box( $post ) {
		$current_products = $this->article_relations->get_related_products( $post->ID );

		$products = get_posts(
			array(
				'post_type'      => ProductPostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		echo '<select name="mansa_related_products[]" id="mansa_related_products" multiple style="width: 100%; height: 150px;">';

		foreach ( $products as $product ) {
			$selected = in_array( $product->ID, $current_products, true ) ? 'selected' : '';
			echo '<option value="' . esc_attr( $product->ID ) . '" ' . esc_attr( $selected ) . '>';
			echo esc_html( $product->post_title );
			echo '</option>';
		}

		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Hold Ctrl/Cmd to select multiple products.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Render article brands meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_article_brands_meta_box( $post ) {
		$current_brands = $this->article_relations->get_related_brands( $post->ID );

		$brands = get_posts(
			array(
				'post_type'      => BrandPostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		echo '<select name="mansa_related_brands[]" id="mansa_related_brands" multiple style="width: 100%; height: 150px;">';

		foreach ( $brands as $brand ) {
			$selected = in_array( $brand->ID, $current_brands, true ) ? 'selected' : '';
			echo '<option value="' . esc_attr( $brand->ID ) . '" ' . esc_attr( $selected ) . '>';
			echo esc_html( $brand->post_title );
			echo '</option>';
		}

		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Hold Ctrl/Cmd to select multiple brands.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Render product where to buy links meta box.
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_product_where_to_buy_meta_box( $post ) {
		$current_link = get_post_meta( $post->ID, '_mansa_where_to_buy_link', true );
		echo '<input type="url" name="mansa_where_to_buy_link" id="mansa_where_to_buy_link" value="' . esc_attr( $current_link ) . '" style="width: 100%;" />';
		echo '<p class="description">' . esc_html__( 'Enter a URL where customers can buy this product.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Save product where to buy link.
	 */
	protected function save_product_where_to_buy( $post_id ) {
		if ( isset( $_POST['mansa_where_to_buy_link'] ) ) {
			$link = esc_url_raw( $_POST['mansa_where_to_buy_link'] );
			update_post_meta( $post_id, '_mansa_where_to_buy_link', $link );
		}
	}



	/**
	 * Save product brand.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_product_brand( $post_id ) {
		if ( isset( $_POST['mansa_brand_id'] ) ) {
			$brand_id = absint( $_POST['mansa_brand_id'] );
			$this->product_brand_relation->set_brand( $post_id, $brand_id );
		}
	}

	/**
	 * Save article relations.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_article_relations( $post_id ) {
		if ( isset( $_POST['mansa_related_products'] ) ) {
			$product_ids = array_map( 'absint', (array) $_POST['mansa_related_products'] );
			$this->article_relations->set_related_products( $post_id, $product_ids );
		}

		if ( isset( $_POST['mansa_related_brands'] ) ) {
			$brand_ids = array_map( 'absint', (array) $_POST['mansa_related_brands'] );
			$this->article_relations->set_related_brands( $post_id, $brand_ids );
		}
	}
}