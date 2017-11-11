<?php
/**
 * Contains code to manage schedualed event of deleting offers.
 *
 * @package wyz
 */

/**
 * Check for offers expiry date.
 */
function wyz_offers_expiry_check() {
	$ex_date = get_option( 'wyz_offer_expiry_date' );
	if ( 1 > $ex_date ) {
		return;
	}
	global $wpdb;
	$date = date( 'Y-m-d', strtotime( '-' . $ex_date . 'days' ) );

	$post_type = 'wyz_offers';

	$query = '
	SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = \'' . $post_type . '\' AND post_status = \'publish\' AND post_date < \'' . $date . '\'';
	$results = $wpdb->get_results( $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = \'%s\' AND post_status = \'publish\' AND post_date < \'%s\'', $post_type, $date ) ); // Db call ok; no-cache ok.
	$deleted_posts = $query;
	$deleted_posts .= 'deleted offers: ';
	foreach ( $results as $post ) {
		$deleted_posts .= $post->ID . '  ';
		wp_trash_post( $post->ID );
	}
}
add_action( 'wyz_daily_event','wyz_offers_expiry_check' );

if ( ! wp_next_scheduled( 'wyz_daily_event' ) ) {
	wp_schedule_event( time(), 'daily', 'wyz_daily_event' );
}
