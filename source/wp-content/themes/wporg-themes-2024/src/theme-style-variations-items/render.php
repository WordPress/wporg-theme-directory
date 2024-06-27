<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\get_theme_style_variations;
use function WordPressdotorg\Theme\Theme_Directory_2024\Theme_Style_Variations_Items\get_style_variation_card;

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

$theme_post = get_post( $block->context['postId'] );
$styles = get_theme_style_variations( $theme_post );
$count = $styles ? count( $styles ) : 0;

if ( ! $count ) {
	return '';
}

$selected_index = 0;
if ( isset( $_GET['style_variation'] ) ) {
	foreach ( $styles as $i => $style ) {
		if ( $style->title === $_GET['style_variation'] ) {
			$selected_index = $i;
			break;
		}
	}
}

// Initial state to pass to JS (*not* Interactivty API).
$init_state = [
	'hideOverflow' => false,
	'initialCount' => $count,
	'totalCount' => $count,
	'initialSelected' => $selected_index,
];
$encoded_state = wp_json_encode( $init_state );

?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
	data-initial-state="<?php echo esc_attr( $encoded_state ); ?>"
>
	<div class="wporg-theme-style-variations__heading">
		<h2 id="wporg-theme-style-variations-heading"><?php esc_html_e( 'Style variations', 'wporg-themes' ); ?></h2>
	</div>

	<ul
		tabindex="0"
		role="listbox"
		aria-labelledby="wporg-theme-style-variations-heading"
		class="wporg-theme-style-variations__grid wporg-theme-listbox"
	>
		<?php
		foreach ( $styles as $i => $style ) {
			echo get_style_variation_card( $style, $i === $selected_index ); // phpcs:ignore
		}
		?>
	</ul>
</div>
