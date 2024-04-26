<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\get_theme_style_variations;
use function WordPressdotorg\Theme\Theme_Directory_2024\Theme_Style_Variations\get_style_variation_card;

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

$theme_post = get_post( $block->context['postId'] );
$styles = get_theme_style_variations( $theme_post->post_name );

if ( ! count( $styles ) ) {
	return '';
}

?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>>
	<div class="wporg-theme-style-variations__row">
		<?php
		foreach ( $styles as $i => $style ) {
			$style->preview_base = untrailingslashit( get_permalink( $theme_post ) ) . '/preview/';
			echo get_style_variation_card( $style ); // phpcs:ignore
		}
		?>
	</div>
</div>
