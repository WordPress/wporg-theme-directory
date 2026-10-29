/**
 * WordPress dependencies
 */
import { getContext, store } from '@wordpress/interactivity';

const { state } = store( 'wporg/themes/theme-previewer-settings', {
	state: {
		isError: false,
		isSuccess: false,
		isInvalid: false,
		isSubmitting: false,
		get resultMessage() {
			const { labels } = getContext();
			if ( state.isInvalid ) {
				return labels.invalid;
			}
			if ( state.isSuccess ) {
				return labels.success;
			}
			if ( state.isError ) {
				return labels.error;
			}
			return '';
		},
	},
	actions: {
		onChange() {
			state.isSuccess = false;
			state.isError = false;
			state.isInvalid = false;
		},
		*onSubmit( event ) {
			event.preventDefault();
			const context = getContext();
			const { slug } = context;
			const newBlueprint = event.target.elements.blueprint?.value || '';

			try {
				const decoded = JSON.parse( newBlueprint );
				if ( typeof decoded !== 'object' || ! decoded.steps ) {
					throw new Error( 'Invalid blueprint.' );
				}
			} catch {
				state.isInvalid = true;
				return;
			}

			state.isSubmitting = true;
			try {
				const response = yield wp.apiFetch( {
					path: '/themes/v1/preview-blueprint/' + slug,
					method: 'POST',
					data: { blueprint: newBlueprint },
				} );
				if ( typeof response.steps === 'undefined' ) {
					throw new Error( 'Invalid response from API.' );
				}
				state.isSuccess = true;
				state.blueprint = newBlueprint;
			} catch {
				state.isError = true;
			}
			state.isSubmitting = false;
		},
	},
} );
