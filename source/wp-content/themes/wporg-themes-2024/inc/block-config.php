<?php
/**
 * Set up configuration for dynamic blocks.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Block_Config;

use WP_HTML_Tag_Processor, WP_Block_Supports;
use const WordPressdotorg\Theme\Theme_Directory_2024\THEME_POST_TYPE;
use function WordPressdotorg\Theme\Theme_Directory_2024\{ get_query_tags, get_support_url, get_theme_information, wporg_themes_get_feature_list };
use function WordPressdotorg\Theme\Theme_Directory_2024\SEO_Social_Meta\{get_archive_title};

add_filter( 'wporg_query_total_label', __NAMESPACE__ . '\update_query_total_label', 10, 2 );
add_filter( 'wporg_query_filter_options_layouts', __NAMESPACE__ . '\get_layouts_options' );
add_filter( 'wporg_query_filter_options_features', __NAMESPACE__ . '\get_features_options' );
add_filter( 'wporg_query_filter_options_subjects', __NAMESPACE__ . '\get_subjects_options' );
add_action( 'wporg_query_filter_in_form', __NAMESPACE__ . '\inject_other_filters', 10, 2 );
add_filter( 'wporg_block_navigation_menus', __NAMESPACE__ . '\add_site_navigation_menus' );
add_filter( 'wporg_favorite_button_settings', __NAMESPACE__ . '\get_favorite_settings', 10, 2 );
add_filter( 'wporg_ratings_data', __NAMESPACE__ . '\set_rating_data', 10, 2 );
add_filter( 'render_block_wporg/link-wrapper', __NAMESPACE__ . '\inject_permalink_link_wrapper' );
add_filter( 'render_block_wporg/language-suggest', __NAMESPACE__ . '\inject_language_suggest_endpoint' );
add_filter( 'render_block_core/search', __NAMESPACE__ . '\inject_browse_search_block' );
add_filter( 'render_block_core/query-title', __NAMESPACE__ . '\update_archive_title', 10, 3 );
add_filter( 'render_block_wporg/language-suggest', __NAMESPACE__ . '\filter_language_suggest_block' );
add_filter( 'render_block_core/site-title', __NAMESPACE__ . '\update_site_title' );

/**
 * Update the query total label to reflect "patterns" found.
 *
 * @param string $label       The maybe-pluralized label to use, a result of `_n()`.
 * @param int    $found_posts The number of posts to use for determining pluralization.
 * @return string Updated string with total placeholder.
 */
function update_query_total_label( $label, $found_posts ) {
	global $wp_query;
	$current_browse = $wp_query->query['browse'] ?? false;
	if ( 'commercial' === $current_browse ) {
		/* translators: %s: the result count. */
		return _n( '%s commercial theme', '%s commercial themes', $found_posts, 'wporg-themes' );
	} else if ( 'community' === $current_browse ) {
		/* translators: %s: the result count. */
		return _n( '%s community theme', '%s community themes', $found_posts, 'wporg-themes' );
	}

	/* translators: %s: the result count. */
	return _n( '%s theme', '%s themes', $found_posts, 'wporg-themes' );
}

/**
 * Get the destination for query-filter submission based on the current page.
 *
 * @return string
 */
function get_filter_action_url() {
	global $wp;
	if ( is_author() ) {
		return home_url( $wp->request );
	}
	return home_url( '/' );
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
		'action' => get_filter_action_url(),
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
		'action' => get_filter_action_url(),
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
		'action' => get_filter_action_url(),
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
	$current_browse = $wp_query->query['browse'] ?? false;
	$current_tag = get_query_tags();

	$menu = array();

	$menu[] = array(
		'label' => __( 'Submit a theme', 'wporg-themes' ),
		'url' => '/getting-started/',
	);
	$menu[] = array(
		'label' => __( 'Commercial theme companies', 'wporg-themes' ),
		'url' => '/commercial/',
	);
	$menu[] = array(
		'label' => __( 'My favorites', 'wporg-themes' ),
		'url' => '/browse/favorites/',
		'className' => ( 'favorites' === $current_browse ? 'current-menu-item ' : '' ) . 'has-separator',
	);

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
 * Configure the favorite button.
 *
 * @param array $settings Array of settings for this filter.
 * @param int   $post_id  The current post ID.
 *
 * @return array|bool Settings array or false if not a theme.
 */
function get_favorite_settings( $settings, $post_id ) {
	$theme_post = get_theme_information( $post_id );
	if ( ! $theme_post ) {
		return false;
	}

	return array(
		'is_favorite' => wporg_themes_is_favourited( $theme_post->slug ),
		'add_callback' => function( $post_id ) {
			$theme_post = get_theme_information( $post_id );
			if ( ! $theme_post ) {
				return new \WP_Error( 'theme-not-found', 'Theme not found.' );
			}

			return wporg_themes_add_favorite( $theme_post->slug );
		},
		'delete_callback' => function( $post_id ) {
			$theme_post = get_theme_information( $post_id );
			if ( ! $theme_post ) {
				return new \WP_Error( 'theme-not-found', 'Theme not found.' );
			}

			return wporg_themes_remove_favorite( $theme_post->slug );
		},
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

/**
 * Update the endpoint used in `wporg/language-suggest` for the current theme.
 *
 * @param string $block_content The block content.
 *
 * @return array The updated block.
 */
function inject_language_suggest_endpoint( $block_content ) {
	$html = new WP_HTML_Tag_Processor( $block_content );
	$html->next_tag();
	$endpoint_url = rest_url( '/wporg-themes/v1/locale-banner/' );
	if ( is_single() ) {
		$endpoint_url = trailingslashit( $endpoint_url . get_queried_object()->post_name );
	}
	$html->set_attribute( 'data-endpoint', $endpoint_url );
	return $html->get_updated_html();
}

/**
 * Inject the current browse filter into the search form.
 *
 * @param string $block_content
 *
 * @return string
 */
function inject_browse_search_block( $block_content ) {
	global $wp_query;
	$inputs = '';
	$current_browse = $wp_query->query['browse'] ?? false;
	if ( in_array( $current_browse, [ 'community', 'commercial' ] ) ) {
		$inputs .= sprintf( '<input type="hidden" name="browse" value="%s" />', esc_attr( $current_browse ) );
	}

	if ( isset( $wp_query->query['tag'] ) ) {
		$inputs .= sprintf( '<input type="hidden" name="tag" value="%s" />', esc_attr( $wp_query->query['tag'] ) );
	}

	return str_replace( '</form>', $inputs . '</form>', $block_content );
}

/**
 * Update the archive title for all filter views.
 *
 * @param string   $block_content The block content.
 * @param array    $block         The full block, including name and attributes.
 * @param WP_Block $instance      The block instance.
 */
function update_archive_title( $block_content, $block, $instance ) {
	global $wp_query;
	$attributes = $block['attrs'];

	if ( isset( $attributes['type'] ) && 'filter' === $attributes['type'] ) {
		// Skip output if there are no results. The `query-no-results` has an h1.
		if ( ! $wp_query->found_posts ) {
			return '';
		}

		$current_browse = $wp_query->query['browse'] ?? false;

		if ( is_front_page() && ! $current_browse && ! is_paged() ) {
			return '';
		}

		$title = get_archive_title();

		$author = isset( $wp_query->query['author_name'] ) ? get_user_by( 'slug', $wp_query->query['author_name'] ) : false;
		if ( $author ) {
			// Unhide this block on author archives.
			if ( isset( $block['attrs']['className'] ) ) {
				$block['attrs']['className'] = str_replace( 'screen-reader-text', '', $block['attrs']['className'] );
			}
		}

		$tag_name           = isset( $attributes['level'] ) ? 'h' . (int) $attributes['level'] : 'h1';
		$align_class_name   = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";

		// Required to prevent `block_to_render` from being null in `get_block_wrapper_attributes`.
		$parent = WP_Block_Supports::$block_to_render;
		WP_Block_Supports::$block_to_render = $block;
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );
		WP_Block_Supports::$block_to_render = $parent;

		return sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			$tag_name,
			$wrapper_attributes,
			$title
		);
	}
	return $block_content;
}

/**
 * Increase the visibility of the language suggest bar on single themes.
 *
 * @param string $block_content
 * @return string
 */
function filter_language_suggest_block( $block_content ) {
	if ( is_singular( THEME_POST_TYPE ) ) {
		$html = new \WP_HTML_Tag_Processor( $block_content );
		$html->next_tag();
		$html->add_class( 'is-style-prominent' );
		$block_content = $html->get_updated_html();
	}

	return $block_content;
}

/**
 * Update the archive title for all filter views.
 *
 * @param string $block_content The block content.
 */
function update_site_title( $block_content ) {
	return str_replace(
		get_bloginfo( 'name' ),
		__( 'Themes', 'wporg-themes' ),
		$block_content
	);
}

/**
 * Update ratings blocks with real rating data.
 *
 * @param array $data    Rating data.
 * @param int   $post_id Current post.
 *
 * @return array
 */
function set_rating_data( $data, $post_id ) {
	$theme = get_theme_information( $post_id );
	if ( ! $theme ) {
		return $data;
	}

	return array(
		'rating' => $theme->rating,
		'ratingsCount' => $theme->num_ratings,
		'ratings' => $theme->ratings,
		'supportUrl' => get_support_url( $theme->slug . '/reviews/' ),
	);
}
