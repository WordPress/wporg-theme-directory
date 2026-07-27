<?php
/**
 * Block Name: Business Model Notice
 * Description: A notice to identify commercial or community themes.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Business_Model_Notice_Block;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/business-model-notice',
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

	$is_commercial = has_term( 'commercial', 'theme_business_model', $theme_post );
	$is_community = has_term( 'community', 'theme_business_model', $theme_post );

	if ( ! $is_commercial && ! $is_community ) {
		return '';
	}

	$heading = '';
	$content = '';
	$url = '';
	$link_text = '';
	$markup = '';

	if ( $is_commercial ) {
		$heading = __( 'Commercial theme', 'wporg-themes' );
		$content = __( 'This theme is free but offers additional paid commercial upgrades or support.', 'wporg-themes' );
		$url = get_post_meta( $theme_post->ID, 'external_support_url', true );
		$link_text = __( 'View support', 'wporg-themes' );
	} else {
		$heading = __( 'Community theme', 'wporg-themes' );
		$content = __( 'This theme is developed and supported by a community.', 'wporg-themes' );
		$url = get_post_meta( $theme_post->ID, 'external_repository_url', true );
		$link_text = __( 'Contribute to this theme', 'wporg-themes' );
	}

	$markup .= '<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"large","fontFamily":"inter"} --><h2 class="wp-block-heading has-inter-font-family has-large-font-size" style="margin-top:0;margin-bottom:0;font-style:normal;font-weight:600">';
	$markup .= esc_html( $heading );
	$markup .= '</h2><!-- /wp:heading -->';

	$markup .= '<!-- wp:paragraph --><p>';
	$markup .= esc_html( $content );
	if ( $url ) {
		$markup .= ' <a href="' . esc_url( $url ) . '" class="external-link" rel="nofollow noopener">' . esc_html( $link_text ) . '</a>';
	}
	$markup .= '</p><!-- /wp:paragraph -->';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		do_blocks( $markup ),
	);
}
