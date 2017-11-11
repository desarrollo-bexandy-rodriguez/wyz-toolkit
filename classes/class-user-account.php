<?php
class WyzUserAccount {

	private $tabs = array();
	private $UserAccountType;
	public $is_business_owner;
	public $user_id;
	private $template_type;
	
	public function __construct( $acc_type ) {
		$this->template_type = 1;
		if ( function_exists( 'wyz_get_theme_template' ) )
			$this->template_type = wyz_get_theme_template();
		$this->UserAccountType = $acc_type;
		$this->is_business_owner = current_user_can( 'publish_businesses' );
		if ( ! $this->UserAccountType ) { return; }
		$this->user_id = get_current_user_id();

		$this->tabs[] = new AccountBusiness($this->is_business_owner, $this->user_id );
		$this->tabs[] = new AccountProfile($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountFavorite($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountWoo($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountVendor($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountProducts($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountSubscription($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountJob($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountBooking($this->is_business_owner, $this->user_id);

		if ( class_exists('WP_Job_Manager_Shortcodes') ) {
			add_action( 'wp', function(){
				$shortcodes_handler = WP_Job_Manager_Shortcodes::instance();
				$shortcodes_handler->job_dashboard_handler();
			} );
		}
	}

	public function the_account_tabs() {

		if ( ! $this->UserAccountType ) { return; }

		if ( $this->UserAccountType == WyzQueryVars::Dashboard ) {?>

		<div class="business-profile-tab-list">
			<?php if ( 1 == $this->template_type ) echo '<div class="container">';?>
					<!-- Tab List -->
					<div class="profile-tab-list col-xs-12">
						<ul>
							<?php
							foreach ( $this->tabs as $tab )
								$tab->the_tab();
							?>
						</ul>
						<div class="scrollbar wyz-primary-color wyz-prim-color"><div class="handle"><div class="mousearea"></div></div></div>
					</div>
					<div class="profile-tab-list profile-tab-list-dropdown col-xs-12">
						<select id="profile-tab-list-dropdown" class="wyz-input wyz-select">
							<?php
							foreach ( $this->tabs as $tab )
								$tab->the_tab_drop();
							?>
						</select>
						<div class="scrollbar wyz-primary-color wyz-prim-color"><div class="handle"><div class="mousearea"></div></div></div>
					</div>
			<?php if ( 1 == $this->template_type ) echo '</div>';?>	
		</div>
		<div class="clear"></div>
	<?php
		} elseif ( $this->UserAccountType ) {
			//$this->the_points_status( false );
		}
	}

	public function the_page_title() {
		global $WYZ_USER_ACCOUNT_TYPE;

		if ( ! $WYZ_USER_ACCOUNT_TYPE || $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::Dashboard )
			return  the_title( '', '' );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::ManageBusiness )
			return sprintf( esc_html__( 'Manage %s', 'wyzi-business-finder' ), get_the_title( $_GET[ $WYZ_USER_ACCOUNT_TYPE ] ) );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddNewBusiness )
			return sprintf( esc_html__( 'Add New %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditBusiness )
			return sprintf( esc_html__( 'Edit %s: %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT, get_the_title( $_GET[ $WYZ_USER_ACCOUNT_TYPE ] ) );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditOffer )
			return sprintf( esc_html__( 'Edit %s: %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT, get_the_title( $_GET[ $WYZ_USER_ACCOUNT_TYPE ] ) );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::GetPoints )
			return esc_html__( 'Buy Points', 'wyzi-business-finder' );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::TransferPoints )
			return esc_html__( 'Transfer Points', 'wyzi-business-finder' );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::BusinessCalendar )
			return sprintf( esc_html__( '%s Calendar', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT );
		
		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddProduct )
			return sprintf( esc_html__( 'Add a New Product', 'wyzi-business-finder' ) );
	}

	public function the_points_status( $tabs_visible = false ) {

		if ( ! $this->UserAccountType || 'on' == get_option( 'wyz_hide_points' ) ) { return; }

		$user_can_business = current_user_can( 'publish_businesses' );
		$user_points = get_user_meta( $this->user_id, 'points_available', true );
		if ( '' != $user_points ) {
			$user_points = intval( $user_points );
		} else {
			$user_points = 0;
		}
		if ( ! $tabs_visible ) { ?>

		<div class="business-profile-tab-list">
			<div class="container">
				<div class="row">

		<?php }?>

		<div id="pts-info-cont" class="float-right">
			<h2><span id="youhave"><?php echo sprintf( esc_html__( 'You Have %d Points', 'wyzi-business-finder' ), $user_points );?></span></h2>
			<div class="buy-transfer-points">
				<a id="buy-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::GetPoints => true ) ); ?>"><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' );?></a>
				<?php if( 'on' == get_option( 'wyz_businesses_points_transfer' ) ) {?>
					<br/><a id="transfer-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::TransferPoints => true ) ); ?>"><?php esc_html_e( 'Transfer Points', 'wyzi-business-finder' );?></a>
				<?php }?>
			</div>
		</div>

		<?php if ( ! $tabs_visible ) { ?>

				</div>
			</div>
		</div>

		<?php
		}
	}

	public function the_account_content() {
		if ( $this->UserAccountType != WyzQueryVars::Dashboard ) { return; } ?>
		<div class="business-profile-page">
			<div class="tab-content">
				<?php foreach ( $this->tabs as $tab ) {
					$tab->the_content();
				}?>
			</div>
		</div>
		<?php
	}
}

abstract class AccountContent {

	protected $active;
	protected $condition;
	protected $tab_title;
	protected $link;
	protected $is_business_owner;
	protected $user_id;
	protected $template_type;

	public function __construct( $is_business_owner, $user_id ) {
		$this->is_business_owner = $is_business_owner;
		$this->user_id = $user_id;
		$this->template_type = 1;
		if ( function_exists( 'wyz_get_theme_template' ) )
			$this->template_type = wyz_get_theme_template();
		$this->the_condition();
		$this->tab_title();
		$this->active();
		$this->link();
	}

	abstract protected function the_condition();
	abstract protected function _active();
	abstract protected function tab_title();
	abstract protected function link();
	abstract protected function notifications();
	abstract protected function content();

	private function active() {

		if ( $this->_active() ) {

			$this->active = ' active';
		} else {

			$this->active = '';
		}
	}


	public function the_tab(){
		if ( $this->condition ) {
			echo '<li class="'. $this->active . ' ' . $this->link . '" ><a class="profile-tab wyz-prim-color-txt-hover" data-link="' . $this->link . '" id="link-' . $this->link . '" href="#' . $this->link . '">' . $this->tab_title . '</a></li>';
		}
	}

	public function the_tab_drop(){
		if ( $this->condition ) {
			echo '<option' . ( 'active' == $this->active ? ' selected' : '' ) . ' value="' . $this->link . '" class="'. $this->active . ' ' . $this->link . '" >' . $this->tab_title . '</option>';
		}
	}

	public function the_content() {

		if ( $this->condition ) {

			echo '<div class="tab-pane' . $this->active . '" id="' . $this->link . '">';
			
			$this->notifications();
			$this->content();
			
			echo '</div>';
		}
		
	}
}

class AccountBusiness extends AccountContent {

	public function the_condition() { $this->condition = $this->is_business_owner; }

	public function _active () {
		return $this->is_business_owner && (! isset( $_POST['wyz-updateuser'] ) || ( function_exists( 'is_wc_endpoint_url' ) && ! is_wc_endpoint_url() ) ) ;
	}

	public function tab_title () {
		$this->tab_title = sprintf( esc_html__( 'my %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT );
	}

	public function link () {
		$this->link = 'my-business';
	}


	public function notifications() {
		$output = '';
		if ( isset( $_GET['post_submitted'] ) ) {
			if ( 'off' == get_option( 'wyz_offer_immediate_publish' ) ) {

				$output .= WyzHelpers::wyz_success( sprintf( esc_html__( 'Thank you, your new %s is now pending for submission.', 'wyzi-business-finder' ), esc_html( $name ), WYZ_OFFERS_CPT ),true);

			} else {
				$output .= WyzHelpers::wyz_success( sprintf( esc_html__( 'Thank you, your new %s has been published successfully.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),ture);
			}
		} elseif( isset( $_GET['offer_updated'] ) ) {

			// Add notice of submission to our output.
			$output .= WyzHelpers::wyz_success( sprintf( esc_html__( 'Thank you, your %s was updated successfully.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),true);
		}
		echo $output;
	}

	public function content() {
		echo wyz_business();
	}
}

class AccountProfile extends AccountContent {

	public function the_condition() { $this->condition = true; }

	public function _active () {
		return isset( $_POST['updateuser'] ) || ! $this->is_business_owner;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'profile', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'profile';
	}

	public function notifications() {
		return;
	}

	public function content() {
		echo wyz_user_profile_form_display();
	}
}
class AccountFavorite extends AccountContent {

	public function the_condition() { $this->condition = ( 'on' == get_option( 'wyz_enable_favorite_business' ) ); }

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Favorite', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'favorite';
	}

	public function notifications() {
		return;
	}

	public function content() {
		$favorites = WyzHelpers::get_user_favorites( $this->user_id );
		if ( empty( $favorites ) )
			WyzHelpers::wyz_info( esc_html__( 'You don\'t have any favorates yet', 'wyzi-business-finder' ) );
		else {
			if ( $this->template_type == 1 ) {
				$query = new WP_Query(array('post_type'=>'wyz_business','post_status'=>array('publish'),'post__in' => $favorites ) );
				while($query->have_posts()){
					$query->the_post();
					echo WyzBusinessPost::wyz_create_business();
				}
				wp_reset_postdata();
			}
			elseif ( function_exists( 'wyz_get_option' ) ) {
				$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
				if ( '' != $grid_alias )
					echo do_shortcode( '[ess_grid alias="' . $grid_alias . '" posts='.implode(',',$favorites).']' );
			}
		}
	}
}

class AccountWoo extends AccountContent {

	public function the_condition() { 
		$this->condition = class_exists( 'WooCommerce' ) && 'on' != get_option( 'wyz_woocommerce_hide_orders_tab' );
	}

	public function _active () {
		return function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url();
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Shop', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'woo-profile';
	}

	public function notifications() { }

	public function content() {
		echo do_shortcode( '[woocommerce_my_account]' );
	}
}
class AccountVendor extends AccountContent {

	public function the_condition() {
		$this->condition = ! current_user_can( 'manage_options' ) && $this->is_business_owner && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id,'wyzi_sub_business_can_apply_vendor')
							&& WyzHelpers::wyz_has_business( $this->user_id ) && class_exists( 'WooCommerce' ) &&
							class_exists( 'WCMp' ) && function_exists( 'is_user_wcmp_vendor' ) &&
							! is_user_wcmp_vendor( $this->user_id ) && 'off' != get_option( 'wyz_can_become_vendor' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Become a vendor', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'vendor-form';
	}

	public function notifications() {
		
	}

	public function content() {
		echo do_shortcode( '[vendor_registration]' );
	}
}

class AccountProducts extends AccountContent {

	public function the_condition() {
		$this->condition = $this->is_business_owner && class_exists( 'WooCommerce' ) && class_exists( 'WCMp' ) &&
							function_exists( 'is_user_wcmp_vendor' ) && is_user_wcmp_vendor( $this->user_id ) &&
							'off' != get_option( 'wyz_display_vendor_products' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html( get_option( 'products_tab_label' ) );
	}

	public function link () {
		$this->link = 'products';
	}

	public function notifications() { }

	public function content() {
		global $current_user;
		wp_get_current_user();
		$user_login = $current_user->user_login;

		if(current_user_can('dc_vendor') && ('on' == get_option('wyz_allow_front_end_submit','on'))){

			echo '<div id="shop-settings" class="float-right" style="margin-bottom: 10px;"><a class="wyz-primary-color wyz-prim-color btn-square" href="'.get_home_url(null,'/user-account/?product_id=1').'">'.esc_html__('Add a Product','wyzi-business-finder').'</a></div>';

		}
		
		echo do_shortcode( "[wcmp_products vendor='$user_login']" );
	}
}

class AccountSubscription extends AccountContent {

	public function the_condition() {
		$this->condition = $this->is_business_owner && 'on' == get_option( 'wyz_sub_mode_on_off' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Subscription', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'subscription';
	}

	public function notifications() { }

	public function content() {
	
	$wyz_sub_pay_woo_on_off = get_option('wyz_sub_pay_woo_on_off','off');

		if ( 'off' == $wyz_sub_pay_woo_on_off ) {
	
			echo do_shortcode( '[memberlite_levels]'); 
		}
		
		else { 
		
			
			
			if(is_user_logged_in() && function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel()) {
				global $current_user;
				$current_user->membership_level = pmpro_getMembershipLevelForUser($current_user->ID);
				WyzHelpers::wyz_info( sprintf( esc_html__( 'Your Membership Level: %s', 'wyzi-business-finder' ),$current_user->membership_level->name ) );
			} else {
				echo WyzHelpers::wyz_info( sprintf( esc_html__( 'You don\'t have a subscription yet', 'wyzi-business-finder' ) ) );
			}
		// lets get products Ids with memberships assigned to it
		global $wpdb;
			
		$product_ids = '';
			
		$get_product_ids_with_membership = $wpdb->get_results( "SELECT post_id FROM  ".$wpdb->prefix . "postmeta where meta_key ='_membership_product_level' and meta_value != 0");
			
		foreach ($get_product_ids_with_membership as  $key  ) {
			$product_ids .= $key->post_id .',';
		}
		
		if (!empty($product_ids)) 
			echo do_shortcode('[products ids="'.$product_ids.'"]');
		 
		}
		
	}
}

class AccountBooking extends AccountContent {

	public function the_condition() {
		$this->condition =  'off' != get_option( 'wyz_users_can_booking' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Appointments', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'booking';
	}

	public function notifications() { }

	public function content() {
		echo do_shortcode( '[booked-profile]' );
	}
}


class AccountJob extends AccountContent {

	public function the_condition() {
		$this->condition =  $this->is_business_owner && 'on' == get_option( 'wyz_users_can_job' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->user_id,'wyzi_sub_can_create_job') && class_exists( 'WP_Job_Manager' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Jobs', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'jobs';
	}

	public function notifications() { }

	public function content() {
		$step='';
		$steps  = array('submit' => '','preview' => '','done' => '');
		if ( isset( $_POST['step'] ) ) {
			$step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $steps ) );
		}
		$can = WyzHelpers::user_can_create_job( $this->user_id );
		if ( ( isset( $_GET['add-job'] ) && $can )|| $step == 1 ) {
			echo do_shortcode( '[submit_job_form]' );
		}
		if ( ! isset( $_GET['add-job'] ) || $step == 1) {
			echo do_shortcode( '[job_dashboard]' );

			if ( $can && ( ! isset( $_GET['action'] ) || 'edit' != $_GET['action'] ) )
				echo '<a href="' . WyzHelpers::add_clear_query_arg( array( 'add-job' => true ) ) . '" class="action-btn btn-bg-blue btn-rounded wyz-button wyz-primary-color wyz-prim-color">' . esc_html__( 'Add New Job', 'wyzi-business-finder' ) . '</a>';
			elseif( ! WyzHelpers::current_user_affords_job_registry( $this->user_id ) )
				WyzHelpers::wyz_info( esc_html__( 'You don\'t have enough points to publish a new job.', 'wyzi-business-finder' ) );
		}
	}
}


class BusinessCalendar extends AccountContent {

	public function the_condition() {
		$this->condition = $this->is_business_owner && 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->author_id,'wyzi_sub_business_can_create_bookings') &&
						class_exists( 'WooCommerce' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Booking Calendar', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'booking-calendar';
	}

	public function notifications() { }

	public function content() {
		if ( WyzHelpers::get_user_calendar() ) {
			//$bk = new booked_plugin();$bk->plugin_settings_page();
		}
	}

}


/*function wyz_create_user_calendar() {
	if ( ! isset( $_POST['wyz_create_user_calendar'] ) )
		return;
	$user_id = get_current_user_id();
	if ( ! wp_verify_nonce( 'wyz_create_user_calendar', 'wyz_calendar_nonce_field' ) )
		wp_die( 'Security Violation' );
	$can_create_calendar = WyzHelpers::wyz_sub_can_bus_owner_do( $user_id,'wyzi_sub_business_can_create_bookings') &&
						class_exists( 'WooCommerce' );
	if ( ! $can_create_calendar )
		wp_die( esc_html__( 'You don\'t have the rights to create a booking calendar' ) );

	if ( '' != get_user_meta( $user_id, 'wyz_user_calendar', true ) )
		wp_die( esc_html__( 'You already have a calendar', 'wyzi-business-finder' ) );

	wp_insert_term( "user_$user_id_", $taxonomy, $args = array() );
}*/
//add_action( 'init', 'wyz_create_user_calendar' );

global $WYZ_USER_ACCOUNT;
$WYZ_USER_ACCOUNT_TYPE;

function wyz_init_user_account() {

	global $WYZ_USER_ACCOUNT;
	global $WYZ_USER_ACCOUNT_TYPE;

	if ( isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::AddNewBusiness;
	elseif ( isset( $_GET[ WyzQueryVars::EditBusiness ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::EditBusiness;
	elseif ( isset( $_GET[ WyzQueryVars::ManageBusiness ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::ManageBusiness;
	elseif ( isset( $_GET[ WyzQueryVars::AddNewOffer ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::AddNewOffer;
	elseif ( isset( $_GET[ WyzQueryVars::EditOffer ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::EditOffer;
	elseif ( isset( $_GET[ WyzQueryVars::GetPoints ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::GetPoints;
	elseif ( isset( $_GET[ WyzQueryVars::TransferPoints ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::TransferPoints;
	elseif ( isset( $_GET[ WyzQueryVars::BusinessCalendar ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::BusinessCalendar;
	elseif ( isset( $_GET[ WyzQueryVars::AddProduct ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::AddProduct;
	elseif ( is_page( 'user-account' ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::Dashboard;
	else $WYZ_USER_ACCOUNT_TYPE = false;

	$WYZ_USER_ACCOUNT = new WyzUserAccount( $WYZ_USER_ACCOUNT_TYPE );
}
add_action( 'wp', 'wyz_init_user_account',1 );
?>