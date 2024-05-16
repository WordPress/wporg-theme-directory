<?php

if ( ! $block->context['postId'] ) {
	return '';
}

$theme_post = get_post( $block->context['postId'] );

$model_type = false;
$url = '';

if ( has_term( 'commercial', 'theme_business_model', $theme_post ) ) {
	$model_type = 'commercial';
	$url = get_post_meta( $theme_post->ID, 'external_support_url', true );
} elseif ( has_term( 'community', 'theme_business_model', $theme_post ) ) {
	$model_type = 'community';
	$url = get_post_meta( $theme_post->ID, 'external_repository_url', true );
}

// Not community/commercial, or the user is not an owner/admin.
if ( ! $model_type || ! current_user_can( 'edit_post', $theme_post->ID ) ) {
	return;
}

// Enqueue this script, so that it's available for the interactivity view script.
wp_enqueue_script( 'wp-api-fetch' );

$labels = [
	'heading' => __( 'Theme options', 'wporg-themes' ),
];
if ( 'community' === $model_type ) {
	$labels['type'] = __( 'Community theme:', 'wporg-themes' );
	$labels['description'] = __( 'This theme is developed and supported by a community.', 'wporg-themes' );
	$labels['form_label'] = __( 'Development repository URL', 'wporg-themes' );
	$labels['form_help'] = __( 'Optional. The URL where development happens, such as at github.com.', 'wporg-themes' );
} elseif ( 'commercial' === $model_type ) {
	$labels['type'] = __( 'Commercial theme:', 'wporg-themes' );
	$labels['description'] = __( 'This theme is free but offers paid upgrades, support, and/or add-ons.', 'wporg-themes' );
	$labels['form_label'] = __( 'Commercial support URL', 'wporg-themes' );
	$labels['form_help'] = __( 'Optional. The URL for theme support, other than its support forum on wordpress.org.', 'wporg-themes' );
}

// Initial state to pass to Interactivity API.
$init_state = [
	'slug' => $theme_post->post_name,
	'type' => $model_type,
	'url' => $url,
	'labels' => [
		'success' => __( 'URL saved correctly!', 'wporg-themes' ),
		'error' => __( 'Error updating the URL. Please try again.', 'wporg-themes' ),
	],
];
$encoded_state = wp_json_encode( $init_state );

?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
	data-wp-interactive="wporg/themes/theme-settings"
	data-wp-context="<?php echo esc_attr( $encoded_state ); ?>"
>
	<h2 class="has-heading-4-font-size"><?php echo esc_html( $labels['heading'] ); ?></h2>
	<p class="wporg-theme-settings__description">
		<strong><?php echo esc_html( $labels['type'] ); ?></strong>
		<?php echo esc_html( $labels['description'] ); ?>
	</p>
	<form data-wp-on--submit="actions.onSubmit" method="POST">
		<div class="wporg-theme-settings__url-field">
			<label for="wporg-theme-settings-url"><?php echo esc_html( $labels['form_label'] ); ?></label>
			<input
				id="wporg-theme-settings-url"
				aria-describedby="wporg-theme-settings__form-help"
				type="url"
				name="url"
				value="<?php echo esc_attr( $url ); ?>"
				data-wp-bind--value="context.url"
				data-wp-on--keydown="actions.onChange"
			/>
			<p class="wporg-theme-settings__form-help" id="wporg-theme-settings__form-help"><?php echo esc_html( $labels['form_help'] ); ?></p>
		</div>
		<div class="wporg-theme-settings__button wp-block-button is-small">
			<button
				class="wp-block-button__link wp-element-button"
				data-wp-bind--aria-disabled="state.isSubmitting"
			>
				<?php esc_html_e( 'Save', 'wporg-themes' ); ?>
			</button>
			<div aria-live="polite" aria-atomic="true">
				<span data-wp-text="state.resultMessage"></span>
			</div>
		</div>
	</form>
</div>
