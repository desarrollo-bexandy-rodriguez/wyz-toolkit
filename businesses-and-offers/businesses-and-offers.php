<?php
/**
 * Main business and offers initialize.
 *
 * @package wyz
 */

// Initialize forms and business CPT.
require_once( plugin_dir_path( __FILE__ ) . 'businesses/initialize-businesses.php' );

// Add admin publish hook.
require_once( plugin_dir_path( __FILE__ ) . 'businesses/admin-publish.php' );

// Initialize forms and offers CPT.
require_once( plugin_dir_path( __FILE__ ) . 'offers/initialize-offers.php' );

global $transfer_errors, $transfer_complete,$draft_id;

function wyz_get_business_form_header() {

	$wyz_business_form_data = get_option( 'wyz_business_form_builder_data', array() );
	$form_size = count( $wyz_business_form_data );
	$counter = 0;
	//Custom form fields
	if ( ! empty( $wyz_business_form_data ) ) {
		foreach ( $wyz_business_form_data as $key => $value ) {
			( 'separator' == $value['type'] ) && $counter++;
		}
	}

	$output = '<div class="section-title col-xs-12 margin-bottom-50"></div>';


	/*$prog_bar_pg_1_ttl = apply_filters( 'prog_bar_pg_1_ttl', esc_html__( 'Title and Description', 'wyzi-business-finder' ) );
	$prog_bar_pg_2_ttl = apply_filters( 'prog_bar_pg_2_ttl', esc_html__( 'Open/Close Times', 'wyzi-business-finder' ) );
	$prog_bar_pg_3_ttl = apply_filters( 'prog_bar_pg_3_ttl', esc_html__( 'Address and Contact', 'wyzi-business-finder' ) );
	$prog_bar_pg_4_ttl = apply_filters( 'prog_bar_pg_4_ttl', esc_html__( 'Extra Fields', 'wyzi-business-finder' ) );*/

	if ( $counter ) {
		$width = 100.0/($counter+1);

		//progress bar
		$output = '<ul id="business-progressbar"><li class="active" style="width:' . $width .'%;"></li>';
		while ( $counter-- )
			$output .= '<li style="width:' . $width .'%;"></li>';

		$output .= '</ul>';

	}

	return $output;

}
// Case of add new business page.
add_action( 'init', function() {

	if ( isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ) {
		global $draft_id;
		$transfer_complete = -1;
		$draft_id = wyz_create_draft_business();
		add_action( 'cmb2_after_init', 'wyz_handle_frontend_new_business_submission_form' );
		require_once( plugin_dir_path( __FILE__ ) . 'businesses/front-end-business-submission.php' );
	} elseif ( isset( $_GET[ WyzQueryVars::EditBusiness ] ) ) {
		add_action( 'cmb2_after_init', 'wyz_handle_frontend_business_update_form' );
		require_once( plugin_dir_path( __FILE__ ) . 'businesses/edit-business.php' );
	} elseif ( isset( $_GET[ WyzQueryVars::AddNewOffer ] ) ) {
		global $draft_id;
		$draft_id = wyz_create_draft_offer();
		add_action( 'cmb2_after_init', 'wyz_handle_frontend_new_offer_submission_form' );
		require_once( plugin_dir_path( __FILE__ ) . 'offers/front-end-offer-submission.php' );
	} elseif ( isset( $_GET[ WyzQueryVars::EditOffer ] ) ) {
		add_action( 'cmb2_after_init', 'wyz_handle_frontend_offer_update_form' );
		require_once( plugin_dir_path( __FILE__ ) . 'offers/edit-offer.php' );
	} elseif ( isset( $_POST['action'] ) && 'points-transfer' == $_POST['action'] && 'on' == get_option( 'wyz_businesses_points_transfer' ) ) {
		global $transfer_errors;
		$transfer_errors = array();
		if ( ! isset( $_POST['points_nonce'] ) || ! wp_verify_nonce( $_POST['points_nonce'], 'points_form_nonce' ) ) {
			$transfer_errors[] = WyzHelpers::wyz_error( esc_html__( 'Security Violation', 'wyzi-business-finder' ), true );
		} else {
			if ( isset( $_POST['username-email'] ) ) {
				( $transfee_user = get_user_by( 'login', $_POST['username-email'] ) ) || ( $transfee_user = get_user_by( 'email', $_POST['username-email'] ) );
				if ( ! $transfee_user ) {
					$transfer_errors[] = WyzHelpers::wyz_error( esc_html__( 'User not found.', 'wyzi-business-finder'), true );
				} else {
					if ( ! isset( $_POST['points'] ) || 0 >= $_POST['points'] ) {
						$transfer_errors[] = WyzHelpers::wyz_error( esc_html__( 'Please enter a valid amount of points to transfer', 'wyzi-business-finder'), trye );
					} else {
						$curr_user_id = get_current_user_id();
						$points_available = intval( get_user_meta( $curr_user_id, 'points_available', true ) );
						$points_fee = intval( get_option( 'wyz_points_transfer_fee' ) );
						$points_transfer = intval( $_POST['points'] );
						if ( $points_available < $points_fee + $points_transfer ) {
							$transfer_errors[] = WyzHelpers::wyz_error( esc_html__( 'You don\'t have enough points to finish the transfer.', 'wyzi-business-finder' ), true );
						} else {
							$transfee_points = get_user_meta( $transfee_user->ID, 'points_available', true );
							$transfee_points += $points_transfer;
							update_user_meta( $transfee_user->ID, 'points_available', $transfee_points );
							$points_available -= ( $points_fee + $points_transfer );
							update_user_meta( $curr_user_id, 'points_available', $points_available );
							$GLOBALS['transfer_complete'] = $points_transfer;
						}
					}
				}
			} else {
				$transfer_errors[] = WyzHelpers::wyz_error( esc_html__( 'User field required', 'wyzi-business-finder'), true );
			}
		}
	}
}, 10 );

// This shortcode is added automatically to the auto-generated 'my account' page.
add_shortcode( 'wyz_my_account', 'wyz_do_frontend_my_account_display_shortcode' );

/**
 * The shortcode handler.
 *
 * @param array $atts the shortcode attributes.
 */
function wyz_do_frontend_my_account_display_shortcode( $atts = array() ) {
	global $wp;
	global $WYZ_USER_ACCOUNT_TYPE;
	global $WYZ_USER_ACCOUNT;
	global $template_type;

	if ( ! is_user_logged_in() ) {
		WyzHelpers::wyz_warning( esc_html__( 'You don\'t have the permission to view this page\'s content', 'wyzi-business-finder' ) );
		return;
	}
	add_action( 'wp_footer', 'wyz_add_user_account_js' );

	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	require_once( plugin_dir_path( __FILE__ ) . 'offers/offers-table-display.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'businesses/business-display.php' );

	$hb = WyzHelpers::wyz_has_business( get_current_user_id() );

	if ( 2 == $template_type )
		$WYZ_USER_ACCOUNT->the_account_tabs();

	//if ( ! current_user_can( 'publish_businesses' ) ) :?>
		
		<?php //echo wyz_user_profile_form_display();?>

	<?php /*else*/if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::Dashboard ) : 

		$WYZ_USER_ACCOUNT->the_account_content();

	elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddNewBusiness ) : ?>
		<div class="wyz-form-wrapper">
			<?php echo wyz_display_add_new_business_form( $atts );?>
		</div>

	<?php elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditBusiness ) :?>
		<div class="wyz-form-wrapper">
			<?php echo wyz_do_frontend_business_edit( $atts );?>
		</div>

	<?php elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::GetPoints ) :
		echo wyz_user_profile_form_display();

	elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::TransferPoints && 'on' == get_option( 'wyz_businesses_points_transfer' ) ) :
		$points_available = get_user_meta( get_current_user_id(), 'points_available', true );
		$points_fee = get_option( 'wyz_points_transfer_fee', 0 );
		if( 0 == $points_available ){
			WyzHelpers::wyz_error( esc_html__( 'You don\'t have any points.', 'wyzi-business-finder' ) );
		} elseif ( '' == $points_available ||  $points_fee > $points_available ) {
			WyzHelpers::wyz_error( esc_html__( 'You need to have a minimum of', 'wyzi-business-finder' ) . ' ' . $points_fee . ' ' . esc_html( 'to be able to transfer points.', 'wyzi-business-finder' ) );
		} elseif ( isset( $GLOBALS['transfer_complete'] ) && $GLOBALS['transfer_complete'] > 0 ) {
			WyzHelpers::wyz_success( $GLOBALS['transfer_complete'] . esc_html__( ' points transfered successfully.', 'wyzi-business-finder' ) );
		}else {
			wyz_transfer_points_form();
		}
		
	elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddProduct) :
		require_once( plugin_dir_path( __FILE__ ) . 'products/product.php' );

	elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::ManageBusiness ) :
		wyz_display_user_business();
	elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::BusinessCalendar ) :
		$business_id = $_GET[ WyzQueryVars::BusinessCalendar ];
		$user_businesses = WyzHelpers::get_user_businesses();
		if ( ! in_array( $business_id, $user_businesses['published'] ) && ! in_array( $business_id, $user_businesses['pending'] ) ) :
			return;
		endif;

		if ( 'off' == get_option( 'wyz_users_can_booking' ) || ! WyzHelpers::wyz_sub_can_bus_owner_do( get_current_user_id() , 'wyzi_sub_business_can_create_bookings' ) ) return;
		
		$calendar = WyzHelpers::get_user_calendar();
 		if ( ! $calendar ) :
 			WyzHelpers::wyz_info( sprintf( esc_html__( 'You don\'t have a calendar for this %s yet', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ) );
			?>
			<form method="post">
				<?php wp_nonce_field( 'wyz_create_user_calendar', 'wyz_calendar_nonce_field' );?>
				<input type="hidden" value="<?php echo $business_id;?>" name="calendar_business_id"/>
				<div class="input-box"><input type="text" class="wyz-input" name="wyz_calendar_name" placeholder="<?php esc_html_e( 'Calendar Name', 'wyzi-business-finder');?>"/></div>
				<input type="submit" class="btn-square wyz-primary-color wyz-prim-color" value="<?php esc_html_e( 'Create Calendar', 'wyzi-business-finder' );?>"/>
			</form>
			<?php
		elseif ( current_user_can( 'manage_options' ) ) :
			WyzHelpers::wyz_info( sprintf( esc_html__( 'Admins can manage calendars from the %sbackend%s.', 'wyzi-business-finder' ), '<a href="' . admin_url('admin.php?page=booked-settings') . '">', '</a>' ) );
		else :
			$bk = new booked_plugin();
			$bk->plugin_settings_page();
		endif;
	elseif ( isset( $hb ) && $hb ) :
		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddNewOffer ) : ?>
			<div class="wyz-form-wrapper">
				<?php echo wyz_display_add_new_offer_form( $atts );?>
			</div>
		<?php elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditOffer ) : ?>
			<div class="wyz-form-wrapper">
				<?php echo wyz_do_frontend_offers_edit( $atts );?>
			</div>
		<?php
		endif;
	endif;
}

/**
 * Enqueue 'my account' page js.
 */
function wyz_add_user_account_js() {
	if ( ! wp_script_is( 'wyz_my_account_js', 'enqueued' ) ) {
		wp_enqueue_script( 'wyz_my_account_js' );
	}
}


/**
 * Shortcode for wall, display all posts.
 *
 * @param array $atts the shortcode attributes.
 */
function wyz_do_wall_display( $atts = array() ) {
	require( plugin_dir_path( __FILE__ ) . 'businesses/business-wall-display.php' );
	return wyz_display_wall( $atts );
}

/* Create draft business*/
function wyz_create_draft_business() {
	if ( ! is_user_logged_in() ) {
		wp_die( 'You don\'t have the rights to access this page' );
	}
	if ( $d_id = WyzHelpers::wyz_user_has_draft_business( get_current_user_id() ) ) {
		return $d_id;
	}
	return wp_insert_post( array( 'post_type' => 'wyz_business', 'post_status' => 'draft' ) );
}

/* Create draft offer*/
function wyz_create_draft_offer() {
	if ( ! is_user_logged_in() ) {
		wp_die( 'You don\'t have the rights to access this page' );
	}
	if ( $d_id = WyzHelpers::wyz_user_has_draft_offer( get_current_user_id() ) ) {
		return $d_id;
	}
	return wp_insert_post( array( 'post_type' => 'wyz_offers', 'post_status' => 'draft' ) );
}

/**
 * Register shortcode for wall.
 */
function wyz_register_wall_shortcode() {
	add_shortcode( 'wyz_business_wall', 'wyz_do_wall_display' );
}
add_action( 'init', 'wyz_register_wall_shortcode' );

