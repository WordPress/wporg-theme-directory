<?php
/**
 * Block Name: Downloads Chart
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Downloads_Block;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/theme-downloads',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);

	// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- third-party script.
	wp_register_script( 'google-charts-loader', 'https://www.gstatic.com/charts/loader.js', array( 'jquery' ), null, true );
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
	if ( isset( $theme->error ) ) {
		return '';
	}

	$attributes = array(
		'theme' => $theme->slug,
		'label-date' => __( 'Date', 'wporg-themes' ),
		'label-value' => __( 'Downloads', 'wporg-themes' ),
	);

	$attribute_markup = '';
	foreach ( $attributes as $key => $value ) {
		$attribute_markup .= sprintf( 'data-%s="%s" ', $key, esc_attr( $value ) );
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s %s></div>',
		$wrapper_attributes,
		$attribute_markup
	);
}
