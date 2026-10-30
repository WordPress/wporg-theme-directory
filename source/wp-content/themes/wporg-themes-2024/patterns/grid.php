<?php
/**
 * Title: Theme Grid
 * Slug: wporg-themes-2024/grid
 * Inserter: no
 */

?>
<!-- wp:query {"queryId":0,"query":{"inherit":true},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide">
	<!-- wp:navigation {"menuSlug":"browse","ariaLabel":"<?php esc_attr_e( 'Filters menu', 'wporg-themes' ); ?>","overlayMenu":"never","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left","flexWrap":"nowrap"},"fontSize":"small","className":"is-style-button-list"} /-->

	<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"}} -->
		<div class="wp-block-group">
			<!-- wp:search {"showLabel":false,"placeholder":"<?php esc_html_e( 'Search themes', 'wporg-themes' ); ?>","width":100,"widthUnit":"%","buttonText":"<?php esc_html_e( 'Search', 'wporg-themes' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control"} /-->

			<!-- wp:wporg/query-total /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap"},"className":"wporg-query-filters"} -->
		<div class="wp-block-group wporg-query-filters">
			<!-- wp:wporg/query-filter {"key":"layouts"} /-->
			<!-- wp:wporg/query-filter {"key":"features"} /-->
			<!-- wp:wporg/query-filter {"key":"subjects"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"var:preset|spacing|50","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
	<div style="margin-top:0;margin-bottom:0;height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:query-title {"type":"filter","level":1,"className":"screen-reader-text","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|30"}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"large","fontFamily":"inter"} /-->

	<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","columnCount":3}} -->
		<!-- wp:wporg/link-wrapper {"className":"is-style-no-underline"} -->
		<a class="wp-block-wporg-link-wrapper is-style-no-underline" href="">
			<!-- wp:group {"style":{"spacing":{"blockGap":"0"},"border":{"radius":"2px","style":"solid","width":"1px"}},"borderColor":"black-opacity-15"} -->
			<div class="wp-block-group has-border-color has-black-opacity-15-border-color" style="border-style:solid;border-width:1px;border-radius:2px">
				<!-- wp:post-featured-image {"aspectRatio":"4/3"} /-->

				<!-- wp:group {"style":{"border":{"top":{"style":"solid","width":"1px","color":"var:preset|color|black-opacity-15"},"right":{"style":"none","width":"0px"},"bottom":{"style":"none","width":"0px"},"left":{"style":"none","width":"0px"}},"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}}},"layout":{"type":"default"}} -->
				<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--black-opacity-15);border-top-style:solid;border-top-width:1px;border-right-style:none;border-right-width:0px;border-bottom-style:none;border-bottom-width:0px;border-left-style:none;border-left-width:0px;padding-top:var(--wp--preset--spacing--10);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--10);padding-left:var(--wp--preset--spacing--20)">
					<!-- wp:post-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}}},"textColor":"charcoal-1","fontSize":"normal","fontFamily":"inter"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</a>
		<!-- /wp:wporg/link-wrapper -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
		<!-- wp:query-pagination-previous {"label":"<?php esc_attr_e( 'Previous', 'wporg-themes' ); ?>"} /-->

		<!-- wp:query-pagination-numbers /-->

		<!-- wp:query-pagination-next {"label":"<?php esc_attr_e( 'Next', 'wporg-themes' ); ?>"} /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"heading-2"} -->
		<h1 class="wp-block-heading has-text-align-center has-heading-2-font-size"><?php esc_attr_e( 'No results found', 'wporg-themes' ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">
			<?php
			printf(
				/* translators: %s is the homepage url. */
				wp_kses_post( __( 'View <a href="%s">all themes</a> or try a different search. ', 'wporg-themes' ) ),
				esc_url( home_url( '/' ) )
			);
			?>
		</p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->

<!-- wp:spacer {"height":"60px","align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|50"}}}} -->
<div style="margin-top:0;margin-bottom:var(--wp--preset--spacing--50);height:60px" aria-hidden="true" class="wp-block-spacer alignwide"></div>
<!-- /wp:spacer -->
