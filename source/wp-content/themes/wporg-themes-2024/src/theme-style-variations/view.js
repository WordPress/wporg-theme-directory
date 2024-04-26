/**
 * WordPress dependencies
 */
import { getContext, getElement, store } from '@wordpress/interactivity';

const OFFSET_WIDTH = 220;

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

// Note about RTL: Browsers scroll "left" into the negative when on RTL, so the
// logic for when we "canNext"/"canPrevious" are swapped, as are the offsets
// triggered when clicking the arrow buttons.

const { state } = store( 'wporg/themes/style-variations', {
	state: {
		get canPrevious() {
			const { isRTL } = getContext();
			if ( isRTL ) {
				return state.position < 0;
			}
			return state.position > 0;
		},
		get canNext() {
			const { isRTL } = getContext();
			if ( isRTL ) {
				return state.position >= state.overflow * -1;
			}
			return state.position < state.overflow;
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
			state.overflow =
				element.children.length * 100 + ( element.children.length - 1 ) * 10 - element.clientWidth;
		},
		handleScroll() {
			const element = getRowElement( getElement().ref );
			state.position = element.scrollLeft;
		},
		handlePrevious() {
			const { isRTL } = getContext();
			const element = getRowElement( getElement().ref );
			const position = isRTL ? element.scrollLeft + OFFSET_WIDTH : element.scrollLeft - OFFSET_WIDTH;
			element.scrollTo( {
				left: position,
				behavior: 'smooth',
			} );
		},
		handleNext() {
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
		},
	},
} );
