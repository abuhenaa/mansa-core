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
					<h2>Explore Categories</h2>
				</div>
				<div className="grid-4">
					<div className="cat-card">
						<div className="cat-icon">
							<svg viewBox="0 0 24 24">
								<path d="M3 2h3a2 2 0 012 2v2a2 2 0 01-2 2H5l-2 4v8h18v-8l-2-4h-1a2 2 0 01-2-2V4a2 2 0 012-2h3"/>
								<path d="M3 18h18"/>
							</svg>
						</div>
						<h3>Food & Drink</h3>
					</div>
					<div className="cat-card">
						<div className="cat-icon">
							<svg viewBox="0 0 24 24">
								<path d="M12 3l1.5 3.2 3.5.5-2.5 2.5.6 3.5L12 11l-3.1 1.7.6-3.5L7 6.7l3.5-.5z"/>
								<path d="M9 18c0 2 1.5 3 3 3s3-1 3-3"/>
								<path d="M12 14v4"/>
							</svg>
						</div>
						<h3>Beauty & Personal Care</h3>
					</div>
					<div className="cat-card">
						<div className="cat-icon">
							<svg viewBox="0 0 24 24">
								<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
								<polyline points="9 22 9 12 15 12 15 22"/>
							</svg>
						</div>
						<h3>Home & Lifestyle</h3>
					</div>
					<div className="cat-card">
						<div className="cat-icon">
							<svg viewBox="0 0 24 24">
								<path d="M20.38 3.46L16 2 12 3.46 8 2 3.62 3.46a1 1 0 00-.62.94V20a1 1 0 001 1h16a1 1 0 001-1V4.4a1 1 0 00-.62-.94z"/>
								<path d="M8 2v20M16 2v20"/>
							</svg>
						</div>
						<h3>Fashion & Textiles</h3>
					</div>
				</div>
			</div>
		</div>
	);
}
