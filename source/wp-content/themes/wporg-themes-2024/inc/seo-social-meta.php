<?php
/**
 * Set up the SEO & social sharing meta tags.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\SEO_Social_Meta;

use function WordPressdotorg\Theme\Theme_Directory_2024\{ get_query_tags, get_tag_labels, get_theme_information };
use const WordPressdotorg\Theme\Theme_Directory_2024\THEME_POST_TYPE;

add_filter( 'document_title_parts', __NAMESPACE__ . '\set_document_title' );
add_filter( 'document_title_separator', __NAMESPACE__ . '\document_title_separator' );
add_action( 'jetpack_open_graph_tags', __NAMESPACE__ . '\add_social_meta_tags', 20 );
add_action( 'jetpack_seo_meta_tags', __NAMESPACE__ . '\add_seo_meta_tags', 20 );

add_filter( 'jetpack_enable_open_graph', '__return_true', 100 ); // Enable Jetpack Open Graph tags.
remove_action( 'wp_head', 'wporg_themes_add_meta_tags' ); // Remove the theme-plugin-added meta tags.

/**
 * Append an optimized site name.
 *
 * @param array $title {
 *     The document title parts.
 *
 *     @type string $title   Title of the viewed page.
 *     @type string $page    Optional. Page number if paginated.
 *     @type string $tagline Optional. Site description when on home page.
 *     @type string $site    Optional. Site title when not on home page.
 * }
 * @return array Filtered title parts.
 */
function set_document_title( $title ) {
	global $wp_query;

	$current_browse = $wp_query->query['browse'] ?? false;

	if ( is_front_page() && ! $current_browse ) {
		$title['title']   = __( 'WordPress Theme Directory', 'wporg-themes' );
		$title['tagline'] = __( 'WordPress.org', 'wporg-themes' );
	} else {
		if ( is_singular( THEME_POST_TYPE ) ) {
			if ( isset( $wp_query->query_vars['view'] ) ) {
				/* translators: Theme title */
				$title['title'] = sprintf( __( '%s Preview', 'wporg-themes' ), $title['title'] );
			}

			$title['title'] .= ' ' . document_title_separator() . ' ' . __( 'WordPress Theme', 'wporg-themes' );
		} elseif ( is_author() ) {
			/* translators: Author name */
			$title['title'] = sprintf( __( 'WordPress Themes by %s', 'wporg-themes' ), $title['title'] );
		} else {
			$title['title'] = get_archive_title();
		}

		// If results are paged and the max number of pages is known.
		if ( is_paged() && $wp_query->max_num_pages ) {
			$title['page'] = sprintf(
				// translators: 1: current page number, 2: total number of pages
				__( 'Page %1$s of %2$s', 'wporg-themes' ),
				get_query_var( 'paged' ),
				$wp_query->max_num_pages
			);
		}

		$title['site'] = __( 'WordPress.org', 'wporg-themes' );
	}

	return $title;
}

/**
 * Set the separator for the document title.
 *
 * @return string Document title separator.
 */
function document_title_separator() {
	return ( is_feed() ) ? '&#8212;' : '&#124;';
}

/**
 * Add meta tags for richer social media integrations.
 */
function add_social_meta_tags( $tags ) {
	$default_image = 'https://wordpress.org/files/2024/06/wporg-themes-ogimage-2024.png';
	$site_title = function_exists( '\WordPressdotorg\site_brand' ) ? \WordPressdotorg\site_brand() : 'WordPress.org';
	$description = __( 'Find the perfect theme for your WordPress website. Choose from thousands of stunning designs with a wide variety of features and customization options.', 'wporg-themes' );

	$tags['og:site_name']    = $site_title;
	$tags['og:title']        = __( 'WordPress Theme Directory', 'wporg-themes' );
	$tags['og:description']  = $description;
	$tags['og:image']        = esc_url( $default_image );
	$tags['og:image:alt']    = __( 'WordPress Theme Directory', 'wporg-themes' );
	$tags['og:locale']       = get_locale();
	$tags['twitter:card']    = 'summary_large_image';

	$current_browse = $wp_query->query['browse'] ?? false;

	if ( is_front_page() && ! $current_browse ) {
		return $tags;
	}

	$tags['og:title'] = get_archive_title();

	if ( is_author() ) {
		$tags['og:title'] = sprintf(
			/* translators: Author name */
			__( 'WordPress Themes by %s', 'wporg-themes' ),
			get_the_author()
		);
	} else if ( is_singular( THEME_POST_TYPE ) ) {
		$theme = get_theme_information();
		if ( ! $theme ) {
			return $tags;
		}

		$sep = document_title_separator();

		$tags['og:title']            = join( ' ', [ $theme->name, $sep, __( 'WordPress Theme Directory', 'wporg-themes' ) ] );
		$tags['twitter:text:title']  = join( ' ', [ $theme->name, $sep, __( 'WordPress Theme Directory', 'wporg-themes' ) ] );
		$tags['og:description']      = $theme->description;
		$tags['twitter:description'] = $theme->description;

		if ( $theme->screenshot_url ) {
			$tags['og:image']          = $theme->screenshot_url;
			$tags['og:image:alt']      = $theme->name;
			$tags['twitter:image']     = $theme->screenshot_url;
			$tags['twitter:image:alt'] = $theme->name;
		}
	}

	return $tags;
}


/**
 * Update the description meta value.
 */
function add_seo_meta_tags( $tags ) {
	$description = __( 'Find the perfect theme for your WordPress website. Choose from thousands of stunning designs with a wide variety of features and customization options.', 'wporg-themes' );
	$tags['description'] = $description;

	if ( is_singular( THEME_POST_TYPE ) ) {
		$theme = get_theme_information();
		if ( $theme ) {
			$tags['description'] = $theme->description;
		}
	}

	return $tags;
}

/**
 * Get a human-friendly title for the current view.
 *
 * @return string
 */
function get_archive_title() {
	global $wp_query;

	$author = isset( $wp_query->query['author_name'] ) ? get_user_by( 'slug', $wp_query->query['author_name'] ) : false;
	$current_browse = $wp_query->query['browse'] ?? false;
	$tags = get_query_tags();

	if ( is_front_page() && ! $current_browse && ! is_paged() ) {
		$title = 'WordPress Themes';
	} else if ( $author ) {
		// translators: %s Author name.
		$title = sprintf( __( 'Author: %s', 'wporg-themes' ), $author->display_name );
	} else if ( is_search() ) {
		$title = __( 'Search results', 'wporg-themes' );
	} else if ( ! empty( $tags ) ) {
		$labels = get_tag_labels( $tags );
		$title = wp_sprintf_l( '%l', $labels );
	} else if ( 'community' === $current_browse ) {
		$title = __( 'Community themes', 'wporg-themes' );
	} else if ( 'commercial' === $current_browse ) {
		$title = __( 'Commercial themes', 'wporg-themes' );
	} else if ( 'new' === $current_browse ) {
		$title = __( 'Latest themes', 'wporg-themes' );
	} else if ( 'updated' === $current_browse ) {
		$title = __( 'Recently updated themes', 'wporg-themes' );
	} else if ( 'favorites' === $current_browse ) {
		$title = __( 'My favorites', 'wporg-themes' );
	} else if ( is_page() ) {
		$title = get_the_title();
	} else {
		$title = __( 'All themes', 'wporg-themes' );
	}

	return $title;
}
