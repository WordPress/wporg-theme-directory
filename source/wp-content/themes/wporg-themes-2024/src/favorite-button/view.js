/**
 * WordPress dependencies
 */
import { getContext, store } from '@wordpress/interactivity';

store( 'wporg/themes/favorite-button', {
	state: {
		get labelAction() {
			const { label, isFavorite } = getContext();
			return isFavorite ? label.remove : label.add;
		},
	},
	actions: {
		*triggerAction() {
			const context = getContext();
			if ( context.isFavorite ) {
				try {
					yield wp.apiFetch( {
						path: '/wporg-themes/v1/favorite',
						method: 'DELETE',
						data: { theme_slug: context.themeSlug },
					} );
					context.isFavorite = false;
					wp.a11y.speak( context.label.unfavorited, 'polite' );
				} catch ( error ) {}
			} else {
				try {
					yield wp.apiFetch( {
						path: '/wporg-themes/v1/favorite',
						method: 'POST',
						data: { theme_slug: context.themeSlug },
					} );
					context.isFavorite = true;
					wp.a11y.speak( context.label.favorited, 'polite' );
				} catch ( error ) {}
			}
		},
	},
} );
