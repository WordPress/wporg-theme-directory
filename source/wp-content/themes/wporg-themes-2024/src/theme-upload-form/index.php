<?php
/**
 * Block Name: Theme upload form
 * Description: The handler for uploading a theme.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Upload_Form;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/theme-upload-form',
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
	// Enqueue the notice style so the below markup is styled correctly.
	wp_enqueue_style( 'wporg-notice-style' );

	// Theme check results are echo'd directly, so catch all output.
	ob_start();
	echo do_shortcode( '[wporg-themes-upload]' );
	$form = ob_get_clean();

	// Replace default HTML with styled block components.
	$form  = preg_replace(
		'/(<div class=\'notice notice-error notice-large\'><ul>)(.+)(<\/ul><\/div>)/is',
		'<div class="wp-block-wporg-notice is-warning-notice"><div class="wp-block-wporg-notice__icon"></div><div class="wp-block-wporg-notice__content"><ul>$2</ul></div></div>',
		$form
	);
	$form  = preg_replace(
		'/(<div class=\'notice notice-warning notice-large\'><ul>)(.+)(<\/ul><\/div>)/is',
		'<div class="wp-block-wporg-notice is-info-notice"><div class="wp-block-wporg-notice__icon"></div><div class="wp-block-wporg-notice__content"><ul>$2</ul></div></div>',
		$form
	);
	$form  = preg_replace(
		'/<button id="upload_button" class="button"([^>]*)>(.+)<\/button>/is',
		'<div class="wp-block-button"><button id="upload_button" class="wp-block-button__link wp-element-button"$1>$2</button></div>',
		$form
	);

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		$form
	);
}
