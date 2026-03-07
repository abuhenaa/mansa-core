/**
 * Mansa Core - Plugin Structure Overview
 *
 * Directory Structure:
 * mansa-core/
 * ├── mansa-core.php          # Main plugin file with bootstrap
 * ├── composer.json           # PSR-4 autoloading configuration
 * ├── README.md              # Plugin documentation
 * ├── PLUGIN-OVERVIEW.md     # Detailed architecture overview
 * └── src/
 *     ├── Core/
 *     │   ├── Plugin.php      # Main plugin class with container management
 *     │   ├── Container.php   # Dependency injection container
 *     │   ├── ServiceProvider.php # Abstract service provider
 *     │   ├── QueryHelpers.php # Helper methods for common queries
 *     │   └── UsageExamples.php # Code examples and usage patterns
 *     ├── Providers/
 *     │   ├── PostTypeServiceProvider.php
 *     │   ├── TaxonomyServiceProvider.php
 *     │   ├── RelationshipServiceProvider.php
 *     │   └── AdminServiceProvider.php
 *     ├── PostTypes/
 *     │   ├── ProductPostType.php
 *     │   ├── BrandPostType.php
 *     │   └── ArticlePostType.php
 *     ├── Taxonomies/
 *     │   ├── ProductCategoryTaxonomy.php
 *     │   ├── OriginTaxonomy.php
 *     │   └── ArticleTopicTaxonomy.php
 *     ├── Relationships/
 *     │   ├── ProductBrandRelation.php
 *     │   └── ArticleRelations.php
 *     ├── Admin/
 *     │   └── MetaBoxes.php
 *     └── Helpers/
 *         └── Sanitizer.php
 *
 * Key Features Implemented:
 *
 * 1. Custom Post Types:
 *    - Products (slug: products)
 *    - Brands (slug: brands)
 *    - Articles (slug: articles)
 *    All with REST API support, archives, and proper WordPress integration.
 *
 * 2. Taxonomies:
 *    - Product Categories (hierarchical, attached to products & brands)
 *    - Origin (hierarchical, attached to products)
 *    - Article Topics (hierarchical, attached to articles)
 *    All with REST API support and proper rewrite rules.
 *
 * 3. Relationships:
 *    - Product → Brand (one-to-one via meta field _mansa_brand_id)
 *    - Article → Related Products (many-to-many via meta field _mansa_related_products)
 *    - Article → Related Brands (many-to-many via meta field _mansa_related_brands)
 *
 * 4. Admin Interface:
 *    - Product editor: Brand selector dropdown
 *    - Article editor: Related products multi-select, related brands multi-select
 *    All with proper nonce verification, capability checks, and data sanitization.
 *
 * 5. Query Helpers:
 *    - Efficient methods for querying relationships
 *    - Brand → Products queries
 *    - Article → Related content queries
 *    - All using optimized WP_Query with meta_query
 *
 * 6. Security:
 *    - All user input sanitized (sanitize_text_field, absint, etc.)
 *    - All output escaped (esc_html, esc_attr, esc_url)
 *    - Nonce verification for all form submissions
 *    - Capability checks for all operations
 *    - Never trusts user input
 *
 * 7. Architecture:
 *    - Dependency Injection Container (minimal, WordPress-friendly)
 *    - Service Provider pattern for modular functionality
 *    - PSR-4 autoloading via Composer
 *    - Loose coupling between components
 *    - Easy to extend and maintain
 *
 * Usage Examples:
 *
 * // Get products for a brand
 * $query = \Mansa\Core\QueryHelpers::get_products_by_brand( $brand_id );
 * $products = $query->posts;
 *
 * // Get related content for an article
 * $related = \Mansa\Core\QueryHelpers::get_article_related_content( $article_id );
 *
 * // Create product with brand
 * $product_id = \Mansa\Core\UsageExamples::create_product_with_brand(
 *     'Product Title',
 *     'Product content...',
 *     $brand_id
 * );
 *
 * // Get container instance
 * $container = \Mansa\Core\Plugin::get_container();
 * $relation = $container->get( \Mansa\Core\Relationships\ProductBrandRelation::class );
 *
 * WordPress Standards Compliance:
 * - Follows WordPress PHP Coding Standards
 * - Uses snake_case for functions
 * - Proper docblocks and comments
 * - No global side effects
 * - All hooks prefixed with mansa_
 * - All meta keys prefixed with _mansa_
 *
 * Performance Considerations:
 * - Minimal database queries
 * - Efficient meta queries
 * - Lazy loading where appropriate
 * - No unnecessary dependencies
 * - Proper indexing on meta keys recommended
 *
 * Activation/Deactivation:
 * - Flushes rewrite rules on activation/deactivation
 * - Registers all post types and taxonomies properly
 * - No data loss on deactivation (relationships stored in post meta)
 */