<?php
/**
 * Template : User profile
 *
 * @package wyz
 */

$update_error = array();
function wyz_user_profile_form_display() {
	$user_id = get_current_user_id();
	global $WYZ_USER_ACCOUNT_TYPE;
	global $template_type;
	ob_start(); ?>

	<div class="col-xs-12" id="post-<?php echo esc_attr( get_the_ID() ); ?>">

	<?php if ( ! is_user_logged_in() ) { ?>

			<p class="warning">
			    <?php esc_html_e( 'You must be logged in to edit your profile.', 'wyzi-business-finder' ); ?>
			</p>

	<?php } elseif ( isset( $_POST['upgrade-role-yes'] ) && 1 == $_POST['upgrade-role-yes'] ) {

			$u = new WP_User( $user_id );
			$u->remove_role( 'client' );
			$u->add_role( 'business_owner' );
			WyzHelpers::add_extra_points( $user_id );
			WyzHelpers::wyz_success( esc_html__( 'Profile Updated', 'wyzi-business-finder' ) );
			echo '<script type="text/javascript">//<![CDATA[ 
			setTimeout(function(){ window.location = ' . wp_json_encode( get_permalink() ) . '; }, 2500); //]]></script>';

	}elseif ( isset( $_POST['upgrade-role'] ) && 1 == $_POST['upgrade-role'] ) { ?>

		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'Upgrade Account', 'wyzi-business-finder' );?></h1>
		</div>
		<p>
		<?php echo sprintf( esc_html__( 'You are about to upgrade your account to Business Owner. This allows you to create a business and start publishing %s.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?><br/>
		<?php esc_html_e( 'Be aware that this step is', 'wyzi-business-finder' );?> <font color="red"><?php esc_html_e( 'irreversible', 'wyzi-business-finder' );?></font>.
		<?php esc_html_e( 'Click the button below to proceed.', 'wyzi-business-finder' );?>
		</p>
		<form name="upgrade-role-yes" method="POST">
			<input name="upgrade-role-yes" type="hidden" value="1"/>
			<button type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php esc_html_e( 'Upgrade', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

	<?php
	} elseif ( isset( $_POST['action'] ) && 'points-transfer' == $_POST['action'] && 'on' == get_option( 'wyz_businesses_points_transfer' ) ) { ?>

		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'Upgrade Account', 'wyzi-business-finder' );?></h1>
		</div>
		<p>
		<?php echo sprintf( esc_html__( 'You are about to upgrade your account to Business Owner. This allows you to create a business and start publishing %s.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?><br/>
		<?php esc_html_e( 'Be aware that this step is', 'wyzi-business-finder' );?> <font color="red"><?php esc_html_e( 'irreversible', 'wyzi-business-finder' );?></font>.
		<?php esc_html_e( 'Click the button below to proceed.', 'wyzi-business-finder' );?>
		</p>
		<form name="upgrade-role-yes" method="POST">
			<input name="upgrade-role-yes" type="hidden" value="1"/>
			<button type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php esc_html_e( 'Upgrade', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

	<?php
	} elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::GetPoints ) { ?>
		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' );?></h1>
		</div>
		<?php 

		echo do_shortcode('[product_category category="points-category"]');
		
	} else {
		global $update_error;
		if ( isset( $update_error ) && count( $update_error ) > 0 ) {
			WyzHelpers::wyz_error( implode( '', $update_error ) );
		}?>
		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'profile', 'wyzi-business-finder' );?></h1>
		</div>
		
		<?php switch( $template_type ) {
			case 1:
			profile_form_1( $user_id );
			break;
			case 2:
			profile_form_2( $user_id );
			break;
		}?>
		
		
		<?php if ( ! current_user_can( 'publish_businesses' ) ) { ?> 

		<form name="upgrade-role" id="upgrade-role" method="POST">
			<input type="hidden" name="upgrade-role" value="1" />
			<button class="wyz-button wyz-primary-color wyz-prim-color icon" type="submit"><?php esc_html_e( 'Upgrade Account to Business Owner', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

		<?php }?>
	<?php } ?>

	</div>

	<?php return ob_get_clean();
}

function profile_form_1( $user_id ) {
	?>
	<div class="profile-form text-center col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-12">
		<form method="post">
			<div class="input-two space-80">
				<div class="input-box">
					<label for="first-name"><?php esc_html_e( 'First Name', 'wyzi-business-finder' ); ?></label>
					<input name="first-name" type="text" id="first-name" value="<?php echo esc_attr( get_the_author_meta( 'first_name', $user_id ) ); ?>" />
				</div>
				<div class="input-box">
					<label for="last-name"><?php esc_html_e( 'Last Name', 'wyzi-business-finder' ); ?></label>
					<input name="last-name" type="text" id="last-name" value="<?php echo esc_attr( get_the_author_meta( 'last_name', $user_id ) ); ?>" />
				</div>
			</div>
			<div class="input-box">
				<div class="input-box">
					<label for="email"><?php esc_html_e( 'Email Address', 'wyzi-business-finder' ); ?></label>
					<input name="email" type="text" id="email" value="<?php echo esc_attr( get_the_author_meta( 'user_email', $user_id ) ); ?>" />
				</div>
			</div>
			<div class="input-two space-80">
				<div class="input-box">
					<label for="pass1"><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
					<input id="pass1" type="password" name="pass1"/>
				</div>
				<div class="input-box">
					<label for="pass2"><?php esc_html_e( 'Repeat Password', 'wyzi-business-finder' ); ?></label>
					<input name="pass2" type="password" id="pass2" />
				</div>
			</div>
			<div class="input-box">
				<div id="pass-stren-cont">
					<span id="password-strength"></span>
				</div>
			</div>
			<?php if ( current_user_can( 'publish_businesses' ) && 'on' != get_option( 'wyz_hide_points' ) ) {
				$points_credit = get_the_author_meta( 'points_available', $user_id );
				$points_credit = ( isset( $points_credit ) ? $points_credit : 0 );?>
			<div class="input-two space-80">
				<div class="input-box gray-bg">
					<label for="available-points"><?php esc_html_e( 'Available Points', 'wyzi-business-finder' ); ?></label>
					<input name="available-points" type="text" disabled value="<?php echo esc_html( $points_credit );?>" />
				</div>
				<div class="input-box">
					<label class="opacity"><?php esc_html_e( 'buy points', 'wyzi-business-finder' );?></label>
					<a id="buy-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::GetPoints =>true ) ); ?>"><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' ); ?></a>
				</div>
			</div>
			<?php }
			// Action hook for plugin and extra fields.
			do_action( 'wyz_edit_user_profile', $user_id ); ?>
			
			<button id="wyz-update-user" type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php echo esc_html__( 'update', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
			<a id="logout-btn" href="<?php echo wp_logout_url( home_url() ); ;?>"  class="action-btn logout-btn"><?php echo esc_html__( 'logout', 'wyzi-business-finder' ); ?></a>
			<?php wp_nonce_field( 'wyz-update-user' ); ?>
			<input name="action" type="hidden" id="action" value="wyz-update-user" />
			<input name="wyz-updateuser" type="hidden" value="1" />
		</form>
	</div>
	<?php
}

function profile_form_2($user_id ) {
	?>
	<div class="profile-form text-center col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-12">
		<div class="row">
		<form method="post">
			<div class="col-xs-12 mb-50">
				<label for="email"><?php esc_html_e( 'Email', 'wyzi-business-finder' ); ?></label>
				<input id="email" name="email" type="email" value="<?php echo esc_attr( get_the_author_meta( 'user_email', $user_id ) ); ?>" placeholder="<?php esc_html_e( 'Enter your e-mail address', 'wyzi-business-finder' );?>" required/>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 mb-25">
				<label for="first-name"><?php esc_html_e( 'First Name', 'wyzi-business-finder' ); ?></label>
				<input id="first-name" name="first-name" type="text" value="<?php echo esc_attr( get_the_author_meta( 'first_name', $user_id ) ); ?>" placeholder="<?php esc_html_e( 'Enter your First Name', 'wyzi-business-finder' );?>" required/>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 mb-25">
				<label for="last-name"><?php esc_html_e( 'Last Name', 'wyzi-business-finder' ); ?></label>
				<input id="last-name" name="last-name" type="text" value="<?php echo esc_attr( get_the_author_meta( 'last_name', $user_id ) ); ?>" placeholder="<?php esc_html_e( 'Enter your Last Name', 'wyzi-business-finder' );?>" required/>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 mb-25">
				<label for="pass1 "><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
				<input id="pass1 " name="pass1" type="password" required/>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 mb-25">
				<label for="pass2"><?php esc_html_e( 'Repeat Password', 'wyzi-business-finder' ); ?></label>
				<input id="pass2" name="pass2" type="password" required/>
			</div>
			<div class="col-xs-12 mb-25">
				<div id="pass-stren-cont">
					<span id="password-strength"></span>
				</div>
			</div>
			<?php if ( current_user_can( 'publish_businesses' ) && 'on' != get_option( 'wyz_hide_points' ) ) {
				$points_credit = get_the_author_meta( 'points_available', $user_id );
				$points_credit = ( isset( $points_credit ) ? $points_credit : 0 );?>
			<div class="col-xs-6 mb-25">
				<div class="input-box gray-bg">
					<label for="available-points"><?php esc_html_e( 'Available Points', 'wyzi-business-finder' ); ?></label>
					<input name="available-points" type="text" disabled value="<?php echo esc_html( $points_credit );?>" />
				</div>
			</div>
			<div class="col-xs-6 mb-25">
				<label class="opacity"><?php esc_html_e( 'buy points', 'wyzi-business-finder' );?></label>
				<a id="buy-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::GetPoints =>true ) ); ?>"><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' ); ?></a>
			</div>
			<?php }
			// Action hook for plugin and extra fields.
			do_action( 'wyz_edit_user_profile', $user_id ); ?>
			<div class="col-xs-12">
				<center><button id="wyz-update-user" type="submit" class="action-btn btn-bg-blue wyz-prim-color btn-rounded"><?php echo esc_html__( 'update', 'wyzi-business-finder' ); ?></button></center>
				<a id="logout-btn" href="<?php echo wp_logout_url( home_url() ); ;?>"  class="action-btn logout-btn"><?php echo esc_html__( 'logout', 'wyzi-business-finder' ); ?></a>
			</div>
			<?php wp_nonce_field( 'wyz-update-user' ); ?>
			<input name="action" type="hidden" id="action" value="wyz-update-user" />
			<input name="wyz-updateuser" type="hidden" value="1" />
		</form>
		</div>
	</div>
	<?php
}

function wyz_transfer_points_form() {
	global $transfer_errors;
	?>
	<div class="section-title text-center margin-bottom-50">
		<h1><?php esc_html_e( 'Transfer Points', 'wyzi-business-finder' );?></h1>
	</div>

	<div class="profile-form text-center col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-12">
		<?php if ( ! empty( $transfer_errors) ) {
			foreach ($transfer_errors as $error) {
				echo $error;
			}
		}?>
		<form method="post">
			
			<div class="input-box">
				<div class="input-box">
					<label for="username-email"><?php esc_html_e( 'email/username of the user you want to transfer points to', 'wyzi-business-finder' ); ?></label>
					<input name="username-email" required="required" type="text" id="username-email" value="<?php echo esc_attr( isset( $_POST['username-email'] ) ? $_POST['username-email'] : '' ); ?>" />
				</div>
				<div class="input-box">
					<label for="points"><?php esc_html_e( 'amount of points you want to transfer', 'wyzi-business-finder' ); ?></label>
					<input name="points" required="required" type="number" id="transfer-points" value="<?php echo esc_attr( isset( $_POST['points'] ) ? $_POST['points'] : '' ); ?>" />
					<?php $points_fee = intval( get_option( 'wyz_points_transfer_fee', 0 ) );
					if ( $points_fee > 0 ) {
					WyzHelpers::wyz_info( esc_html__( 'Points transfer costs' ) . '<b id="points-fee">' . $points_fee . '</b> ' . esc_html__( 'points', 'wyzi-business-finder' ) . '<br/><span id="amount-notif"></span>' );
					 }?>
				</div>
			</div>
			
			<?php wp_nonce_field( 'points_form_nonce', 'points_nonce' ); ?>
			<input name="action" type="hidden" id="action" value="points-transfer" />
			<button id="wyz-update-user" type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php echo esc_html__( 'transfer', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
		</form>
	</div>
	<?php
}

/**
 * Check user input and update profile.
 */
function wyz_check_user_update() {
	global $update_error;
	$update_error = array();
	$user_id = get_current_user_id();
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['wyz-updateuser'] ) ) {
		//Update user password. 
		if ( '' != $_POST['pass1'] && '' != $_POST['pass2'] ) {
			if ( $_POST['pass1'] == $_POST['pass2'] ) {
				wp_update_user( array( 'ID' => $user_id, 'user_pass' => $_POST['pass1'] ) );
			} else {
				$update_error[] = '<p>' . esc_html__( 'The passwords you entered do not match.', 'wyzi-business-finder' ) . '</p>';
			}
		}

		// Update user information.
		if ( ! empty( $_POST['email'] ) ) {
			$ex = email_exists( $_POST['email'] );
			if ( ! is_email( esc_attr( $_POST['email'] ) ) ) {
				$update_error[] = '<p>' . esc_html__( 'The Email you entered is not valid.', 'wyzi-business-finder' ) . '</p>';
			} elseif ( $ex && $ex != $user_id ) {
				$update_error[] = '<p>' . esc_html__( 'This email is already in use.', 'wyzi-business-finder' ) . '</p>';
			} elseif ( ! isset( $_POST['first-name'] ) || empty( $_POST['first-name'] ) ) {
				$update_error[] = '<p>' . esc_html__( 'Please enter your first name.', 'wyzi-business-finder' ) . '</p>';
			} elseif ( !isset( $_POST['last-name'] ) || empty( $_POST['last-name'] ) ) {
				$update_error[] = '<p>' . esc_html__( 'Please enter your last name.', 'wyzi-business-finder' ) . '</p>';
			}
		}
		// Redirect so the page will show updated info.
		if ( count( $update_error ) == 0 ) {
			$user = wp_get_current_user();
			$roles = $user->roles;
			wp_update_user( array(
				'ID' => $user_id,
				'first_name' => wp_filter_nohtml_kses( $_POST['first-name'] ),
				'last_name' => wp_filter_nohtml_kses( $_POST['last-name'] ),
				'user_email' => esc_attr( $_POST['email'] ),
			) );

			foreach ($roles as $role) {
				$user->add_role($role);
			}
			// Action hook for plugins and extra fields saving.
			WyzHelpers::add_extra_points( $user_id );
			do_action( 'edit_user_profile_update', $user_id );
			//wp_redirect( get_the_permalink() );
		}
	}
}
add_action( 'init', 'wyz_check_user_update');?>
