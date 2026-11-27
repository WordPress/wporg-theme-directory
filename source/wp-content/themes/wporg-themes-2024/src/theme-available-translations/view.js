/**
 * WordPress dependencies
 */
import { getContext, store } from '@wordpress/interactivity';

store( 'wporg/themes/available-translations', {
	state: {
		get toggleLabel() {
			const context = getContext();
			return context.isExpanded ? context.showLessLabel : context.showMoreLabel;
		},
	},
	actions: {
		toggle() {
			const context = getContext();
			context.isExpanded = ! context.isExpanded;
		},
	},
} );
