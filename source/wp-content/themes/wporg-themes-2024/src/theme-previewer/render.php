<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\{ get_theme_patterns, get_theme_style_variations };

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

$theme_post = get_post( $block->context['postId'] );
$theme = wporg_themes_theme_information( $theme_post->post_name );

$url = $theme->preview_url ?? '';

// Get the device size from the query.
$devices = [ 'desktop', 'tablet', 'mobile' ];
$device = 'desktop';
if ( isset( $_REQUEST['device'] ) && in_array( $_REQUEST['device'], $devices ) ) {
	$device = $_REQUEST['device']; // phpcs:ignore -- exact match to a given string.
}

// Switch to using the pattern URL if a pattern is requested.
if ( isset( $_REQUEST['pattern_name'] ) ) {
	$show_pattern = wp_unslash( $_REQUEST['pattern_name'] ); // phpcs:ignore -- exact match to a given string.
	$patterns = get_theme_patterns( $theme_post->post_name );
	if ( $patterns ) {
		$matches = wp_list_filter( $patterns, [ 'name' => $show_pattern ] );
		if ( $matches ) {
			$url = current( $matches )->link;
		}
	}
}

// Add the style variation to the URL if one is selected.
if ( isset( $_REQUEST['style_variation'] ) ) {
	$show_style = wp_unslash( $_REQUEST['style_variation'] ); // phpcs:ignore -- exact match to a given string.
	$styles = get_theme_style_variations( $theme_post->post_name );
	if ( $styles ) {
		$matches = wp_list_filter( $styles, [ 'title' => $show_style ] );
		if ( $matches ) {
			$url = add_query_arg( 'style_variation', $show_style, $url );
		}
	}
}

$is_valid_url = $url && 'wp-themes.com' === wp_parse_url( $url, PHP_URL_HOST );

$classes = array();
$notice = '';

if ( ! $is_valid_url ) {
	$notice = '<!-- wp:wporg/notice {"type":"warning"} -->';
	$notice .= '<div class="wp-block-wporg-notice is-warning-notice">';
	$notice .= '<div class="wp-block-wporg-notice__icon"></div>';
	$notice .= '<div class="wp-block-wporg-notice__content"><p>';
	$notice .= sprintf(
		__( 'The preview could not be loaded.', 'wporg-themes' ),
	);
	$notice .= '</p></div></div>';
	$notice .= '<!-- /wp:wporg/notice -->';

	$classes[] = 'has-invalid-preview';
}

// Initial state to pass to Interactivity API.
$init_state = [
	'device' => $device,
	'isLoaded' => ! $is_valid_url,
];
$encoded_state = wp_json_encode( $init_state );

?>
<div
	<?php echo get_block_wrapper_attributes( [ 'class' => implode( ' ', $classes ) ] ); // phpcs:ignore ?>
	data-wp-interactive="wporg/themes/preview"
	data-wp-context="<?php echo esc_attr( $encoded_state ); ?>"
	data-wp-class--is-loaded="state.isLoaded"
>
	<?php echo do_blocks( $notice ); // phpcs:ignore ?>
	<?php if ( $is_valid_url ) : ?>
		<section class="wporg-theme-previewer__controls wp-block-buttons" aria-label="<?php esc_attr_e( 'Preview width', 'wporg-themes' ); ?>">
			<div class="wp-block-button is-style-toggle is-small">
				<button
					class="wp-block-button__link wp-element-button"
					data-wp-bind--aria-pressed="state.isDeviceDesktop"
					data-wp-on--click="actions.onDeviceChange"
					data-device="desktop"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" aria-hidden="true" focusable="false"><path d="M20.5 16h-.7V8c0-1.1-.9-2-2-2H6.2c-1.1 0-2 .9-2 2v8h-.7c-.8 0-1.5.7-1.5 1.5h20c0-.8-.7-1.5-1.5-1.5zM5.7 8c0-.3.2-.5.5-.5h11.6c.3 0 .5.2.5.5v7.6H5.7V8z"></path></svg>
					<span><?php echo esc_attr_x( 'Wide', 'theme preview size toggle', 'wporg-themes' ); ?></span>
				</button>
			</div>
			<div class="wp-block-button is-style-toggle is-small">
				<button
					class="wp-block-button__link wp-element-button"
					data-wp-bind--aria-pressed="state.isDeviceTablet"
					data-wp-on--click="actions.onDeviceChange"
					data-device="tablet"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" aria-hidden="true" focusable="false"><path d="M17 4H7c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H7c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v12zm-7.5-.5h4V16h-4v1.5z"></path></svg>
					<span><?php echo esc_attr_x( 'Medium', 'theme preview size toggle', 'wporg-themes' ); ?></span>
				</button>
			</div>
			<div class="wp-block-button is-style-toggle is-small">
				<button
					class="wp-block-button__link wp-element-button"
					data-wp-bind--aria-pressed="state.isDeviceMobile"
					data-wp-on--click="actions.onDeviceChange"
					data-device="mobile"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" aria-hidden="true" focusable="false"><path d="M15 4H9c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h6c.3 0 .5.2.5.5v12zm-4.5-.5h2V16h-2v1.5z"></path></svg>
					<span><?php echo esc_attr_x( 'Narrow', 'theme preview size toggle', 'wporg-themes' ); ?></span>
				</button>
			</div>
		</section>
		<iframe
			src="<?php echo esc_url_raw( $url ); ?>"
			data-wp-style--width="state.iframeWidthCSS"
			data-wp-style--height="state.iframeHeightCSS"
			data-wp-on--load="actions.onLoad"
		/>
	<?php endif; ?>
</div>
