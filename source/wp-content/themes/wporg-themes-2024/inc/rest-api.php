<?php
/**
 * Set up some helper API endpoints.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\REST_API;

use function WordPressdotorg\Theme\Theme_Directory_2024\REST_API\Locale\{ get_all_locales_with_subdomain, get_all_valid_locales, get_locale_from_header, get_transalated_locales };

add_action( 'rest_api_init', __NAMESPACE__ . '\init' );

/**
 * Initialize the API endpoint(s).
 */
function init() {
	$namespace = 'wporg-themes/v1';
	$favorite_args = array(
		array(
			'theme_slug' => array(
				'validate_callback' => __NAMESPACE__ . '\check_theme_slug',
				'required' => true,
			),
		),
	);
	register_rest_route(
		$namespace,
		'/favorite',
		array(
			'methods' => \WP_REST_Server::CREATABLE,
			'callback' => __NAMESPACE__ . '\set_favorite',
			'args' => $favorite_args,
			'permission_callback' => 'is_user_logged_in',
		)
	);
	register_rest_route(
		$namespace,
		'/favorite',
		array(
			'methods' => \WP_REST_Server::DELETABLE,
			'callback' => __NAMESPACE__ . '\delete_favorite',
			'args' => $favorite_args,
			'permission_callback' => 'is_user_logged_in',
		)
	);
	register_rest_route(
		$namespace,
		'/locale-banner/',
		array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => __NAMESPACE__ . '\get_locale_banner',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		$namespace,
		'/locale-banner/(?P<theme_slug>[^/]+)/',
		array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => __NAMESPACE__ . '\get_locale_banner_for_theme',
			'args' => array(
				'theme_slug' => array(
					'validate_callback' => __NAMESPACE__ . '\check_theme_slug',
				),
			),
			'permission_callback' => '__return_true',
		)
	);
}

/**
 * Validate the theme slug.
 */
function check_theme_slug( $param ) {
	$theme = wporg_themes_theme_information( $param );
	return ! isset( $theme->error );
}

/**
 * Set the favorite status for a given theme.
 */
function set_favorite( $request ) {
	$result = wporg_themes_add_favorite( $request['theme_slug'] );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return new \WP_REST_Response( [ 'success' => true ] );
}

/**
 * Remove the favorite status for a given theme.
 */
function delete_favorite( $request ) {
	$result = wporg_themes_remove_favorite( $request['theme_slug'] );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return new \WP_REST_Response( [ 'success' => true ] );
}

/**
 * Get banner for general theme directory
 */
function get_locale_banner( $request ) {
	if ( ! defined( 'GLOTPRESS_LOCALES_PATH' ) ) {
		return;
	}

	$locale_subdomain_assoc = get_all_locales_with_subdomain();

	require_once GLOTPRESS_LOCALES_PATH;

	$current_locale = get_locale();
	$current_gp_locale = \GP_Locales::by_field( 'wp_locale', $current_locale );
	$translated_locales = get_transalated_locales( $theme_slug );

	// Build a list of WordPress locales which we'll suggest to the user.
	$suggest_locales = array_values( array_intersect( get_locale_from_header(), get_all_valid_locales() ) );

	$suggestion_links = [];
	foreach ( $suggest_locales as $locale ) {
		$language = \GP_Locales::by_field( 'wp_locale', $locale )->native_name;
		$suggestion_links[ $locale ] = sprintf(
			'<a href="https://%s.wordpress.org%s">%s</a>',
			$locale_subdomain_assoc[ $locale ]->subdomain,
			esc_url( get_site()->path ),
			$language
		);
	}

	$suggest_string = '';

	unset( $suggestion_links[ $current_locale ] );

	if ( ! empty( $suggestion_links ) ) {
		$output_locale = key( $suggestion_links );
		switch_to_locale( $output_locale );
		$suggest_string = sprintf(
			// translators: %s: List of links to theme in other locales.
			__( 'The theme directory is also available in %s.', 'wporg-themes' ),
			wp_sprintf_l( '%l', $suggestion_links )
		);
	}

	// The result should be a raw text response.
	add_filter( 'rest_pre_echo_response', __NAMESPACE__ . '\send_plain_text' );
	return new \WP_REST_Response( $suggest_string );
}

/**
 * Get banner for single themes.
 */
function get_locale_banner_for_theme( $request ) {
	// This has already been validated by `validate_callback`.
	$theme_slug = $request['theme_slug'];

	if ( ! defined( 'GLOTPRESS_LOCALES_PATH' ) ) {
		return;
	}

	$locale_subdomain_assoc = get_all_locales_with_subdomain();

	require_once GLOTPRESS_LOCALES_PATH;

	$current_locale = get_locale();
	$current_gp_locale = \GP_Locales::by_field( 'wp_locale', $current_locale );
	$translated_locales = get_transalated_locales( $theme_slug );

	// Build a list of WordPress locales which we'll suggest to the user.
	$suggest_locales = array_values( array_intersect( get_locale_from_header(), $translated_locales ) );

	$suggestion_links = [];
	foreach ( $suggest_locales as $locale ) {
		$language = \GP_Locales::by_field( 'wp_locale', $locale )->native_name;
		$suggestion_links[ $locale ] = sprintf(
			'<a href="https://%s.wordpress.org%s">%s</a>',
			$locale_subdomain_assoc[ $locale ]->subdomain,
			esc_url( get_site()->path . $theme_slug . '/' ),
			$language
		);
	}

	$suggest_string = '';

	unset( $suggestion_links[ $current_locale ] );

	// If we're on a rosetta site, and the theme is not translated, the message should ask for help.
	if ( 'en_US' !== $current_locale && $current_gp_locale && ! in_array( $current_locale, $translated_locales ) ) {
		$output_locale = $current_locale;
		switch_to_locale( $output_locale );
		$suggest_string = sprintf(
			// translators: %s: Locale name.
			__( 'This theme is not translated into %s yet.', 'wporg-themes' ),
			$current_gp_locale->native_name
		);

		// Append some other suggestions if they exist.
		if ( ! empty( $suggestion_links ) ) {
			$suggest_string .= ' ' . sprintf(
				// translators: %s: List of links to theme in other locales.
				__( 'This theme is available in %s.', 'wporg-themes' ),
				wp_sprintf_l( '%l', $suggestion_links )
			);
		}

		// Lastly, add the call for help.
		$suggest_string .= ' ' . sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( 'https://translate.wordpress.org/projects/wp-themes/' . $theme_slug ),
			__( 'Help improve the translation!', 'wporg-themes' )
		);

	} else if ( ! empty( $suggestion_links ) ) {
		$output_locale = key( $suggestion_links );
		switch_to_locale( $output_locale );
		$suggest_string = sprintf(
			// translators: %s: List of links to theme in other locales.
			__( 'This theme is also available in %s.', 'wporg-themes' ),
			wp_sprintf_l( '%l', $suggestion_links )
		);
		$suggest_string .= ' ' . sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( 'https://translate.wordpress.org/projects/wp-themes/' . $theme_slug ),
			__( 'Help improve the translation!', 'wporg-themes' )
		);

	} else if ( ! empty( $locales_from_header ) ) {
		$output_locale = reset( $locales_from_header );
		switch_to_locale( $output_locale );

		$suggest_string = sprintf(
			// translators: %s: Locale name.
			__( 'This theme is not translated into %s yet.', 'wporg-themes' ),
			\GP_Locales::by_field( 'wp_locale', $output_locale )->native_name
		);
		$suggest_string .= ' ' . sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( 'https://translate.wordpress.org/projects/wp-themes/' . $theme_slug ),
			__( 'Help translate it!', 'wporg-themes' )
		);
	}

	// The result should be a raw text response.
	add_filter( 'rest_pre_echo_response', __NAMESPACE__ . '\send_plain_text' );
	return new \WP_REST_Response( $suggest_string );
}

/**
 * Send the response as plain text so it can be used as-is.
 */
function send_plain_text( $result ) {
	// header( 'Content-Type: text/html' );
	header( 'Content-Type: text/text' );
	if ( $result ) {
		echo '<div>' . $result . '</div>'; // phpcs:ignore
	}

	return null;
}
