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

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
	}

	/**
	 * Enqueue editor scripts for metaboxes.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_editor_scripts( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		// Check if we're on the brand post type edit page.
		if ( isset( $_GET['post'] ) ) {
			$post_type = get_post_type( absint( $_GET['post'] ) );
		} elseif ( isset( $_GET['post_type'] ) ) {
			$post_type = sanitize_key( $_GET['post_type'] );
		} else {
			return;
		}

		if ( BrandPostType::POST_TYPE !== $post_type && ProductPostType::POST_TYPE !== $post_type && ArticlePostType::POST_TYPE !== $post_type ) {
			return;
		}

		wp_enqueue_editor();
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
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

		// Article related articles selector.
		add_meta_box(
			'mansa_article_related_articles',
			__( 'Related Articles', 'mansa-core' ),
			array( $this, 'render_article_related_articles_meta_box' ),
			ArticlePostType::POST_TYPE,
			'normal',
			'high'
		);

		// Product related articles.
		add_meta_box(
			'mansa_product_articles',
			__( 'Related Articles', 'mansa-core' ),
			array( $this, 'render_product_articles_meta_box' ),
			ProductPostType::POST_TYPE,
			'side',
			'default'
		);

		// Product related products selector.
		add_meta_box(
			'mansa_product_related_products',
			__( 'Related Products', 'mansa-core' ),
			array( $this, 'render_product_related_products_meta_box' ),
			ProductPostType::POST_TYPE,
			'normal',
			'high'
		);

		// Brand related articles.
		add_meta_box(
			'mansa_brand_articles',
			__( 'Related Articles', 'mansa-core' ),
			array( $this, 'render_brand_articles_meta_box' ),
			BrandPostType::POST_TYPE,
			'side',
			'default'
		);

		// Brand products selector.
		add_meta_box(
			'mansa_brand_products',
			__( 'Brand Products', 'mansa-core' ),
			array( $this, 'render_brand_products_meta_box' ),
			BrandPostType::POST_TYPE,
			'normal',
			'high'
		);

		// Brand founder story.
		add_meta_box(
			'mansa_brand_founder_story',
			__( 'Founder Story', 'mansa-core' ),
			array( $this, 'render_brand_founder_story_meta_box' ),
			BrandPostType::POST_TYPE,
			'normal',
			'default'
		);

		// Brand website and social links.
		add_meta_box(
			'mansa_brand_contact',
			__( 'Contact Info', 'mansa-core' ),
			array( $this, 'render_brand_contact_meta_box' ),
			BrandPostType::POST_TYPE,
			'side',
			'default'
		);

		// Ad Placements #1 and #2.
		$ad_post_types = array(
			ProductPostType::POST_TYPE,
			BrandPostType::POST_TYPE,
			ArticlePostType::POST_TYPE,
		);
		foreach ( $ad_post_types as $pt ) {
			add_meta_box(
				'mansa_ad_placements',
				__( 'Ad Placements', 'mansa-core' ),
				array( $this, 'render_ad_placements_meta_box' ),
				$pt,
				'side',
				'default'
			);
		}
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

		// Save Ad Placements across all supported post types.
		$this->save_ad_placements( $post_id );

		if ( ProductPostType::POST_TYPE === $post_type ) {
			$this->save_product_brand( $post_id );
			$this->save_product_where_to_buy( $post_id );
			$this->save_product_articles( $post_id );
			$this->save_product_related_products( $post_id );
		} elseif ( ArticlePostType::POST_TYPE === $post_type ) {
			$this->save_article_relations( $post_id );
			$this->save_article_related_articles( $post_id );
		} elseif ( BrandPostType::POST_TYPE === $post_type ) {
			$this->save_brand_meta( $post_id );
			$this->save_brand_articles( $post_id );
			$this->save_brand_products( $post_id );
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
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
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
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
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
	 * Render product related articles meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_product_articles_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
		echo '<input type="hidden" name="mansa_product_articles_present" value="1" />';

		$current_articles = $this->article_relations->get_articles_by_product( $post->ID );

		$articles = get_posts(
			array(
				'post_type'      => ArticlePostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		echo '<select name="mansa_related_articles[]" id="mansa_related_articles" multiple style="width: 100%; height: 150px;">';

		foreach ( $articles as $article ) {
			$selected = in_array( $article->ID, $current_articles, true ) ? 'selected' : '';
			echo '<option value="' . esc_attr( $article->ID ) . '" ' . esc_attr( $selected ) . '>';
			echo esc_html( $article->post_title );
			echo '</option>';
		}

		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Hold Ctrl/Cmd to select multiple articles.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Render brand related articles meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_brand_articles_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
		echo '<input type="hidden" name="mansa_brand_articles_present" value="1" />';

		$current_articles = $this->article_relations->get_articles_by_brand( $post->ID );

		$articles = get_posts(
			array(
				'post_type'      => ArticlePostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		echo '<select name="mansa_brand_related_articles[]" id="mansa_brand_related_articles" multiple style="width: 100%; height: 150px;">';

		foreach ( $articles as $article ) {
			$selected = in_array( $article->ID, $current_articles, true ) ? 'selected' : '';
			echo '<option value="' . esc_attr( $article->ID ) . '" ' . esc_attr( $selected ) . '>';
			echo esc_html( $article->post_title );
			echo '</option>';
		}

		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Hold Ctrl/Cmd to select multiple articles.', 'mansa-core' ) . '</p>';
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
	 * Save related articles for a product.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_product_articles( $post_id ) {
		if ( ! isset( $_POST['mansa_product_articles_present'] ) && ! isset( $_POST['mansa_related_articles'] ) ) {
			return;
		}

		$selected = isset( $_POST['mansa_related_articles'] ) ? array_map( 'absint', (array) $_POST['mansa_related_articles'] ) : array();

		$this->article_relations->set_related_articles( $post_id, $selected );
	}

	/**
	 * Save related articles for a brand.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_brand_articles( $post_id ) {
		if ( ! isset( $_POST['mansa_brand_articles_present'] ) && ! isset( $_POST['mansa_brand_related_articles'] ) && ! isset( $_POST['mansa_related_articles'] ) ) {
			return;
		}

		$selected = array();
		if ( isset( $_POST['mansa_brand_related_articles'] ) ) {
			$selected = array_map( 'absint', (array) $_POST['mansa_brand_related_articles'] );
		} elseif ( isset( $_POST['mansa_related_articles'] ) ) {
			$selected = array_map( 'absint', (array) $_POST['mansa_related_articles'] );
		}

		$this->article_relations->set_related_articles( $post_id, $selected );
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

		// if ( isset( $_POST['mansa_related_articles'] ) ) {
		// 	$article_ids = array_map( 'absint', (array) $_POST['mansa_related_articles'] );
		// 	$this->article_relations->set_related_articles( $post_id, $article_ids );
		// }
	}

	/**
	 * Render brand founder story meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_brand_founder_story_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
		$founder_story = get_post_meta( $post->ID, '_mansa_brand_founder_story', true );

		// Use the Quicktags editor (code view) to avoid TinyMCE initialization issues inside a metabox.
		// Keep the textarea name the same so the value is saved correctly.
		wp_editor( $founder_story, 'mansa_brand_founder_story_editor', array(
			'textarea_name' => 'mansa_brand_founder_story',
			'textarea_rows' => 6,
			'tinymce'       => false,
			'quicktags'     => true,
			'media_buttons' => true,
		) );
	}

	/**
	 * Render brand contact info meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_brand_contact_meta_box( $post ) {
		$website = get_post_meta( $post->ID, '_mansa_brand_website', true );
		$social = get_post_meta( $post->ID, '_mansa_brand_social', true );
		?>
		<p>
			<label for="mansa_brand_website"><?php esc_html_e( 'Website URL', 'mansa-core' ); ?></label>
			<input type="url" id="mansa_brand_website" name="mansa_brand_website" value="<?php echo esc_attr( $website ); ?>" style="width: 100%;" />
		</p>
		<p>
			<label for="mansa_brand_social"><?php esc_html_e( 'Social Links', 'mansa-core' ); ?></label>
			<textarea id="mansa_brand_social" name="mansa_brand_social" rows="4" style="width: 100%;"><?php echo esc_textarea( $social ); ?></textarea>
			<small><?php esc_html_e( 'Enter one URL per line (e.g., Instagram, Twitter, Facebook links)', 'mansa-core' ); ?></small>
		</p>
		<?php
	}

	/**
	 * Save brand meta.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_brand_meta( $post_id ) {
		// Founder story.
		if ( isset( $_POST['mansa_brand_founder_story'] ) ) {
			$founder_story = wp_kses_post( wp_unslash( $_POST['mansa_brand_founder_story'] ) );
			update_post_meta( $post_id, '_mansa_brand_founder_story', $founder_story );
		}

		// Website URL.
		if ( isset( $_POST['mansa_brand_website'] ) ) {
			$website = esc_url_raw( wp_unslash( $_POST['mansa_brand_website'] ) );
			update_post_meta( $post_id, '_mansa_brand_website', $website );
		}

		// Social links.
		if ( isset( $_POST['mansa_brand_social'] ) ) {
			$social = sanitize_textarea_field( wp_unslash( $_POST['mansa_brand_social'] ) );
			update_post_meta( $post_id, '_mansa_brand_social', $social );
		}
	}

	/**
	 * Output shared CSS styles for the drag-and-drop sortable meta boxes.
	 */
	protected function render_meta_box_styles() {
		static $styles_rendered = false;
		if ( $styles_rendered ) {
			return;
		}
		$styles_rendered = true;
		?>
		<style>
			.mansa-bp-wrap { margin-top: 10px; font-family: inherit; }
			.mansa-bp-intro { margin-bottom: 12px; color: #50575e; font-size: 13px; }
			.mansa-bp-container { display: flex; gap: 20px; }
			@media (max-width: 960px) { .mansa-bp-container { flex-direction: column; } }
			.mansa-bp-panel { flex: 1; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; flex-direction: column; min-width: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
			.mansa-bp-header { padding: 10px 14px; background: #f6f7f7; border-bottom: 1px solid #dcdcde; display: flex; align-items: center; justify-content: space-between; font-weight: 600; font-size: 13px; }
			.mansa-bp-toolbar { padding: 10px 14px; border-bottom: 1px solid #f0f0f1; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; background: #fafafa; }
			.mansa-bp-search-input { width: 100%; max-width: 280px; padding: 4px 8px; font-size: 13px; }
			.mansa-bp-list { max-height: 380px; min-height: 120px; overflow-y: auto; margin: 0; padding: 0; list-style: none; }
			.mansa-bp-item { display: flex; align-items: center; padding: 8px 14px; border-bottom: 1px solid #f0f0f1; gap: 10px; cursor: pointer; transition: background 0.15s; }
			.mansa-bp-item:last-child { border-bottom: none; }
			.mansa-bp-item:hover { background: #f6f7f7; }
			.mansa-bp-item.is-selected { background: #f0f6fc; }
			.mansa-bp-thumb { width: 34px; height: 34px; border-radius: 4px; object-fit: cover; background: #eaecf0; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #646970; border: 1px solid #dcdcde; overflow: hidden; }
			.mansa-bp-thumb img { width: 100%; height: 100%; object-fit: cover; }
			.mansa-bp-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
			.mansa-bp-title { font-size: 13px; color: #2c3338; line-height: 1.3; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
			.mansa-bp-sub { font-size: 11px; color: #646970; display: flex; gap: 6px; align-items: center; margin-top: 2px; }
			.mansa-bp-badge { font-size: 10px; padding: 1px 5px; border-radius: 3px; background: #e0e0e0; color: #50575e; text-transform: uppercase; font-weight: 600; }
			.mansa-bp-badge--current { background: #dff0d8; color: #2e662d; }
			.mansa-bp-badge--other { background: #fcf8e3; color: #8a6d3b; }
			/* Selected Sortable List */
			.mansa-bp-sortable { margin: 0; padding: 0; list-style: none; min-height: 160px; max-height: 380px; overflow-y: auto; }
			.mansa-bp-sortable-item { display: flex; align-items: center; padding: 8px 12px; border-bottom: 1px solid #f0f0f1; background: #fff; gap: 10px; cursor: move; user-select: none; }
			.mansa-bp-sortable-item:last-child { border-bottom: none; }
			.mansa-bp-sortable-item:hover { background: #fafafa; }
			.mansa-bp-sortable-item.ui-sortable-helper { box-shadow: 0 4px 14px rgba(0,0,0,0.18); border: 1px solid #2271b1; border-radius: 4px; background: #fff; }
			.mansa-bp-placeholder { height: 50px; background: #f0f6fc; border: 2px dashed #2271b1; margin: 2px 0; border-radius: 4px; }
			.mansa-bp-handle { color: #8c8f94; font-size: 16px; cursor: grab; padding: 0 4px; line-height: 1; }
			.mansa-bp-remove-btn { background: none; border: none; color: #d63638; font-size: 20px; line-height: 1; cursor: pointer; padding: 2px 6px; border-radius: 3px; margin-left: auto; }
			.mansa-bp-remove-btn:hover { background: #fcf0f1; }
			.mansa-bp-empty-box { padding: 36px 20px; text-align: center; color: #646970; font-size: 13px; font-style: italic; }
			.mansa-bp-count-badge { font-weight: normal; font-size: 12px; color: #50575e; }
		</style>
		<?php
	}

	/**
	 * Save manually selected products for a brand.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_brand_products( $post_id ) {
		if ( ! isset( $_POST['mansa_brand_products_present'] ) ) {
			return;
		}

		$selected = isset( $_POST['mansa_brand_products'] )
			? array_values( array_unique( array_filter( array_map( 'absint', (array) $_POST['mansa_brand_products'] ) ) ) )
			: array();

		$this->product_brand_relation->set_selected_products( $post_id, $selected );
	}

	/**
	 * Render brand selectable products meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_brand_products_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
		echo '<input type="hidden" name="mansa_brand_products_present" value="1" />';

		$products = get_posts(
			array(
				'post_type'      => ProductPostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			)
		);

		$has_saved_meta = metadata_exists( 'post', $post->ID, ProductBrandRelation::META_KEY_BRAND_PRODUCTS );
		if ( $has_saved_meta ) {
			$selected_product_ids = $this->product_brand_relation->get_selected_products( $post->ID );
		} else {
			// Initial default: preselect products mapped to this brand via _mansa_brand_id
			$selected_product_ids = $this->product_brand_relation->get_products( $post->ID );
		}

		$products_by_id = array();
		foreach ( $products as $prod ) {
			$products_by_id[ $prod->ID ] = $prod;
		}

		// Preserve selected products order
		$selected_products = array();
		foreach ( $selected_product_ids as $sp_id ) {
			if ( isset( $products_by_id[ $sp_id ] ) ) {
				$selected_products[] = $products_by_id[ $sp_id ];
			}
		}

		$this->render_meta_box_styles();
		?>
		<div class="mansa-bp-wrap">
			<p class="mansa-bp-intro">
				<?php esc_html_e( 'Select which products to display on this single brand page instead of automatically shown products. Drag and drop items in the "Selected Products" list to customize their order.', 'mansa-core' ); ?>
			</p>

			<div class="mansa-bp-container">
				<!-- Available Products Panel -->
				<div class="mansa-bp-panel">
					<div class="mansa-bp-header">
						<span><?php esc_html_e( 'Available Products', 'mansa-core' ); ?></span>
						<span class="mansa-bp-count-badge"><?php printf( esc_html__( 'Total: %d', 'mansa-core' ), count( $products ) ); ?></span>
					</div>
					<div class="mansa-bp-toolbar">
						<input type="search" id="mansa-bp-search" class="mansa-bp-search-input" placeholder="<?php esc_attr_e( 'Search products...', 'mansa-core' ); ?>" autocomplete="off" />
						<button type="button" class="button button-small" id="mansa-bp-select-all"><?php esc_html_e( 'Select All Visible', 'mansa-core' ); ?></button>
						<button type="button" class="button button-small" id="mansa-bp-deselect-all"><?php esc_html_e( 'Clear All', 'mansa-core' ); ?></button>
					</div>
					<ul class="mansa-bp-list" id="mansa-bp-available-list">
						<?php
						if ( empty( $products ) ) :
							?>
							<li class="mansa-bp-empty-box"><?php esc_html_e( 'No products found.', 'mansa-core' ); ?></li>
							<?php
						else :
							foreach ( $products as $prod ) :
								$is_selected   = in_array( $prod->ID, $selected_product_ids, true );
								$prod_brand_id = $this->product_brand_relation->get_brand( $prod->ID );
								$thumb_url     = get_the_post_thumbnail_url( $prod->ID, 'thumbnail' );
								$status        = get_post_status( $prod->ID );
								?>
								<li class="mansa-bp-item <?php echo $is_selected ? 'is-selected' : ''; ?>" data-id="<?php echo esc_attr( $prod->ID ); ?>" data-title="<?php echo esc_attr( strtolower( $prod->post_title ) ); ?>">
									<input type="checkbox" class="mansa-bp-check" id="mansa-bp-chk-<?php echo esc_attr( $prod->ID ); ?>" data-id="<?php echo esc_attr( $prod->ID ); ?>" <?php checked( $is_selected ); ?> />
									<div class="mansa-bp-thumb">
										<?php if ( $thumb_url ) : ?>
											<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
										<?php else : ?>
											<span>&#128230;</span>
										<?php endif; ?>
									</div>
									<div class="mansa-bp-info">
										<label for="mansa-bp-chk-<?php echo esc_attr( $prod->ID ); ?>" class="mansa-bp-title"><?php echo esc_html( $prod->post_title ); ?></label>
										<div class="mansa-bp-sub">
											<?php if ( 'publish' !== $status ) : ?>
												<span class="mansa-bp-badge"><?php echo esc_html( ucfirst( $status ) ); ?></span>
											<?php endif; ?>
											<?php if ( $prod_brand_id === $post->ID ) : ?>
												<span class="mansa-bp-badge mansa-bp-badge--current"><?php esc_html_e( 'Linked to this brand', 'mansa-core' ); ?></span>
											<?php elseif ( $prod_brand_id ) : ?>
												<span class="mansa-bp-badge mansa-bp-badge--other"><?php esc_html_e( 'Linked to other brand', 'mansa-core' ); ?></span>
											<?php endif; ?>
										</div>
									</div>
								</li>
								<?php
							endforeach;
						endif;
						?>
					</ul>
				</div>

				<!-- Selected Products Panel (Sortable) -->
				<div class="mansa-bp-panel">
					<div class="mansa-bp-header">
						<span><?php esc_html_e( 'Selected Products (Order for Single Page)', 'mansa-core' ); ?></span>
						<span id="mansa-bp-selected-count" class="mansa-bp-count-badge"><?php printf( esc_html__( '%d selected', 'mansa-core' ), count( $selected_products ) ); ?></span>
					</div>
					<div class="mansa-bp-toolbar">
						<span class="description"><?php esc_html_e( 'Drag items to reorder the slider display.', 'mansa-core' ); ?></span>
					</div>
					<ul class="mansa-bp-sortable" id="mansa-bp-selected-list">
						<?php
						foreach ( $selected_products as $sprod ) :
							$thumb_url = get_the_post_thumbnail_url( $sprod->ID, 'thumbnail' );
							?>
							<li class="mansa-bp-sortable-item" data-id="<?php echo esc_attr( $sprod->ID ); ?>">
								<span class="mansa-bp-handle" title="<?php esc_attr_e( 'Drag to reorder', 'mansa-core' ); ?>">&#9776;</span>
								<div class="mansa-bp-thumb">
									<?php if ( $thumb_url ) : ?>
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
									<?php else : ?>
										<span>&#128230;</span>
									<?php endif; ?>
								</div>
								<div class="mansa-bp-info">
									<span class="mansa-bp-title"><?php echo esc_html( $sprod->post_title ); ?></span>
								</div>
								<button type="button" class="mansa-bp-remove-btn" title="<?php esc_attr_e( 'Remove product', 'mansa-core' ); ?>" data-id="<?php echo esc_attr( $sprod->ID ); ?>">&times;</button>
								<input type="hidden" name="mansa_brand_products[]" value="<?php echo esc_attr( $sprod->ID ); ?>" />
							</li>
							<?php
						endforeach;
						?>
					</ul>
					<div id="mansa-bp-no-selected" class="mansa-bp-empty-box" style="<?php echo empty( $selected_products ) ? '' : 'display: none;'; ?>">
						<?php esc_html_e( 'No products selected. Check products on the left to add them here.', 'mansa-core' ); ?>
					</div>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var $availableList = $('#mansa-bp-available-list');
			var $selectedList = $('#mansa-bp-selected-list');
			var $noSelectedMsg = $('#mansa-bp-no-selected');
			var $countBadge = $('#mansa-bp-selected-count');
			var $searchInput = $('#mansa-bp-search');

			// Init jQuery UI sortable
			if (typeof $.fn.sortable !== 'undefined') {
				$selectedList.sortable({
					handle: '.mansa-bp-handle',
					placeholder: 'mansa-bp-placeholder',
					axis: 'y',
					cursor: 'move',
					containment: 'parent',
					tolerance: 'pointer'
				});
			}

			function updateCount() {
				var count = $selectedList.children('.mansa-bp-sortable-item').length;
				$countBadge.text(count + ' <?php echo esc_js( __( 'selected', 'mansa-core' ) ); ?>');
				if (count === 0) {
					$noSelectedMsg.show();
				} else {
					$noSelectedMsg.hide();
				}
			}

			function addProduct(id, title, thumbHtml) {
				if ($selectedList.find('.mansa-bp-sortable-item[data-id="' + id + '"]').length > 0) {
					return;
				}

				var itemHtml = '<li class="mansa-bp-sortable-item" data-id="' + id + '">' +
					'<span class="mansa-bp-handle" title="<?php echo esc_js( __( 'Drag to reorder', 'mansa-core' ) ); ?>">&#9776;</span>' +
					'<div class="mansa-bp-thumb">' + thumbHtml + '</div>' +
					'<div class="mansa-bp-info"><span class="mansa-bp-title">' + title + '</span></div>' +
					'<button type="button" class="mansa-bp-remove-btn" title="<?php echo esc_js( __( 'Remove product', 'mansa-core' ) ); ?>" data-id="' + id + '">&times;</button>' +
					'<input type="hidden" name="mansa_brand_products[]" value="' + id + '" />' +
					'</li>';

				$selectedList.append(itemHtml);
				updateCount();
			}

			function removeProduct(id) {
				$selectedList.find('.mansa-bp-sortable-item[data-id="' + id + '"]').remove();
				var $chk = $availableList.find('.mansa-bp-check[data-id="' + id + '"]');
				$chk.prop('checked', false);
				$chk.closest('.mansa-bp-item').removeClass('is-selected');
				updateCount();
			}

			// Checkbox change
			$availableList.on('change', '.mansa-bp-check', function() {
				var $chk = $(this);
				var id = $chk.data('id');
				var $item = $chk.closest('.mansa-bp-item');
				var title = $item.find('.mansa-bp-title').text();
				var thumbHtml = $item.find('.mansa-bp-thumb').html();

				if ($chk.is(':checked')) {
					$item.addClass('is-selected');
					addProduct(id, title, thumbHtml);
				} else {
					$item.removeClass('is-selected');
					removeProduct(id);
				}
			});

			// Clicking on row toggles checkbox if not clicked directly on input or label
			$availableList.on('click', '.mansa-bp-item', function(e) {
				if ($(e.target).is('input, label')) {
					return;
				}
				var $chk = $(this).find('.mansa-bp-check');
				$chk.prop('checked', !$chk.prop('checked')).trigger('change');
			});

			// Remove button click in selected list
			$selectedList.on('click', '.mansa-bp-remove-btn', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				removeProduct(id);
			});

			// Live Search
			$searchInput.on('input', function() {
				var query = $(this).val().toLowerCase().trim();
				$availableList.find('.mansa-bp-item').each(function() {
					var title = $(this).data('title');
					if (!query || title.indexOf(query) !== -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});

			// Select All Visible
			$('#mansa-bp-select-all').on('click', function() {
				$availableList.find('.mansa-bp-item:visible').each(function() {
					var $chk = $(this).find('.mansa-bp-check');
					if (!$chk.is(':checked')) {
						$chk.prop('checked', true).trigger('change');
					}
				});
			});

			// Deselect All
			$('#mansa-bp-deselect-all').on('click', function() {
				$availableList.find('.mansa-bp-check:checked').each(function() {
					$(this).prop('checked', false).trigger('change');
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Render Ad Placements meta box.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_ad_placements_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );

		$ad_1 = get_post_meta( $post->ID, '_mansa_ad_placement_1', true );
		$ad_2 = get_post_meta( $post->ID, '_mansa_ad_placement_2', true );
		?>
		<p>
			<label for="mansa_ad_placement_1"><strong><?php esc_html_e( 'Ad Placement #1', 'mansa-core' ); ?></strong></label>
			<textarea id="mansa_ad_placement_1" name="mansa_ad_placement_1" rows="4" style="width: 100%; font-family: monospace; font-size: 12px;" placeholder="<?php esc_attr_e( 'HTML, image tag, iframe, or shortcode...', 'mansa-core' ); ?>"><?php echo esc_textarea( $ad_1 ); ?></textarea>
		</p>
		<p>
			<label for="mansa_ad_placement_2"><strong><?php esc_html_e( 'Ad Placement #2', 'mansa-core' ); ?></strong></label>
			<textarea id="mansa_ad_placement_2" name="mansa_ad_placement_2" rows="4" style="width: 100%; font-family: monospace; font-size: 12px;" placeholder="<?php esc_attr_e( 'HTML, image tag, iframe, or shortcode...', 'mansa-core' ); ?>"><?php echo esc_textarea( $ad_2 ); ?></textarea>
		</p>
		<p class="description">
			<?php esc_html_e( 'Leave empty to hide the ad spot completely on the front-end.', 'mansa-core' ); ?>
		</p>
		<?php
	}

	/**
	 * Save Ad Placements meta.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_ad_placements( $post_id ) {
		if ( isset( $_POST['mansa_ad_placement_1'] ) ) {
			// Admins can insert arbitrary HTML/embed code; non-admins filtered with kses.
			$ad_1 = current_user_can( 'unfiltered_html' )
				? wp_unslash( $_POST['mansa_ad_placement_1'] )
				: wp_kses_post( wp_unslash( $_POST['mansa_ad_placement_1'] ) );
			update_post_meta( $post_id, '_mansa_ad_placement_1', $ad_1 );
		}

		if ( isset( $_POST['mansa_ad_placement_2'] ) ) {
			$ad_2 = current_user_can( 'unfiltered_html' )
				? wp_unslash( $_POST['mansa_ad_placement_2'] )
				: wp_kses_post( wp_unslash( $_POST['mansa_ad_placement_2'] ) );
			update_post_meta( $post_id, '_mansa_ad_placement_2', $ad_2 );
		}
	}

	/**
	 * Save manually selected related products for a product.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_product_related_products( $post_id ) {
		if ( ! isset( $_POST['mansa_product_related_products_present'] ) ) {
			return;
		}

		$selected = isset( $_POST['mansa_product_related_products'] )
			? array_values( array_unique( array_filter( array_map( 'absint', (array) $_POST['mansa_product_related_products'] ) ) ) )
			: array();

		// Remove self-reference if present.
		$selected = array_values( array_diff( $selected, array( $post_id ) ) );

		update_post_meta( $post_id, '_mansa_related_products', $selected );
	}

	/**
	 * Render product related products meta box with search and drag-and-drop sortable interface.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_product_related_products_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
		echo '<input type="hidden" name="mansa_product_related_products_present" value="1" />';

		$products = get_posts(
			array(
				'post_type'      => ProductPostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'exclude'        => array( $post->ID ),
			)
		);

		$raw_selected = get_post_meta( $post->ID, '_mansa_related_products', true );
		if ( is_string( $raw_selected ) && ! empty( $raw_selected ) ) {
			$selected_product_ids = array_values( array_filter( array_map( 'absint', explode( ',', $raw_selected ) ) ) );
		} elseif ( is_array( $raw_selected ) ) {
			$selected_product_ids = array_values( array_filter( array_map( 'absint', $raw_selected ) ) );
		} else {
			$selected_product_ids = array();
		}

		$products_by_id = array();
		foreach ( $products as $prod ) {
			$products_by_id[ $prod->ID ] = $prod;
		}

		// Preserve selected products order
		$selected_products = array();
		foreach ( $selected_product_ids as $sp_id ) {
			if ( isset( $products_by_id[ $sp_id ] ) ) {
				$selected_products[] = $products_by_id[ $sp_id ];
			}
		}

		$this->render_meta_box_styles();
		?>
		<div class="mansa-bp-wrap">
			<p class="mansa-bp-intro">
				<?php esc_html_e( 'Select which related products to display on this single product page. Drag and drop items in the "Selected Related Products" list to customize their order. If nothing is selected, the Related Products section will be hidden on the front-end.', 'mansa-core' ); ?>
			</p>

			<div class="mansa-bp-container">
				<!-- Available Products Panel -->
				<div class="mansa-bp-panel">
					<div class="mansa-bp-header">
						<span><?php esc_html_e( 'Available Products', 'mansa-core' ); ?></span>
						<span class="mansa-bp-count-badge"><?php printf( esc_html__( 'Total: %d', 'mansa-core' ), count( $products ) ); ?></span>
					</div>
					<div class="mansa-bp-toolbar">
						<input type="search" id="mansa-prp-search" class="mansa-bp-search-input" placeholder="<?php esc_attr_e( 'Search products...', 'mansa-core' ); ?>" autocomplete="off" />
						<button type="button" class="button button-small" id="mansa-prp-select-all"><?php esc_html_e( 'Select All Visible', 'mansa-core' ); ?></button>
						<button type="button" class="button button-small" id="mansa-prp-deselect-all"><?php esc_html_e( 'Clear All', 'mansa-core' ); ?></button>
					</div>
					<ul class="mansa-bp-list" id="mansa-prp-available-list">
						<?php
						if ( empty( $products ) ) :
							?>
							<li class="mansa-bp-empty-box"><?php esc_html_e( 'No other products found.', 'mansa-core' ); ?></li>
							<?php
						else :
							foreach ( $products as $prod ) :
								$is_selected = in_array( $prod->ID, $selected_product_ids, true );
								$thumb_url   = get_the_post_thumbnail_url( $prod->ID, 'thumbnail' );
								$status      = get_post_status( $prod->ID );
								?>
								<li class="mansa-bp-item <?php echo $is_selected ? 'is-selected' : ''; ?>" data-id="<?php echo esc_attr( $prod->ID ); ?>" data-title="<?php echo esc_attr( strtolower( $prod->post_title ) ); ?>">
									<input type="checkbox" class="mansa-prp-check" id="mansa-prp-chk-<?php echo esc_attr( $prod->ID ); ?>" data-id="<?php echo esc_attr( $prod->ID ); ?>" <?php checked( $is_selected ); ?> />
									<div class="mansa-bp-thumb">
										<?php if ( $thumb_url ) : ?>
											<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
										<?php else : ?>
											<span>&#128230;</span>
										<?php endif; ?>
									</div>
									<div class="mansa-bp-info">
										<label for="mansa-prp-chk-<?php echo esc_attr( $prod->ID ); ?>" class="mansa-bp-title"><?php echo esc_html( $prod->post_title ); ?></label>
										<div class="mansa-bp-sub">
											<?php if ( 'publish' !== $status ) : ?>
												<span class="mansa-bp-badge"><?php echo esc_html( ucfirst( $status ) ); ?></span>
											<?php endif; ?>
										</div>
									</div>
								</li>
								<?php
							endforeach;
						endif;
						?>
					</ul>
				</div>

				<!-- Selected Products Panel (Sortable) -->
				<div class="mansa-bp-panel">
					<div class="mansa-bp-header">
						<span><?php esc_html_e( 'Selected Related Products (Order for Single Page)', 'mansa-core' ); ?></span>
						<span id="mansa-prp-selected-count" class="mansa-bp-count-badge"><?php printf( esc_html__( '%d selected', 'mansa-core' ), count( $selected_products ) ); ?></span>
					</div>
					<div class="mansa-bp-toolbar">
						<span class="description"><?php esc_html_e( 'Drag items to reorder the slider display.', 'mansa-core' ); ?></span>
					</div>
					<ul class="mansa-bp-sortable" id="mansa-prp-selected-list">
						<?php
						foreach ( $selected_products as $sprod ) :
							$thumb_url = get_the_post_thumbnail_url( $sprod->ID, 'thumbnail' );
							?>
							<li class="mansa-bp-sortable-item" data-id="<?php echo esc_attr( $sprod->ID ); ?>">
								<span class="mansa-bp-handle" title="<?php esc_attr_e( 'Drag to reorder', 'mansa-core' ); ?>">&#9776;</span>
								<div class="mansa-bp-thumb">
									<?php if ( $thumb_url ) : ?>
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
									<?php else : ?>
										<span>&#128230;</span>
									<?php endif; ?>
								</div>
								<div class="mansa-bp-info">
									<span class="mansa-bp-title"><?php echo esc_html( $sprod->post_title ); ?></span>
								</div>
								<button type="button" class="mansa-bp-remove-btn" title="<?php esc_attr_e( 'Remove product', 'mansa-core' ); ?>" data-id="<?php echo esc_attr( $sprod->ID ); ?>">&times;</button>
								<input type="hidden" name="mansa_product_related_products[]" value="<?php echo esc_attr( $sprod->ID ); ?>" />
							</li>
							<?php
						endforeach;
						?>
					</ul>
					<div id="mansa-prp-no-selected" class="mansa-bp-empty-box" style="<?php echo empty( $selected_products ) ? '' : 'display: none;'; ?>">
						<?php esc_html_e( 'No related products selected. Check products on the left to add them here.', 'mansa-core' ); ?>
					</div>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var $availableList = $('#mansa-prp-available-list');
			var $selectedList = $('#mansa-prp-selected-list');
			var $noSelectedMsg = $('#mansa-prp-no-selected');
			var $countBadge = $('#mansa-prp-selected-count');
			var $searchInput = $('#mansa-prp-search');

			// Init jQuery UI sortable
			if (typeof $.fn.sortable !== 'undefined') {
				$selectedList.sortable({
					handle: '.mansa-bp-handle',
					placeholder: 'mansa-bp-placeholder',
					axis: 'y',
					cursor: 'move',
					containment: 'parent',
					tolerance: 'pointer'
				});
			}

			function updateCount() {
				var count = $selectedList.children('.mansa-bp-sortable-item').length;
				$countBadge.text(count + ' <?php echo esc_js( __( 'selected', 'mansa-core' ) ); ?>');
				if (count === 0) {
					$noSelectedMsg.show();
				} else {
					$noSelectedMsg.hide();
				}
			}

			function addProduct(id, title, thumbHtml) {
				if ($selectedList.find('.mansa-bp-sortable-item[data-id="' + id + '"]').length > 0) {
					return;
				}

				var itemHtml = '<li class="mansa-bp-sortable-item" data-id="' + id + '">' +
					'<span class="mansa-bp-handle" title="<?php echo esc_js( __( 'Drag to reorder', 'mansa-core' ) ); ?>">&#9776;</span>' +
					'<div class="mansa-bp-thumb">' + thumbHtml + '</div>' +
					'<div class="mansa-bp-info"><span class="mansa-bp-title">' + title + '</span></div>' +
					'<button type="button" class="mansa-bp-remove-btn" title="<?php echo esc_js( __( 'Remove product', 'mansa-core' ) ); ?>" data-id="' + id + '">&times;</button>' +
					'<input type="hidden" name="mansa_product_related_products[]" value="' + id + '" />' +
					'</li>';

				$selectedList.append(itemHtml);
				updateCount();
			}

			function removeProduct(id) {
				$selectedList.find('.mansa-bp-sortable-item[data-id="' + id + '"]').remove();
				var $chk = $availableList.find('.mansa-prp-check[data-id="' + id + '"]');
				$chk.prop('checked', false);
				$chk.closest('.mansa-bp-item').removeClass('is-selected');
				updateCount();
			}

			// Checkbox change
			$availableList.on('change', '.mansa-prp-check', function() {
				var $chk = $(this);
				var id = $chk.data('id');
				var $item = $chk.closest('.mansa-bp-item');
				var title = $item.find('.mansa-bp-title').text();
				var thumbHtml = $item.find('.mansa-bp-thumb').html();

				if ($chk.is(':checked')) {
					$item.addClass('is-selected');
					addProduct(id, title, thumbHtml);
				} else {
					$item.removeClass('is-selected');
					removeProduct(id);
				}
			});

			// Clicking on row toggles checkbox if not clicked directly on input or label
			$availableList.on('click', '.mansa-bp-item', function(e) {
				if ($(e.target).is('input, label')) {
					return;
				}
				var $chk = $(this).find('.mansa-prp-check');
				$chk.prop('checked', !$chk.prop('checked')).trigger('change');
			});

			// Remove button click in selected list
			$selectedList.on('click', '.mansa-bp-remove-btn', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				removeProduct(id);
			});

			// Live Search
			$searchInput.on('input', function() {
				var query = $(this).val().toLowerCase().trim();
				$availableList.find('.mansa-bp-item').each(function() {
					var title = $(this).data('title');
					if (!query || title.indexOf(query) !== -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});

			// Select All Visible
			$('#mansa-prp-select-all').on('click', function() {
				$availableList.find('.mansa-bp-item:visible').each(function() {
					var $chk = $(this).find('.mansa-prp-check');
					if (!$chk.is(':checked')) {
						$chk.prop('checked', true).trigger('change');
					}
				});
			});

			// Deselect All
			$('#mansa-prp-deselect-all').on('click', function() {
				$availableList.find('.mansa-prp-check:checked').each(function() {
					$(this).prop('checked', false).trigger('change');
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Save manually selected related articles for an article.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	protected function save_article_related_articles( $post_id ) {
		if ( ! isset( $_POST['mansa_article_related_articles_present'] ) ) {
			return;
		}

		$selected = isset( $_POST['mansa_article_related_articles'] )
			? array_values( array_unique( array_filter( array_map( 'absint', (array) $_POST['mansa_article_related_articles'] ) ) ) )
			: array();

		// Remove self-reference if present.
		$selected = array_values( array_diff( $selected, array( $post_id ) ) );

		update_post_meta( $post_id, '_mansa_related_articles', $selected );
	}

	/**
	 * Render article related articles meta box with search and drag-and-drop sortable interface.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_article_related_articles_meta_box( $post ) {
		wp_nonce_field( 'mansa_save_meta', 'mansa_meta_nonce' );
		echo '<input type="hidden" name="mansa_article_related_articles_present" value="1" />';

		$articles = get_posts(
			array(
				'post_type'      => ArticlePostType::POST_TYPE,
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'exclude'        => array( $post->ID ),
			)
		);

		$raw_selected = get_post_meta( $post->ID, '_mansa_related_articles', true );
		if ( is_string( $raw_selected ) && ! empty( $raw_selected ) ) {
			$selected_article_ids = array_values( array_filter( array_map( 'absint', explode( ',', $raw_selected ) ) ) );
		} elseif ( is_array( $raw_selected ) ) {
			$selected_article_ids = array_values( array_filter( array_map( 'absint', $raw_selected ) ) );
		} else {
			$selected_article_ids = array();
		}

		$articles_by_id = array();
		foreach ( $articles as $art ) {
			$articles_by_id[ $art->ID ] = $art;
		}

		// Preserve selected articles order
		$selected_articles = array();
		foreach ( $selected_article_ids as $sa_id ) {
			if ( isset( $articles_by_id[ $sa_id ] ) ) {
				$selected_articles[] = $articles_by_id[ $sa_id ];
			}
		}

		$this->render_meta_box_styles();
		?>
		<div class="mansa-bp-wrap">
			<p class="mansa-bp-intro">
				<?php esc_html_e( 'Select which related articles to display on this single article page. Drag and drop items in the "Selected Related Articles" list to customize their order. If nothing is selected, the Related Articles section will be hidden on the front-end.', 'mansa-core' ); ?>
			</p>

			<div class="mansa-bp-container">
				<!-- Available Articles Panel -->
				<div class="mansa-bp-panel">
					<div class="mansa-bp-header">
						<span><?php esc_html_e( 'Available Articles', 'mansa-core' ); ?></span>
						<span class="mansa-bp-count-badge"><?php printf( esc_html__( 'Total: %d', 'mansa-core' ), count( $articles ) ); ?></span>
					</div>
					<div class="mansa-bp-toolbar">
						<input type="search" id="mansa-ara-search" class="mansa-bp-search-input" placeholder="<?php esc_attr_e( 'Search articles...', 'mansa-core' ); ?>" autocomplete="off" />
						<button type="button" class="button button-small" id="mansa-ara-select-all"><?php esc_html_e( 'Select All Visible', 'mansa-core' ); ?></button>
						<button type="button" class="button button-small" id="mansa-ara-deselect-all"><?php esc_html_e( 'Clear All', 'mansa-core' ); ?></button>
					</div>
					<ul class="mansa-bp-list" id="mansa-ara-available-list">
						<?php
						if ( empty( $articles ) ) :
							?>
							<li class="mansa-bp-empty-box"><?php esc_html_e( 'No other articles found.', 'mansa-core' ); ?></li>
							<?php
						else :
							foreach ( $articles as $art ) :
								$is_selected = in_array( $art->ID, $selected_article_ids, true );
								$thumb_url   = get_the_post_thumbnail_url( $art->ID, 'thumbnail' );
								$status      = get_post_status( $art->ID );
								?>
								<li class="mansa-bp-item <?php echo $is_selected ? 'is-selected' : ''; ?>" data-id="<?php echo esc_attr( $art->ID ); ?>" data-title="<?php echo esc_attr( strtolower( $art->post_title ) ); ?>">
									<input type="checkbox" class="mansa-ara-check" id="mansa-ara-chk-<?php echo esc_attr( $art->ID ); ?>" data-id="<?php echo esc_attr( $art->ID ); ?>" <?php checked( $is_selected ); ?> />
									<div class="mansa-bp-thumb">
										<?php if ( $thumb_url ) : ?>
											<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
										<?php else : ?>
											<span>&#128240;</span>
										<?php endif; ?>
									</div>
									<div class="mansa-bp-info">
										<label for="mansa-ara-chk-<?php echo esc_attr( $art->ID ); ?>" class="mansa-bp-title"><?php echo esc_html( $art->post_title ); ?></label>
										<div class="mansa-bp-sub">
											<?php if ( 'publish' !== $status ) : ?>
												<span class="mansa-bp-badge"><?php echo esc_html( ucfirst( $status ) ); ?></span>
											<?php endif; ?>
										</div>
									</div>
								</li>
								<?php
							endforeach;
						endif;
						?>
					</ul>
				</div>

				<!-- Selected Articles Panel (Sortable) -->
				<div class="mansa-bp-panel">
					<div class="mansa-bp-header">
						<span><?php esc_html_e( 'Selected Related Articles (Order for Single Page)', 'mansa-core' ); ?></span>
						<span id="mansa-ara-selected-count" class="mansa-bp-count-badge"><?php printf( esc_html__( '%d selected', 'mansa-core' ), count( $selected_articles ) ); ?></span>
					</div>
					<div class="mansa-bp-toolbar">
						<span class="description"><?php esc_html_e( 'Drag items to reorder the slider display.', 'mansa-core' ); ?></span>
					</div>
					<ul class="mansa-bp-sortable" id="mansa-ara-selected-list">
						<?php
						foreach ( $selected_articles as $sart ) :
							$thumb_url = get_the_post_thumbnail_url( $sart->ID, 'thumbnail' );
							?>
							<li class="mansa-bp-sortable-item" data-id="<?php echo esc_attr( $sart->ID ); ?>">
								<span class="mansa-bp-handle" title="<?php esc_attr_e( 'Drag to reorder', 'mansa-core' ); ?>">&#9776;</span>
								<div class="mansa-bp-thumb">
									<?php if ( $thumb_url ) : ?>
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
									<?php else : ?>
										<span>&#128240;</span>
									<?php endif; ?>
								</div>
								<div class="mansa-bp-info">
									<span class="mansa-bp-title"><?php echo esc_html( $sart->post_title ); ?></span>
								</div>
								<button type="button" class="mansa-bp-remove-btn" title="<?php esc_attr_e( 'Remove article', 'mansa-core' ); ?>" data-id="<?php echo esc_attr( $sart->ID ); ?>">&times;</button>
								<input type="hidden" name="mansa_article_related_articles[]" value="<?php echo esc_attr( $sart->ID ); ?>" />
							</li>
							<?php
						endforeach;
						?>
					</ul>
					<div id="mansa-ara-no-selected" class="mansa-bp-empty-box" style="<?php echo empty( $selected_articles ) ? '' : 'display: none;'; ?>">
						<?php esc_html_e( 'No related articles selected. Check articles on the left to add them here.', 'mansa-core' ); ?>
					</div>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var $availableList = $('#mansa-ara-available-list');
			var $selectedList = $('#mansa-ara-selected-list');
			var $noSelectedMsg = $('#mansa-ara-no-selected');
			var $countBadge = $('#mansa-ara-selected-count');
			var $searchInput = $('#mansa-ara-search');

			// Init jQuery UI sortable
			if (typeof $.fn.sortable !== 'undefined') {
				$selectedList.sortable({
					handle: '.mansa-bp-handle',
					placeholder: 'mansa-bp-placeholder',
					axis: 'y',
					cursor: 'move',
					containment: 'parent',
					tolerance: 'pointer'
				});
			}

			function updateCount() {
				var count = $selectedList.children('.mansa-bp-sortable-item').length;
				$countBadge.text(count + ' <?php echo esc_js( __( 'selected', 'mansa-core' ) ); ?>');
				if (count === 0) {
					$noSelectedMsg.show();
				} else {
					$noSelectedMsg.hide();
				}
			}

			function addArticle(id, title, thumbHtml) {
				if ($selectedList.find('.mansa-bp-sortable-item[data-id="' + id + '"]').length > 0) {
					return;
				}

				var itemHtml = '<li class="mansa-bp-sortable-item" data-id="' + id + '">' +
					'<span class="mansa-bp-handle" title="<?php echo esc_js( __( 'Drag to reorder', 'mansa-core' ) ); ?>">&#9776;</span>' +
					'<div class="mansa-bp-thumb">' + thumbHtml + '</div>' +
					'<div class="mansa-bp-info"><span class="mansa-bp-title">' + title + '</span></div>' +
					'<button type="button" class="mansa-bp-remove-btn" title="<?php echo esc_js( __( 'Remove article', 'mansa-core' ) ); ?>" data-id="' + id + '">&times;</button>' +
					'<input type="hidden" name="mansa_article_related_articles[]" value="' + id + '" />' +
					'</li>';

				$selectedList.append(itemHtml);
				updateCount();
			}

			function removeArticle(id) {
				$selectedList.find('.mansa-bp-sortable-item[data-id="' + id + '"]').remove();
				var $chk = $availableList.find('.mansa-ara-check[data-id="' + id + '"]');
				$chk.prop('checked', false);
				$chk.closest('.mansa-bp-item').removeClass('is-selected');
				updateCount();
			}

			// Checkbox change
			$availableList.on('change', '.mansa-ara-check', function() {
				var $chk = $(this);
				var id = $chk.data('id');
				var $item = $chk.closest('.mansa-bp-item');
				var title = $item.find('.mansa-bp-title').text();
				var thumbHtml = $item.find('.mansa-bp-thumb').html();

				if ($chk.is(':checked')) {
					$item.addClass('is-selected');
					addArticle(id, title, thumbHtml);
				} else {
					$item.removeClass('is-selected');
					removeArticle(id);
				}
			});

			// Clicking on row toggles checkbox if not clicked directly on input or label
			$availableList.on('click', '.mansa-bp-item', function(e) {
				if ($(e.target).is('input, label')) {
					return;
				}
				var $chk = $(this).find('.mansa-ara-check');
				$chk.prop('checked', !$chk.prop('checked')).trigger('change');
			});

			// Remove button click in selected list
			$selectedList.on('click', '.mansa-bp-remove-btn', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				removeArticle(id);
			});

			// Live Search
			$searchInput.on('input', function() {
				var query = $(this).val().toLowerCase().trim();
				$availableList.find('.mansa-bp-item').each(function() {
					var title = $(this).data('title');
					if (!query || title.indexOf(query) !== -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});

			// Select All Visible
			$('#mansa-ara-select-all').on('click', function() {
				$availableList.find('.mansa-bp-item:visible').each(function() {
					var $chk = $(this).find('.mansa-ara-check');
					if (!$chk.is(':checked')) {
						$chk.prop('checked', true).trigger('change');
					}
				});
			});

			// Deselect All
			$('#mansa-ara-deselect-all').on('click', function() {
				$availableList.find('.mansa-ara-check:checked').each(function() {
					$(this).prop('checked', false).trigger('change');
				});
			});
		});
		</script>
		<?php
	}
}