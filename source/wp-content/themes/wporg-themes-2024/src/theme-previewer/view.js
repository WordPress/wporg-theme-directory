/**
 * WordPress dependencies
 */
import { getContext, getElement, store } from '@wordpress/interactivity';

store( 'wporg/themes/preview', {
	state: {
		get iframeWidthCSS() {
			const { device } = getContext();
			if ( device === 'mobile' ) {
				return '360px';
			} else if ( device === 'tablet' ) {
				return '768px';
			}
			return '100%';
		},
		get iframeHeightCSS() {
			const { device } = getContext();
			if ( device === 'mobile' ) {
				return '800px';
			} else if ( device === 'tablet' ) {
				return '1024px';
			}
			return '100%';
		},
		get isDeviceDesktop() {
			return getContext().device === 'desktop';
		},
		get isDeviceTablet() {
			return getContext().device === 'tablet';
		},
		get isDeviceMobile() {
			return getContext().device === 'mobile';
		},
		get isLoaded() {
			return getContext().isLoaded;
		},
	},
	actions: {
		onDeviceChange() {
			const { ref } = getElement();
			const context = getContext();
			const { device = '' } = ref.dataset;

			if ( [ 'desktop', 'tablet', 'mobile' ].includes( device ) ) {
				context.device = device;
				const url = new URL( window.location.href );
				url.searchParams.set( 'device', device );
				window.history.replaceState( {}, '', url.href );
			}
		},
		onLoad() {
			const context = getContext();
			context.isLoaded = true;
		},
	},
} );
