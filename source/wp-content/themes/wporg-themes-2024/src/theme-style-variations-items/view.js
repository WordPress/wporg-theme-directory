/**
 * Internal dependencies
 */
import wporgListbox from '../utils/listbox';

window.addEventListener( 'load', () => {
	const containers = document.querySelectorAll( '.wp-block-wporg-theme-style-variations-items' );
	if ( ! containers ) {
		return;
	}

	containers.forEach( ( container ) => {
		const state = JSON.parse( container.dataset.initialState );
		new wporgListbox( container, state );
	} );
} );
