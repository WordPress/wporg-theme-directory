<?php
/**
 * Customizations for oEmbed output.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Embed;

use const WordPressdotorg\Theme\Theme_Directory_2024\THEME_POST_TYPE;

/**
 * Actions and filters.
 */
add_action( 'enqueue_embed_scripts', __NAMESPACE__ . '\enqueue_embed_styles' );
add_filter( 'embed_thumbnail_id', __NAMESPACE__ . '\enable_embed_thumbnail' );
add_action( 'embed_thumbnail_image_shape', __NAMESPACE__ . '\update_thumbnail_image_shape' );
add_action( 'wp_get_attachment_image', __NAMESPACE__ . '\update_embed_thumbnail', 10, 5 );
add_filter( 'get_site_icon_url', __NAMESPACE__ . '\update_site_icon', 10, 3 );

/**
 * Inject some simple CSS into the embed template.
 */
function enqueue_embed_styles() {
	$asset_file = get_theme_file_path( 'build/embed/index.asset.php' );
	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$metadata = require $asset_file;
	wp_enqueue_style(
		'wporg-theme-directory-2024-embed',
		get_theme_file_uri( 'build/embed/style-index.css' ),
		$metadata['dependencies'],
		$metadata['version']
	);
}

/**
 * Return a truthy value if this is a theme embed.
 *
 * It doesn't matter that this isn't a real ID, since the image itself is replaced in `update_embed_thumbnail`.
 *
 * @return bool
 */
function enable_embed_thumbnail() {
	return is_singular( THEME_POST_TYPE );
}

/**
 * Force the shape to "rectangular".
 *
 * @param string $shape Thumbnail image shape. Either 'rectangular' or 'square'.
 *
 * @return string
 */
function update_thumbnail_image_shape( $shape ) {
	return 'rectangular';
}

/**
 * Update the image on theme embeds.
 *
 * @param string       $html          HTML img element or empty string on failure.
 * @param int          $attachment_id Image attachment ID.
 * @param string|int[] $size          Requested image size. Can be any registered image size name, or
 *                                    an array of width and height values in pixels (in that order).
 * @param bool         $icon          Whether the image should be treated as an icon.
 * @param string[]     $attr          Array of attribute values for the image markup, keyed by attribute name.
 *                                    See wp_get_attachment_image().
 *
 * @return string
 */
function update_embed_thumbnail( $html, $attachment_id, $size, $icon, $attr ) {
	global $post;
	if ( is_embed() && is_singular( THEME_POST_TYPE ) ) {
		$theme = new \WPORG_Themes_Repo_Package( $post );
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
				$html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
			}
		}

		$html .= ' />';
	}
	return $html;
}

/**
 * Filters the site icon URL.
 *
 * @since 4.4.0
 *
 * @param string $url     Site icon URL.
 * @param int    $size    Size of the site icon.
 * @param int    $blog_id ID of the blog to get the site icon for.
 */
function update_site_icon( $url, $size, $blog_id ) {
	return 'https://wordpress.org/files/2024/06/charcoal-logo.png';
}
