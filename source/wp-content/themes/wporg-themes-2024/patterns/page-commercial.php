<?php
/**
 * Title: Page: Commercial
 * Slug: wporg-themes-2024/page-commercial
 * Inserter: no
 */

?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group alignwide">
	<!-- wp:heading {"level":1,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
	<h1 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--30)"><?php esc_html_e( 'Commercially supported GPL themes', 'wporg-themes' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>
		<?php esc_html_e( 'While our directory is full of fantastic themes, sometimes people want to use something that they know has support behind it, and don&rsquo;t mind paying for that.', 'wporg-themes' ); ?>
		<?php esc_html_e( 'The GPL doesn&rsquo;t say that everything must be zero-cost, just that when you receive the software it must not restrict your freedoms in how you use it.', 'wporg-themes' ); ?>
	</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p>
		<?php esc_html_e( 'With that in mind, here are a collection of folks who provide GPL themes with extra paid services available around them.', 'wporg-themes' ); ?>
		<?php esc_html_e( 'Some of them you may pay to access, some of them are membership sites, some may give you the theme for zero-cost and just charge for support.', 'wporg-themes' ); ?>
		<?php esc_html_e( 'What they all have in common is people behind them who support open source, WordPress, and its GPL license.', 'wporg-themes' ); ?>
	</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				/* translators: Element on page. */
				__( 'Want to see your company on this list? <a href="%s">View the requirements</a>.', 'wporg-themes' ),
				'#theme-requirements'
			)
		);
		?>
	</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"align":"wide"} -->
<h2 class="wp-block-heading alignwide"><?php esc_html_e( 'Themes List', 'wporg-themes' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query {"align":"wide","queryId":1,"query":{"perPage":12,"pages":0,"offset":0,"postType":"theme_shop","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"parents":[]},"className":"is-commercial-loop"} -->
<div class="wp-block-query is-commercial-loop alignwide">
	<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","columnCount":3}} -->
		<!-- wp:wporg/link-wrapper {"className":"is-style-no-underline"} -->
		<a class="wp-block-wporg-link-wrapper is-style-no-underline" href="">
			<!-- wp:group {"style":{"spacing":{"blockGap":"0"},"border":{"width":"1px","style":"solid"}},"borderColor":"black-opacity-15"} -->
			<div class="wp-block-group has-border-color has-black-opacity-15-border-color" style="border-style:solid;border-width:1px">
				<!-- wp:post-featured-image /-->

				<!-- wp:group {"style":{"border":{"top":{"style":"solid","width":"1px","color":"var:preset|color|black-opacity-15"},"right":{"style":"none","width":"0px"},"bottom":{"style":"none","width":"0px"},"left":{"style":"none","width":"0px"}},"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}}},"layout":{"type":"default"}} -->
				<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--black-opacity-15);border-top-style:solid;border-top-width:1px;border-right-style:none;border-right-width:0px;border-bottom-style:none;border-bottom-width:0px;border-left-style:none;border-left-width:0px;padding-top:var(--wp--preset--spacing--10);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--10);padding-left:var(--wp--preset--spacing--20)">
					<!-- wp:post-title {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}}},"textColor":"charcoal-1","fontSize":"normal","fontFamily":"inter","className":"external-link"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</a>
		<!-- /wp:wporg/link-wrapper -->
	<!-- /wp:post-template -->
</div>
<!-- /wp:query -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
<div id="theme-requirements" class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50);">
	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'If you would like to be included in this list please send your info to themes at wordpress dot org. To be included, you should:', 'wporg-themes' ); // phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText -- A spelled-out email address. ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:list -->
	<ul>
		<!-- wp:list-item -->
		<li><?php esc_html_e( 'Distribute 100% GPL themes, including artwork and CSS.', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: theme directory URL.
					__( 'Have at least one theme in the WordPress.org <a href="%s">Theme Directory</a> that is actively maintained (i.e. updated within the last year).', 'wporg-themes' ),
					home_url( '/' )
				)
			);
			?>
		</li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li><?php esc_html_e( 'Have professional support options, and optionally customization.', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li><?php esc_html_e( 'Your site should be complete, well-designed, up to date, and professional looking.', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li><?php esc_html_e( 'Provide and keep us up-to-date with a contact email address in the event we need to reach you.', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: URL to Haiku page on wikipedia.
					__( 'Provide a <a href="%s">haiku</a> (5-7-5) about yourself to be included.', 'wporg-themes' ),
					__( 'https://en.wikipedia.org/wiki/Haiku_in_English', 'wporg-themes' )
				)
			);
			?>
			</li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"60px","className":"alignwide","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|50"}}}} -->
<div style="margin-top:0;margin-bottom:var(--wp--preset--spacing--50);height:60px" aria-hidden="true" class="wp-block-spacer alignwide"></div>
<!-- /wp:spacer -->
