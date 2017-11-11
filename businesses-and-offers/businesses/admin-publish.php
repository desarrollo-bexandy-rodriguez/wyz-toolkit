<?php
/**
 * Backend notofocations display on business publish.
 *
 * @package wyz
 */

/**
 * Allow admin Register businesses.
 */
function wyz_admin_business_publish() {
	if ( ! is_admin() ) {
		return;
	}
	global $current_screen;
	if ( 'wyz_business' === $current_screen->post_type && filter_input( INPUT_GET, 'action' ) && 'edit' === filter_input( INPUT_GET, 'action' ) ) {
		$post = get_post( filter_input( INPUT_GET, 'post' ) );

		if ( ! $post  ) {
			return;
		}

		$points_available = get_the_author_meta( 'points_available', $post->post_author );

		if ( '' === $points_available || $points_available < get_option( 'wyz_businesses_registery_price' ) ) {
			echo '<div class="error"><p>' . esc_html__( 'This user doesn\'t have enough points to register his business', 'wyzi-business-finder' ) . '</p></div>';
		} else {
			$can_publish = true;
			echo '<div class="updated"><p>' . esc_html__( 'This user has enough points to create a business', 'wyzi-business-finder' ) . '</p></div>';
		}
	}
}
add_action( 'edit_form_after_title', 'wyz_admin_business_publish' );

/**
 * Subtract from user points on business creaion.
 */
function wyz_business_publish_override( $post_ID, $post ) {
	if ( ! is_admin() ) {
		return;
	}

	// A function to perform actions when a post is published.
	$points_available = 0;
	$points_available = intval( get_the_author_meta( 'points_available', $post->post_author ) );

	if ( $points_available < intval( get_option( 'wyz_businesses_registery_price' ) ) ) {
		// Nothing to see here.
	} else {
		$points_left = intval( $points_available - intval( get_option( 'wyz_businesses_registery_price' ) ) );
		update_user_meta( $post->post_author, 'points_available', $points_left );
	}
}
add_action( 'publish_offers', 'wyz_business_publish_override', 10, 2 );
?>
