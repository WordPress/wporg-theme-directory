<?php
/**
 * Set up some helper API endpoints.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\I18N;

use WP_Post;
use function WordPressdotorg\Theme\Theme_Directory_2024\wporg_themes_get_feature_list;

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

	$theme_post = get_post( $_post );
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
 * Translate tag names into the current site locale.
 *
 * Only tags are visible on the frontend. We can ignore categories,
 * and `theme_business_model` is never output as terms.
 *
 * @param WP_Term $term The WP_Term object being loaded.
 */
function translate_term( $term ) {
	if ( is_admin() || 'post_tag' !== $term->taxonomy ) {
		return $term;
	}

	// Get the feature list, collapsed into a one-dimensional array.
	$features = wporg_themes_get_feature_list( 'all' );
	$features = array_merge( ...array_values( $features ) );

	if ( isset( $features[ $term->slug ] ) ) {
		$term->name = $features[ $term->slug ];
	}

	return $term;
}
