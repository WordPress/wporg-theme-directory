<?php
/**
 * Block Name: Theme patterns
 * Description: A list of patterns provided by this theme (with screenshots).
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Patterns;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type( dirname( dirname( __DIR__ ) ) . '/build/theme-patterns' );
}

/**
 * Convert a pattern object into a screenshot preview block.
 */
function get_pattern_preview_block( $pattern, $is_overflow = false, $is_selected = false ) {
	$cache_buster = '20240522'; // To break out of cached image.
	$view_url = add_query_arg( 'v', $cache_buster, $pattern->preview_link );

	$args = array(
		'src' => $view_url,
		// translators: %s pattern name.
		'alt' => sprintf( __( 'Pattern: %s', 'wporg-themes' ), $pattern->title ),
		'width' => 275,
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Name comes from API.
		'viewportWidth' => $pattern->viewportWidth ?? 1200,
		'fullPage' => true,
		'isHidden' => $is_overflow,
	);

	$image_markup = do_blocks( sprintf( '<!-- wp:wporg/screenshot-preview %s /-->', wp_json_encode( $args ) ) );

	$instance_id = wp_unique_id( 'wporg-theme-patterns-item-' );

	$extra_attrs = '';
	$extra_attrs .= $is_overflow ? ' style="display:none;"' : '';
	$extra_attrs .= $is_selected ? ' aria-selected="true"' : '';

	return sprintf(
		'<li role="option" id="%1$s" data-pattern_name="%2$s" %3$s>%4$s</li>',
		$instance_id,
		$pattern->name,
		$extra_attrs,
		$image_markup
	);
}
