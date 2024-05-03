<?php
/**
 * Set up some helper API endpoints.
 */

namespace WordPressdotorg\Theme\Theme_Directory_2024\REST_API\Locale;

/**
 * Get all locales with subdomain mapping.
 */
function get_all_locales_with_subdomain() {
	global $wpdb;
	return $wpdb->get_results(
		"SELECT locale, subdomain FROM wporg_locales WHERE locale NOT LIKE '%\_%\_%'",
		OBJECT_K
	);
}

/**
 * Validate that all the locale subdomains have valid WordPress locale values (hint: They don't).
 */
function get_all_valid_locales() {
	$all_locales = get_all_locales_with_subdomain();
	// Retrieve all the WordPress locales.
	$all_locales = wp_list_pluck( $all_locales, 'locale' );

	return array_filter(
		$all_locales,
		function( $locale ) {
			return \GP_Locales::by_field( 'wp_locale', $locale );
		}
	);
}

/**
 * Get locales matching the HTTP accept language header.
 *
 * @return array List of locales.
 */
function get_locale_from_header() {
	$res = array();

	$available_locales = get_all_valid_locales();
	if ( ! $available_locales ) {
		return $res;
	}

	if ( ! isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
		return $res;
	}

	$http_locales = get_http_locales( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ); // phpcs:ignore

	if ( is_array( $http_locales ) ) {
		foreach ( $http_locales as $http_locale ) {
			$lang   = $http_locale;
			$region = $http_locale;
			if ( str_contains( $http_locale, '-' ) ) {
				list( $lang, $region ) = explode( '-', $http_locale );
			}

			/*
			 * Discard English -- it's the default for all browsers,
			 * ergo not very reliable information
			 */
			if ( 'en' === $lang ) {
				continue;
			}

			// Region should be uppercase.
			$region = strtoupper( $region );

			$mapped = map_locale( $lang, $region, $available_locales );
			if ( $mapped ) {
				$res[] = $mapped;
			}
		}

		$res = array_unique( $res );
	}

	return $res;
}

/**
 * Given a HTTP Accept-Language header $header
 * returns all the locales in it.
 *
 * @param string $header HTTP acccept header.
 * @return array Matched locales.
 */
function get_http_locales( $header ) {
	$locale_part_re = '[a-z]{2,}';
	$locale_re      = "($locale_part_re(\-$locale_part_re)?)";

	if ( preg_match_all( "/$locale_re/i", $header, $matches ) ) {
		return $matches[0];
	} else {
		return [];
	}
}

/**
 * Tries to map a lang/region pair to one of our locales.
 *
 * @param string $lang              Lang part of the HTTP accept header.
 * @param string $region            Region part of the HTTP accept header.
 * @param array  $available_locales List of available locales.
 * @return string|false Our locale matching $lang and $region, false otherwise.
 */
function map_locale( $lang, $region, $available_locales ) {
	$uregion  = strtoupper( $region );
	$ulang    = strtoupper( $lang );
	$variants = array(
		"$lang-$region",
		"{$lang}_$region",
		"$lang-$uregion",
		"{$lang}_$uregion",
		"{$lang}_$ulang",
		$lang,
	);

	foreach ( $variants as $variant ) {
		if ( in_array( $variant, $available_locales ) ) {
			return $variant;
		}
	}

	foreach ( $available_locales as $locale ) {
		list( $locale_lang, ) = preg_split( '/[_-]/', $locale );
		if ( $lang === $locale_lang ) {
			return $locale;
		}
	}

	return false;
}

/**
 * Get the active language packs for a theme.
 */
function get_transalated_locales( $theme_slug ) {
	global $wpdb;

	$language_packs = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT *
			FROM language_packs
			WHERE
				type = 'theme' AND
				domain = %s AND
				active = 1
			GROUP BY language",
			$theme_slug
		)
	);

	// Retrieve all the WordPress locales in which the theme is translated.
	$translated_locales = wp_list_pluck( $language_packs, 'language' );

	require_once GLOTPRESS_LOCALES_PATH;

	// Validate the list of locales can be found by `wp_locale`.
	return array_filter(
		$translated_locales,
		function( $locale ) {
			return \GP_Locales::by_field( 'wp_locale', $locale );
		}
	);
}
