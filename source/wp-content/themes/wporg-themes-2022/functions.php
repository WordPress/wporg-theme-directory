<?php

add_action( 'init', function() {
	// Temporary fix for permission problem during local install
	if ( defined( 'WP_CLI' ) ) {
		remove_filter( 'pre_insert_term', 'wporg_themes_pre_insert_term' );
	}

	// Temporary fix for missing WPORG_Ratings class
	if ( !class_exists( 'WPORG_Ratings' ) ) {
		class WPORG_Ratings {
			public static function get_post_rating( $post_id = 0 ) {}
			public static function get_user_rating( $object_type, $object_slug, $user_id ) {}
			public static function set_rating( $post_id, $object_type, $object_slug, $user_id, $rating ) {}
			public static function get_avg_rating( $object_type, $object_slug ) {}
			public static function get_rating_count( $object_type, $object_slug, $rating = 0 ) {}
			public static function get_rating_counts( $object_type, $object_slug ) {}
			public static function bbpress1_ratings_init( $topic, $object_type, $object_slug ) {}
			public static function get_dashicons_stars( $rating = 0 ) {}
			public static function get_dashicons_form( $object_type, $object_slug, $editable = false ) {}
		}
	}
} );