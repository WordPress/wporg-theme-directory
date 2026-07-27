<?php
/**
 * Block Name: Meta List
 * Description: Display a theme's metadata as a list.
 *
 * @package wporg
 */

 namespace WordPressdotorg\Theme\Theme_Directory_2024\Meta_List;

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Register the block.
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/meta-list',
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
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$list_items = array();

	$meta_fields = array(
		array(
			'label' => __( 'Version', 'wporg-themes' ),
			'key' => 'version',
		),
		array(
			'label' => __( 'Last updated', 'wporg-themes' ),
			'key' => 'last-updated',
		),
		array(
			'label' => __( 'Active installations', 'wporg-themes' ),
			'key' => 'active-installs',
		),
		array(
			'label' => __( 'WordPress version', 'wporg-themes' ),
			'key' => 'requires-wp',
		),
		array(
			'label' => __( 'PHP version', 'wporg-themes' ),
			'key' => 'requires-php',
		),
		array(
			'label' => __( 'Theme homepage', 'wporg-themes' ),
			'key' => 'theme-link',
		),
	);
	$show_label = $attributes['showLabel'] ?? false;

	if ( isset( $attributes['meta'] ) ) {
		$meta_fields = array_filter(
			$meta_fields,
			function ( $field ) use ( $attributes ) {
				return in_array( $field['key'], $attributes['meta'] );
			}
		);
	}

	foreach ( $meta_fields as $field ) {
		$value = get_value( $field['key'], $block->context['postId'] );

		if ( ! empty( $value ) ) {
			if ( 'theme-link' === $field['key'] ) {
				$list_items[] = sprintf(
					'<li class="is-meta-%1$s">
						<a href="%2$s" class="external-link" rel="noopener noreferrer">%3$s</a>
					</li>',
					$field['key'],
					esc_url( $value ),
					$field['label']
				);
			} else {
				$list_items[] = sprintf(
					'<li class="is-meta-%1$s">
						<span%2$s>%3$s</span>
						<span>%4$s</span>
					</li>',
					$field['key'],
					$show_label ? '' : ' class="screen-reader-text"',
					$field['label'],
					wp_kses_post( $value )
				);
			}
		}
	}

	$class = $show_label ? '' : 'has-hidden-label';
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $class ) );
	return sprintf(
		'<div %s><ul>%s</ul></div>',
		$wrapper_attributes,
		join( '', $list_items )
	);
}

/**
 * Retrieves a value from a given theme.
 *
 * @param string $key Name of meta information.
 * @param string $post_id ID of the post to look up.
 *
 * @return string
 */
function get_value( $key, $post_id ) {
	$p = get_post( $post_id );
	if ( ! $p || ! ( $p instanceof \WP_Post ) ) {
		return '';
	}

	$theme = wporg_themes_theme_information( $p->post_name );
	if ( ! $theme || isset( $theme->error ) ) {
		return '';
	}

	switch ( $key ) {
		case 'version':
			return $theme->version ?? '';
		case 'last-updated':
			/* translators: localized date format, see http://php.net/date */
			return date_i18n( _x( 'F j, Y', 'last update date format', 'wporg-themes' ), strtotime( $theme->last_updated ) );
		case 'active-installs':
			$active_installs = $theme->active_installs ?? 0;
			if ( $active_installs < 10 ) {
				$active_installs = __( 'Less than 10', 'wporg-themes' );
			} elseif ( $active_installs >= 1000000 ) {
				$active_installs = __( '1+ million', 'wporg-themes' );
			} else {
				$active_installs = number_format_i18n( $active_installs ) . '+';
			}
			return $active_installs;
		case 'requires-wp':
			if ( ! empty( $theme->requires ) ) {
				return $theme->requires;
			}
			return '';
		case 'requires-php':
			if ( ! empty( $theme->requires_php ) ) {
				return $theme->requires_php;
			}
			return '';
		case 'theme-link':
			if ( ! empty( $theme->theme_url ) ) {
				return $theme->theme_url;
			}
			return '';
	}

	return '';
}
