<?php
/**
 * Block Name: Available translations
 * Description: A list of the available translations for this theme.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Theme_Available_Translations_Block;

use function WordPressdotorg\MU_Plugins\Helpers\Locale\{ get_all_locales_with_subdomain, get_translated_locales };

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/theme-available-translations',
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
	if ( ! $block->context['postId'] || ! defined( 'GLOTPRESS_LOCALES_PATH' ) ) {
		return '';
	}

	$theme_post = get_post( $block->context['postId'] );
	$theme = wporg_themes_theme_information( $theme_post->post_name );
	if ( isset( $theme->error ) ) {
		return '';
	}

	$languages = get_language_links( $theme->slug );

	if ( empty( $languages ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		sprintf(
			// translators: %s is a list of links to the theme in each language.
			esc_html__( 'This theme is available in the following languages: %s', 'wporg-themes' ),
			wp_sprintf( '%l.', $languages )
		)
	);
}

/**
 * Get an array of links to Rosetta sites where this theme is translated.
 */
function get_language_links( $slug ) {
	$locale_subdomain_assoc = get_all_locales_with_subdomain();
	$translated_locales = get_translated_locales( 'theme', $slug );

	$available_languages = array();
	foreach ( $translated_locales as $locale ) {
		if ( isset( $locale_subdomain_assoc[ $locale ] ) ) {
			$language = \GP_Locales::by_field( 'wp_locale', $locale );

			$available_languages[ $locale ] = sprintf(
				'<a href="https://%1$s.wordpress.org%2$s" lang="%3$s">%4$s</a>',
				$locale_subdomain_assoc[ $locale ]->subdomain,
				esc_url( trailingslashit( get_site()->path . $slug ) ),
				$language->slug,
				$language->native_name
			);
		}
	}

	// Add in an "English (US)" link back to the main site.
	if ( $available_languages || 'en_US' !== get_locale() ) {
		$available_languages['en_US'] = sprintf(
			'<a href="%1$s" lang="en-US">%2$s</a>',
			esc_url( trailingslashit( 'https://wordpress.org/themes/' . $slug ) ),
			'English (US)', // Not translated, locale name is in native language.
		);
	}

	ksort( $available_languages, SORT_NATURAL );

	return $available_languages;
}
