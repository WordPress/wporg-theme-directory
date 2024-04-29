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
function get_style_variation_card( $style ) {
	$preview_link = $style->preview_base;
	if ( str_contains( $style->link, 'style_variation' ) ) {
		$preview_link = add_query_arg( 'style_variation', $style->title, $style->preview_base );
	}

	$args = array(
		'src' => $style->preview_link,
		// translators: %s pattern name.
		'alt' => sprintf( __( 'Style: %s', 'wporg-themes' ), $style->title ),
		'href' => $preview_link,
		'width' => 100,
		'viewportWidth' => 1180,
		'viewportHeight' => 740,
		'fullPage' => false,
	);
	$block_markup = do_blocks( sprintf( '<!-- wp:wporg/screenshot-preview %s /-->', wp_json_encode( $args ) ) );

	$html = new WP_HTML_Tag_Processor( $block_markup );
	$html->next_tag( 'a' );
	$html->set_attribute( 'data-wp-on--click', 'wporg/themes/preview::actions.setIframeUrl' );

	return $html->get_updated_html();
}
