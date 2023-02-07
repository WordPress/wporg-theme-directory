<?php
/**
 * Plugin Name: WordPress.org Ratings System
 * Version: 0.1
 * Author: Otto
 * Author URI: http://ottopress.com
 * Description: Contains code needed for doing plugin/theme/other ratings on WordPress.org systems.
 */

/**
 * Add a new cache group, because we're switching over to using post_id instead
 * of topic id (as review id). This will allow us to start using the new data in
 * place while still supporting the plugin/theme directory calls to the old
 * bbPress tables.
 */
wp_cache_add_global_groups( 'wporg-ratings' );

class WPORG_Ratings {

	const CACHE_GROUP = 'wporg-ratings';
	const CACHE_TIME = HOUR_IN_SECONDS;
	const REVIEWS_FORUM = 21272; // Support Forum reviews forum, is the post_parent of all reviews.

	/**
	 * Retrieves a rating by post id, and caches the result.
	 * @param $post_id post id of review. Default 0.
	 * @return int.
	 */
	public static function get_post_rating( int $post_id = 0 ): int {
		global $wpdb;

		$cache_key = "rating:" . $post_id;
		if ( false != ( $rating = wp_cache_get( $cache_key, self::CACHE_GROUP ) ) ) {
			return (int) $rating;
		}

		// Use int to set the rating to 0 for no rating.
		$rating = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT rating
				FROM ratings
				WHERE post_id = %d",
			$post_id
		) );

		wp_cache_set( $cache_key, $rating, self::CACHE_GROUP, self::CACHE_TIME );

		return $rating;
	}

	/**
	 * Retrieves a user's rating, then sets a cache.
	 * @param $object_type either theme or plugin.
	 * @param $object_slug plugin or theme slug.
	 * @param $user_id id of user's review.
	 * @return int.
	 */
	public static function get_user_rating( $object_type, $object_slug, $user_id ): int {
		global $wpdb;

		$cache_key = "rating:" . $object_type . ":" . $object_slug . ":" . $user_id;
		if ( false !== ( $rating = wp_cache_get( $cache_key, self::CACHE_GROUP ) ) ) {
			return (int) $rating;
		}

		// Use int to set the rating to 0 for no rating.
		$rating = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT rating
				FROM ratings
				WHERE object_type = %s AND object_slug = %s AND user_id = %d
				LIMIT 1",
			$object_type,
			$object_slug,
			$user_id
		) );

		// Set a lower cache time if the result wasn't successful (does not exist or error).
		if ( 0 === $rating ) {
			$cache_time = 5 * MINUTE_IN_SECONDS;
		} else {
			$cache_time = self::CACHE_TIME;
		}

		wp_cache_set( $cache_key, $rating, self::CACHE_GROUP, $cache_time );

		return (int) $rating;
	}

	/**
	 * Sets a rating on a theme or plugin for a user.
	 * @param $post_id id of the review. Default 0.
	 * @param $object_type either theme or plugin.
	 * @param $object_slug slug of theme or plugin.
	 * @param $user_id id of users' rating.
	 * @param $rating the rating being set.
	 */
	public static function set_rating( $post_id = 0, $object_type, $object_slug, $user_id, $rating ) {
		global $wpdb;

		// Make sure the current user has permissions to submit the rating.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$rating = (int) $rating;
		$rating = max( $rating, 1 );
		$rating = min( $rating, 5 );

		$wpdb->query( $wpdb->prepare(
			"INSERT
				INTO ratings ( post_id, object_type, object_slug, user_id, rating )
				VALUES ( %d, %s, %s, %d, %d )
				ON DUPLICATE KEY UPDATE rating = VALUES( `rating` )",
			$post_id, $object_type, $object_slug, $user_id, $rating
		) );

		// Clear relevant caches.
		wp_cache_delete( "rating:" . $post_id, self::CACHE_GROUP );
		wp_cache_delete( "rating:" . $object_type . ":" . $object_slug . ":" . $user_id, self::CACHE_GROUP );
		wp_cache_delete( "rating:counts:" . $object_type . ":" . $object_slug, self::CACHE_GROUP );
	}

	/**
	 * Gets the average rating for a theme or plugin.
	 * @param $object_type either a theme or plugin.
	 * @param $object_slug the slug of the theme or plugin.
	 * @return float rounded average rating to 1 decimal place.
	 */
	public static function get_avg_rating( $object_type, $object_slug ): float {
		$ratings = self::get_rating_counts( $object_type, $object_slug );

		$sum = array_sum( $ratings );
		if ( ! $sum ) {
			return 0;
		}

		// Calculate the weighted sum of the ratings.
		$avg = (
			$ratings[1] * 1 +
			$ratings[2] * 2 +
			$ratings[3] * 3 +
			$ratings[4] * 4 +
			$ratings[5] * 5
		);

		// Divided by the count is the average.
		$avg /= $sum;

		// Round it to 1 decimal place, which coincidentally also results in the percentage always being an int.
		$avg = round( $avg, 1 );

		return $avg;
	}

	/**
	 * Get the rating count for a theme or plugin.
	 * @param $object_type either a theme or plugin.
	 * @param $object_slug the slug of the theme or plugin.
	 * @param $rating the individual rating count to return. Default 0.
	 */
	public static function get_rating_count( $object_type, $object_slug, $rating = 0 ) {
		$ratings = self::get_rating_counts( $object_type, $object_slug );

		if ( $rating ) {
			return $ratings[ $rating ] ?? 0;
		}

		return array_sum( $ratings );
	}

	/**
	 * Get the rating counts (1-5) for a plugin or theme.
	 * @param $object_type either a theme or plugin.
	 * @param $object_slug the slug of the theme or plugin.
	 * @return an array of rating counts
	 */
	public static function get_rating_counts( $object_type, $object_slug ): array {
		global $wpdb;

		$cache_key = "rating:counts:" . $object_type . ":" . $object_slug;
		if ( false != ( $counts = wp_cache_get( $cache_key, self::CACHE_GROUP ) ) ) {
			return $counts;
		}

		// Get the number of reviews for each rating.
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT rating, COUNT(*) as c
				FROM ratings
				JOIN wporg_419_posts ON (
					wporg_419_posts.ID = ratings.post_id AND
					wporg_419_posts.post_status IN ( 'publish', 'closed' ) AND
					wporg_419_posts.post_parent = %d
				)
				WHERE ratings.object_type = %s AND ratings.object_slug = %s
				GROUP BY rating",
			self::REVIEWS_FORUM,
			$object_type,
			$object_slug
		) );

		$counts = array_fill( 1, 5, 0 );
		foreach ( $results as $r ) {
			// Only valid ratings, 1..5
			if ( $r->rating < 1 || $r->rating > 5 ) {
				unset( $counts[ $r->rating ] );
				continue;
			}

			$counts[ $r->rating ] = (int) $r->c;
		}

		wp_cache_set( $cache_key, $counts, self::CACHE_GROUP, self::CACHE_TIME );

		return $counts;
	}

	/**
	 * Get the theme or plugin rating displayed as stars.
	 * @param $rating the numerical rating for a theme or plugin. Default 0.
	 * @return string a html string representing the rating.
	 */
	public static function get_dashicons_stars( $rating = 0 ): string {
		$title   = sprintf( __( "%d out of 5 stars", 'wporg-forums' ), $rating );
		$output  = "<div class='wporg-ratings' title='" . esc_attr( $title ) . "' style='color:#ffb900;'>";
		$counter = round( $rating * 2 );
		for ( $i = 0; $i < 5; $i ++ ) {
			switch ( $counter ) {
				case 0 :
					$output .= '<span class="dashicons dashicons-star-empty"></span>';
					break;
				case 1 :
					$output .= '<span class="dashicons dashicons-star-half"></span>';
					$counter --;
					break;
				default :
					$output  .= '<span class="dashicons dashicons-star-filled"></span>';
					$counter -= 2;
					break;
			}
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Outputs the form to allow ratings to be submitted.
	 * @param $object_type either theme or plugin.
	 * @param $object_slug the slug of theme or plugin.
	 * @param $editable whether the form is editable. Default false.
	 */
	public static function get_dashicons_form( $object_type, $object_slug, $editable = false ) {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$user_id = get_current_user_id();

		$rating = self::get_user_rating( $object_type, $object_slug, $user_id );
		if ( ! $rating ) {
			if ( $editable ) {
				$rating = empty( $_GET['rate'] ) ? 5 : (int) $_GET['rate'];
			} else {
				$rating = 0;
			}
		}
		$titles = array(
			1 => __( 'Poor', 'wporg' ),
			2 => __( 'Works', 'wporg' ),
			3 => __( 'Good', 'wporg' ),
			4 => __( 'Great', 'wporg' ),
			5 => __( 'Fantastic!', 'wporg' ),
		);
		?>
        <div id="rate-response"></div>
        <div class="rate">
			<?php
			$output = '<fieldset class="wporg-ratings rating-stars">';
			for ( $i = 1; $i <= 5; $i ++ ) {
				$class   = ( $i <= $rating ) ? 'dashicons-star-filled' : '';
				$text    = $titles[ $i ];
				$checked = checked( $i, $rating, false );

				$output .= "<label for='rating_" . esc_attr( $i ) . "'>";
				$output .= "<input class='hidden' id='rating_" . esc_attr( $i ) . "' type='radio' name='rating' " . esc_attr( $checked ) . " value='" . esc_attr( $i ) . "'>";
				$output .= "<span class='dashicons dashicons-star-empty " . esc_attr( $class ) . "' style='color:#ffb900 !important;' title='" . esc_attr( $text ) . "'></span>";
				$output .= "<span class='screen-reader-text'>" . esc_html( $text ) . "</span>";
				$output .= "</label>";
			}
			$output .= '</fieldset>';
			echo $output;

			if ( $editable ) {
				?>
                <input type="hidden" name="rating" id="rating" value="<?php echo esc_attr( $rating ); ?>"/>
                <input type="hidden" name="wporg_type" value="<?php echo esc_attr( $object_type ); ?>"/>
                <input type="hidden" name="wporg_slug" value="<?php echo esc_attr( $object_slug ); ?>"/>
                <script>
                    jQuery(document).ready(function ($) {
                        var ratings = $('.rating-stars');
                        var selectedClass = 'dashicons-star-filled';

                        function toggleStyles(currentInput) {
                            var thisInput = $(currentInput);
                            var index = parseInt(thisInput.val());

                            stars.removeClass(selectedClass);
                            stars.slice(0, index).addClass(selectedClass);
                        }

                        // If the ratings exist on the page
                        if (ratings.length !== 0) {
                            var inputs = ratings.find('input[type="radio"]');
                            var labels = ratings.find('label');
                            var stars = inputs.next();

                            inputs.on('change', function (event) {
                                toggleStyles(event.target)
                                // Add to hidden input
                                $('input#rating').val($(event.target).val());
                            });

                            labels.hover(function (event) {
                                $curInput = $(event.currentTarget).find('input');
                                toggleStyles($curInput);
                            }, function () {
                                $currentSelected = ratings.find('input[type="radio"]:checked');
                                toggleStyles($currentSelected)
                            });
                        }
                    });
                </script>
				<?php
			}
			?>
        </div>
		<?php
	}
}
