<?php
/**
 * Block Name: Child Theme Notice
 * Description: A notice to identify child themes.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Child_Theme_Notice_Block;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/child-theme-notice',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {
	if ( ! $block->context['postId'] ) {
		return '';
	}

	$theme_post = get_post( $block->context['postId'] );
	$theme = wporg_themes_theme_information( $theme_post->post_name );

	if ( empty( $theme->parent ) ) {
		return '';
	}

	$markup = '<!-- wp:wporg/notice {"type":"info"} -->';
	$markup .= '<div class="wp-block-wporg-notice is-info-notice">';
	$markup .= '<div class="wp-block-wporg-notice__icon"></div>';
	$markup .= '<div class="wp-block-wporg-notice__content"><p>';
	$markup .= sprintf(
		// translators: %s: parent theme name.
		__( 'This is a child theme of %s.', 'wporg-themes' ),
		sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( home_url( $theme->parent['slug'] . '/' ) ),
			esc_html( $theme->parent['name'] )
		)
	);
	$markup .= '</p></div></div>';
	$markup .= '<!-- /wp:wporg/notice -->';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		do_blocks( $markup ),
	);
}
