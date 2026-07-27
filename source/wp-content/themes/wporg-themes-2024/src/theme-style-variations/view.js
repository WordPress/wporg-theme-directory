/**
 * WordPress dependencies
 */
import { getContext, getElement, store } from '@wordpress/interactivity';

const OFFSET_WIDTH = 220;

/**
 * Tolerance in pixels when comparing scroll positions. On zoomed or high-DPI
 * displays `scrollLeft` can be fractional and never exactly reach the
 * calculated overflow, which would leave the arrows enabled at the ends.
 */
const SCROLL_TOLERANCE = 1;

/**
 * Get the "row" element starting with any block child element.
 *
 * @param {Element} ref
 *
 * @return {Element}
 */
function getRowElement( ref ) {
	const container = ref.closest( '.wp-block-wporg-theme-style-variations' );
	const element = container.querySelector( '.wporg-theme-style-variations__row' );
	return element;
}

/*
 * Note about RTL: Browsers scroll "left" into the negative when on RTL, so the
 * `scrolled` getter normalizes the sign for the "canNext"/"canPrevious"
 * checks; the offsets triggered when clicking the arrow buttons are swapped.
 */

const { state } = store( 'wporg/themes/style-variations', {
	state: {
		get scrolled() {
			const { isRTL } = getContext();
			return isRTL ? -state.position : state.position;
		},
		get canPrevious() {
			return state.scrolled > SCROLL_TOLERANCE;
		},
		get canNext() {
			return state.scrolled < state.overflow - SCROLL_TOLERANCE;
		},
		get hasOverscroll() {
			return state.canNext || state.canPrevious;
		},
		get isHidden() {
			// Each screenshot resets the context with its style.
			const context = getContext();
			return context.style !== state.selected;
		},
		position: 0,
		overflow: 0,
		selected: 'default',
	},
	actions: {
		init() {
			const element = getRowElement( getElement().ref );
			state.position = element.scrollLeft;

			// How much extra scroll overflow do we have?
			state.overflow = element.scrollWidth - element.clientWidth;
		},
		handleScroll() {
			const element = getRowElement( getElement().ref );
			state.position = element.scrollLeft;
		},
		handlePrevious() {
			if ( ! state.canPrevious ) {
				return;
			}
			const { isRTL } = getContext();
			const element = getRowElement( getElement().ref );
			const position = isRTL ? element.scrollLeft + OFFSET_WIDTH : element.scrollLeft - OFFSET_WIDTH;
			element.scrollTo( {
				left: position,
				behavior: 'smooth',
			} );
		},
		handleNext() {
			if ( ! state.canNext ) {
				return;
			}
			const { isRTL } = getContext();
			const element = getRowElement( getElement().ref );
			const position = isRTL ? element.scrollLeft - OFFSET_WIDTH : element.scrollLeft + OFFSET_WIDTH;
			element.scrollTo( {
				left: position,
				behavior: 'smooth',
			} );
		},
		onStyleClick( event ) {
			event.preventDefault();
			const { ref } = getElement();
			state.selected = ref?.dataset.style;

			// If the preview exists, update the URL to the selected variation.
			const previewButton = document.getElementById( 'wporg-theme-button-preview' );
			if ( previewButton ) {
				const previewURL = new URL( previewButton.href );
				previewURL.searchParams.set( 'style_variation', state.selected );
				previewButton.href = previewURL;
			}
		},
	},
} );
