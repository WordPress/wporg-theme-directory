<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\get_theme_patterns;
use function WordPressdotorg\Theme\Theme_Directory_2024\Theme_Patterns\get_pattern_preview_block;

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

$show_all = $attributes['showAll'] ?? false;

$theme_post = get_post( $block->context['postId'] );
$theme = wporg_themes_theme_information( $theme_post->post_name );

$patterns = get_theme_patterns( $theme_post->post_name );
$pattern_count = count( $patterns );
$initial_count = $show_all ? $pattern_count : 6;

if ( ! $pattern_count ) {
	return '';
}

$selected_index = -1;
if ( isset( $_GET['pattern_name'] ) ) {
	foreach ( $patterns as $i => $pattern ) {
		if ( $pattern->name === $_GET['pattern_name'] ) {
			$selected_index = $i;
			break;
		}
	}
}

// Initial state to pass to JS (*not* Interactivty API).
$init_state = [
	'hideOverflow' => true,
	'allowUnselect' => true,
	'initialCount' => $initial_count,
	'totalCount' => $pattern_count,
	'initialSelected' => $selected_index,
];
$encoded_state = wp_json_encode( $init_state );
?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
	data-initial-state="<?php echo esc_attr( $encoded_state ); ?>"
>
	<h2 id="wporg-theme-patterns-heading" class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Patterns', 'wporg-themes' ); ?></h2>

	<ul
		tabindex="0"
		role="listbox"
		aria-labelledby="wporg-theme-patterns-heading"
		class="wporg-theme-patterns__grid wporg-theme-listbox"
	>
		<?php
		foreach ( $patterns as $i => $pattern ) {
			echo get_pattern_preview_block( $pattern, $i >= $initial_count, $i === $selected_index ); // phpcs:ignore
		}
		?>
	</ul>

	<?php if ( $pattern_count > $initial_count ) : ?>
	<div class="wporg-theme-patterns__button wp-block-button is-style-outline is-small">
		<button class="wp-block-button__link wp-element-button">
			<?php esc_html_e( 'Show all patterns', 'wporg-themes' ); ?>
		</button>
	</div>
	<?php endif; ?>
</div>
