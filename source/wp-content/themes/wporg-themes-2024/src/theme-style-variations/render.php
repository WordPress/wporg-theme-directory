<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\get_theme_style_variations;
use function WordPressdotorg\Theme\Theme_Directory_2024\Theme_Style_Variations\{get_style_variation_card, get_theme_preview_images};

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

$theme_post = get_post( $block->context['postId'] );
$styles = get_theme_style_variations( $theme_post->post_name );
$count = count( $styles );

if ( ! $count ) {
	// phpcs:ignore
	echo do_blocks( '<!-- wp:post-featured-image {"style":{"border":{"radius":"3px","style":"solid","width":"1px"}},"borderColor":"black-opacity-15"} /-->' );
	return;
}

$label = sprintf(
	/* translators: Heading for style variations, %s is the number of styles. */
	_n( 'Style variations (%s)', 'Style variations (%s)', $count, 'wporg-themes' ),
	$count
);

// Initial state to pass to Interactivity API.
$init_state = [
	'isRTL' => is_rtl(),
	'selected' => 'default',
];
$encoded_state = wp_json_encode( $init_state );

?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
	data-wp-interactive="wporg/themes/style-variations"
	data-wp-context="<?php echo esc_attr( $encoded_state ); ?>"
	data-wp-init="actions.init"
	data-wp-on-window--resize="actions.init"
>
	<div class="wporg-theme-style-variations__screenshot">
		<?php echo get_theme_preview_images( $theme_post ); // phpcs:ignore ?>
	</div>

	<div class="wporg-theme-style-variations__heading">
		<h2><?php echo esc_html( $label ); ?></h2>

		<div
			class="wporg-theme-style-variations__buttons wp-block-buttons"
			data-wp-bind--hidden="!state.hasOverscroll"
		>
			<div class="wp-block-button is-style-toggle is-small">
				<button
					tabindex="-1"
					class="wp-block-button__link wp-element-button"
					aria-label="Prev"
					data-wp-on--click="actions.handlePrevious"
					data-wp-bind--disabled="!state.canPrevious"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M20 11.2H6.8l3.7-3.7-1-1L3.9 12l5.6 5.5 1-1-3.7-3.7H20z"></path></svg>
				</button>
			</div>
			<div class="wp-block-button is-style-toggle is-small">
				<button
					tabindex="-1"
					class="wp-block-button__link wp-element-button"
					aria-label="Next"
					data-wp-on--click="actions.handleNext"
					data-wp-bind--disabled="!state.canNext"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="m14.5 6.5-1 1 3.7 3.7H4v1.6h13.2l-3.7 3.7 1 1 5.6-5.5z"></path></svg>
				</button>
			</div>
		</div>
	</div>

	<div
		class="wporg-theme-style-variations__row"
		data-wp-on--scroll="actions.handleScroll"
	>
		<?php
		foreach ( $styles as $i => $style ) {
			$style->preview_base = untrailingslashit( get_permalink( $theme_post ) ) . '/preview/';
			echo get_style_variation_card( $style ); // phpcs:ignore
		}
		?>
	</div>
</div>
