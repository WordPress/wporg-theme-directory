<?php
/**
 * Title: Theme Detail
 * Slug: wporg-themes-2024/single
 * Inserter: no
 */

?>
<!-- wp:group {"align":"wide"} -->
<div class="wp-block-group alignwide">
	
	<!-- wp:post-title {"level":1,"fontSize":"heading-3"} /-->

	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"},"align":"wide"} -->
	<div class="wp-block-group alignwide">
		<!-- wp:wporg/favorite-button /-->

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group">
			<!-- wp:avatar {"size":24,"style":{"border":{"radius":"100%"}}} /-->

			<!-- wp:post-author-name {"isLink":true,"style":{"typography":{"fontStyle":"normal"},"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}}},"textColor":"charcoal-1","fontSize":"small"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:wporg/theme-status-notice {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} /-->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40"}}},"className":"wporg-theme-overview"} -->
<div class="wp-block-columns wporg-theme-overview alignwide" style="margin-top:var(--wp--preset--spacing--40)">
	<!-- wp:column {"width":"70%","style":{"spacing":{"blockGap":"0"}}} -->
	<div class="wp-block-column" style="flex-basis:70%">
		<!-- wp:wporg/theme-style-variations /-->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"width":"30%"} -->
	<div class="wp-block-column" style="flex-basis:30%">
		<!-- wp:wporg/business-model-notice {"fontSize":"small","style":{"spacing":{"blockGap":"var:preset|spacing|10"}}} /-->

		<!-- wp:buttons {"layout":{"type":"flex"},"className":"wporg-theme-actions"} -->
		<div class="wp-block-buttons wporg-theme-actions">
			<!-- wp:button {"className":"is-style-outline","metadata":{"bindings":{"url":{"source":"wporg-themes/meta","args":{"key":"preview-url"}}}}} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" id="wporg-theme-button-preview"><?php esc_html_e( 'Preview', 'wporg-themes' ); ?></a></div>
			<!-- /wp:button -->

			<!-- wp:button {"metadata":{"bindings":{"url":{"source":"wporg-themes/meta","args":{"key":"download-url"}}}}} -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" id="wporg-theme-button-download"><?php esc_html_e( 'Download', 'wporg-themes' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

		<!-- wp:wporg/child-theme-notice /-->

		<!-- wp:wporg/meta-list /-->

	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--40)">
	<!-- wp:column {"width":"70%","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
	<div class="wp-block-column" style="flex-basis:70%">
		<!-- wp:post-content /-->

		<!-- wp:group -->
		<div class="wp-block-group">
			<!-- wp:heading {"fontSize":"heading-4"} -->
			<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Features', 'wporg-themes' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:post-terms {"term":"post_tag"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:wporg/theme-patterns /-->

		<!-- wp:group -->
		<div class="wp-block-group">
			<!-- wp:heading {"fontSize":"heading-4"} -->
			<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Downloads per day', 'wporg-themes' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"active-installs"}}}}} -->
			<p>Active Installations</p>
			<!-- /wp:paragraph -->

			<!-- wp:wporg/theme-downloads /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:wporg/theme-settings /-->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"width":"30%","fontSize":"small"} -->
	<div class="wp-block-column has-small-font-size" style="flex-basis:30%">
		<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"fontSize":"heading-4"} -->
			<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Ratings', 'wporg-themes' ); ?></h2>
			<!-- /wp:heading -->

		</div>
		<!-- /wp:group -->

		<!-- wp:wporg/ratings-stars /-->

		<!-- wp:wporg/ratings-bars /-->

		<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"},"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--10)">
			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"submit-review-link"}}}}} -->
			<p>Add my review</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"ratings-link"}}}},"className":"wporg-ratings-link"} -->
			<p class="wporg-ratings-link">See all</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:heading {"fontSize":"heading-4"} -->
		<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Support', 'wporg-themes' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Got something to say? Need help?', 'wporg-themes' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"support-forum-link"}}}}} -->
		<p style="margin-top:var(--wp--preset--spacing--10)">View support forum</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"fontSize":"heading-4"} -->
		<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Report', 'wporg-themes' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Does this theme have major issues?', 'wporg-themes' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"report-link"}}}}} -->
		<p style="margin-top:var(--wp--preset--spacing--10)">Report this theme</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"fontSize":"heading-4"} -->
		<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Translations', 'wporg-themes' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:wporg/theme-available-translations /-->

		<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"translate-link"}}}}} -->
		<p>Translate this theme</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"fontSize":"heading-4"} -->
		<h2 class="wp-block-heading has-heading-4-font-size"><?php esc_html_e( 'Browse the code', 'wporg-themes' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"trac-log-link"}}}}} -->
			<p></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"trac-svn-link"}}}}} -->
			<p></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"trac-browse-link"}}}}} -->
			<p></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"wporg-themes/meta","args":{"key":"trac-tickets-link"}}}}} -->
			<p></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"60px","align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|50"}}}} -->
<div style="margin-top:0;margin-bottom:var(--wp--preset--spacing--50);height:60px" aria-hidden="true" class="wp-block-spacer alignwide"></div>
<!-- /wp:spacer -->

<!-- wp:group {"align":"full","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull">
	<!-- wp:pattern {"slug":"wporg-parent-2021/post-navigation"} /-->
</div>
<!-- /wp:group -->
