<?php

$errors = '';

// Check empty required fields.
if ( empty( $_POST['wyz_offers_title'] ) ) {
	$errors .= '<p>' . sprintf( esc_html__( '%s Name is Required', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p>';
}
if ( empty( $_POST['wyz_offers_excerpt'] ) ) {
	$errors .= '<p>' . sprintf( esc_html__( '%s Description is Required', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p>';
}
if ( empty( $_POST['wyz_offers_description'] ) ) {
	$errors .= '<p>' . sprintf( esc_html__( 'Information About Your %s is Required', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p>';
}
if ( empty( $_POST['wyz_offers_image'] ) ) {
	$errors .= '<p>' . sprintf( esc_html__( '%s Image is Required', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p>';
}
if ( '0' == $_POST['wyz_offers_category_check'] ) {
	$errors .= '<p>' . sprintf( esc_html__( '%s Category is Required', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p>';
}
?>
