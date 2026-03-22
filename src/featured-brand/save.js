/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

export default function save() {
	return (
		<div { ...useBlockProps.save() }>
			<div className="container">
				<div className="section-header">
					<h2>Featured Brand</h2>
				</div>
				<div className="brand-grid">
					<div className="brand-img">
						<img src="placeholder-brand.jpg" alt="Terra & Ember" />
					</div>
					<div className="brand-text">
						<span className="brand-label">Brand Spotlight</span>
						<h3>Terra & Ember</h3>
						<p>Born in a small workshop in Oaxaca, Terra & Ember crafts homeware from locally sourced clay and reclaimed wood. Every piece carries the warmth of hands that shaped it — honest, intentional, and built to last generations.</p>
						<a href="#" className="btn btn-primary">Explore Brand</a>
					</div>
				</div>
			</div>
		</div>
	);
}
