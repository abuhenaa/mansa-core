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
import { PanelBody, TextControl, RangeControl, SelectControl, Placeholder } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

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
	const { postType, postsPerPage, sectionTitle, sectionDescription } = attributes;

	//fetch post types for select options
	const postTypes = useSelect( ( select ) => {
		return select( coreStore ).getPostTypes({ per_page: -1 } );
	}, [] );

	const postTypeOptions = postTypes
		? postTypes.filter( ( type ) => type.viewable ).map( ( type ) => ( { label: type.labels.singular_name, value: type.slug } ) )
		: [];
	return (
		<>
			<InspectorControls>
				<PanelBody title={__( 'Settings', 'mansa-core' )}>
					<TextControl
						label={__( 'Section Title', 'mansa-core' )}
						value={ sectionTitle }
						onChange={ ( value ) => setAttributes( { sectionTitle: value } ) }
					/>
					<TextControl
						label={__( 'Section Description', 'mansa-core' )}
						value={ sectionDescription }
						onChange={ ( value ) => setAttributes( { sectionDescription: value } ) }
					/>
					<SelectControl
						label={__( 'Post Type', 'mansa-core' )}
						value={ postType }
						options={ postTypeOptions }
						onChange={ ( value ) => setAttributes( { postType: value } ) }
					/>
					<RangeControl
						label={__( 'Posts Per Page', 'mansa-core' )}
						value={ postsPerPage }
						onChange={ ( value ) => setAttributes( { postsPerPage: value } ) }
						min={ 1 }
						max={ 12 }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<Placeholder label={ __( 'Mansa Featured Discovery', 'mansa-core' ) } instructions={ __( 'Select a post type and number of contents to display.', 'mansa-core' ) } />
				<div className="mansa-featured-discovery-placeholder">
					{ postType ? (
						<p>{ __( 'Displaying', 'mansa-core' ) } { postsPerPage } { __( 'items from', 'mansa-core' ) } { postType }</p>
					) : (
						<p>{ __( 'Please select a post type to display content.', 'mansa-core' ) }</p>
					) }
				</div>
			</div>
		</>
	);
}
