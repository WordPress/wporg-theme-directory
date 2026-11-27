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
	$theme      = wporg_themes_theme_information( $theme_post->post_name );
	if ( isset( $theme->error ) ) {
		return '';
	}

	$languages = get_language_links( $theme->slug );

	if ( empty( $languages ) ) {
		return '';
	}

	$initial_limit = 10;
	if ( count( $languages ) <= $initial_limit ) {
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

	$visible_languages = array_slice( $languages, 0, $initial_limit, true );
	$hidden_languages  = array_slice( $languages, $initial_limit, null, true );

	/* translators: %d: Number of additional languages. */
	$show_more_label = sprintf( __( 'Show %d more', 'wporg-themes' ), count( $hidden_languages ) );
	$show_less_label = __( 'Show less', 'wporg-themes' );

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'data-wp-interactive' => 'wporg/themes/available-translations',
		)
	);

	$language_list = sprintf(
		'%1$s<span data-wp-bind--hidden="!context.isExpanded" hidden>, %2$s</span>.',
		implode( ', ', $visible_languages ),
		wp_sprintf( '%l', $hidden_languages )
	);

	$toggle_button = sprintf(
		' <button type="button" class="wp-block-wporg-theme-available-translations__toggle" data-wp-on--click="actions.toggle" data-wp-bind--aria-expanded="context.isExpanded" data-wp-text="state.toggleLabel">%s</button>',
		esc_html( $show_more_label )
	);

	return sprintf(
		'<div %1$s %2$s>%3$s%4$s</div>',
		$wrapper_attributes,
		wp_interactivity_data_wp_context(
			array(
				'isExpanded'    => false,
				'showMoreLabel' => $show_more_label,
				'showLessLabel' => $show_less_label,
			)
		),
		sprintf(
			// translators: %s is a list of links to the theme in each language.
			esc_html__( 'This theme is available in the following languages: %s', 'wporg-themes' ),
			$language_list
		),
		$toggle_button
	);
}

/**
 * Get an array of links to Rosetta sites where this theme is translated.
 */
function get_language_links( $slug ) {
	$locale_subdomain_assoc = get_all_locales_with_subdomain();
	$translated_locales     = get_translated_locales( 'theme', $slug );

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
