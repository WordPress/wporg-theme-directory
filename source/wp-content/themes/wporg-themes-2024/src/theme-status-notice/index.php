<?php
/**
 * Block Name: Theme Status Notice
 * Description: A notice to identify out-of-date themes.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Status_Notice_Block;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/theme-status-notice',
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
	if ( ! $theme_post || ! ( $theme_post instanceof \WP_Post ) ) {
		return '';
	}

	$theme = wporg_themes_theme_information( $theme_post->post_name );
	if ( ! $theme || isset( $theme->error ) || empty( $theme->last_updated ) ) {
		return '';
	}

	if ( time() - strtotime( $theme->last_updated ) <= 2 * YEAR_IN_SECONDS ) {
		return '';
	}

	$markup = '<!-- wp:wporg/notice {"type":"alert"} -->';
	$markup .= '<div class="wp-block-wporg-notice is-alert-notice">';
	$markup .= '<div class="wp-block-wporg-notice__icon"></div>';
	$markup .= '<div class="wp-block-wporg-notice__content"><p>';
	$markup .= __( 'This theme <strong>hasn&#146;t been updated in over 2 years</strong>. It may no longer be maintained or supported and may have compatibility issues when used with more recent versions of WordPress.', 'wporg-themes' );
	$markup .= '</p></div></div>';
	$markup .= '<!-- /wp:wporg/notice -->';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		do_blocks( $markup ),
	);
}
