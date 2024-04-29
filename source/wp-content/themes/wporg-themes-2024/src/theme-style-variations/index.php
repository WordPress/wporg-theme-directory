<?php
/**
 * Block Name: Theme style variations
 * Description: A list of style variations provided by this theme (with screenshots).
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Style_Variations;

use WP_HTML_Tag_Processor;
use function WordPressdotorg\Theme\Theme_Directory_2024\get_theme_style_variations;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type( dirname( dirname( __DIR__ ) ) . '/build/theme-style-variations' );
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
	$html->set_attribute( 'data-wp-on--click', 'wporg/themes/style-variations::actions.onStyleClick' );
	$html->set_attribute( 'data-style', strtolower( $style->title ) );

	return $html->get_updated_html();
}

/**
 * Get the full-sized images for each style variation, hide everything but the currently-selected item.
 */
function get_theme_preview_images( $theme_post ) {
	$styles = get_theme_style_variations( $theme_post->post_name );
	$output = '';

	foreach ( $styles as $style ) {
		if ( ! str_contains( $style->link, 'style_variation' ) ) {
			$block_markup = do_blocks( '<!-- wp:post-featured-image {"style":{"border":{"radius":"3px","style":"solid","width":"1px"}},"borderColor":"black-opacity-15"} /-->' );
		} else {
			$args = array(
				'src' => $style->link,
				'fullPage' => false,
			);
			$block_markup = do_blocks( sprintf( '<!-- wp:wporg/screenshot-preview %s /-->', wp_json_encode( $args ) ) );
		}

		$output .= '<div data-wp-bind--hidden="state.isHidden" data-wp-context=\'{"style":"' . strtolower( $style->title ) . '"}\'>';
		$output .= $block_markup;
		$output .= '</div>';
	}

	return $output;
}
