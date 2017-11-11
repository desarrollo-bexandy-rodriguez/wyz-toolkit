<?php
/**
 * Businesses's contact form.
 *
 * @package wyz
 */

?>

<div class="busi-contact-form">

<?php
$contact_username = '';
$contact_email = '';
$contact_phone = '';
$contact_message = '';

if ( isset( $contact_info_missing ) && true === $contact_info_missing ) {
	WyzHelpers::wyz_error( esc_html__( 'Security Violation Error', 'wyzi-business-finder' ) );
} elseif ( ( isset( $email_errors ) && '' !== $email_errors ) || ( isset( $mail_sent )  && false === $mail_sent ) ) {
	echo $email_errors;
	$contact_username = ( null !== filter_input( INPUT_POST, 'contact_username' ) ? filter_input( INPUT_POST, 'contact_username' ) : '' );
	$contact_email = ( filter_input( INPUT_POST, 'contact_email' ) ? filter_input( INPUT_POST, 'contact_email' ) : '' );
	$contact_phone = ( filter_input( INPUT_POST, 'contact_phone' ) ? filter_input( INPUT_POST, 'contact_phone' ) : '' );
	$contact_message = ( filter_input( INPUT_POST, 'contact_message' ) ? filter_input( INPUT_POST, 'contact_message' ) : '' );
} elseif ( isset( $mail_sent ) && true === $mail_sent ) {
	WyzHelpers::wyz_success( esc_html__( 'Email sent succesfully', 'wyzi-business-finder' ) );
} ?>

	<form method="post" class="wyz-form contact-form">
		<div class="input-three">
			<div class="input-box">
				<label for="name-field"><?php esc_html_e( 'name', 'wyzi-business-finder' );?><span>*</span></label>
				<input type="text" name="contact_username" id="name-field" placeholder="<?php esc_html_e( 'enter your name', 'wyzi-business-finder' );?>" value="<?php echo esc_attr( $contact_username );?>" size="30" required />
			</div>
			<div class="input-box">
				<label for="email-field"><?php esc_html_e( 'email address', 'wyzi-business-finder' );?><span>*</span></label>
				<input id="email-field" name="contact_email" type="email" value="<?php echo esc_attr( $contact_email );?>" placeholder="<?php esc_html_e( 'enter your email', 'wyzi-business-finder' );?>" required />
			</div>
			<div class="input-box">
				<label for="phone"><?php esc_html_e( 'phone number', 'wyzi-business-finder' );?></label>
				<input id="phone" name="contact_phone" type="text" value="<?php echo esc_attr( $contact_phone );?>" placeholder="<?php esc_html_e( 'enter your phone number', 'wyzi-business-finder' );?>" />
			</div>
		</div>
		<div class="input-box">
			<label for="new-message"><?php esc_html_e( 'message', 'wyzi-business-finder' );?><span>*</span></label>
			<textarea id="new-message" name="contact_message" placeholder="<?php esc_html_e( 'write a message', 'wyzi-business-finder' );?>" ><?php echo esc_html( $contact_message );?></textarea>
		</div>
		<input type="hidden" name="wyz_business_contact_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-business-contact-nonce' ) ); ?>"/>

		<input type="hidden" name="wyz_business_contact_verify" value="<?php esc_html_e( 'email sent', 'wyzi-business-finder' );?>"/>
		<input type="submit" class="wyz-primary-color wyz-prim-color" name="submit" id="submit" value="<?php esc_html_e( 'send message', 'wyzi-business-finder' );?>" />
	</form>
</div>
