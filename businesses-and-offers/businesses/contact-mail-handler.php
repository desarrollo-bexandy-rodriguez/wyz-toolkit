<?php
/**
 * Contact form handler
 *
 * @package wyz
 */

$mail_sent;

$contact_info_missing = false;

if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'wyz_business_contact_nonce' ), 'wyz-business-contact-nonce' ) ) {
	$contact_info_missing = true;
} else {
	$email_errors = '';

	if ( ! filter_input( INPUT_POST, 'contact_username' ) || '' === filter_input( INPUT_POST, 'contact_username' ) ) {
		$email_errors .= esc_html__( 'Name is required', 'wyzi-business-finder' ) . '<br/>';
	}
	if ( ! filter_input( INPUT_POST, 'contact_email' ) || '' === filter_input( INPUT_POST, 'contact_email' ) ) {
		$email_errors .= esc_html__( 'Email is required', 'wyzi-business-finder' ) . '<br/>';
	} elseif ( ! filter_var( filter_input( INPUT_POST, 'contact_email' ), FILTER_VALIDATE_EMAIL ) ) {
		$email_errors .= esc_html__( 'Invalid email format', 'wyzi-business-finder' ) . '<br/>';
	}

	if ( ! filter_input( INPUT_POST, 'contact_message' ) || '' === filter_input( INPUT_POST, 'contact_message' ) ) {
		$email_errors .= esc_html__( 'Message field cannot be empty', 'wyzi-business-finder' ) . '<br/>';
	}
	if ( '' === $email_errors ) {
		
		global $wpdb;
		$to = $wpdb->get_results( 'SELECT user_id FROM ' . $wpdb->prefix . 'usermeta WHERE meta_key = "business_id" and meta_value = ' . $id , OBJECT );
		if ( empty( $to ) ) {
			$to = get_option('admin_email');
		} else {
			if ( is_array( $to ) ) {
				$to = $to[0];
			}
			$to = $to->user_id;
			$to = get_userdata( $to );
			$to = $to->user_email;
		}
		
		$sender_name = wp_filter_nohtml_kses( $_POST['contact_username'] );
		$email = wp_filter_nohtml_kses( $_POST['contact_email'] );
		$phone = wp_filter_nohtml_kses( $_POST['contact_phone'] );
		$content = wp_filter_nohtml_kses( $_POST['contact_message'] );

		$message  = wyz_get_option( 'business-contact-mail' );

		$message = str_replace( '%NAME%', $sender_name, $message );
		$message = str_replace( '%BUSINESSNAME%', $name, $message );
		$message = str_replace( '%EMAIL%', $email, $message );
		$message = str_replace( '%PHONE%', $phone, $message );
		$message = str_replace( '%MESSAGE%', $content, $message );


		$subject = esc_html__( 'You got a new Email from', 'wyzi-business-finder' ) . ' {' . home_url() . '}';
		if( class_exists( 'WyzHelpers' ) ) {
			$mail_sent = WyzHelpers::wyz_mail( $to, $subject, $message, 'contact' );
		}
	}
}
?>
