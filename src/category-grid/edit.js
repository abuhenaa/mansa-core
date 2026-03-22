/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import { useState } from '@wordpress/element';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { sectionTitle, categories } = attributes;

	const updateCategory = ( index, field, value ) => {
		const newCategories = [ ...categories ];
		newCategories[ index ][ field ] = value;
		setAttributes( { categories: newCategories } );
	};

	const addCategory = () => {
		setAttributes( {
			categories: [ ...categories, { name: '', icon: 'grid-view', link: '#' } ]
		} );
	};

	const removeCategory = ( index ) => {
		const newCategories = categories.filter( ( _, i ) => i !== index );
		setAttributes( { categories: newCategories } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__( 'Settings', 'mansa-core' )}>
					<TextControl
						label={__( 'Section Title', 'mansa-core' )}
						value={ sectionTitle }
						onChange={ ( value ) => setAttributes( { sectionTitle: value } ) }
					/>
				</PanelBody>
				<PanelBody title={__( 'Categories', 'mansa-core' )}>
					{categories.map( ( category, index ) => (
						<div key={index} style={{ marginBottom: '16px', padding: '12px', border: '1px solid #ddd', borderRadius: '4px' }}>
							<TextControl
								label={__( 'Category Name', 'mansa-core' )}
								value={ category.name }
								onChange={ ( value ) => updateCategory( index, 'name', value ) }
							/>
							<TextControl
								label={__( 'Icon (Dashicon name)', 'mansa-core' )}
								value={ category.icon }
								onChange={ ( value ) => updateCategory( index, 'icon', value ) }
							/>
							<TextControl
								label={__( 'Link URL', 'mansa-core' )}
								value={ category.link }
								onChange={ ( value ) => updateCategory( index, 'link', value ) }
							/>
							<Button
								isDestructive
								onClick={ () => removeCategory( index ) }
								style={{ marginTop: '8px' }}
							>
								{__( 'Remove Category', 'mansa-core' )}
							</Button>
						</div>
					) )}
					<Button
						isPrimary
						onClick={ addCategory }
						style={{ marginTop: '16px' }}
					>
						{__( 'Add Category', 'mansa-core' )}
					</Button>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div className="container">
					<div className="section-header">
						<h2>{sectionTitle}</h2>
					</div>
					<div className="grid-4">
						{categories.map( ( category, index ) => (
							<div key={index} className="cat-card">
								<div className="cat-icon">
									<span className="dashicons dashicons-<?php echo esc_attr($category['icon']); ?>"></span>
								</div>
								<h3>{category.name}</h3>
							</div>
						) )}
					</div>
				</div>
			</div>
		</>
	);
}
