# Mansa Core

A WordPress plugin that provides the core CMS architecture for the Mansa Market content discovery platform.

## Features

- **Custom Post Types**: Products, Brands, Articles with REST API support
- **Taxonomies**: Product Categories, Origin, Article Topics
- **Relationships**: Product-Brand, Article-Products, Article-Brands
- **Admin UI**: Meta boxes for managing relationships
- **Query Helpers**: Efficient database queries for relationships
- **Security**: WordPress security best practices
- **Performance**: Optimized queries and proper indexing

## Installation

1. Upload the `mansa-core` folder to `/wp-content/plugins/`
2. Run `composer install` in the plugin directory
3. Activate the plugin through the WordPress admin

## Architecture

### Dependency Injection Container

The plugin uses a minimal DI container for service management:

```php
$container = \Mansa\Core\Plugin::get_container();
$relation = $container->get(\Mansa\Core\Relationships\ProductBrandRelation::class);
```

### Service Provider Pattern

Modular functionality through service providers:
- `PostTypeServiceProvider` - Registers custom post types
- `TaxonomyServiceProvider` - Registers taxonomies
- `RelationshipServiceProvider` - Handles relationships
- `AdminServiceProvider` - Admin interface components

## Usage Examples

### Creating Content with Relationships

```php
// Create a product with brand
$product_id = \Mansa\Core\UsageExamples::create_product_with_brand(
    'Premium Coffee Beans',
    'High-quality arabica beans...',
    $brand_id
);

// Create an article with related content
$article_id = \Mansa\Core\UsageExamples::create_article_with_relations(
    'Best Coffee Brands 2024',
    'Article content...',
    [$product_id],
    [$brand_id]
);
```

### Querying Relationships

```php
// Get products by brand
$query = \Mansa\Core\QueryHelpers::get_products_by_brand($brand_id);
$products = $query->posts;

// Get article relationships
$related = \Mansa\Core\QueryHelpers::get_article_related_content($article_id);
```

### Displaying Content

```php
// Display product with brand
\Mansa\Core\UsageExamples::display_product_with_brand($product_id);

// Display article with related content
\Mansa\Core\UsageExamples::display_article_with_relations($article_id);
```

## Custom Post Types

### Products (`mansa_product`)
- Slug: `products`
- Supports: title, editor, author, thumbnail, excerpt, custom-fields
- REST API: enabled
- Archive: enabled

### Brands (`mansa_brand`)
- Slug: `brands`
- Supports: title, editor, author, thumbnail, excerpt, custom-fields
- REST API: enabled
- Archive: enabled

### Articles (`mansa_article`)
- Slug: `articles`
- Supports: title, editor, author, thumbnail, excerpt, custom-fields
- REST API: enabled
- Archive: enabled

## Taxonomies

### Product Categories (`mansa_product_category`)
- Hierarchical: true
- Attached to: Products, Brands
- REST API: enabled

### Origin (`mansa_origin`)
- Hierarchical: true
- Attached to: Products
- REST API: enabled

### Article Topics (`mansa_article_topic`)
- Hierarchical: true
- Attached to: Articles
- REST API: enabled

## Relationships

### Product → Brand
- Meta key: `_mansa_brand_id`
- Type: Single relationship (one brand per product)

### Article → Related Products
- Meta key: `_mansa_related_products`
- Type: Multiple relationships (array of product IDs)

### Article → Related Brands
- Meta key: `_mansa_related_brands`
- Type: Multiple relationships (array of brand IDs)

## Admin Interface

### Product Editor
- **Brand selector**: Dropdown to assign a brand to a product

### Article Editor
- **Related Products**: Multi-select dropdown for products
- **Related Brands**: Multi-select dropdown for brands

## Security

All code follows WordPress security standards:
- Input sanitization with `sanitize_text_field()`, `absint()`, etc.
- Output escaping with `esc_html()`, `esc_attr()`, `esc_url()`
- Nonce verification for all form submissions
- Capability checks for all operations

## Performance

- Efficient `WP_Query` with optimized `meta_query`
- Proper database indexing recommendations
- Minimal overhead and no unnecessary dependencies
- Lazy loading where appropriate

## Development

### Requirements
- WordPress 5.9+
- PHP 7.4+
- Composer

### Coding Standards
- WordPress PHP Coding Standards
- PSR-4 autoloading
- Prefixed hooks and meta keys (`mansa_`, `_mansa_`)
- Comprehensive documentation

## License

GPL-2.0-or-later

## Changelog

### 1.0.0
- Initial release
- Custom post types: Products, Brands, Articles
- Taxonomies: Product Categories, Origin, Article Topics
- Relationships: Product-Brand, Article-Products, Article-Brands
- Admin interface with meta boxes
- Query helpers and usage examples
- Dependency injection container
- Service provider pattern