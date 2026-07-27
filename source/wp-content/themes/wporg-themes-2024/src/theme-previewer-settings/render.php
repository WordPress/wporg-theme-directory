<?php

if ( ! $block->context['postId'] ) {
	return '';
}

$theme_post = get_post( $block->context['postId'] );

// Not blueprint enabled, or the user is not an owner/admin.
if (
	(
		! $theme_post->preview_blueprint &&
		empty( $_GET['playground-preview'] )
	) || (
		! current_user_can( 'edit_post', $theme_post->ID ) &&
		get_current_user_id() != $theme_post->post_author // Note: Loose comparison; int != string.
	)
) {
	return;
}

// Enqueue this script, so that it's available for the interactivity view script.
wp_enqueue_script( 'wp-api-fetch' );

// Default blueprint is just install the theme & login.
$blueprint = [
	'steps' => [
		[
			'step' => 'installTheme',
			'themeZipFile' => [
				'resource' => 'wordpress.org/themes',
				'slug'     => $theme_post->post_name,
			],
		],
		[
			'step'     => 'login',
			'username' => 'admin',
			'password' => 'password',
		],
	],
];

if ( $theme_post->preview_blueprint ) {
	$blueprint = $theme_post->preview_blueprint;
}

$blueprint = wp_json_encode( $blueprint, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

// Initial state to pass to Interactivity API.
$init_state = [
	'slug'      => $theme_post->post_name,
	'blueprint' => $blueprint,
	'labels'    => [
		'invalid' => __( 'Invalid Blueprint provided, verify the JSON validates.', 'wporg-themes' ),
		'success' => __( 'Blueprint saved correctly!', 'wporg-themes' ),
		'error'   => __( 'Error updating the Blueprint. Please try again.', 'wporg-themes' ),
	],
];
$encoded_state = wp_json_encode( $init_state );

?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
	data-wp-interactive="wporg/themes/theme-previewer-settings"
	data-wp-context="<?php echo esc_attr( $encoded_state ); ?>"
>
	<h2><?php esc_html_e( 'Theme Preview options', 'wporg-themes' ); ?></h2>
	<p class="wporg-theme-settings__description">
		This is a work in progress, and not currently fully supported.<br>
		Provide a Playground Blueprint to of your theme to be used for previews.<br>
		The <a href="https://playground.wordpress.net/builder/builder.html#<?php echo urlencode( $blueprint ); ?>">Blueprint Builder</a> can be used to validate your JSON.
	</p>
	<form data-wp-on--submit="actions.onSubmit" method="POST">
		<div class="wporg-theme-settings__blueprint-field">
			<textarea
				id="wporg-theme-settings-blueprint"
				aria-describedby="wporg-theme-settings__blueprint-help"
				name="blueprint"
				rows="10"
				data-wp-text="context.blueprint"
				data-wp-on--keydown="actions.onChange"
			><?php esc_textarea( $blueprint ); ?></textarea>
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
