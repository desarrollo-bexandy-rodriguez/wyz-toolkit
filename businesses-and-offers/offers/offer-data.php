<?php
/**
 * Data of single offer page
 *
 *  @package wyz
 */

$prefix = 'wyz_';
if ( have_posts() ) {
	the_post();

	$author_id = get_the_author_meta( 'ID' );
	$business_id = get_post_meta( get_the_ID(), 'business_id', true );
	if ( is_array( $business_id ) ) {
		$business_id = $business_id[0];
	}

	$name = get_the_title( $business_id );
	$business_link = get_permalink( $business_id );

	$category = get_the_terms( $business_id, $prefix . 'business_category' );
	if ( ! is_null( $category ) && '' !== $category ) {
		$category = $category[0];
	}
}

$description = get_post_meta( $business_id, $prefix . 'business_excerpt', true );
if ( has_post_thumbnail( $business_id ) ) {
	$logo = wp_get_attachment_url( get_post_thumbnail_id( $business_id ) );
} else {
	$logo = WyzHelpers::get_default_image( 'business' );
}
$slogan = get_post_meta( $business_id, $prefix . 'business_slogan', true );

$email1 = get_post_meta( $business_id, $prefix . 'business_email1', true );
$email2 = get_post_meta( $business_id, $prefix . 'business_email2', true );
$final_email = '';
if ( '' === $email2 ) {
	$final_email = $email1;
} elseif ( '' === $email1 ) {
	$final_email = $email2;
} else {
	$final_email = $email1 . ' - ' . $email2;
}

$id = get_the_ID();
$exrpt = get_post_meta( $id, 'wyz_offers_excerpt', true );
$desc = wpautop( get_the_content() );
$post_class = 'offer-area ' . ( 'on' === wyz_get_option( 'resp' ) ? 'col-lg-8 col-md-7' : 'col-xs-8' ) . ' col-xs-12';

$img = get_post_meta( $id, 'wyz_offers_image_id', true );
if( '' != $img)
    $img = wp_get_attachment_image( $img, 'large', true );
else
    $img = '<img src="' . WyzHelpers::get_default_image( 'offer' ) . '" class="attachment-large size-large"/>';
$dscnt = get_post_meta( $id, 'wyz_offers_discount', true );
?>
