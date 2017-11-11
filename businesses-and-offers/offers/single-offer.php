<?php
/**
 * Template Name: Offers Template.
 *
 * @package wyz
 */

require_once( plugin_dir_path( __FILE__ ) . 'offer-data.php' );

get_header();

/**
 * Enqueue single offer js.
 */
function wyz_offer_js_enqueue() {
	wp_localize_script( 'wyz_single_business_js', 'business', array(
		'showMore' => esc_html__( 'Show More', 'wyzi-business-finder' ),
		'showLess' => esc_html__( 'Show Less','wyzi-business-finder' ),
		'isBusiness' => false,
	) );
	wp_enqueue_script( 'wyz_single_business_js' );
}
add_action( 'wp_footer', 'wyz_offer_js_enqueue' );

global $template_type;
$header_content = get_option( 'wyz_business_header_content' );
if ( '' == $header_content ) $header_content = 'map';

if ( 'on' !== get_option('wyz_business_map_hide_in_single_bus' ) && WyzHelpers::wyz_sub_can_bus_owner_do( WyzHelpers::wyz_the_business_author_id(),'wyzi_sub_business_show_map' ) ) {
	if ( $header_content == 'map' ) {
		if ( $template_type == 1 ) {
			WyzMap::wyz_the_business_map( $business_id, true );
		} else {
			WyzMap::listing_single_business_map( $business_id );
		}
	} elseif ( $header_content == 'image' ) {
		WyzMap::wyz_get_business_header_image( $business_id );
	}
	
}

//WyzHelpers::wyz_the_business_subheader( $business_id/*, $logo, $name, $slogan, $description*/ );


require_once( plugin_dir_path( __FILE__ ) . "/templates/single-offer-$template_type.php" );

get_footer();
