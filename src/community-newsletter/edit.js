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
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';

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
		sectionDescription, 
		placeholderText, 
		buttonText, 
		whatsappText, 
		whatsappUrl,
		actionUrl
	} = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__( 'Settings', 'mansa-core' )}>
					<TextControl
						label={__( 'Section Title', 'mansa-core' )}
						value={ sectionTitle }
						onChange={ ( value ) => setAttributes( { sectionTitle: value } ) }
					/>
					<TextareaControl
						label={__( 'Section Description', 'mansa-core' )}
						value={ sectionDescription }
						onChange={ ( value ) => setAttributes( { sectionDescription: value } ) }
						rows={2}
					/>
					<TextControl
						label={__( 'Email Placeholder', 'mansa-core' )}
						value={ placeholderText }
						onChange={ ( value ) => setAttributes( { placeholderText: value } ) }
					/>
					<TextControl
						label={__( 'Subscribe Button Text', 'mansa-core' )}
						value={ buttonText }
						onChange={ ( value ) => setAttributes( { buttonText: value } ) }
					/>
					<TextControl
						label={__( 'Form Action URL', 'mansa-core' )}
						value={ actionUrl }
						onChange={ ( value ) => setAttributes( { actionUrl: value } ) }
						help={__( 'Leave empty for demo mode', 'mansa-core' )}
					/>
				</PanelBody>
				<PanelBody title={__( 'WhatsApp Settings', 'mansa-core' )}>
					<TextareaControl
						label={__( 'WhatsApp Link Text', 'mansa-core' )}
						value={ whatsappText }
						onChange={ ( value ) => setAttributes( { whatsappText: value } ) }
						rows={2}
					/>
					<TextControl
						label={__( 'WhatsApp URL', 'mansa-core' )}
						value={ whatsappUrl }
						onChange={ ( value ) => setAttributes( { whatsappUrl: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div className="community">
					<h2 style={{fontSize: 'clamp(1.75rem, 4vw, 2.25rem)', fontWeight: '600', marginBottom: '0.75rem'}}>
						{sectionTitle}
					</h2>
					<p style={{color: '#6b7280', fontSize: '1.1rem', marginBottom: '2.5rem', lineHeight: '1.6'}}>
						{sectionDescription}
					</p>
					<form className="subscribe-form" onSubmit={(e) => e.preventDefault()}>
						<input 
							type="email" 
							placeholder={placeholderText}
							style={{flex: 1, padding: '0.875rem 1.25rem', borderRadius: '8px', border: '1px solid #e5e7eb', background: '#fff', fontSize: '0.875rem'}}
						/>
						<button type="submit" className="btn btn-primary">
							{buttonText}
						</button>
					</form>
					<a href={whatsappUrl} className="wa-link" style={{display: 'inline-flex', alignItems: 'center', gap: '0.5rem', color: '#6b7280', fontSize: '0.875rem', fontWeight: '500'}}>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" style={{width: '1.25rem', height: '1.25rem'}}>
							<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
						</svg>
						{whatsappText}
					</a>
				</div>
			</div>
		</>
	);
}
