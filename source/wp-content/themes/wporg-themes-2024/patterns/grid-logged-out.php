<?php
/**
 * Title: Grid (logged out)
 * Slug: wporg-themes-2024/grid-logged-out
 * Inserter: no
 *
 * This pattern is swapped out automatically when a logged out user visits `/favorites/`.
 */

$login_url    = wp_login_url( home_url( '/browse/favorites/' ) );
$register_url = wp_registration_url();

?>
<!-- wp:group {"layout":{"type":"constrained","contentSize":"30rem","justifyContent":"left"},"align":"wide"} -->
<div class="wp-block-group alignwide">
	<!-- wp:heading {"level":1,"fontSize":"heading-3","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
	<h1 class="wp-block-heading has-heading-3-font-size" style="margin-bottom:var(--wp--preset--spacing--30);"><?php esc_html_e( 'Favorites', 'wporg-themes' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'Log in to your WordPress.org account and you&#8217;ll be able to see all your favorite themes in one place.', 'wporg-themes' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:spacer {"height":"var:preset|spacing|30","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
	<div style="margin-top:0;margin-bottom:0;height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"className":"is-small"} -->
		<div class="wp-block-button is-small"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Log in', 'wporg-themes' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

	<!-- wp:paragraph -->
	<p><a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Create an account', 'wporg-themes' ); ?></a></p>
	<!-- /wp:paragraph -->

	<!-- wp:spacer {"height":"var:preset|spacing|40","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
	<div style="margin-top:0;margin-bottom:0;height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
</div>
<!-- /wp:group -->
