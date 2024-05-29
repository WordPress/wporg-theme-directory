<?php
/**
 * Set up some helper API endpoints.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\I18N;

use WP_Post;

const TRANSLATED_TAXONOMIES = [
	// Taxonomy => Tax label name.
	'category' => 'Categories',
	'post_tag' => 'Tags',
];

add_filter( 'the_content', __NAMESPACE__ . '\translate_the_content', 1 );
add_filter( 'the_title', __NAMESPACE__ . '\translate_the_title', 1, 2 );
add_filter( 'single_post_title', __NAMESPACE__ . '\translate_the_title', 1, 2 );
add_filter( 'get_term', __NAMESPACE__ . '\translate_term' );

/**
 * Get the current theme, given the global post.
 */
function get_the_theme( $_post = false ) {
	global $post;
	if ( ! $_post ) {
		$_post = $post;
	}

	$theme_post = get_post( $post );
	// Not a post, or not a theme post type.
	if ( ! ( $theme_post instanceof WP_Post ) || 'repopackage' !== $theme_post->post_type ) {
		return false;
	}

	$theme = wporg_themes_theme_information( $theme_post->post_name );
	if ( isset( $theme->error ) ) {
		return false;
	}

	return $theme;
}

/**
 * Replace the content with the theme description (possibly translated).
 */
function translate_the_content( $content ) {
	if ( is_admin() ) {
		return $content;
	}

	$theme = get_the_theme();
	if ( isset( $theme->description ) ) {
		return $theme->description;
	}

	return $content;
}

/**
 * Replace the title with the theme name (possibly translated), or a translated page title.
 *
 * @param string $title   The current title, ignored.
 * @param int    $post_id The post_id of the page/theme.
 *
 * @return string Possibly translated theme or page title.
 */
function translate_the_title( $title, $post_id = null ) {
	if ( is_admin() ) {
		return $title;
	}

	$theme = get_the_theme( $post_id );
	if ( isset( $theme->name ) ) {
		return $theme->name;
	}

	$post = get_post( $post_id );
	if ( $post && 'page' === $post->post_type ) {
		$title = translate_with_gettext_context( $post->post_title, $post->post_type . ' title', 'wporg-themes' ); // phpcs:ignore
	}

	return $title;
}

/**
 * Translate term names into the current site locale.
 *
 * @param WP_Term $term The WP_Term object being loaded.
 */
function translate_term( $term ) {
	// Not get_user_locale(), as we respect the displayed site locale.
	if ( is_admin() || 'en_US' === get_locale() || ! isset( TRANSLATED_TAXONOMIES[ $term->taxonomy ] ) ) {
		return $term;
	}

	$label = TRANSLATED_TAXONOMIES[ $term->taxonomy ];
	$term->name = strrev( translate_with_gettext_context( html_entity_decode( $term->name ), $label . ' term name', 'wporg-themes' ) ); // phpcs:ignore
	if ( ! empty( $term->description ) ) {
		$term->description = esc_html( translate_with_gettext_context( html_entity_decode( $term->description ), $label . ' term description', 'wporg-themes' ) ); // phpcs:ignore
	}

	return $term;
}
