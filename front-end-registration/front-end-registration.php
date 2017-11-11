<?php
/**
 * Front end registration nitializer.
 *
 * @package wyz
 */

$mail_success = '';

/**
 * Display signup form.
 */

//require_once (dirname(__FILE__) . '/social-login/facebook/facebook-connect.php');
require_once (dirname(__FILE__) . '/social-login/facebook/facebook-login.php');
require_once (dirname(__FILE__) . '/social-login/google/google-connect.php');
function wyz_signup_display() {
	global $wpdb;
	global $template_type;
	if ( 1 != get_option( 'users_can_register' ) && (! isset( $_GET['action'] ) || 'login' != $_GET['action'] ) ) {
		return WyzHelpers::wyz_error( esc_html__( 'User Registration is not enabled', 'wyzi-business-finder' ),true);
	}

	if ( is_user_logged_in() ) 
		return WyzHelpers::wyz_warning( esc_html__( 'You are already logged in.', 'wyzi-business-finder' ),true );

	if ( isset( $_GET['reset-pass'] ) && true == $_GET['reset-pass'] ) {
		return wyz_reset_pass_form();
	} elseif ( isset( $_GET['key'] ) && 'reset_pwd' === $_GET['action'] ) {
		$reset_key = $_GET['key'];
		$user_login = $_GET['login'];
		$user_data = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login ) );
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		if ( ! empty( $reset_key ) && ! empty( $user_data ) ) {
			$from = get_bloginfo( 'name' );
			$no_reply = 'no-reply-@' . get_option( 'siteurl' );
			$semi_rand = md5( time() );
			$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

			$headers = array(
							"Reply-To: \"$no_reply\"" ,
							"MIME-Version: 1.0",
							"Content-type: text/html; charset=UTF-8",
							" boundary=\"{$mime_boundary}\""
			);
			$headers = implode( "\r\n" , $headers );

			$new_password = wp_generate_password( 7, false );
			// Mailing the reset details to the user.
			$message = esc_html__( 'Your new password for the account at', 'wyzi-business-finder' ) . ":\r\n\r\n";
			$message .= get_bloginfo( 'name' ) . "\r\n\r\n";
			$message .= esc_html__( 'Username', 'wyzi-business-finder' ) . ': ' . $user_login . "\r\n\r\n";
			$message .= esc_html__( 'Password', 'wyzi-business-finder' ) . ': ' . $new_password . "\r\n\r\n";
			$message .= esc_html__( 'You can now login with your new password at', 'wyzi-business-finder' ) . ': ' . get_option( 'siteurl' ).'/login' . "\r\n\r\n";
			if ( $message && ! wp_mail( $user_email, esc_html__( 'Password Reset Request', 'wyzi-business-finder' ), $message, $headers ) ) {
				WyzHelpers::wyz_error( esc_html__( 'Sending email failed', 'wyzi-business-finder' ));
				exit();
			} else {
				wp_set_password( $new_password, $user_data->ID );
				WyzHelpers::wyz_success( esc_html__( 'Password reset Complete', 'wyzi-business-finder' ));
				echo '<p>' . esc_html__( 'We have sent you an email with your new password', 'wyzi-business-finder' ) . '</p>';
			}
		} else {
			exit( WyzHelpers::wyz_error( esc_html__( 'Not a Valid Key', 'wyzi-business-finder' ),true) );
		}
	} else {
		if ( 1 == $template_type ) {
			if ( isset( $_GET['action'] ) && 'login' == $_GET['action'] ) {
				return wyz_login_form();
			} else {
				return wyz_registration_form();
			}
		} elseif ( 2 == $template_type ) {
			return wyz_login_registration_form();
		}
	}
}
add_shortcode( 'wyz_signup_form', 'wyz_signup_display' );

/**
 * User registration form.
 */
function wyz_registration_form() {
	// Only show the registration form to non-logged-in members.
	if ( ! is_user_logged_in() ) {
		add_action( 'wp_footer', 'wyz_add_pass_strength_script' );
		$output = wyz_registration_form_fields();
		return $output;
	} else {
		return esc_html__( 'You are already logged in', 'wyzi-business-finder' );
	}
}

function wyz_add_pass_strength_script() {
	wp_enqueue_script( 'wyz_pass_strength_js' );
}


/**
 * User login form.
 */
function wyz_login_form() {
	if ( ! is_user_logged_in() ) {
		$output = wyz_login_form_fields();
	} else {
		$output = WyzHelpers::wyz_warning( esc_html__( 'You are already logged in.', 'wyzi-business-finder' ),true);
	}
	return $output;
}

function wyz_reset_pass_form() {
	if ( ! is_user_logged_in() ) {
		$output = wyz_reset_pass_form_fields();
	} else {
		return WyzHelpers::wyz_warning( esc_html__( 'You are already logged in.', 'wyzi-business-finder' ),true);
	}
	return $output;
}

function wyz_reset_pass_form_fields() {
	ob_start();
	global $mail_success;
	global $template_type;
	wyz_show_error_messages();
	if ( '' != $mail_success ) {
		WyzHelpers::wyz_success( $mail_success );
	}
	if ( 2 == $template_type )
		$submit_button = '<button id="submit" type="submit" class="action-btn btn-bg-blue btn-rounded">' . esc_html__( 'GET NEW PASSWORD', 'wyzi-business-finder' ) . '</button>';
	else
		$submit_button = '<button id="submit" type="submit" class="wyz-button wyz-primary-color wyz-prim-color icon">' . esc_html__( 'GET NEW PASSWORD', 'wyzi-business-finder' ) . '<i class="fa fa-angle-right"></i></button>';?>
	<!-- <div class="section-title col-xs-12 margin-bottom-50">
		<h1><?php  esc_html_e( 'Reset Password', 'wyzi-business-finder' ); ?></h1>
	</div> -->
	<div class="login-form col-lg-6 col-md-7 col-xs-12 fix">
		<form id="wyz_registration_form" method="POST">
			<div class="input-two  mb-25">
				<div class="input-box">
					<label for="wyz_reset_Identifier"><?php esc_html_e( 'Username/E-mail:', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_Identifier" id="wyz_user_Identifier" class="text-input" type="text"/>
				</div>
			</div>
			<input type="hidden" name="wyz_reset_pass_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-reset_pass-nonce' ) ); ?>"/>
			<?php echo $submit_button;?>
		</form>
	</div>
	<?php return ob_get_clean();
}

// registration form fields
function wyz_registration_form_fields() {
	ob_start();

	$user_login = isset( $_POST['wyz_user_register'] ) ? $_POST['wyz_user_register'] : '';
	$user_email = isset( $_POST['wyz_user_email'] ) ? $_POST['wyz_user_email'] : '';
	$user_first = isset( $_POST['wyz_user_first'] ) ? $_POST['wyz_user_first'] : '';
	$user_last = isset( $_POST['wyz_user_last'] ) ? $_POST['wyz_user_last'] : '';
	$subscribtion = isset( $_POST['subscribtion'] ) ? $_POST['subscribtion'] : '';?>
	
	<?php wyz_show_error_messages(); ?>
	<div class="section-title text-center col-xs-12 margin-bottom-50">
		<h1><?php  esc_html_e( 'Sign Up To Your Account', 'wyzi-business-finder' ); ?></h1>
	</div>
	<?php $offset = '';
	if ( is_page_template( 'templates/full-width-page.php' ) || ( is_page_template( 'default_template' ) && wyz_get_option( 'sidebar-layout' ) == 'full-width' ) ) {
		$offset = 'col-lg-6 col-lg-offset-3 ';
	}
	?>
<div class="register-form text-center <?php echo $offset;?>col-md-offset-2 col-md-8 col-xs-12">
		
		<form id="wyz_registration_form" class="wyz-form" method="POST">
			
			<div class="input-two space-80">
				<div class="input-box">
					<label for="wyz_user_register"><?php esc_html_e( 'Username', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_register" id="wyz_user_register" type="text" value="<?php echo esc_attr( $user_login );?>" required/>
				</div>
				<div class="input-box">
					<label for="wyz_user_email"><?php esc_html_e( 'Email', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_email" id="wyz_user_email" type="email" value="<?php echo esc_attr( $user_email );?>" required/>
				</div>
			</div>
			<div class="input-two space-80">
				<div class="input-box">
					<label for="wyz_user_first"><?php esc_html_e( 'First Name', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_first" id="wyz_user_first" class="text-input" type="text" value="<?php echo esc_attr( $user_first );?>" required/>
				</div>
				<div class="input-box">
					<label for="wyz_user_last"><?php esc_html_e( 'Last Name', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_last" id="wyz_user_last" class="text-input" type="text" value="<?php echo esc_attr( $user_last );?>" required/>
				</div>
			</div>
			<div class="input-two space-80">
				<div class="input-box">
					<label for="password"><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_pass" id="password" type="password" required/>
				</div>
				<div class="input-box">
					<label for="password_again"><?php esc_html_e( 'Password Again', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_pass_confirm" id="password_again" type="password" required/>
				</div>
			</div>
			<div>
				<span id="password-strength"></span>
			</div>
			<?php $user = wp_get_current_user();
			$def_role = get_option( 'wyz_reg_def_user_role' );
			if ( 'client' != $def_role && 'business_owner' != $def_role ) {?>
			<div class="input-box">
				<label for="subscribtion"><?php esc_html_e( 'Subscription', 'wyzi-business-finder' );?></label>
				<select name="subscribtion" id="subscribtion" required>
					<option value=""><?php esc_html_e( 'Select your subscription...', 'wyzi-business-finder' );?></option>
					<option value="client" <?php echo ( 'client' == $subscribtion ? 'selected' : '' ); ?>>
						<?php echo esc_html( get_option( 'wyz_businesses_user_client' ) ); ?>
					</option>
					<option value="business_owner" <?php echo ( 'business_owner' == $subscribtion ? 'selected' : '' ); ?>>
						<?php echo esc_html( get_option( 'wyz_businesses_user_owner' ) ); ?>
					</option>
				</select>
			</div>
			<?php }?>
			<input type="hidden" name="wyz_register_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-register-nonce' ) ); ?>"/>
			<button id="submit" type="submit" class="wyz-button wyz-secondary-color icon" value=""><?php esc_html_e( 'SIGN UP', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
			<?php if ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( 'terms-and-cond-on-off' ) ) {?>
			<div class="terms-and-cond fix">
				<?php wyz_extract_termsandconditions();?>
			</div>
			<?php }?>
			<div class="social-login-container">
			<?php if ( '' != get_option( 'wyz_fb_app_id' ) && '' != get_option( 'wyz_fb_app_secret' ) ) {
			/*<a href="<?php echo wp_login_url() . '?loginFacebook=1&redirect=' . home_url( '/user-account/' );?>" class="wyz-button blue icon social-login facebook"><i class="fa fa-facebook"></i><?php esc_html_e( 'Sign Up with facebook', 'wyzi-business-finder' );?></a>*/
				echo do_shortcode( '[fbl_login_button]' );
			}
				if ( '' != get_option( 'wyz_google_client_id' ) && '' != get_option( 'wyz_google_client_secret' ) && '' != get_option( 'wyz_google_developer_key' ) ){ ?>
			<a href="<?php echo wp_login_url() . '?loginGoogle=1&redirect=' . home_url( '/user-account/' );?>" class="wyz-button icon social-login google"><i class="fa fa-google"></i><?php esc_html_e( 'Sign Up with google', 'wyzi-business-finder' );?></a>
			<?php }?>
			</div>
			<p id="have-acc" class="margin-top-15  wyz-prim-color-txt wyz-primary-color-text"><?php esc_html_e( 'Already have an account?', 'wyzi-business-finder' );?> <a href="<?php echo esc_url( home_url( '/signup/?action=login' ) );?>" class="link"><?php esc_html_e( 'Login', 'wyzi-business-finder' );?></a></p>
		</form>
	</div>
	
	<?php return ob_get_clean();
}

function wyz_extract_termsandconditions() {
	if ( ! function_exists( 'wyz_get_option' ) )
		return;

	$str = wyz_get_option( 'terms-and-conditions' );

	preg_match( '/\%.*\%/', $str, $matches);

	if ( empty( $matches ) ) return;

	foreach ( $matches as $match ) {
		$str = str_replace( $match, '<a class="wy-link" href="' . esc_url( home_url( '/terms-and-conditions/' ) ) . '">' . substr( $match, 1, strlen( $match ) -2 ) . '</a>' , $str );
	}

	echo $str;
}

/**
 * Login form fields.
 */
function wyz_login_form_fields() {
	ob_start();
	$reset_pass_query = WyzHelpers::add_clear_query_arg( array( 'reset-pass' => true ) );
	// Show any error messages after form submission.
	wyz_show_error_messages(); ?>
	
	<div class="section-title col-xs-12 margin-bottom-50">
		<h1><?php  esc_html_e( 'Sign In To Your Account', 'wyzi-business-finder' ); ?></h1>
	</div>
	<!-- col-lg-6 col-md-7-->
	<div class="login-form col-xs-12 fix">
		<form id="wyz_registration_form" class="wyz-form" method="POST">
			<div class="input-two">
				<div class="input-box">
					<label for="wyz_user_login"><?php esc_html_e( 'Username', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_login" id="wyz_user_login" class="text-input" type="text"/>
				</div>
				<div class="input-box">
					<label for="wyz_user_pass_login"><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_pass_login" id="wyz_user_pass_login" class="text-input" type="password"/>
				</div>
			</div>
			<input type="hidden" name="wyz_login_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-login-nonce' ) ); ?>"/>
			<div class="remember-forget-pass fix">
				<input type="checkbox" id="remember" name="remember-me" />
				<label for="remember"><?php esc_html_e( 'Remember me', 'wyzi-business-finder' ); ?></label>
				<a id="forgot-pass" class="wyz-primary-color-text wyz-prim-color-txt" href="<?php echo esc_url( $reset_pass_query );?>"> <?php esc_html_e( 'Forgot Password?', 'wyzi-business-finder' );?></a>
			</div>
			<button id="wyz_login_submit" type="submit" class="wyz-button wyz-primary-color wyz-prim-color icon"><?php esc_html_e( 'SIGN IN', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>

			<div class="social-login-container">
				<?php if ( '' != get_option( 'wyz_fb_app_id' ) && '' != get_option( 'wyz_fb_app_secret' ) ) {
					echo do_shortcode( '[fbl_login_button]' );
				}
				if ( '' != get_option( 'wyz_google_client_id' ) && '' != get_option( 'wyz_google_client_secret' ) && '' != get_option( 'wyz_google_developer_key' ) ){ ?>
				<a href="<?php echo wp_login_url() . '?loginGoogle=1&redirect=' . home_url( '/user-account/' );?>" class="wyz-button icon social-login google"><i class="fa fa-google"></i><?php esc_html_e( 'Sign In with google', 'wyzi-business-finder' );?></a>
				<?php }?>
			</div>

		</form>
	</div>
	<?php return ob_get_clean();
}


function wyz_login_registration_form() {
	add_action( 'wp_footer', 'wyz_add_pass_strength_script' );
	ob_start();

	$user_login = isset( $_POST['wyz_user_register'] ) ? $_POST['wyz_user_register'] : '';
	$user_email = isset( $_POST['wyz_user_email'] ) ? $_POST['wyz_user_email'] : '';
	$user_first = isset( $_POST['wyz_user_first'] ) ? $_POST['wyz_user_first'] : '';
	$user_last = isset( $_POST['wyz_user_last'] ) ? $_POST['wyz_user_last'] : '';
	$subscribtion = isset( $_POST['subscribtion'] ) ? $_POST['subscribtion'] : '';
	$reset_pass_query = WyzHelpers::add_clear_query_arg( array( 'reset-pass' => true ) );

	$login_active = ( isset( $_GET['action'] ) && 'login' == $_GET['action'] ? 'active' : '' );
	$register_active = ( '' == $login_active ? 'active' : '' );?>
	
	<?php wyz_show_error_messages(); ?>

	<!-- Sidebar Wrapper -->
	<!-- <div class="col-md-7 col-sm-8 col-md-offset-0 col-sm-offset-2 col-xs-12"> -->
		<div class="login-reg-forms">
			<!-- Login Register Tab List -->
			<ul class="login-reg-tab-list mb-50">
				<li class="<?php echo $login_active;?>"><a class="wyz-prim-color-txt-hover" href="#login-form" data-toggle="tab">login</a></li>
				<li class="<?php echo $register_active;?>"><a class="wyz-prim-color-txt-hover" href="#reg-form" data-toggle="tab">register</a></li>
			</ul>

			<!-- Login Register Tab Content -->
			<div class="login-reg-tab-content tab-content">
				<div class="tab-pane <?php echo $login_active;?>" id="login-form">
					<form action="#" class="login-reg-form" method="post">
						<div class="row">
							<div class="col-xs-12 mb-50">
								<label for="wyz_user_login"><?php esc_html_e( 'Username/Email', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_login" name="wyz_user_login" type="text" required/>
							</div>
							<div class="col-xs-12 mb-25">
								<label for="wyz_user_pass_login"><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_pass_login" name="wyz_user_pass_login" type="password" required/>
							</div>
							<input type="hidden" name="wyz_login_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-login-nonce' ) ); ?>"/>
							<div class="col-xs-12 mb-25">
								<input id="login-remember-pass" name="remember-me" type="checkbox">
								<label for="login-remember-pass"><?php esc_html_e( 'Remember me', 'wyzi-business-finder' ); ?></label>
								<a id="forgot-pass" href="<?php echo esc_url( $reset_pass_query );?>"><?php esc_html_e( 'Forgot Password?', 'wyzi-business-finder' );?></a>
							</div>
							<div class="col-xs-12">
								<button id="submit" type="submit" class="action-btn btn-bg-blue wyz-prim-color btn-rounded"><?php esc_html_e( 'SIGN IN', 'wyzi-business-finder' ); ?></button>
								<?php wyz_this_get_login_social_options();?>
							</div>
						</div>
					</form>
				</div>
				<div class="tab-pane <?php echo $register_active;?>" id="reg-form">
					<form id="wyz_registration_form" class="login-reg-form" method="POST">
						<div class="row">
							<div class="col-xs-12 mb-50">
								<label for="wyz_user_register"><?php esc_html_e( 'Username', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_register" name="wyz_user_register" type="text" value="<?php echo esc_attr( $user_login );?>" placeholder="<?php esc_html_e( 'Enter your Username', 'wyzi-business-finder' );?>" required/>
							</div>
							<div class="col-xs-12 mb-50">
								<label for="wyz_user_email"><?php esc_html_e( 'Email', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_email" name="wyz_user_email" type="email" value="<?php echo esc_attr( $user_email );?>" placeholder="<?php esc_html_e( 'Enter your e-mail address', 'wyzi-business-finder' );?>" required/>
							</div>
							<div class="col-md-6 col-xs-12 mb-50 ">
								<label for="wyz_user_first"><?php esc_html_e( 'First Name', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_first" name="wyz_user_first" type="text" value="<?php echo esc_attr( $user_first );?>" placeholder="<?php esc_html_e( 'Enter your First Name', 'wyzi-business-finder' );?>" required/>
							</div>
							<div class="col-md-6 col-xs-12 mb-50">
								<label for="wyz_user_last"><?php esc_html_e( 'Last Name', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_last" name="wyz_user_last" type="text" value="<?php echo esc_attr( $user_last );?>" placeholder="<?php esc_html_e( 'Enter your Last Name', 'wyzi-business-finder' );?>" required/>
							</div>
							<div class="col-md-6 col-xs-12 mb-50">
								<label for="wyz_user_pass "><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_pass " name="wyz_user_pass" type="password" required/>
							</div>
							<div class="col-md-6 col-xs-12 mb-50">
								<label for="password_again"><?php esc_html_e( 'Password Again', 'wyzi-business-finder' ); ?></label>
								<input id="password_again" name="wyz_user_pass_confirm" type="password" required/>
							</div>
							<div class="col-xs-12">
								<span id="password-strength"></span>
							</div>
							<?php $user = wp_get_current_user();
							$def_role = get_option( 'wyz_reg_def_user_role' );
							if ( 'client' != $def_role && 'business_owner' != $def_role ) {?>
							<div class="col-xs-12 mb-50">
								<label for="subscribtion"><?php esc_html_e( 'Subscription', 'wyzi-business-finder' );?></label>
								<select name="subscribtion" id="subscribtion" class="wyz-input" required>
									<option value=""><?php esc_html_e( 'Select your subscription...', 'wyzi-business-finder' );?></option>
									<option value="client" <?php echo ( 'client' == $subscribtion ? 'selected' : '' ); ?>>
										<?php echo esc_html( get_option( 'wyz_businesses_user_client' ) ); ?>
									</option>
									<option value="business_owner" <?php echo ( 'business_owner' == $subscribtion ? 'selected' : '' ); ?>>
										<?php echo esc_html( get_option( 'wyz_businesses_user_owner' ) ); ?>
									</option>
								</select>
							</div>
							<?php }?>
							<input type="hidden" name="wyz_register_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-register-nonce' ) ); ?>"/>
							<div class="col-xs-12">
								<button id="submit" type="submit" class="action-btn btn-bg-blue wyz-prim-color btn-rounded"><?php esc_html_e( 'SIGN UP', 'wyzi-business-finder' ); ?></button>
								<?php wyz_this_get_login_social_options();?>
							</div>
							<?php if ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( 'terms-and-cond-on-off' ) ) {?>
								<div class="col-xs-12 terms-and-cond">
									<?php wyz_extract_termsandconditions();?>
								</div>
							<?php }?>
						</div>
					</form>
				</div>
			</div>
		</div>
	<!-- </div> -->
<?php
}

function wyz_this_get_login_social_options() {
	$have_fb_login = ( '' != get_option( 'wyz_fb_app_id' ) && '' != get_option( 'wyz_fb_app_secret' ) );
	$have_google_login = ( '' != get_option( 'wyz_google_client_id' ) && '' != get_option( 'wyz_google_client_secret' ) && '' != get_option( 'wyz_google_developer_key' ) );
	
	if ( $have_fb_login || $have_google_login ) {?>
	<div class="social-login-cont">
		<h5 class="wyz-prim-color-txt"><?php esc_html_e( 'Other Login Options', 'wyzi-business-finder' );?></h5>
		<?php if ( $have_fb_login ) {
			echo do_shortcode( '[fbl_login_button]' );
		}if ( $have_google_login ){ ?>
		<a href="<?php echo wp_login_url() . '?loginGoogle=1&redirect=' . home_url( '/user-account/' );?>" class="social-login google"><i class="fa fa-google"></i></a>
		<?php }?>
	</div>
	<?php }
}

/**
 * Logs a member in after submitting a form.
 */
function wyz_login_member() {
	if ( isset( $_POST['wyz_user_login'] ) && wp_verify_nonce( $_POST['wyz_login_nonce'], 'wyz-login-nonce' ) ) {
		// This returns the user ID and other info from the user name.
		$user = get_user_by( 'login', wp_filter_nohtml_kses( $_POST['wyz_user_login'] ) );

		if ( ! $user || ! is_object( $user ) ) {
			// If the user name doesn't exist.
			wyz_errors()->add( 'no_user', esc_html__( 'User not found', 'wyzi-business-finder' ) );
			return;
		}

		if ( ! isset( $_POST['wyz_user_pass_login'] ) || '' == $_POST['wyz_user_pass_login'] ) {
			// If no password was entered.
			wyz_errors()->add( 'empty_password', esc_html__( 'Please enter a password', 'wyzi-business-finder' ) );
		}

		// Check the user's login with their password.
		if ( ! wp_check_password( $_POST['wyz_user_pass_login'], $user->user_pass, $user->ID ) ) {
			// If the password is incorrect for the specified user.
			wyz_errors()->add( 'empty_password', esc_html__( 'Incorrect password', 'wyzi-business-finder' ) );
		}

		// Retrieve all error messages.
		$errors = wyz_errors()->get_error_messages();

		// Only log the user in if there are no errors.
		if ( empty( $errors ) ) {
			$creds = array();
			$creds['user_login'] = wp_filter_nohtml_kses( $_POST['wyz_user_login'] );
			$creds['user_password'] = $_POST['wyz_user_pass_login'];
			$creds['remember'] = isset( $_POST['remember-me'] ) ? true : false;
			$user = wp_signon( $creds, is_ssl() );
			wp_redirect( home_url( '/user-account/' ) );
			exit;
		}
	}
}
add_action( 'init', 'wyz_login_member' );


/**
 * Logs a member in after submitting a form.
 */
function wyz_reset_user_pass() {
	if ( isset( $_POST['wyz_user_Identifier'] ) && wp_verify_nonce( $_POST['wyz_reset_pass_nonce'], 'wyz-reset_pass-nonce' ) ) {
		if ( is_user_logged_in() ) {
			wp_redirect( home_url() );
			exit;
		}
		$user_email = email_exists( $_POST['wyz_user_Identifier'] );
		$user_name = username_exists( $_POST['wyz_user_Identifier'] );
		$user_data = '';
		$user_login = '';
		$verified = false;
		if ( $user_email ) {
			$user_data = get_userdata( $user_email );
			$verified = true;
		} elseif ( $user_name ) {
			$user_data = get_userdata( $user_name );
			$verified = true;
		}
		if ( $verified ) {
			$user_email = $user_data->user_email;
			$user_name = $user_data->display_name;
			$user_login = $user_data->user_login;
			wyz_send_pass_email( $user_email, $user_name, $user_login );
		} else {
			wyz_errors()->add( 'username_email_invalid', esc_html__( 'Username/Email not found', 'wyzi-business-finder' ) );
		}

		// Retrieve all error messages.
		$errors = wyz_errors()->get_error_messages();
	}
}
add_action( 'wp', 'wyz_reset_user_pass' );

function wyz_send_pass_email( $email, $name, $login ) {
	global $wpdb;
	$key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $login ) );
	if ( empty( $key ) ) {
		// Generate reset key.
		$key = wp_generate_password( 20, false );
	}
	$Fname = $name;
	$no_reply = 'no-reply-@' . get_option( 'siteurl' );
	$from = get_bloginfo( 'url' );
	$to = $email;

	$page_url = site_url( "signup/?action=reset_pwd&key=$key&login=" . rawurlencode( $login ) );
	
	// Emailing password change request details to the user.
	$semi_rand = md5( time() );
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
	$headers = array( //'From: "' . get_bloginfo('name') . '"' . " < $no_reply >",
					"Reply-To: \"$no_reply\"" ,
					"MIME-Version: 1.0",
					"Content-type: text/html; charset=UTF-8",
					" boundary=\"{$mime_boundary}\""
	);
	$headers = implode( "\r\n" , $headers );

	$message = esc_html__( 'Someone requested that the password be reset for the following account:' ) . "\r\n\r\n";
	$message .= get_option( 'siteurl' ) . "\r\n\r\n";
	$message .= esc_html__( 'Username', 'wyzi-business-finder' ) . ': ' . $name . "\r\n\r\n";
	$message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.', 'wyzi-business-finder' ) . "\r\n\r\n";
	$message .= esc_html__( 'To reset your password, visit the following address', 'wyzi-business-finder' ) . ': ' . "\r\n\r\n";
	$message .= $page_url . "\r\n";
	if ( $message && ! wp_mail( $email, esc_html__( 'Password Reset Request', 'wyzi-business-finder' ), $message, $headers ) ) {
		wyz_errors()->add( 'reset_pass_email_fail', esc_html__( 'Sending email failed', 'wyzi-business-finder' ) );
	} else {
		global $mail_success;
		$mail_success = esc_html__( 'We have just sent you an email with Password reset instructions', 'wyzi-business-finder' );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $login ) );
	}
	wyz_errors()->get_error_messages();
}


/**
 * Register a new user.
 */
function wyz_add_new_member() {
	if ( isset( $_POST["wyz_user_register"] ) && wp_verify_nonce( $_POST['wyz_register_nonce'], 'wyz-register-nonce' ) ) {

		$user_login = wp_filter_nohtml_kses( $_POST['wyz_user_register'] );
		$user_email = wp_filter_nohtml_kses( $_POST['wyz_user_email'] );
		$user_first = wp_filter_nohtml_kses( $_POST['wyz_user_first'] );
		$user_last = wp_filter_nohtml_kses( $_POST['wyz_user_last'] );
		$user_pass = $_POST['wyz_user_pass'];
		$pass_confirm = $_POST['wyz_user_pass_confirm'];
		$def_role = get_option( 'wyz_reg_def_user_role' );
		$subscribtion = ( 'client' != $def_role && 'business_owner' != $def_role ) ? $_POST['subscribtion'] : $def_role;

		if ( username_exists( $user_login ) ) {
			// Username already registered.
			wyz_errors()->add( 'username_unavailable', esc_html__( 'Username already taken', 'wyzi-business-finder' ) );
		}
		if ( ! validate_username( $user_login ) ) {
			// Invalid username.
			wyz_errors()->add( 'username_invalid', esc_html__( 'Invalid username', 'wyzi-business-finder' ) );
		}
		if ( '' === $user_login ) {
			// Empty username.
			wyz_errors()->add( 'username_empty', esc_html__( 'Please enter a username', 'wyzi-business-finder' ) );
		}
		if ( ! is_email( $user_email ) ) {
			// Invalid email.
			wyz_errors()->add( 'email_invalid', esc_html__( 'Invalid email', 'wyzi-business-finder' ) );
		}
		if ( email_exists( $user_email ) ) {
			// Email address already registered.
			wyz_errors()->add( 'email_used', esc_html__( 'Email already registered', 'wyzi-business-finder' ) );
		}
		if ( '' === $user_pass ) {
			// Passwords do not match.
			wyz_errors()->add( 'password_empty', esc_html__( 'Please enter a password', 'wyzi-business-finder' ) );
		}
		if ( $user_pass !== $pass_confirm ) {
			// Passwords do not match.
			wyz_errors()->add( 'password_mismatch', esc_html__( 'Passwords do not match', 'wyzi-business-finder' ) );
		}
		if ( 'business_owner' !== $subscribtion && 'client' !== $subscribtion ) {
			// Empty subscribtion.
			wyz_errors()->add( 'no_subscribtion', esc_html__( 'Please choose your subscription', 'wyzi-business-finder' ) );
		}

		$errors = wyz_errors()->get_error_messages();

		// Only create the user in if there are no errors.
		if ( empty( $errors ) ) {
			$new_user_id = wp_insert_user( array(
				'user_login' => $user_login,
				'user_pass' => $user_pass,
				'user_email' => $user_email,
				'first_name' => $user_first,
				'last_name' => $user_last,
				'user_registered' => date( 'Y-m-d H:i:s' ),
				'role' => $subscribtion,
			) );

			if ( $new_user_id ) {
				// Send an email to the admin alerting them of the registration.
				wp_new_user_notification( $new_user_id );

				// Give the user initial points value of zero.
				update_user_meta( $new_user_id, 'points_available', 0 );
				update_user_meta( $new_user_id, 'has_business', false );
				$user_businesses = array(
					'pending' => array(),
					'published' => array(),
				);
				update_user_meta( $new_user_id, 'wyz_user_businesses', $user_businesses );
				update_user_meta( $new_user_id, 'wyz_user_businesses_count', 0 );

				do_action( 'wyz_after_user_register', $new_user_id );
				
				$creds = array();
				$creds['user_login'] = $user_login;
				$creds['user_password'] = $user_pass;
				$creds['remember'] = true;
				$user = wp_signon( $creds, is_ssl() );

				// Send the newly created user to the user account page after logging him in.
				wp_redirect( home_url( '/user-account/' ) );
				exit;
			}
		}
	}
}
add_action( 'init', 'wyz_add_new_member' );


/**
 * Used for tracking error messages.
 */
function wyz_errors() {
	static $wp_error;
	// Will hold global variable safely.
	return isset( $wp_error ) ? $wp_error :( $wp_error = new WP_Error( null, null, null ) );
}

/**
 * Displays error messages from form submissions.
 */
function wyz_show_error_messages() {
	if ( $codes = wyz_errors()->get_error_codes() ) {
		$msgs = '';
		// Loop error codes and display errors.
		foreach ( $codes as $code ) {
			$message = wyz_errors()->get_error_message( $code );
			$msgs .=  '<p>' . esc_html__( 'Error', 'wyzi-business-finder' ) . '</strong>: ' . esc_html( $message ) . '</p>';
		}
		WyzHelpers::wyz_error( $msgs );
	}
}



function wyz_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if ( empty( $args ) ) return $caps;
	/* If editing, deleting, or reading an offer, get the post and post type object. */
	if ( 'edit_offer' == $cap || 'delete_offer' == $cap || 'read_offer' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

		/* Set an empty array for the capabilities. */
		$caps = array();
	}

	/* If editing an offer, assign the required capability. */
	if ( 'edit_offer' == $cap ) {

		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->edit_posts;
		} else {
			$caps[] = $post_type->cap->edit_others_posts;
		}
	} elseif ( 'delete_offer' == $cap ) { /* If deleting an offer, assign the required capability. */
		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->delete_posts;
		} else {
			$caps[] = $post_type->cap->delete_others_posts;
		}
	} elseif ( 'read_offer' == $cap ) { /* If reading a private offer, assign the required capability. */
		if ( 'private' != $post->post_status ) {
			$caps[] = 'read';
		} elseif ( $user_id == $post->post_author ) {
			$caps[] = 'read';
		} else {
			$caps[] = $post_type->cap->read_private_posts;
		}
	}

	/* Return the capabilities required by the user. */
	return $caps;
}
add_filter( 'map_meta_cap', 'wyz_map_meta_cap', 10, 4 );
