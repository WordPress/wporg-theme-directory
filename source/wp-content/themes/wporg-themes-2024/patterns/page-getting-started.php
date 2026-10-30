<?php
/**
 * Title: Page: Getting Started
 * Slug: wporg-themes-2024/page-getting-started
 * Inserter: no
 */

?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group alignwide">
	<!-- wp:heading {"level":1,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
	<h1 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--30)"><?php esc_html_e( 'Getting started', 'wporg-themes' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'The WordPress Theme Directory is used by millions of WordPress users all over the world. Themes in the directory are available for download from WordPress.org, and WordPress users can also install them directly from their administration screens.', 'wporg-themes' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'By hosting your theme on WordPress.org, you&#8217;ll get:', 'wporg-themes' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:list -->
	<ul>
		<!-- wp:list-item -->
		<li><?php esc_html_e( 'Stats on how many times your theme has been downloaded', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li><?php esc_html_e( 'User feedback in the forums', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li><?php esc_html_e( 'Ratings, to see what users think of your theme', 'wporg-themes' ); ?></li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->

	<!-- wp:paragraph -->
	<p>
		<?php esc_html_e( 'The goal of the Theme Directory isn&#8217;t to host every theme in the world, it&#8217;s to host the best open source WordPress themes around.', 'wporg-themes' ); ?>
		<?php esc_html_e( 'Themes hosted on WordPress.org pass on the same user freedoms as WordPress itself; this means that they are 100% GPL or compatible.', 'wporg-themes' ); ?>
	</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading -->
	<h2 class="wp-block-heading"><?php esc_html_e( 'Guidelines &amp; resources', 'wporg-themes' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'To ensure that WordPress users are guaranteed a good experience, every theme in the directory is reviewed by the Themes team. Your theme will have the best chance of being approved if you:', 'wporg-themes' ); ?></p>
	<!-- /wp:paragraph -->


	<!-- wp:list -->
	<ul>
		<!-- wp:list-item -->
		<li>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %1$s: Theme review handbook URL, %2$s: Theme review requirements handbook URL.
					__( 'Read the <a href="%1$s">theme review guidelines</a> and follow the <a href="%2$s">submission requirements</a>.', 'wporg-themes' ),
					'https://make.wordpress.org/themes/handbook/review/',
					'https://make.wordpress.org/themes/handbook/review/required/'
				)
			);
			?>
		</li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: Theme unit test Codex URL.
					__( 'Test your theme using the <a href="%s">Theme Unit Test data</a> before you upload and submit.', 'wporg-themes' ),
					'https://codex.wordpress.org/Theme_Unit_Test'
				)
			);
			?>
		</li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->

	<!-- wp:paragraph -->
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				// translators: %s: Theme handbook on Developer Resources URL.
				__( 'For more theme development resources, check out the <a href="%s">Theme Developer Handbook</a>.', 'wporg-themes' ),
				__( 'https://developer.wordpress.org/themes/', 'wporg-themes' )
			)
		);
		echo ' ';
		echo wp_kses_post(
			sprintf(
				// translators: %s: URL to the themes forum on support.
				__( 'For specific questions, please use the <a href="%s">Themes and Templates forum</a>.', 'wporg-themes' ),
				__( 'https://wordpress.org/support/forum/themes-and-templates', 'wporg-themes' )
			)
		);
		?>
	</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/upload/' ) ); ?>"><?php esc_html_e( 'Upload your theme', 'wporg-themes' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"60px","className":"alignwide","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|50"}}}} -->
<div style="margin-top:0;margin-bottom:var(--wp--preset--spacing--50);height:60px" aria-hidden="true" class="wp-block-spacer alignwide"></div>
<!-- /wp:spacer -->
