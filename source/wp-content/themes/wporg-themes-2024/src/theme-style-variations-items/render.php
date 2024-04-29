<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\get_theme_style_variations;
use function WordPressdotorg\Theme\Theme_Directory_2024\Theme_Style_Variations_Items\get_style_variation_card;

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

$theme_post = get_post( $block->context['postId'] );
$styles = get_theme_style_variations( $theme_post->post_name );
$count = count( $styles );

if ( ! $count ) {
	return '';
}

$label = sprintf(
	/* translators: Heading for style variations, %s is the number of styles. */
	_n( 'Style variations (%s)', 'Style variations (%s)', $count, 'wporg-themes' ),
	$count
);

?>
<div
	data-wp-interactive="wporg/themes/style-variations"
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
>
	<div class="wporg-theme-style-variations__heading">
		<h2><?php echo esc_html( $label ); ?></h2>
	</div>

	<div class="wporg-theme-style-variations__grid">
		<?php
		foreach ( $styles as $i => $style ) {
			$style->preview_base = untrailingslashit( get_permalink( $theme_post ) ) . '/preview/';
			echo get_style_variation_card( $style ); // phpcs:ignore
		}
		?>
	</div>
</div>
