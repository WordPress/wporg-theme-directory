/* global CustomEvent */
/**
 * WordPress dependencies
 */
import { DOWN, END, ENTER, HOME, LEFT, RIGHT, SPACE, UP } from '@wordpress/keycodes';

class wporgListbox {
	constructor( container, state ) {
		this.state = state;
		this.container = container;
		this.selected = state.initialSelected > 0 ? state.initialSelected : null;
		this.current = null;

		const button = container.querySelector( 'button' );
		if ( button ) {
			button.addEventListener( 'click', this.showAll.bind( this ) );
		}

		const listbox = container.querySelector( '[role="listbox"]' );
		listbox.addEventListener( 'focus', this.handleFocus.bind( this ) );
		listbox.addEventListener( 'keydown', this.handleKeyboard.bind( this ) );

		const listItems = listbox.querySelectorAll( 'li' );
		listItems.forEach( ( element, index ) => {
			element.dataset.index = index;
			element.addEventListener( 'click', this.handleClick.bind( this ) );
		} );

		this.updateRender();
	}

	handleClick( event ) {
		const element = event.target.closest( 'li' );
		if ( ! element ) {
			return;
		}

		// Focus the container.
		element.closest( 'ul' ).focus();

		this.current = element.dataset.index * 1;

		this.updateSelected();
		this.updateRender();
	}

	handleFocus() {
		this.current = this.current || 0;
		this.updateRender();
	}

	increment( jump = false ) {
		const max = this.state.hideOverflow ? this.state.initialCount : this.state.totalCount;
		if ( jump ) {
			this.current = max - 1;
		} else if ( this.current + 1 <= max - 1 ) {
			this.current += 1;
		}
	}

	decrement( jump = false ) {
		if ( jump ) {
			this.current = 0;
		} else if ( this.current - 1 >= 0 ) {
			this.current -= 1;
		}
	}

	/**
	 * Handle the keyboard events.
	 * - Move forward if down, right, or end are pressed.
	 * - Move backward if up, left, or home are pressed.
	 * - Holding cmd/ctrl (event.metaKey is true) while arrow jumps to the end of the list.
	 * - Space and enter trigger the current item to be selected.
	 *
	 * @param {KeyboardEvent} event
	 */
	handleKeyboard( event ) {
		if ( event.keyCode === DOWN || event.keyCode === RIGHT ) {
			this.increment( event.metaKey );
		} else if ( event.keyCode === END ) {
			this.increment( true );
		} else if ( event.keyCode === UP || event.keyCode === LEFT ) {
			this.decrement( event.metaKey );
		} else if ( event.keyCode === HOME ) {
			this.decrement( true );
		} else if ( event.keyCode === ENTER || event.keyCode === SPACE ) {
			this.updateSelected();
		} else {
			// Do nothing if none of the previous conditions triggered.
			return;
		}

		event.preventDefault();
		this.updateRender();
	}

	showAll() {
		this.state.hideOverflow = false;
		this.updateRender();

		// Trigger the custom "show" event on each image.
		this.container.querySelectorAll( '.wp-block-wporg-screenshot-preview' ).forEach( ( element ) => {
			const dispatchEvent = new Event( 'wporg-show' );
			element.dispatchEvent( dispatchEvent );
		} );

		// Move focus from the now-removed button to the first-visible element.
		setTimeout( () => {
			const listbox = this.container.querySelector( '[role="listbox"]' );
			if ( listbox ) {
				listbox.focus();
			}
		}, 0 );
	}

	/**
	 * Update the current selected item.
	 *
	 * Set selected to the focused element. If the focused item is already
	 * selected, and we allow unselecting (set in the initial state), then
	 * unselect the item. Otherwise, do nothing and return.
	 *
	 * Once the selected item is updated, trigger a custom event with the
	 * selected element. The previewer listens for this event to update
	 * the iframe's content.
	 */
	updateSelected() {
		// Maybe untoggle the element.
		if ( this.current === this.selected ) {
			if ( this.state.allowUnselect ) {
				this.selected = null;
			} else {
				// Noop, this is already selected.
				return;
			}
		} else {
			this.selected = this.current;
		}

		// Push the selected event out to anyone listening (theme previewer).
		const listbox = this.container.querySelector( '[role="listbox"]' );
		const listItems = listbox.querySelectorAll( 'li' );
		if ( ! listItems ) {
			return;
		}

		if ( listItems[ this.selected ] ) {
			const dispatchEvent = new CustomEvent( 'wporg-select' );
			dispatchEvent.selectedElement = listItems[ this.selected ];
			listbox.dispatchEvent( dispatchEvent );
		} else if ( listItems[ this.current ] ) {
			// If the selected item is not found, it's null (was just unselected),
			// and we should use the currently-focused element instead.
			const dispatchEvent = new CustomEvent( 'wporg-unselect' );
			dispatchEvent.selectedElement = listItems[ this.current ];
			listbox.dispatchEvent( dispatchEvent );
		}
	}

	/**
	 * Update the assorted classes, attributes, and styles for the
	 * currently focused and selected items, unset for the rest.
	 * Ensure the focused item is correctly flagged, and in view.
	 *
	 * This function is called after every update (manually).
	 */
	updateRender() {
		const listbox = this.container.querySelector( '[role="listbox"]' );
		const listItems = listbox.querySelectorAll( 'li' );
		if ( typeof listItems[ this.current ] !== 'undefined' ) {
			listbox.setAttribute( 'aria-activedescendant', listItems[ this.current ].id );
			listItems[ this.current ].scrollIntoView( { block: 'nearest', inline: 'nearest' } );
		}

		listItems.forEach( ( element, index ) => {
			// Mark the focused item.
			if ( index === this.current ) {
				element.classList.add( 'is-focus' );
			} else {
				element.classList.remove( 'is-focus' );
			}

			// Mark the selected item.
			if ( index === this.selected ) {
				element.setAttribute( 'aria-selected', 'true' );
			} else {
				element.setAttribute( 'aria-selected', 'false' );
			}

			// Hide (or unhide) overflow items.
			if ( this.state.hideOverflow && index > this.state.initialCount - 1 ) {
				element.style.display = 'none';
			} else {
				element.style.display = null;
			}
		} );
	}
}

export default wporgListbox;
