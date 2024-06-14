<?php

use function WordPressdotorg\Theme\Theme_Directory_2024\{ get_theme_patterns, get_theme_style_variations };

$current_post_id = $block->context['postId'];
if ( ! $current_post_id ) {
	return;
}

// Manually enqueue this script, so that it's available for the interactivity view script.
wp_enqueue_script( 'wp-a11y' );

$theme_post = get_post( $block->context['postId'] );
$theme = wporg_themes_theme_information( $theme_post->post_name );

$url = $theme->preview_url ?? '';
$permalink = get_permalink() . 'preview/';
$selected = array();

// Switch to using the pattern URL if a pattern is requested.
if ( isset( $_REQUEST['pattern_name'] ) ) {
	$show_pattern = wp_unslash( $_REQUEST['pattern_name'] ); // phpcs:ignore -- exact match to a given string.
	$patterns = get_theme_patterns( $theme_post->post_name );
	if ( $patterns ) {
		$matches = wp_list_filter( $patterns, [ 'name' => $show_pattern ] );
		if ( $matches ) {
			$url = current( $matches )->link;
			$selected['pattern_name'] = $show_pattern;
		}
	}
}

// Add the style variation to the URL if one is selected.
if ( isset( $_REQUEST['style_variation'] ) ) {
	$show_style = wp_unslash( $_REQUEST['style_variation'] ); // phpcs:ignore -- exact match to a given string.
	$styles = get_theme_style_variations( $theme_post->post_name );
	if ( $styles ) {
		$matches = wp_list_filter( $styles, [ 'title' => $show_style ] );
		if ( $matches ) {
			$url = add_query_arg( 'style_variation', $show_style, $url );
			$selected['style_variation'] = $show_style;
		}
	}
}

$is_valid_url = $url && 'wp-themes.com' === wp_parse_url( $url, PHP_URL_HOST );

// Initial state to pass to Interactivity API.
$init_state = [
	'isLoaded' => ! $is_valid_url,
	'url' => $url,
	'permalink' => $permalink,
	'previewBase' => $theme->preview_url,
	'selected' => $selected,
	'label' => array(
		'postNavigate' => __( 'Theme preview frame updated.', 'wporg-themes' ),
	),
];
$encoded_state = wp_json_encode( $init_state );

$markup = <<<BLOCKS
<!-- wp:columns {"align":"full","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}},"className":"wporg-theme-preview__container"} -->
<div class="wp-block-columns alignfull wporg-theme-preview__container">
	<!-- wp:column {"width":"300px","style":{"elements":{"link":{"color":{"text":"var:preset|color|blueberry-3"}}},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}},"border":{"right":{"color":"var:preset|color|white-opacity-15","style":"solid","width":"1px"},"top":{},"bottom":{},"left":{}}},"backgroundColor":"charcoal-1","textColor":"white"} -->
	<div class="wp-block-column has-white-color has-charcoal-1-background-color has-text-color has-background has-link-color" style="border-right-color:var(--wp--preset--color--white-opacity-15);border-right-style:solid;border-right-width:1px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20);flex-basis:300px">%s</div>
	<!-- /wp:column -->
		
	<!-- wp:column {"width":""} -->
	<div class="wp-block-column">
		<!-- wp:wporg/theme-previewer-iframe {"url":"%s"} /-->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
BLOCKS;

$html = new WP_HTML_Tag_Processor( $content );
while ( $html->next_tag( [ 'class_name' => 'wporg-theme-listbox' ] ) ) {
	$html->set_attribute( 'data-wp-on--wporg-select', 'wporg/themes/preview::actions.navigateIframe' );
	$html->set_attribute( 'data-wp-on--wporg-unselect', 'wporg/themes/preview::actions.navigateIframe' );
}

$content = $html->get_updated_html();

$markup = sprintf( $markup, $content, esc_url_raw( $url ) );

?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
	data-wp-interactive="wporg/themes/preview"
	data-wp-context="<?php echo esc_attr( $encoded_state ); ?>"
>
	<?php echo do_blocks( $markup ); // phpcs:ignore ?>
</div>
