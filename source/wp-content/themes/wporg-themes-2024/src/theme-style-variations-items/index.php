<?php
/**
 * Block Name: Theme style variations (items)
 * Description: A list of style variations provided by this theme (with demo screenshots), variations only, grid-style.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Style_Variations_Items;

use WP_HTML_Tag_Processor;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type( dirname( dirname( __DIR__ ) ) . '/build/theme-style-variations-items' );
}

/**
 * Convert a style object into a screenshot preview block.
 */
function get_style_variation_card( $style, $is_selected = false ) {
	$args = array(
		'src' => $style->preview_link,
		// translators: %s pattern name.
		'alt' => sprintf( __( 'Style: %s', 'wporg-themes' ), $style->title ),
		'width' => 100,
		'viewportWidth' => 1180,
		'viewportHeight' => 740,
		'fullPage' => false,
	);
	$block_markup = do_blocks( sprintf( '<!-- wp:wporg/screenshot-preview %s /-->', wp_json_encode( $args ) ) );

	$instance_id = wp_unique_id( 'wporg-theme-style-var-item-' );

	$extra_attrs = '';
	$extra_attrs .= $is_selected ? ' aria-selected="true"' : '';

	return sprintf(
		'<li role="option" id="%1$s" data-style_variation="%2$s" %3$s>%4$s</li>',
		$instance_id,
		$style->title,
		$extra_attrs,
		$block_markup
	);
}
