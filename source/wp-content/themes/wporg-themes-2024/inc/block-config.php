<?php
/**
 * Set up configuration for dynamic blocks.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Block_Config;

use function WordPressdotorg\Theme\Theme_Directory_2024\{ get_query_tags, wporg_themes_get_feature_list };

add_filter( 'wporg_query_total_label', __NAMESPACE__ . '\update_query_total_label', 10, 2 );
add_filter( 'wporg_query_filter_options_layouts', __NAMESPACE__ . '\get_layouts_options' );
add_filter( 'wporg_query_filter_options_features', __NAMESPACE__ . '\get_features_options' );
add_filter( 'wporg_query_filter_options_subjects', __NAMESPACE__ . '\get_subjects_options' );
add_action( 'wporg_query_filter_in_form', __NAMESPACE__ . '\inject_other_filters', 10, 2 );
add_filter( 'wporg_block_navigation_menus', __NAMESPACE__ . '\add_site_navigation_menus' );
add_filter( 'render_block_wporg/link-wrapper', __NAMESPACE__ . '\inject_permalink_link_wrapper' );

/**
 * Update the query total label to reflect "patterns" found.
 *
 * @param string $label       The maybe-pluralized label to use, a result of `_n()`.
 * @param int    $found_posts The number of posts to use for determining pluralization.
 * @return string Updated string with total placeholder.
 */
function update_query_total_label( $label, $found_posts ) {
	/* translators: %s: the result count. */
	return _n( '%s theme', '%s themes', $found_posts, 'wporg-themes' );
}

/**
 * Provide a list of layout options.
 *
 * @param array $options The options for this filter.
 * @return array New list of layout options.
 */
function get_layouts_options( $options ) {
	$tags = get_query_tags();
	$layouts = wporg_themes_get_feature_list( 'active', 'layouts' );
	$selected = array_intersect( $tags, array_keys( $layouts ) );

	$count = count( $selected );
	$label = sprintf(
		/* translators: The dropdown label for filtering, %s is the selected term count. */
		_n( 'Layout <span>%s</span>', 'Layout <span>%s</span>', $count, 'wporg-themes' ),
		$count
	);

	return array(
		'label' => $label,
		'title' => __( 'Layout', 'wporg-themes' ),
		'key' => 'tag_slug__and',
		'action' => home_url( '/' ),
		'options' => $layouts,
		'selected' => $selected,
	);
}

/**
 * Provide a list of feature options.
 *
 * @param array $options The options for this filter.
 * @return array New list of feature options.
 */
function get_features_options( $options ) {
	$tags = get_query_tags();
	$features = wporg_themes_get_feature_list( 'active', 'features' );
	$selected = array_intersect( $tags, array_keys( $features ) );

	$count = count( $selected );
	$label = sprintf(
		/* translators: The dropdown label for filtering, %s is the selected term count. */
		_n( 'Feature <span>%s</span>', 'Features <span>%s</span>', $count, 'wporg-themes' ),
		$count
	);

	return array(
		'label' => $label,
		'title' => __( 'Features', 'wporg-themes' ),
		'key' => 'tag_slug__and',
		'action' => home_url( '/' ),
		'options' => $features,
		'selected' => $selected,
	);
}

/**
 * Provide a list of subject options.
 *
 * @param array $options The options for this filter.
 * @return array New list of subject options.
 */
function get_subjects_options( $options ) {
	$tags = get_query_tags();
	$subjects = wporg_themes_get_feature_list( 'active', 'subjects' );
	$selected = array_intersect( $tags, array_keys( $subjects ) );

	$count = count( $selected );
	$label = sprintf(
		/* translators: The dropdown label for filtering, %s is the selected term count. */
		_n( 'Subject <span>%s</span>', 'Subjects <span>%s</span>', $count, 'wporg-themes' ),
		$count
	);

	return array(
		'label' => $label,
		'title' => __( 'Subjects', 'wporg-themes' ),
		'key' => 'tag_slug__and',
		'action' => home_url( '/' ),
		'options' => $subjects,
		'selected' => $selected,
	);
}

/**
 * Add in the other existing filters as hidden inputs in the filter form.
 *
 * Enables combining filters by building up the correct URL on submit,
 * for example sites using a tag, a category, and matching a search term:
 *   ?browse=commercial&tag[]=accessibility-ready&s=blue`
 *
 * @param string   $key   The key for the current filter.
 * @param WP_Block $block The current block being rendered.
 */
function inject_other_filters( $key, $block ) {
	global $wp_query;

	$values = get_query_tags();
	if ( $values ) {
		if ( 'tag_slug__and' === $key ) {
			$tag_key = $block->parsed_block['attrs']['key'] ?? false;
			if ( in_array( $tag_key, [ 'layouts', 'features', 'subjects' ] ) ) {
				$features = wporg_themes_get_feature_list( 'active', $tag_key );
				$values = array_diff( $values, array_keys( $features ) );
			}
		}

		if ( is_array( $values ) ) {
			foreach ( $values as $value ) {
				printf( '<input type="hidden" name="tag_slug__and[]" value="%s" />', esc_attr( $value ) );
			}
		}
	}

	if ( isset( $wp_query->query['browse'] ) && 'browse' !== $key ) {
		$values = $wp_query->query['browse'];
		printf( '<input type="hidden" name="browse" value="%s" />', esc_attr( $values ) );
	}

	// Pass through search query.
	if ( isset( $wp_query->query['s'] ) ) {
		printf( '<input type="hidden" name="s" value="%s" />', esc_attr( $wp_query->query['s'] ) );
	}
}

/**
 * Provide a list of local navigation menus.
 */
function add_site_navigation_menus( $menus ) {
	global $wp_query;

	$menu = array();

	$menu[] = array(
		'label' => __( 'My favorites', 'wporg-themes' ),
		'url' => '/browse/favorites/',
	);
	$menu[] = array(
		'label' => __( 'Submit a theme', 'wporg-themes' ),
		'url' => '/getting-started/',
	);
	$menu[] = array(
		'label' => __( 'Commercial theme companies', 'wporg-themes' ),
		'url' => '/commercial/',
	);

	$current_browse = $wp_query->query['browse'] ?? false;
	$current_tag = get_query_tags();

	$browse_menu = array(
		array(
			'label' => __( 'Popular', 'wporg-themes' ),
			'url' => home_url( '/' ),
			'className' => is_home() && ! $current_browse ? 'current-menu-item' : '',
		),
		array(
			'label' => __( 'Latest', 'wporg-themes' ),
			'url' => home_url( '/browse/new/' ),
			'className' => 'new' === $current_browse ? 'current-menu-item' : '',
		),
		array(
			'label' => __( 'Community', 'wporg-themes' ),
			'url' => home_url( '/browse/community/' ),
			'className' => 'community' === $current_browse ? 'current-menu-item' : '',
		),
		array(
			'label' => __( 'Commercial', 'wporg-themes' ),
			'url' => home_url( '/browse/commercial/' ),
			'className' => 'commercial' === $current_browse ? 'current-menu-item' : '',
		),
		array(
			'label' => __( 'Block themes', 'wporg-themes' ),
			'url' => home_url( '/tags/full-site-editing/' ),
			'className' => in_array( 'full-site-editing', $current_tag ) && ! $current_browse ? 'current-menu-item' : '',
		),
	);

	return array(
		'main' => $menu,
		'browse' => $browse_menu,
	);
}

/**
 * Update the link in `wporg/link-wrapper` to use the current post permalink.
 *
 * @param string $block_content The block content.
 *
 * @return array The updated block.
 */
function inject_permalink_link_wrapper( $block_content ) {
	return str_replace( 'href=""', 'href="' . get_permalink() . '"', $block_content );
}
