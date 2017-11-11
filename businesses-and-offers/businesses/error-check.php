<?php
$errors = '';

$correlation = array(
	'name'			=>'wyz_business_name',
	'logo' 			=>'wyz_business_logo',
	'logoBg'		=>'wyz_business_logo_bg',
	'desc' 			=>'wyz_business_excerpt',
	'about' 		=>'wyz_business_description',
	'slogan' 		=>'wyz_business_slogan',
	'category' 		=>'wyz_business_categories',
	'categoryIcon' 	=>'wyz_business_category_icon',
	'bldg' 			=>'wyz_business_bldg',
	'street' 		=>'wyz_business_street',
	'city' 			=>'wyz_business_city',
	'location' 		=>'wyz_business_country',
	'addAddress' 	=>'wyz_business_addition_address_line',
	'map' 			=>'wyz_business_location',
	'phone1' 		=>'wyz_business_phone1',
	'phone2' 		=>'wyz_business_phone2',
	'email1'		=>'wyz_business_email1',
	'email2' 		=>'wyz_business_email2',
	'website'		=>'wyz_business_website',
	'fb' 			=>'wyz_business_facebook',
	'twitter' 		=>'wyz_business_twitter',
	'gplus' 		=>'wyz_business_google_plus',
	'linkedin' 		=>'wyz_business_linkedin',
	'youtube' 		=>'wyz_business_youtube',
	'insta' 		=>'wyz_business_instagram',
	'flicker' 		=>'wyz_business_flicker',
	'pinterest' 	=>'wyz_business_pinterest',
	'comments' 		=>'wyz_business_comments',
	'tags' 			=>'wyz_business_tags'
);

$wyz_business_form_data = get_option( 'wyz_business_form_builder_data', array() );

if ( ! empty( $wyz_business_form_data ) ) {
	foreach ( $wyz_business_form_data as $key => $value ) {
		if ( 'map' == $value['type'] && ! empty( $value['required'] ) ) {
			if ( ! isset( $_POST[ 'wyz_business_location' ] ) ) {
				$errors .=  sprintf( esc_html__( '%s is Required.' , 'wyzi-business-finder' ), $value['label'] ) . "\n ";
			} else {
				$temp_location = $_POST[ 'wyz_business_location' ];
				if ( empty( $temp_location['latitude'] ) || empty( $temp_location['longitude'] ) ) {
					$errors .=  sprintf( esc_html__( '%s is Required.' , 'wyzi-business-finder' ), $value['label'] ) . "\n ";
				}
			}
		} elseif ( ! empty( $value['required'] ) && isset( $correlation[ $value['type'] ] ) && ( ! isset($_POST[ $correlation[ $value['type'] ] ] ) || empty( $_POST[ $correlation[ $value['type'] ] ] ) ) ){
			$errors .=  sprintf( esc_html__( '%s is Required.' , 'wyzi-business-finder' ), $value['label'] ) . "\n ";
		}
	}
}

$time_fields = array( 'wyz_business_open_monday', 'wyz_business_open_tuesday', 'wy_business_open_wednesday', 'wyz_business_open_thursday', 'wyz_business_open_friday', 'wyz_business_open_saturday', 'wyz_business_open_sunday', 'wyz_business_close_monday', 'wyz_business_close_tuesday', 'wy_business_close_wednesday', 'wyz_business_close_thursday', 'wyz_business_close_friday', 'wyz_business_close_saturday', 'wyz_business_close_sunday' );

$days = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

$i = 0;
foreach ( $time_fields as $time ) {
	$t = filter_input( INPUT_POST, $time );
	if ( $t && '' !== $t && ! WyzHelpers::wyz_date_auth( $t ) ) {
		$errors .= esc_html__( 'Incorrect time format for Time ' .( $i < 7 ? 'Open ' : 'Close ' ) . $days[ $i % 7 ] . '.', 'wyzi-business-finder' ) . "\n ";
	}
	$i++;
}

?>
