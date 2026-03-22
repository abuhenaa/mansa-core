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
			<div className="community">
				<h2 style={{fontSize: 'clamp(1.75rem,4vw,2.25rem)', fontWeight: '600', marginBottom: '0.75rem'}}>
					Stay Ahead of What's Next
				</h2>
				<p style={{color: 'var(--muted)', fontSize: '1.1rem', marginBottom: '2.5rem', lineHeight: '1.6'}}>
					Receive curated discoveries, product highlights, and emerging brands.
				</p>
				<form className="subscribe-form" onSubmit={(e) => e.preventDefault()}>
					<input type="email" placeholder="Your email address" />
					<button type="submit" className="btn btn-primary">Subscribe</button>
				</form>
				<a href="#" className="wa-link">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
						<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
					</svg>
					Join our WhatsApp Community — Get weekly curated product discoveries
				</a>
			</div>
		</div>
	);
}
