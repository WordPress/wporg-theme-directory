/* global CustomEvent */
/**
 * WordPress dependencies
 */
import { DOWN, END, ENTER, HOME, LEFT, RIGHT, SPACE, UP } from '@wordpress/keycodes';

class wporgListbox {
	constructor( container, state ) {
		this.state = state;
		this.container = container;
		this.current = null;
		this.selected = null;

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
		this.selected = element.dataset.index * 1;

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
			this.selected = this.current;
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

	updateSelected() {
		// Push the selected event out to anyone listening (theme previewer).
		const listbox = this.container.querySelector( '[role="listbox"]' );
		const listItems = listbox.querySelectorAll( 'li' );
		if ( listbox && listItems && listItems[ this.selected ] ) {
			const dispatchEvent = new CustomEvent( 'wporg-select' );
			dispatchEvent.selectedElement = listItems[ this.selected ];
			listbox.dispatchEvent( dispatchEvent );
		}
	}

	updateRender() {
		const listbox = this.container.querySelector( '[role="listbox"]' );
		const listItems = listbox.querySelectorAll( 'li' );
		if ( this.current !== null ) {
			listbox.setAttribute( 'aria-activedescendant', listItems[ this.current ].id );
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
