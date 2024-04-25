<?php

namespace WordPressdotorg\Theme\Theme_Directory_2024;

require_once( __DIR__ . '/inc/block-bindings.php' );
require_once( __DIR__ . '/inc/block-config.php' );

// Block files
require_once( __DIR__ . '/src/business-model-notice/index.php' );
require_once( __DIR__ . '/src/child-theme-notice/index.php' );
require_once( __DIR__ . '/src/ratings-bars/index.php' );
require_once( __DIR__ . '/src/ratings-stars/index.php' );
require_once( __DIR__ . '/src/theme-patterns/index.php' );
require_once( __DIR__ . '/src/theme-previewer/index.php' );

/**
 * Actions and filters.
 */
add_action( 'init', __NAMESPACE__ . '\fix_term_imports' );
add_action( 'init', __NAMESPACE__ . '\add_preview_endpoint' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );
add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\post_thumbnail_html', 10, 5 );
add_action( 'body_class', __NAMESPACE__ . '\add_extra_body_class' );
add_filter( 'frontpage_template_hierarchy', __NAMESPACE__ . '\use_archive_template_paged' );
add_action( 'single_template_hierarchy', __NAMESPACE__ . '\load_theme_preview' );

// Remove filters added by plugin.
remove_filter( 'post_thumbnail_html', 'wporg_themes_post_thumbnail_html', 10, 5 );

// Hide admin bar on preview pages.
add_filter(
	'show_admin_bar',
	function( $should_show ) {
		global $wp_query;

		if ( isset( $wp_query->query_vars['view'] ) ) {
			return false;
		}

		return $should_show;
	},
	2000
);

/**
 * Temporary fix for permission problem during local install.
 */
function fix_term_imports() {
	if ( defined( 'WP_CLI' ) ) {
		remove_filter( 'pre_insert_term', 'wporg_themes_pre_insert_term' );
	}
}

/**
 * Set up the `view` endpoint.
 */
function add_preview_endpoint() {
	add_rewrite_endpoint( 'preview', EP_PERMALINK, 'view' );
}

/**
 * Enqueue scripts and styles.
 */
function enqueue_assets() {
	// The parent style is registered as `wporg-parent-2021-style`, and will be loaded unless
	// explicitly unregistered. We can load any child-theme overrides by declaring the parent
	// stylesheet as a dependency.
	wp_enqueue_style(
		'wporg-theme-directory-2024-style',
		get_stylesheet_uri(),
		array( 'wporg-parent-2021-style', 'wporg-global-fonts' ),
		filemtime( __DIR__ . '/style.css' )
	);
}

/**
 * Use theme screenshot for post thumbnails, add attributes to image tag.
 *
 * @param string   $html
 * @param int      $post_id
 * @param int      $post_thumbnail_id
 * @param string   $size
 * @param string[] $attr
 *
 * @return string
 */
function post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	$current_post = get_post( $post_id );
	if ( 'repopackage' == $current_post->post_type ) {
		$theme = new \WPORG_Themes_Repo_Package( $current_post );
		$src   = add_query_arg(
			array(
				'w' => $size,
				'strip' => 'all',
			),
			$theme->screenshot_url()
		);

		$html = '<img src="' . esc_url( $src ) . '"';

		if ( is_array( $attr ) ) {
			$attr['alt'] = '';

			foreach ( $attr as $name => $value ) {
				$html .= " $name=" . '"' . $value . '"';
			}
		}

		$html .= ' />';
	}

	return $html;
}

/**
 * Add some custom classes to `body`.
 *
 * @param string[] $classes   An array of body class names.
 */
function add_extra_body_class( $classes ) {
	global $wp_query;

	if ( isset( $wp_query->query_vars['view'] ) ) {
		$classes[] = 'wporg-theme-preview';
	}

	return $classes;
}

/**
 * Switch to the archive.html template on paged requests.
 *
 * @param string[] $templates A list of template candidates, in descending order of priority.
 */
function use_archive_template_paged( $templates ) {
	if ( is_paged() ) {
		array_unshift( $templates, 'archive.html' );
	}
	return $templates;
}

/**
 * If this is the `view` query, use preview template.
 *
 * @param string[] $templates A list of template candidates, in descending order of priority.
 */
function load_theme_preview( $templates ) {
	global $wp_query;

	if ( isset( $wp_query->query_vars['view'] ) ) {
		return [ 'preview.html' ];
	}

	return $templates;
}

/**
 * Generate the support URL for this theme.
 *
 * @todo Handle rosetta URLs.
 */
function get_support_url( $path ) {
	return 'https://wordpress.org/support/theme/' . $path;
}

/**
 * This is a copy of get_theme_feature_list(), but with the wporg-themes text domain
 *
 * @param string $include Optional. Type of list: 'active', 'deprecated' or 'all'. Default 'active'.
 * @param string $subset  Optional. Returns only the selected subset of features.
 *
 * @return array List of features.
 */
function wporg_themes_get_feature_list( $include = 'active', $subset = '' ) {
	$features = array();

	if ( 'active' === $include || 'all' === $include ) {
		$features = array(
			__( 'Layout', 'wporg-themes' )   => array(
				'grid-layout'   => __( 'Grid Layout', 'wporg-themes' ),
				'one-column'    => __( 'One Column', 'wporg-themes' ),
				'two-columns'   => __( 'Two Columns', 'wporg-themes' ),
				'three-columns' => __( 'Three Columns', 'wporg-themes' ),
				'four-columns'  => __( 'Four Columns', 'wporg-themes' ),
				'left-sidebar'  => __( 'Left Sidebar', 'wporg-themes' ),
				'right-sidebar' => __( 'Right Sidebar', 'wporg-themes' ),
				'wide-blocks'   => __( 'Wide Blocks', 'wporg-themes' ),
			),
			__( 'Features', 'wporg-themes' ) => array(
				'accessibility-ready'   => __( 'Accessibility Ready', 'wporg-themes' ),
				'block-patterns'        => __( 'Block Editor Patterns', 'wporg-themes' ),
				'block-styles'          => __( 'Block Editor Styles', 'wporg-themes' ),
				'buddypress'            => __( 'BuddyPress', 'wporg-themes' ),
				'custom-background'     => __( 'Custom Background', 'wporg-themes' ),
				'custom-colors'         => __( 'Custom Colors', 'wporg-themes' ),
				'custom-header'         => __( 'Custom Header', 'wporg-themes' ),
				'custom-logo'           => __( 'Custom Logo', 'wporg-themes' ),
				'custom-menu'           => __( 'Custom Menu', 'wporg-themes' ),
				'editor-style'          => __( 'Editor Style', 'wporg-themes' ),
				'featured-image-header' => __( 'Featured Image Header', 'wporg-themes' ),
				'featured-images'       => __( 'Featured Images', 'wporg-themes' ),
				'flexible-header'       => __( 'Flexible Header', 'wporg-themes' ),
				'footer-widgets'        => __( 'Footer Widgets', 'wporg-themes' ),
				'front-page-post-form'  => __( 'Front Page Posting', 'wporg-themes' ),
				'full-site-editing'     => __( 'Full Site Editing', 'wporg-themes' ),
				'full-width-template'   => __( 'Full Width Template', 'wporg-themes' ),
				'microformats'          => __( 'Microformats', 'wporg-themes' ),
				'post-formats'          => __( 'Post Formats', 'wporg-themes' ),
				'rtl-language-support'  => __( 'RTL Language Support', 'wporg-themes' ),
				'sticky-post'           => __( 'Sticky Post', 'wporg-themes' ),
				'style-variations'      => __( 'Style Variations', 'wporg-themes' ),
				'template-editing'      => __( 'Template Editing', 'wporg-themes' ),
				'theme-options'         => __( 'Theme Options', 'wporg-themes' ),
				'threaded-comments'     => __( 'Threaded Comments', 'wporg-themes' ),
				'translation-ready'     => __( 'Translation Ready', 'wporg-themes' ),
			),
			__( 'Subject', 'wporg-themes' )  => array(
				'blog'           => __( 'Blog', 'wporg-themes' ),
				'e-commerce'     => __( 'E-Commerce', 'wporg-themes' ),
				'education'      => __( 'Education', 'wporg-themes' ),
				'entertainment'  => __( 'Entertainment', 'wporg-themes' ),
				'food-and-drink' => __( 'Food & Drink', 'wporg-themes' ),
				'holiday'        => __( 'Holiday', 'wporg-themes' ),
				'news'           => __( 'News', 'wporg-themes' ),
				'photography'    => __( 'Photography', 'wporg-themes' ),
				'portfolio'      => __( 'Portfolio', 'wporg-themes' ),
			),
		);
	}

	if ( 'deprecated' === $include || 'all' === $include ) {
		$features[ __( 'Colors', 'wporg-themes' ) ] = array(
			'black'  => __( 'Black', 'wporg-themes' ),
			'blue'   => __( 'Blue', 'wporg-themes' ),
			'brown'  => __( 'Brown', 'wporg-themes' ),
			'gray'   => __( 'Gray', 'wporg-themes' ),
			'green'  => __( 'Green', 'wporg-themes' ),
			'orange' => __( 'Orange', 'wporg-themes' ),
			'pink'   => __( 'Pink', 'wporg-themes' ),
			'purple' => __( 'Purple', 'wporg-themes' ),
			'red'    => __( 'Red', 'wporg-themes' ),
			'silver' => __( 'Silver', 'wporg-themes' ),
			'tan'    => __( 'Tan', 'wporg-themes' ),
			'white'  => __( 'White', 'wporg-themes' ),
			'yellow' => __( 'Yellow', 'wporg-themes' ),
			'dark'   => __( 'Dark', 'wporg-themes' ),
			'light'  => __( 'Light', 'wporg-themes' ),
		);

		if ( 'deprecated' === $include ) {
			// Initialize arrays.
			$features[ __( 'Layout', 'wporg-themes' ) ]   = array();
			$features[ __( 'Features', 'wporg-themes' ) ] = array();
			$features[ __( 'Subject', 'wporg-themes' ) ]  = array();
		}

		$features[ __( 'Layout', 'wporg-themes' ) ] = array_merge(
			$features[ __( 'Layout', 'wporg-themes' ) ],
			array(
				'fixed-layout'      => __( 'Fixed Layout', 'wporg-themes' ),
				'fluid-layout'      => __( 'Fluid Layout', 'wporg-themes' ),
				'responsive-layout' => __( 'Responsive Layout', 'wporg-themes' ),
			)
		);

		$features[ __( 'Features', 'wporg-themes' ) ] = array_merge(
			$features[ __( 'Features', 'wporg-themes' ) ],
			array(
				'blavatar' => __( 'Blavatar', 'wporg-themes' ),
			)
		);

		$features[ __( 'Subject', 'wporg-themes' ) ] = array_merge(
			$features[ __( 'Subject', 'wporg-themes' ) ],
			array(
				'photoblogging' => __( 'Photoblogging', 'wporg-themes' ),
				'seasonal'      => __( 'Seasonal', 'wporg-themes' ),
			)
		);
	}

	if ( 'layouts' === $subset ) {
		return $features[ __( 'Layout', 'wporg-themes' ) ];
	} else if ( 'features' === $subset ) {
		return $features[ __( 'Features', 'wporg-themes' ) ];
	} else if ( 'subjects' === $subset ) {
		return $features[ __( 'Subject', 'wporg-themes' ) ];
	}

	return $features;
}

/**
 * Get the list of patterns from wp-themes.com API.
 */
function get_theme_patterns( $theme_name ) {
	$cache_key = 'wporg-themes-' . $theme_name . '-patterns';
	$patterns = get_transient( $cache_key );

	if ( false === $patterns ) {
		$url = 'https://wp-themes.com/' . $theme_name . '/';
		$url = add_query_arg( 'rest_route', '/wporg-patterns/v1/patterns', $url );
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			// Set a short timeout to avoid hammering the API during outages.
			set_transient( $cache_key, [], 0.5 * MINUTE_IN_SECONDS );
			return [];
		}

		// This is decoded twice because the response is a quoted JSON string.
		// The first decode parses out to JSON, the second parses out to an object.
		$patterns = json_decode( json_decode( wp_remote_retrieve_body( $response ) ) );

		set_transient( $cache_key, $patterns, HOUR_IN_SECONDS );
	}

	return $patterns;
}

/**
 * Get the list of style variations from wp-themes.com API.
 */
function get_theme_style_variations( $theme_name ) {
	$cache_key = 'wporg-themes-' . $theme_name . '-style-variations';
	$styles = get_transient( $cache_key );

	if ( false === $styles ) {
		$url = 'https://wp-themes.com/' . $theme_name . '/';
		$url = add_query_arg( 'rest_route', '/wporg-styles/v1/variations', $url );
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			// Set a short timeout to avoid hammering the API during outages.
			set_transient( $cache_key, [], 0.5 * MINUTE_IN_SECONDS );
			return [];
		}

		// This is decoded twice because the response is a quoted JSON string.
		// The first decode parses out to JSON, the second parses out to an object.
		$styles = json_decode( json_decode( wp_remote_retrieve_body( $response ) ) );

		set_transient( $cache_key, $styles, HOUR_IN_SECONDS );
	}

	return $styles;
}
