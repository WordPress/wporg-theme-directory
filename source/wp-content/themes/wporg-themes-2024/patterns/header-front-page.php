<?php
/**
 * Title: Front Page Header
 * Slug: wporg-themes-2024/header-front-page
 * Inserter: no
 */

use const WordPressdotorg\Theme\Theme_Directory_2024\THEME_POST_TYPE;

$count       = wp_count_posts( THEME_POST_TYPE )->publish;
$count       = floor( $count / 1000 ) * 1000;
$description = sprintf(
	/* Translators: Total number of themes, rounded to thousands (ex, 12,000). */
	_n(
		'Over %s free themes to customize your WordPress site.',
		'Over %s free themes to customize your WordPress site.',
		$count,
		'wporg-themes'
	),
	number_format_i18n( $count )
);

?>
<!-- wp:wporg/global-header {"style":{"border":{"bottom":{"color":"var:preset|color|white-opacity-15","style":"solid","width":"1px"}}}} /-->

<!-- wp:wporg/local-navigation-bar {"className":"has-display-contents","backgroundColor":"charcoal-2","style":{"elements":{"link":{"color":{"text":"var:preset|color|white"},":hover":{"color":{"text":"var:preset|color|white"}}}}},"textColor":"white","fontSize":"small"} -->

	<!-- wp:site-title {"level":0,"fontSize":"small","className":"wporg-local-navigation-bar__show-on-scroll"} /-->

	<!-- wp:navigation {"menuSlug":"main","icon":"menu","overlayBackgroundColor":"charcoal-2","overlayTextColor":"white","layout":{"type":"flex","orientation":"horizontal"},"fontSize":"small"} /-->

<!-- /wp:wporg/local-navigation-bar -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"backgroundColor":"charcoal-2","className":"has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)">

	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	
		<!-- wp:site-title {"level":1,"isLink":false,"style":{"typography":{"fontSize":"50px","lineHeight":"1.2"}}} /-->

		<!-- wp:paragraph {"style":{"typography":{"lineHeight":"2.3"}},"textColor":"white"} -->
		<p class="has-white-color has-text-color" style="line-height:2.3"><?php echo esc_html( $description ); ?></p>
		<!-- /wp:paragraph -->
	
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

<!-- wp:wporg/language-suggest {"align":"full"} -->
<div class="wp-block-wporg-language-suggest alignfull"></div>
<!-- /wp:wporg/language-suggest -->

<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
