/**
 * WordPress dependencies
 */
import { getContext, getElement, store } from '@wordpress/interactivity';

store( 'wporg/themes/preview', {
	state: {
		get isLoaded() {
			const context = getContext();
			return context.isLoaded;
		},
	},
	actions: {
		onLoad() {
			const context = getContext();
			context.isLoaded = true;
		},
		navigateIframe( event ) {
			const context = getContext();
			const { selectedElement: ref } = event;
			if ( ref && ref.dataset ) {
				context.isLoaded = false;
				if ( ref.dataset.style_variation ) {
					context.selected.style_variation = ref.dataset.style_variation;
				}
				if ( ref.dataset.pattern_name ) {
					context.selected.pattern_name = ref.dataset.pattern_name;
				}

				const params = new URLSearchParams( '' );
				if ( context.selected.style_variation ) {
					params.set( 'style_variation', context.selected.style_variation );
				}
				if ( context.selected.pattern_name ) {
					params.set( 'pattern_name', context.selected.pattern_name );
					params.set( 'page_id', 9999 );
				}

				const previewURL = new URL( context.previewBase );
				previewURL.search = params.toString();

				const permalinkURL = new URL( context.permalink );
				params.delete( 'page_id' );
				permalinkURL.search = params.toString();

				context.url = previewURL;
				window.history.replaceState( {}, '', permalinkURL );
			}
		},
	},
} );
