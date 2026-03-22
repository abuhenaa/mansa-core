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
import { PanelBody, TextControl, TextareaControl, Button } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

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
	const { 
		sectionTitle, 
		brandName, 
		brandLabel, 
		brandDescription, 
		brandImage, 
		buttonText, 
		buttonUrl 
	} = attributes;

	const onSelectImage = ( media ) => {
		setAttributes( {
			brandImage: {
				id: media.id,
				url: media.url,
				alt: media.alt || media.title,
			}
		} );
	};

	const onRemoveImage = () => {
		setAttributes( {
			brandImage: null
		} );
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
					<TextControl
						label={__( 'Brand Name', 'mansa-core' )}
						value={ brandName }
						onChange={ ( value ) => setAttributes( { brandName: value } ) }
					/>
					<TextControl
						label={__( 'Brand Label', 'mansa-core' )}
						value={ brandLabel }
						onChange={ ( value ) => setAttributes( { brandLabel: value } ) }
					/>
					<TextareaControl
						label={__( 'Brand Description', 'mansa-core' )}
						value={ brandDescription }
						onChange={ ( value ) => setAttributes( { brandDescription: value } ) }
						rows={4}
					/>
					<TextControl
						label={__( 'Button Text', 'mansa-core' )}
						value={ buttonText }
						onChange={ ( value ) => setAttributes( { buttonText: value } ) }
					/>
					<TextControl
						label={__( 'Button URL', 'mansa-core' )}
						value={ buttonUrl }
						onChange={ ( value ) => setAttributes( { buttonUrl: value } ) }
					/>
				</PanelBody>
				<PanelBody title={__( 'Brand Image', 'mansa-core' )}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes={ [ 'image' ] }
							value={ brandImage?.id }
							render={ ( { open } ) => (
								<>
									{ ! brandImage ? (
										<Button
											onClick={ open }
											className="button button-large"
										>
											{ __( 'Select Image', 'mansa-core' ) }
										</Button>
									) : (
										<>
											<img 
												src={ brandImage.url } 
												alt={ brandImage.alt }
												style={{ maxWidth: '100%', height: 'auto', marginBottom: '10px' }}
											/>
											<Button
												onClick={ open }
												className="button"
												style={{ marginRight: '10px' }}
											>
												{ __( 'Replace Image', 'mansa-core' ) }
											</Button>
											<Button
												onClick={ onRemoveImage }
												className="button is-destructive"
											>
												{ __( 'Remove Image', 'mansa-core' ) }
											</Button>
										</>
									) }
								</>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div className="container">
					<div className="section-header">
						<h2>{sectionTitle}</h2>
					</div>
					<div className="brand-grid">
						<div className="brand-img">
							{ brandImage ? (
								<img src={ brandImage.url } alt={ brandImage.alt } />
							) : (
								<div style={{ 
									background: '#f3f4f6', 
									display: 'flex', 
									alignItems: 'center', 
									justifyContent: 'center',
									height: '300px',
									borderRadius: '8px'
								}}>
									{ __( 'No image selected', 'mansa-core' ) }
								</div>
							) }
						</div>
						<div className="brand-text">
							<span className="brand-label">{brandLabel}</span>
							<h3>{brandName}</h3>
							<p>{brandDescription}</p>
							<a href={buttonUrl} className="btn btn-primary">{buttonText}</a>
						</div>
					</div>
				</div>
			</div>
		</>
	);
}
