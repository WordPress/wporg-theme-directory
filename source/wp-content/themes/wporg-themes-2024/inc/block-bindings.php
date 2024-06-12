<?php
/**
 * Set up custom block bindings.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\Block_Bindings;

use function WordPressdotorg\Theme\Theme_Directory_2024\get_support_url;

add_action( 'init', __NAMESPACE__ . '\register_block_bindings' );

/**
 * Register block bindings.
 *
 * This registers some sources which can be used to dynamically inject content
 * into block text or attributes.
 */
function register_block_bindings() {
	register_block_bindings_source(
		'wporg-themes/meta',
		array(
			'label' => 'Theme meta',
			'uses_context' => [ 'postId' ],
			'get_value_callback' => __NAMESPACE__ . '\get_meta_block_value',
		)
	);
}

/**
 * Callback to provide the binding value.
 */
function get_meta_block_value( $args, $block ) {
	if ( ! isset( $args['key'] ) ) {
		return '';
	}

	$p = get_post( $block->context['postId'] );
	$theme = wporg_themes_theme_information( $p->post_name );
	if ( isset( $theme->error ) ) {
		return '';
	}

	$report_url = add_query_arg(
		urlencode_deep(
			array_filter(
				array(
					'rep-theme'   => "https://wordpress.org/themes/{$theme->slug}/",
					'rep-subject' => "Reported Theme: {$theme->name}", // Not translated, email subject.
					'rep-name'    => wp_get_current_user()->user_login,
				)
			)
		),
		'https://make.wordpress.org/themes/report-theme/'
	);

	switch ( $args['key'] ) {
		case 'active-installs':
			$active_installs = $theme->active_installs;
			if ( $active_installs < 10 ) {
				$active_installs = __( 'Less than 10', 'wporg-themes' );
			} elseif ( $active_installs >= 1000000 ) {
				$active_installs = __( '1+ million', 'wporg-themes' );
			} else {
				$active_installs = number_format_i18n( $active_installs ) . '+';
			}
			return sprintf(
				/* translators: %s: active install count. */
				__( 'Active Installations: %s', 'wporg-themes' ),
				'<strong>' . $active_installs . '</strong>'
			);
		case 'preview-url':
			return esc_url( untrailingslashit( get_permalink( $p ) ) . '/preview/' );
		case 'download-url':
			return esc_url( $theme->download_link );
		case 'download-text':
			return esc_html__( 'Download', 'wporg-themes' );
		case 'ratings-link':
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_support_url( $theme->slug . '/reviews/' ) ),
				__( 'See all<span class="screen-reader-text"> reviews</span>', 'wporg-themes' )
			);
		case 'support-forum-url':
			return esc_url( get_support_url( $theme->slug ) );
		case 'support-forum-link':
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_support_url( $theme->slug ) ),
				__( 'View support forum', 'wporg-themes' )
			);
		case 'submit-review-url':
			return esc_url( get_support_url( $theme->slug . '/reviews/#new-post' ) );
		case 'submit-review-link':
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_support_url( $theme->slug . '/reviews/#new-post' ) ),
				__( 'Add my review', 'wporg-themes' )
			);
		case 'report-url':
			return esc_url( $report_url );
		case 'report-link':
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( $report_url ),
				__( 'Report this theme', 'wporg-themes' )
			);
		case 'translate-link':
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( "https://translate.wordpress.org/projects/wp-themes/{$theme->slug}" ),
				__( 'Translate this theme', 'wporg-themes' )
			);
		case 'trac-log-link':
			return sprintf(
				'<a href="%s" rel="nofollow">%s</a>',
				esc_url( "https://themes.trac.wordpress.org/log/{$theme->slug}" ),
				__( 'Development log', 'wporg-themes' )
			);
		case 'trac-svn-link':
			return sprintf(
				'<a href="%s" rel="nofollow">%s</a>',
				esc_url( "https://themes.svn.wordpress.org/{$theme->slug}" ),
				__( 'Subversion repository', 'wporg-themes' )
			);
		case 'trac-browse-link':
			return sprintf(
				'<a href="%s" rel="nofollow">%s</a>',
				esc_url( "https://themes.trac.wordpress.org/browser/{$theme->slug}" ),
				__( 'Browse in Trac', 'wporg-themes' )
			);
		case 'trac-tickets-link':
			return sprintf(
				'<a href="%s" rel="nofollow">%s</a>',
				esc_url( "https://themes.trac.wordpress.org/query?keywords=~theme-{$theme->slug}" ),
				__( 'Trac tickets', 'wporg-themes' )
			);
		case 'zip-name':
			$filename = basename( $theme->download_link );
			return esc_html( $filename );
		case 'theme-detail-url':
			return get_permalink();
		case 'preview-back-text':
			return __( '← Back', 'wporg-themes' );
	}
}
