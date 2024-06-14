/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import wporgListbox from '../utils/listbox';

window.addEventListener( 'load', () => {
	const containers = document.querySelectorAll( '.wp-block-wporg-theme-patterns' );
	if ( ! containers ) {
		return;
	}

	containers.forEach( ( container ) => {
		const state = JSON.parse( container.dataset.initialState );
		new wporgListbox( container, state );

		// Not in the previewer, use the select event to navigate to the previewer.
		if ( ! container.closest( '.wp-block-wporg-theme-previewer' ) ) {
			container.querySelector( '[role="listbox"]' ).addEventListener( 'wporg-select', ( event ) => {
				const ref = event.selectedElement;
				if ( ref && ref.dataset ) {
					let url = window.location.toString();
					url = url.replace( /\/$/, '' ) + '/preview/';
					url = addQueryArgs( url, { pattern_name: ref.dataset.pattern_name } );
					window.location = url;
				}
			} );
		}
	} );
} );
