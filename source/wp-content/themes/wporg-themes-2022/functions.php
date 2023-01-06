<?php

add_action( 'init', function() {
	// Temporary fix for permission problem during local install
	if ( defined( 'WP_CLI' ) ) {
		remove_filter( 'pre_insert_term', 'wporg_themes_pre_insert_term' );
	}
} );