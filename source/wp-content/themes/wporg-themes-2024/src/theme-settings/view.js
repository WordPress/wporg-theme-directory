/**
 * WordPress dependencies
 */
import { getContext, store } from '@wordpress/interactivity';

const { state } = store( 'wporg/themes/theme-settings', {
	state: {
		isError: false,
		isSuccess: false,
		isSubmitting: false,
		get resultMessage() {
			const { labels } = getContext();
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
		},
		*onSubmit( event ) {
			event.preventDefault();
			const context = getContext();
			const { type, slug, url } = context;
			const inputEl = event.target.elements.url;
			if ( ! inputEl ) {
				return;
			}
			const newUrl = inputEl.value;
			state.isSubmitting = true;
			try {
				const key = 'commercial' === type ? 'supportURL' : 'repositoryURL';
				const response = yield wp.apiFetch( {
					path: '/themes/v1/theme/' + slug + '/' + type,
					method: 'POST',
					data: { [ key ]: newUrl },
				} );
				if ( typeof response[ key ] === 'undefined' ) {
					throw new Error( 'Invalid response from API.' );
				}
				context.url = response[ key ];
				state.isSuccess = true;
			} catch ( error ) {
				inputEl.value = url;
				state.isError = true;
			}
			state.isSubmitting = false;
		},
	},
} );
