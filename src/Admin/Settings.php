<?php
/**
 * Mansa Settings Panel.
 *
 * @package Mansa\Admin
 */

namespace Mansa\Admin;

/**
 * Settings handler for Mansa MVP admin panel.
 */
class Settings {

	/**
	 * Settings option key.
	 */
	const OPTION_KEY = 'mansa_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register admin menu and submenu.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Mansa MVP', 'mansa-core' ),
			__( 'Mansa MVP', 'mansa-core' ),
			'manage_options',
			'mansa-mvp',
			array( $this, 'render_settings_page' ),
			'dashicons-store',
			30
		);

		add_submenu_page(
			'mansa-mvp',
			__( 'Settings', 'mansa-core' ),
			__( 'Settings', 'mansa-core' ),
			'manage_options',
			'mansa-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings, sections, and fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'mansa_settings_group', self::OPTION_KEY );

		// Product sections.
		add_settings_section(
			'mansa_product_sections',
			__( 'Product Page Sections', 'mansa-core' ),
			array( $this, 'section_callback_product' ),
			'mansa_settings_group'
		);

		add_settings_field(
			'mansa_product_overview_title',
			__( 'Product Overview', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_overview_title',
				'label_for'   => 'mansa_product_overview_title',
				'description' => __( 'Title for the product overview section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_product_about_title',
			__( 'About This Product', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_about_title',
				'label_for'   => 'mansa_product_about_title',
				'description' => __( 'Title for the about product section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_product_benefits_title',
			__( 'Why People Buy This', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_benefits_title',
				'label_for'   => 'mansa_product_benefits_title',
				'description' => __( 'Title for the benefits section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_product_where_to_buy_title',
			__( 'Where To Buy', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_where_to_buy_title',
				'label_for'   => 'mansa_product_where_to_buy_title',
				'description' => __( 'Title for the where to buy section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_product_supplier_title',
			__( 'Supplier', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_supplier_title',
				'label_for'   => 'mansa_product_supplier_title',
				'description' => __( 'Title for the supplier section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_product_similar_title',
			__( 'Similar Products', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_similar_title',
				'label_for'   => 'mansa_product_similar_title',
				'description' => __( 'Title for the similar products section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_product_related_articles_title',
			__( 'Related Articles', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_product_sections',
			array(
				'name'        => 'mansa_product_related_articles_title',
				'label_for'   => 'mansa_product_related_articles_title',
				'description' => __( 'Title for the related articles section', 'mansa-core' ),
			)
		);

		// Brand sections.
		add_settings_section(
			'mansa_brand_sections',
			__( 'Brand Page Sections', 'mansa-core' ),
			array( $this, 'section_callback_brand' ),
			'mansa_settings_group'
		);

		add_settings_field(
			'mansa_brand_founder_story_title',
			__( 'Founder Story', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_brand_sections',
			array(
				'name'        => 'mansa_brand_founder_story_title',
				'label_for'   => 'mansa_brand_founder_story_title',
				'description' => __( 'Title for the founder story section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_brand_about_title',
			__( 'About The Brand', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_brand_sections',
			array(
				'name'        => 'mansa_brand_about_title',
				'label_for'   => 'mansa_brand_about_title',
				'description' => __( 'Title for the about brand section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_brand_contact_title',
			__( 'Connect With Us', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_brand_sections',
			array(
				'name'        => 'mansa_brand_contact_title',
				'label_for'   => 'mansa_brand_contact_title',
				'description' => __( 'Title for the connect with us section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_brand_products_title',
			__( 'Products From This Brand', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_brand_sections',
			array(
				'name'        => 'mansa_brand_products_title',
				'label_for'   => 'mansa_brand_products_title',
				'description' => __( 'Title for the products section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_brand_related_articles_title',
			__( 'Related Articles', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_brand_sections',
			array(
				'name'        => 'mansa_brand_related_articles_title',
				'label_for'   => 'mansa_brand_related_articles_title',
				'description' => __( 'Title for the related articles section', 'mansa-core' ),
			)
		);

		// Article sections.
		add_settings_section(
			'mansa_article_sections',
			__( 'Article Page Sections', 'mansa-core' ),
			array( $this, 'section_callback_article' ),
			'mansa_settings_group'
		);

		add_settings_field(
			'mansa_article_related_products_title',
			__( 'Related Products', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_article_sections',
			array(
				'name'        => 'mansa_article_related_products_title',
				'label_for'   => 'mansa_article_related_products_title',
				'description' => __( 'Title for the related products section', 'mansa-core' ),
			)
		);

		add_settings_field(
			'mansa_article_related_brands_title',
			__( 'Related Brands', 'mansa-core' ),
			array( $this, 'text_field_callback' ),
			'mansa_settings_group',
			'mansa_article_sections',
			array(
				'name'        => 'mansa_article_related_brands_title',
				'label_for'   => 'mansa_article_related_brands_title',
				'description' => __( 'Title for the related brands section', 'mansa-core' ),
			)
		);
	}

	/**
	 * Section callback for product sections.
	 *
	 * @return void
	 */
	public function section_callback_product() {
		echo '<p>' . esc_html__( 'Manage section titles displayed on product pages.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Section callback for brand sections.
	 *
	 * @return void
	 */
	public function section_callback_brand() {
		echo '<p>' . esc_html__( 'Manage section titles displayed on brand pages.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Section callback for article sections.
	 *
	 * @return void
	 */
	public function section_callback_article() {
		echo '<p>' . esc_html__( 'Manage section titles displayed on article pages.', 'mansa-core' ) . '</p>';
	}

	/**
	 * Text field callback.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function text_field_callback( $args ) {
		$settings = get_option( self::OPTION_KEY, array() );
		$value    = isset( $settings[ $args['name'] ] ) ? $settings[ $args['name'] ] : '';

		?>
		<input
			type="text"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="<?php echo esc_attr( self::OPTION_KEY . '[' . $args['name'] . ']' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			style="width: 100%; max-width: 400px;"
		/>
		<?php if ( isset( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif;
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Mansa MVP Settings', 'mansa-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'mansa_settings_group' );
				do_settings_sections( 'mansa_settings_group' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get a setting value.
	 *
	 * @param string $key Setting key.
	 * @param string $default Default value.
	 * @return string|mixed Setting value or default.
	 */
	public static function get_setting( $key, $default = '' ) {
		$settings = get_option( self::OPTION_KEY, array() );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Get all settings.
	 *
	 * @return array All settings.
	 */
	public static function get_all_settings() {
		return get_option( self::OPTION_KEY, array() );
	}
}
