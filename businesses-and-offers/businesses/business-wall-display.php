<?php
/**
 * Business wall template
 *
 * @package wyz
 */

/**
 * Fired by the shortcode to display businesses wall.
 *
 * @param array $atts shortcode attributes.
 */

global $show_wall_footer;

function wyz_display_wall( $atts ) {

	global $show_wall_footer;

	$atts = shortcode_atts( array( 'posts_pull' => '10', 'pull_method' => 'auto','display_footer' => 0, 'category' => '' ), $atts );
	$show_wall_footer = $atts['display_footer'];
	wp_get_current_user();

	wp_enqueue_script( 'wyz_business_post_like' );
	
	add_action( 'wp_footer', 'wyz_localize_scripts' );

	ob_start();
	echo '<div id="postswrapper">';
	
	wp_localize_script( 'wyz_wall_js', 'walll', array( 
		'ind' => isset( $last_index ) ? $last_index : -1,
		'posts_pull' => $atts['posts_pull'],
		'pull_method' => $atts['pull_method'],
		'category' => $atts['category']
	) );

	echo '</div>';
	echo '<div id="loadmoreajaxloader" class="blog-pagination" style="opacity:0;"><div class="loading-spinner"><div class="dot1 wyz-primary-color wyz-prim-color"></div><div class="dot2 wyz-primary-color wyz-prim-color"></div></div></div>';

	return ob_get_clean();
}

/**
 * Localize wall scripts.
 */
function wyz_localize_scripts() {
	global $wpdb;
	global $current_user;
	wp_get_current_user();
	
	$args = array(
		'post_type' => 'wyz_business_post',
		'post_status' => 'publish',
		'posts_per_page' => 1,
	);
	$query = new WP_Query( $args );
	$have_posts = $query->have_posts();
	if ( $have_posts ) {
		$po = $query->the_post();
		wp_reset_postdata();
	}

	$wall_data = array(
		'hasPosts'      => $have_posts,
		'postIndx'      => -1,
		'loggedInUser'  => wp_json_encode( is_user_logged_in() ),
		'noPostsMsg' => esc_html__( 'No more posts to show.', 'wyzi-business-finder' ),
		'loadMoreMsg' => esc_html__( 'Load More', 'wyzi-business-finder' ),
		'loginPermalink' => home_url( '/signup/?action=login' ),
	);
	wp_localize_script( 'wyz_wall_js', 'wall', $wall_data );
	wp_enqueue_script( 'wyz_wall_js' );
	wp_enqueue_script( 'jQuery-inview' );
}
?>
