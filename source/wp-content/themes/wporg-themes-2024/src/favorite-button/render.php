<?php

if ( ! $block->context['postId'] ) {
	return '';
}

$theme_post = get_post( $block->context['postId'] );

$user_id = get_current_user_id();

// Only show for logged in users.
if ( ! $user_id ) {
	return '';
}

// Manually enqueue this script, so that it's available for the interactivity view script.
wp_enqueue_script( 'wp-api-fetch' );
wp_enqueue_script( 'wp-a11y' );

$is_favorite = wporg_themes_is_favourited( $theme_post->post_name );

$classes = $is_favorite ? 'is-favorite' : '';

$add_label = __( 'Add to favorites', 'wporg-themes' );
$remove_label = __( 'Remove from favorites', 'wporg-themes' );
$favorited_label = __( 'Favorited', 'wporg-themes' );
$unfavorited_label = __( 'Removed from favorites', 'wporg-themes' );

// Initial state to pass to Interactivity API.
$init_state = [
	'themeSlug' => $theme_post->post_name,
	'isFavorite' => $is_favorite,
	'label' => [
		'add' => $add_label,
		'remove' => $remove_label,
		'favorited' => $favorited_label,
		'unfavorited' => $unfavorited_label,
	],
];
$encoded_state = wp_json_encode( $init_state );

?>
<div
	<?php echo get_block_wrapper_attributes( [ 'class' => $classes ] ); // phpcs:ignore ?>
	data-wp-interactive="wporg/themes/favorite-button"
	data-wp-context="<?php echo esc_attr( $encoded_state ); ?>"
	data-wp-class--is-favorite="context.isFavorite"
>
	<button
		class="wporg-favorite-button__button"
		disabled="disabled"
		data-wp-bind--disabled="!context.themeSlug"
		data-wp-on--click="actions.triggerAction"
	>
		<svg class="is-heart-filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="24" height="24" aria-hidden="true" focusable="false">
			<path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"/>
		</svg>
		<svg class="is-heart-outline" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="24" height="24" aria-hidden="true" focusable="false">
			<path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z"/>
		</svg>
		<span class="wporg-favorite-button__label screen-reader-text" data-wp-text="state.labelAction">
			<?php echo esc_html( $is_favorite ? $remove_label : $add_label ); ?>
		</span>
	</button>
</div>
