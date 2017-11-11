<?php
/**
 * Main Points initializer
 *
 * @package wyz
 */


/**
 * Register forms and fields for backend points submission.
 *
 * @param object $user current user whos profile is being viewed.
 */
function wyz_add_user_points_fields( $user ) {

	if( is_admin()&& current_user_can( 'administrator' ) ) { 
		$is_vendor = user_can( $user->ID, 'dc_vendor' ); ?>
		<h3><?php esc_html_e( 'Points', 'wyzi-business-finder' ); ?></h3>

		<table class="form-table">

			<tr>
				<th>
					<label for="points-available">
						<?php esc_html_e( 'Available Points', 'wyzi-business-finder' ); ?>
					</label>
				</th>
				<td>
					<input type="text" name="points-available" id="points-available" value="<?php echo esc_attr( get_the_author_meta( 'points_available', $user->ID ) ); ?>" class="regular-text" /><br />
				</td>
			</tr>

			<tr>
				<th>
					<label for="wyz_is_vendor">
						<?php esc_html_e( 'Is Vendor', 'wyzi-business-finder' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="wyz_is_vendor" id="wyz-is-vendor" <?php echo $is_vendor ? 'checked="checked"' : '';?> /><br />
				</td>
			</tr>

		</table>
	<?php }
}
add_action( 'show_user_profile', 'wyz_add_user_points_fields' );
add_action( 'edit_user_profile', 'wyz_add_user_points_fields' );

add_action( 'personal_options_update', 'wyz_save_user_points_fields' );
add_action( 'edit_user_profile_update', 'wyz_save_user_points_fields' );

function wyz_save_user_points_fields( $user_id ) {

	if( !is_admin() || !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	update_user_meta( $user_id, 'points_available', $_POST['points-available'] );
}

add_action( 'profile_update', 'wyz_add_user_vendor_role', 10, 2 );

function wyz_add_user_vendor_role( $user_id, $old_user_data ) {
	if ( ! function_exists( 'add_wcmp_users_caps' ) ) return;
	global $WCMp;
	$user = new WP_User( $user_id );
	if ( isset( $_POST['wyz_is_vendor'] ) && 'on' == $_POST['wyz_is_vendor'] ) {
		$user->remove_role( 'subscriber' );
		$user->remove_role( 'customer' );
		$user->remove_role( 'dc_pending_vendor' );
		$user->remove_role( 'client' );
		$user->add_role( 'business_owner' );
		$user->add_role( 'dc_vendor' );
		$WCMp->user->add_vendor_caps($user_id);
		$vendor = get_wcmp_vendor($user_id);
		$vendor->generate_term();

	} else {
		$user->remove_role( 'dc_vendor' );
	}
}


/**
 * Adds a Points column to the user display dashboard.
 *
 * @param array $columns user column.
 */
function wyz_add_user_points_column( $columns ) {

	if( is_admin() ) {
		$columns['points_available'] = esc_attr( __( 'Points', 'wyzi-business-finder' ) );
		return $columns;
	}
}
add_filter( 'manage_users_columns', 'wyz_add_user_points_column' );

/**
 * add points column to users display table
 *
 * @param object $value not needed here.
 * @param string $column_name current column name.
 * @param integer $user_id id of user whos profile is in view.
 */
function wyz_show_user_points_data( $value, $column_name, $user_id ) {

	if( is_admin() && 'points_available' == $column_name ) {
		return esc_attr( get_user_meta( $user_id, 'points_available', true ) );
	}
}
add_action( 'manage_users_custom_column', 'wyz_show_user_points_data', 10, 3 );
