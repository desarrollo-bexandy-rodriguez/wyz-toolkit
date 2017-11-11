<?php
/**
 * The main file in wizy toolkit plugin
 *
 * @package wyz
 * Plugin Name: WYZI Toolkit
 * Plugin URI: http://wp.wztechno.com/
 * Description: Creates a custom post type displaying companies' offers, payment by coin system and front end registration.
 * Version: 2.1.2
 * Author: WzTechno
 * Author URI: http://wp.wztechno.com/
 */

// Database version number.
global $wyz_db_version;
$wyz_db_version = '1.0';

define('WYZI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Create js ref to plugin dir in header
 */
function wyz_define_globals() {
	define( 'WYZ_OFFERS_CPT', wyz_syntax_permalink( get_option( 'wyz_offers_old_single_permalink' ) ) );
	define( 'WYZ_BUSINESS_CPT', wyz_syntax_permalink( get_option( 'wyz_business_old_single_permalink' ) ) );
	define( 'WYZ_BUSINESS_POST_CPT', wyz_syntax_permalink( get_option( 'wyz_business_post_old_single_permalink' ) ) );
	define( 'LOCATION_CPT', wyz_syntax_permalink( get_option( 'wyz_location_old_single_permalink' ) ) );
	define( 'WYZI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
add_action( 'plugins_loaded','wyz_define_globals', 1 );


function wyz_syntax_permalink( $permalink ) {
	return ucwords( preg_replace( array('/-/', '/_/' ), ' ', $permalink ) );
}


/**
 * Create js ref to plugin dir in header
 */
function wyz_head_js_ref() {
	echo '<script type="text/javascript">//<![CDATA[
	var wyz_plg_ref = ' . wp_json_encode( plugin_dir_url( __FILE__ ) ) . ';';
	if(function_exists('wyz_get_theme_template' ))
		echo 'var wyz_template_type = ' . wyz_get_theme_template() . ';';
	echo 'function wyz_init_load_map_callback(){try{wyz_init_load_map();}catch(err){}}';
	echo '//]]></script>';
}
add_action( 'wp_head', 'wyz_head_js_ref' );


/**
 * Function that runs on plugin activation
 */
function wyz_on_activation() {

	wyz_save_default_options();

	
	// Add custom user roles.
	require_once( WYZI_PLUGIN_DIR . 'front-end-registration/add-new-roles.php' );

	// Flag for renaming not working on plugin update.
	update_option( 'just_activated', true );

	if ( '' == get_option( 'ver_1.3.7' ) ) {
		update_option( 'ver_1.3.7', 1 );
		$args = array(
			'post_type'  => array( 'page' ),
			'meta_query' => array(
				array(
					'key'     => 'wyz_map_checkbox',
					'value'   => 'on',
				),
			),
			'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();
			update_post_meta( get_the_ID(), 'wyz_page_header_content', 'map' );
		}
		wp_reset_postdata();
	}

	if ( '' == get_option( 'ver_1.4.1' ) ) {
		update_option( 'ver_1.4.1', 1 );
		if ( '' == get_option( 'wyz_business_custom_form_data' ) && '' != get_option( 'wyzi_business_custom_form_data' ) ) {
			update_option( 'wyz_business_custom_form_data', get_option( 'wyzi_business_custom_form_data' ) );
		}
		if ( '' == get_option( 'wyz_claim_registration_form_data' ) && '' != get_option( 'wyzi_claim_registration_form_data' ) ) {
			update_option( 'wyz_claim_registration_form_data', get_option( 'wyzi_claim_registration_form_data' ) );
		}
	}

	if ( '' == get_option( 'ver_1.5.0' ) ) {
		update_option( 'ver_1.5.0', 1 );
		$users = get_users(array('fields'=>'ID'));
		foreach ($users as $user) {
			$old_id = get_user_meta( $user, 'business_id', true);
			$business_data = array(
				'pending' => array(),
				'published' => array(),
			);
			$count = 0;
			if ( ! empty( $old_id ) && '' != $old_id ) {
				$old_bus = get_post( $old_id );
				if ( $old_bus ) {

					$products_quer = new WP_Query(
						array(
							'post_type' => 'product',
							'posts_per_page' => -1,
							'author' => $user,
						)
					);

					while ( $products_quer->have_posts() ) {
						$products_quer->the_post();
						update_post_meta(get_the_ID(), 'business_id', $old_bus->ID );
					}
					wp_reset_postdata();

					if ( $old_bus->post_status == 'pending' ) {
						$business_data['pending'][$old_bus->ID] = $old_bus->ID;
						$count = 1;
					} elseif ( $old_bus->post_status == 'publish' ) {
						$business_data['published'][$old_bus->ID] = $old_bus->ID;
						$count = 1;
					}
				}
			}
			
    		$role = get_role( 'business_owner' );
    		$role->add_cap( 'edit_booked_appointments' );
    		
			update_user_meta( $user, 'wyz_user_businesses_count', $count );
			update_user_meta( $user, 'wyz_user_businesses', $business_data );
		}
	}

	if ( '' == get_option( 'ver_1.5.3' ) ) {
		update_option( 'ver_1.5.3', 1 );
		$query = new WP_Query( array(
			'post_type' => 'wyz_business',
			'post_status' => array( 'pending', 'publish' ),
			'posts_per_page' => -1,
			'fields' => 'ID',
		) );

		while( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$content = get_post_meta( $id, 'wyz_business_description', true );
			if ( ! empty( $content ) ) {
				wp_update_post( array(
					'ID' => $id,
					'post_content'=> $content,
				) );
			}
		}

		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
			'post_status' => array( 'pending', 'publish' ),
			'posts_per_page' => -1,
			'fields' => 'ID',
		) );

		while( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$content = get_post_meta( $id, 'wyz_offers_description', true );
			if ( ! empty( $content ) ) {
				wp_update_post( array(
					'ID' => $id,
					'post_content'=> $content,
				) );
			}
		}
	}

	if ( '' == get_option( 'ver_2.0.2' ) ) {
		update_option( 'ver_2.0.2', 1 );
		$partials =  plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/registration-form-builder/partials/';
		$default_business_form = array
			(
			    array
			    (
			        "id"=> "0",
			        "type"=> "name",
			        "label"=> "Name",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "1",
			        "hidden"=> "1",
			    ),
			    array
			    (
			        "id"=> "1",
			        "type"=> "logo",
			        "label"=> "Logo",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),
			    array
			    (
			        "id"=> "2",
			        "type"=> "logoBg",
			        "label"=> "Logo Background",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "3",
			        "type"=> "desc",
			        "label"=> "Description",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "4",
			        "type"=> "about",
			        "label"=> "About",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "5",
			        "type"=> "slogan",
			        "label"=> "Slogan",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "6",
			        "type"=> "category",
			        "label"=> "Categories",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "7",
			        "type"=> "categoryIcon",
			        "label"=> "Category Icon",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "active"=> "1",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "8",
			        "type"=> "separator",
			        "label"=> "New Tab",
			        "required"=> "",
			        "partial"=> $partials . "separator.html",
			        "cssClass"=> "",
			        "active"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "9",
			        "type"=> "time",
			        "label"=> "Open/Close Times",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "10",
			        "type"=> "separator",
			        "label"=> "New Tab",
			        "required"=> "",
			        "partial"=> $partials . "separator.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "11",
			        "type"=> "bldg",
			        "label"=> "Building",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "12",
			        "type"=> "street",
			        "label"=> "Street",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "13",
			        "type"=> "city",
			        "label"=> "City",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "14",
			        "type"=> "location",
			        "label"=> "Location",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "15",
			        "type"=> "addAddress",
			        "label"=> "Additional Address",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "16",
			        "type"=> "map",
			        "label"=> "Map",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "17",
			        "type"=> "phone1",
			        "label"=> "Phone 1",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "18",
			        "type"=> "phone2",
			        "label"=> "Phone 2",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "19",
			        "type"=> "email1",
			        "label"=> "Email 1",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "20",
			        "type"=> "email2",
			        "label"=> "Email 2",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "21",
			        "type"=> "website",
			        "label"=> "Website",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "22",
			        "type"=> "fb",
			        "label"=> "Facebook",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "23",
			        "type"=> "twitter",
			        "label"=> "Twitter",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "24",
			        "type"=> "gplus",
			        "label"=> "Google Plus",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "25",
			        "type"=> "linkedin",
			        "label"=> "Linkedin",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "26",
			        "type"=> "youtube",
			        "label"=> "Youtube",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "27",
			        "type"=> "insta",
			        "label"=> "Instagram",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "28",
			        "type"=> "flicker",
			        "label"=> "Flicker",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "29",
			        "type"=> "pinterest",
			        "label"=> "Pinterest",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "30",
			        "type"=> "separator",
			        "label"=> "New Tab",
			        "required"=> "",
			        "partial"=> $partials . "separator.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "31",
			        "type"=> "comments",
			        "label"=> "Post Comments",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "32",
			        "type"=> "tags",
			        "label"=> "Tags",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			    array
			    (
			        "id"=> "33",
			        "type"=> "custom",
			        "label"=> "Custom Fields",
			        "required"=> "",
			        "partial"=> $partials . "form-element.html",
			        "cssClass"=> "",
			        "hidden"=> "1",
			    ),

			);
		update_option( 'wyz_business_form_builder_data', $default_business_form );
	}
	//disable essencial grid notification nag
	update_option('tp_eg_valid-notice', false );
	//disable Rev Slider notification nag
	update_option('revslider-valid-notice', 'false');


	// Add schedual event.
	require_once( WYZI_PLUGIN_DIR . 'schedual-event.php' );
	wyz_offers_expiry_check();
	wyz_update_tabs_option();

}
register_activation_hook( __FILE__, 'wyz_on_activation' );


function wyz_update_business_taxonomy_metadata(){
	//update_option( 'ver_2.0',''); 
	if ( '' == get_option( 'ver_2.0' ) ) {
		update_option( 'ver_2.0', 1 );
		$terms = get_terms( array(
			'taxonomy' => 'wyz_business_category',
			'hide_empty' => false,
		) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ($terms as $term){
				update_term_meta( $term->term_id, "map_icon1", get_term_meta( $term->term_id, "map_icon", true ) );
			}
		}
	}
}
add_action( 'init', 'wyz_update_business_taxonomy_metadata', 99);

function wyz_update_tabs_option() {
	if ( '' != get_option( 'wyz_business_tabs_order_data' ) ) {
		return;
	}
	$path = plugin_dir_url( __FILE__ ) . '/businesses-and-offers/businesses/forms/tabs/partials/tab.html';
	update_option( 'wyz_business_tabs_order_data', array(
		array ( 
			'id' => 0,
			'type' => 'wall', 
			'label' => 'Wall', 
			'partial' => $path,
			'cssClass' => '',
			'active' => 1,
			'hidden' => 1,
		),
		array ( 
			'id' => 1,
			'type' => 'photo',
			'label' => 'Photo', 
			'partial' => $path,
			'cssClass' => '',
			'active' => '',
			'hidden' => 1,
		),
		array (
			'id' => 2,
			'type' => 'about', 
			'label' => 'About', 
			'partial' => $path,
			'cssClass' => '',
			'active' => '',
			'hidden' => 1,
		),
		array ( 
			'id' => 3,
			'type' => 'offers',
			'label' => 'Offers', 
			'partial' => $path,
			'cssClass' => '',
			'active' => '',
			'hidden' => 1 ,
		),
		array ( 
			'id' => 4,
			'type' => 'message',
			'label' => 'Message', 
			'partial' => $path,
			'cssClass' => '',
			'active' => '',
			'hidden' => 1,
		),
		array ( 
			'id' => 5,
			'type' => 'products',
			'label' => 'Products',
			'partial' => $path, 
			'cssClass' => '',
			'active' => '',
			'hidden' => 1,
		),
		array ( 
			'id' => 6,
			'type' => 'ratings',
			'label' => 'Ratings', 
			'partial' => $path, 
			'cssClass' => '',
			'active' => '',
			'hidden' => 1,
		) 
	) );
}

/**
 * When the plugin is updated.
 */
function wyz_update_db_check() {
	global $wyz_db_version;
	if ( get_option( 'wyz_db_version' ) !== $wyz_db_version ) {
		require_once( WYZI_PLUGIN_DIR . 'front-end-registration/add-new-roles.php' );
	}
}
add_action( 'plugins_loaded', 'wyz_update_db_check' );

/**
 * Register scripts.
 */
function wyz_scripts() {

	$template_type = 1;
	if ( function_exists( 'wyz_get_theme_template' ) ) 
		$template_type = wyz_get_theme_template();

	wp_enqueue_script( 'general_js', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/general.js', array( 'jquery' ), false, true );

	wp_localize_script( 'general_js', 'general', array( 'searchText' => esc_html__( 'Search here...', 'wyzi-business-finder' ) ) );

	// sliders script
	wp_register_script( 'wyz_owl_script', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/owl-carousel/owl.carousel.js', array( 'jquery' ), false, true );

	wp_register_script( 'wyz_offers_script', plugin_dir_url( __FILE__ ) . "templates-and-shortcodes/sliders/offers/js/offers-$template_type.js", array( 'wyz_owl_script' ), false, true );

	wp_register_script( 'wyz_categories_script', plugin_dir_url( __FILE__ ) . "templates-and-shortcodes/sliders/categories/js/categories-$template_type.js", array( 'wyz_owl_script' ), false, true );

	wp_register_script( 'wyz_locations_script', plugin_dir_url( __FILE__ ) . "templates-and-shortcodes/sliders/locations/js/locations-$template_type.js", array( 'wyz_owl_script' ), false, true );

	wp_register_script( 'wyz_rec_added_script', plugin_dir_url( __FILE__ ) . "templates-and-shortcodes/sliders/recently-added/js/rec-added-$template_type.js", array( 'wyz_owl_script' ), false, true );

	$arr = array( 'wyz_owl_script' );
	if ( 2 == $template_type ) {
		wp_register_script( 'wyz_mansory_script', plugin_dir_url( __FILE__ ) . "templates-and-shortcodes/js/mansory.js", array(), false, true );
		$arr[] = 'wyz_mansory_script';
	}
	wp_register_script( 'wyz_featured_script', plugin_dir_url( __FILE__ ) . "templates-and-shortcodes/sliders/featured/js/featured-$template_type.js", $arr, false, true );

	wp_register_script( 'wyz_single_business_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/js/single-business-all.js', array( 'jquery' ), false, true );

	wp_register_script( 'wyz_wall_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/js/wall.js', array( 'jquery' ), false, true );

	wp_register_script( 'wyz_forms_validation_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/js/forms-validation.js', array( 'jquery' ), false, true );

	wp_register_script( 'wyz_pass_strength_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/js/pass-strength.js', array( 'jquery', 'password-strength-meter' ), false, true );

	wp_register_script( 'wyz_vc_cat_select_js', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/admin/custom-vc.js', array(), false, true );

	wp_register_script( 'wyz_my_account_js', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/my-account.js', array( 'jquery', 'password-strength-meter' ), false, true );
	wp_localize_script( 'wyz_my_account_js', 'myAccount', array( 
															'invalidText' => esc_html__( 'Invalid amount', 'wyzi-business-finder' ),
															'reduce' => esc_html__( 'points will be reduced from your balance', 'wyzi-business-finder' ),
															'pointsAvailable' => get_user_meta( get_current_user_id(), 'points_available', true ),
															'exceeds' => esc_html__( 'points exceed your balance', 'wyzi-business-finder' ),
															'logoutText' => esc_html__( 'Are you sure You want to logout?', 'wyzi-business-finder' ),
															'logout' => esc_html__( 'logout', 'wyzi-business-finder' ),
															'cancel' => esc_html__( 'cancel', 'wyzi-business-finder' )
														));

	wp_register_script( 'jQuery-inview', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/jquery.inview.min.js', array( 'jquery' ), false, false );

	wp_enqueue_script( 'jQuery_select', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/fm.selectator.jquery.js', array( 'jquery' ), false, true );

	global $WYZ_USER_ACCOUNT_TYPE;
	if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditBusiness || $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddNewBusiness ) {
		wp_enqueue_script( 'business_form_jQuery', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/jquery.backstretch.min.js', array( 'wyz-bootstrap-meanmenu-magnificpopup-js' ), false, true );
	}

	if ( is_page( 'user-account' ) && ! isset( $_GET['edit-business'] )  && ! isset( $_GET['add-new-business'] ) ) {
		wp_enqueue_script( 'jQuery_dsselect', "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js", array( 'jquery' ), false, true );
		wp_enqueue_script( 'jQuery_sasdelect', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/sly.js', array( 'jquery' ), false, true );
	}
}
add_action( 'wp_enqueue_scripts', 'wyz_scripts', 5 );


/**
 * Enqueues map scripts..
 */
function wyz_enqueue_map_script() {
	wp_enqueue_script( 'wyz_maps_on_pages', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/js/admin.js', array( 'jquery' ) );
}
add_action( 'admin_enqueue_scripts', 'wyz_enqueue_map_script' );

/**
 * Register plugin styles.
 */
function wyz_styles() {
	wp_enqueue_style( 'wyz_owl_style', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/owl-carousel/assets/owl.carousel.min.css' );

	//wp_enqueue_style( 'wyz_rate_css', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/css/rate.css' );

	wp_enqueue_style( 'jQuery_select_css', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/css/fm.selectator.jquery.css' );
	echo '<!--[if IE]>
		<style type="text/css">

		.sample {
		filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'sample_600x600.png\', sizingMethod=\'scale\');
		background:none !important;
		}

		</style>
	<![endif]-->';

	global $WYZ_USER_ACCOUNT_TYPE;
	if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditBusiness || $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddNewBusiness ) {
		wp_enqueue_style( 'wyz_business_form_css', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/css/business-form.css' );
	}
	if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars:: AddProduct ) {
		wp_enqueue_style('ns-option-css-page', plugin_dir_url( __FILE__ )  . 'businesses-and-offers/products/css/product.css');
		wp_enqueue_style('ns-option-css-add-prod-page', plugin_dir_url( __FILE__ )  . 'businesses-and-offers/products/css/product-page.css');
		wp_enqueue_script( 'ns-option-js-page', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/products//js/product-js-page.js', array( 'jquery' ) );
		wp_enqueue_style( 'dashicons' );
	}
}
add_action( 'wp_enqueue_scripts', 'wyz_styles', 4 );


/**
 * Display map metaboxes on page creation in backend.
 */
function wyz_display_map_metaboxes() {
	$prefix = 'wyz_';
	$wyz_cmb_header = new_cmb2_box( array(
		'id' => $prefix . 'maps_on_pages',
		'title' => esc_html__( 'What to Display', 'wyzi-business-finder' ),
		'object_types' => array( 'page' ),
		'context' => 'normal',
		'priority' => 'core',
		'show_names' => true,
	) );


	$opt = array(
		'nothing' => esc_html__( 'Nothing', 'wyzi-business-finder' ),
		'map' => esc_html__( 'Map', 'wyzi-business-finder' ),
		'image' => esc_html__( 'Image', 'wyzi-business-finder' ),
	);
	if(shortcode_exists("rev_slider")){
		$opt['revslider'] = esc_html__( 'Revolution Slider', 'wyzi-business-finder' );
	}

	$wyz_cmb_header->add_field(
		array(
			'name' => esc_html__( 'What to display on this page\'s header', 'wyzi-business-finder' ),
			'id' => $prefix . 'page_header_content',
			'type' => 'select',
			'default_cb' => 'nothing',
			'options' => $opt,
		)
	);

	$wyz_cmb_header_map = new_cmb2_box( array(
		'id' => $prefix . 'header_map',
		'title' => esc_html__( 'Display Map', 'wyzi-business-finder' ),
		'object_types' => array( 'page' ),
		'context' => 'normal',
		'priority' => 'core',
		'show_names' => true,
	) );

	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Map height', 'wyzi-business-finder' ),
		'id'   => $prefix . 'map_height',
		'type' => 'own_slider',
		'min'         => '50',
		'max'         => '1000',
		'default'     => '600', // Start value.
		'value_label' => 'px',
	) );

	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Enable Map zooming by scroll wheel?', 'wyzi-business-finder' ),
		'id'   => $prefix . 'map_scroll_zoom_checkbox',
		'type' => 'checkbox',
		'std' => 0,
	) );


	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Enable Marker autofit', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'If checked, the map will automatically set the zoom and location to display all available businesses. Else, you can set the zoom and location of your map manually by typing in a country name and setting the zoom level on the map.', 'wyzi-business-finder' ),
		'id' => $prefix . 'page_autozoom',
		'type' => 'checkbox',
	));


	$wyz_cmb_header_map->add_field( array(
		'name'             => __( 'Map Skin', 'wyzi-business-finder' ),
		'id'               => $prefix . 'post_map_skin',
		'type'             => 'radio_image',
		'options'          => array(
			''    => esc_html__('Standard', 'wyzi-business-finder'),
			'1'    => esc_html__('Silver', 'wyzi-business-finder'),
			'2'    => esc_html__('Retro', 'wyzi-business-finder'),
			'3'    => esc_html__('Dark', 'wyzi-business-finder'),
			'4'    => esc_html__('Night', 'wyzi-business-finder'),
			'5'    => esc_html__('Aubergine', 'wyzi-business-finder'),
		),
		'images_path'      => WYZI_PLUGIN_URL . '/templates-and-shortcodes/images/',
		'images'           => array(
			''    => "map-skin-.jpg",
			'1'    => "map-skin-1.jpg",
			'2'    => "map-skin-2.jpg",
			'3'    => "map-skin-3.jpg",
			'4'    => "map-skin-4.jpg",
			'5'    => "map-skin-5.jpg",
		)
	) );

	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Hide Map POI', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'If checked, POIs (Business points of interest) will be hidden from this map.', 'wyzi-business-finder' ),
		'id' => $prefix . 'hide_post_map_poi',
		'type' => 'checkbox',
	));


	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'On load location request', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'If checked, map centers on current user location.', 'wyzi-business-finder' ),
		'id' => $prefix . 'on_load_loc_req',
		'type' => 'checkbox',
	));

	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Location', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Select where do you want the map to be centered by default. Without setting a map center, the map will not appear on the front end.', 'wyzi-business-finder' ),
		'default' => '0',
		'id' => $prefix . 'page_map',
		'type' => 'pw_map',
		'split_values' => true,
		'after_field'  => '<p><b>' . esc_html__( 'Note', 'wyzi-business-finder' ) . ': </b>' . esc_html__( 'In order to get the marker on the map, type any location address in the search box, then select it from the dropdown menu that shows up afterwards. Dowing so will show a marker on the map, then you can fine tune the desired location.', 'wyzi-business-finder') . '<br/>' . esc_html__( 'If the map appears blank with no marker showing up, this means you need to input a Google maps API key in Toolkit settings.', 'wyzi-business-finder' ) . '<br>'. esc_html__( 'Use the map to CENTER your map and to set the ZOOM LEVEL shown in the front-end. In the backend only the inner center part at its correct zoom-level will appear. The map in the frontend will show a much wider area. Please check back on Frontend and correct your settings in the backend if necessary.', 'wyzi-business-finder' ) . '</p>',
	));

	$wyz_cmb_header_map->add_field( array(
		'name'    => esc_html__( 'Location Filter Type', 'wyzi-business-finder' ),
		'desc'    => esc_html__( 'Display a dropdown or an auto-complete text as location filter', 'wyzi-business-finder' ),
		'id'      => $prefix . 'map_location_filter_type',
		'type'    => 'select',
		'options' => array( 'text' => esc_html__('Text', 'wyzi-business-finder' ), 'dropdown' => esc_html__( 'Dropdown', 'wyzi-business-finder') ),
	) );

	$wyz_cmb_header_map->add_field( array(
		'name'    => esc_html__( 'Default Location', 'wyzi-business-finder' ),
		'desc'    => sprintf( esc_html__( 'Directly filter your %s to this location in case you\'re using location dropdown', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'id'      => $prefix . 'def_map_location',
		'type'    => 'select',
		'options' => WyzHelpers::get_businesses_locations_options(),
	) );

	$wyz_cmb_header_map->add_field(
		array(
			'name' => esc_html__( 'Default category', 'wyzi-business-finder' ),
			'id' => $prefix . 'default_map_category',
			'desc'    => sprintf( esc_html__( 'Directly filter your %s to this category.', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
			'type' => 'select',
			'default_cb' => '',
			'options' => WyzHelpers::get_business_categories_dropdown_format(true,2),
		)
	);

	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Default Radius', 'wyzi-business-finder' ),
		'id'   => $prefix . 'default_map_radius',
		'type' => 'own_slider',
		'min'         => '0',
		'max'         => '500',
		'default'     => '0', // Start value.
	) );


	$wyz_cmb_header_map->add_field( array(
		'name' => esc_html__( 'Location', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Set your location. Without setting a location, the map will not appear on the front end.', 'wyzi-business-finder' ),
		'default' => '0',
		'id' => $prefix . 'contact_page_map',
		'type' => 'pw_map',
		'split_values' => true,
		'attributes' => array(
			'style' => 'display:none',
		),
	));


	$wyz_cmb_header_image = new_cmb2_box( array(
		'id' => $prefix . 'header_image',
		'title' => esc_html__( 'Display Image', 'wyzi-business-finder' ),
		'object_types' => array( 'page' ),
		'context' => 'normal',
		'priority' => 'core',
		'show_names' => true,
	) );


	$wyz_cmb_header_image->add_field(
		array(
			'name' => esc_html__( 'Header Image', 'wyzi-business-finder' ),
			'id' => $prefix . 'page_header_image',
			'type' => 'file',
			'options' => array( 'url' => false, ),
			'text'    => array(
				'add_upload_file_text' => esc_html__( 'ADD OR UPLOAD FILE', 'wyzi-business-finder' ),
			),
		)
	);

	$wyz_cmb_header_image->add_field( array(
		'name' => esc_html__( 'Image height', 'wyzi-business-finder' ),
		'id'   => $prefix . 'image_height',
		'type' => 'own_slider',
		'min'         => '50',
		'max'         => '1000',
		'default'     => '600', // Start value.
		'value_label' => 'px',
	) );

	$wyz_cmb_header_image->add_field( array(
		'name' => esc_html__( 'Main Text', 'wyzi-business-finder' ),
		'id' => $prefix . 'page_header_image_main_text',
		'type' => 'text',
	));

	$wyz_cmb_header_image->add_field( array(
		'name' => esc_html__( 'Sub Text', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Displays bellow main text', 'wyzi-business-finder' ),
		'id' => $prefix . 'page_header_image_sub_text',
		'type' => 'text',
	));

	$wyz_cmb_header_image->add_field( array(
		'name' => esc_html__( 'Display search fields', 'wyzi-business-finder' ),
		'id'   => $prefix . 'page_header_image_show_filters',
		'type' => 'checkbox',
		'std' => 1,
	) );

	$wyz_cmb_header_image->add_field( array(
		'name' => esc_html__( 'Search fields order', 'wyzi-business-finder' ),
		'id'   => $prefix . 'page_header_image_filters',
		'type' => 'text',
		'desc' => __( 'Enter the order of indexes to display the filters in, comma separated.<br>
								1: Keyword filer<br>
								2: Location filter<br>
								3: Category filter<br>
								4: Open Days Filter.', 'wyzi-business-finder' ),
		'std' => 1,
	) );

	/*$wyz_cmb_header_image->add_field( array(
		'name'    => esc_html__( 'Location Filter Type', 'wyzi-business-finder' ),
		'desc'    => esc_html__( 'Display a dropdown or an auto-complete text as location filter', 'wyzi-business-finder' ),
		'id'      => $prefix . 'image_location_filter_type',
		'type'    => 'select',
		'options' => array( 'text' => esc_html__('Text', 'wyzi-business-finder' ), 'dropdown' => esc_html__( 'Dropdown', 'wyzi-business-finder') ),
	) );*/

	$wyz_cmb_header_image->add_field( array(
		'name'    => esc_html__( 'Default Location', 'wyzi-business-finder' ),
		'desc'    => sprintf( esc_html__( 'Set in case you want a default location to be set', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'id'      => $prefix . 'def_image_location',
		'type'    => 'select',
		'options' => WyzHelpers::get_businesses_locations_options(),
	) );


	if(isset($opt['revslider'])):

	$wyz_cmb_header_rev_slider = new_cmb2_box( array(
		'id' => $prefix . 'header_rev_slider',
		'title' => esc_html__( 'Display Revolution Slider', 'wyzi-business-finder' ),
		'object_types' => array( 'page' ),
		'context' => 'normal',
		'priority' => 'core',
		'show_names' => true,
	) );

	$slider = new RevSlider();
	$revolution_sliders = $slider->getArrSliders();
	$all_sliders = array();
	foreach ( $revolution_sliders as $revolution_slider ) {
		$all_sliders[ $revolution_slider->getAlias() ] = $revolution_slider->getTitle();
	}
	$wyz_cmb_header_rev_slider->add_field(
		array(
			'name' => esc_html__( 'Choose revolution slider', 'wyzi-business-finder' ),
			'id' => $prefix . 'page_header_rev_slider_which',
			'type' => 'select',
			'options' => $all_sliders,
		)
	);
	endif;

	$wyz_cmb_listing_page = new_cmb2_box( array(
		'id' => $prefix . 'listing_page_template',
		'title' => esc_html__( 'Page Sidebar', 'wyzi-business-finder' ),
		'object_types' => array( 'page' ),
		'context' => 'normal',
		'show_names' => true,
	) );
	$wyz_cmb_listing_page->add_field( array(
		'name' => esc_html__( 'Select page sidebar template', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Choose wheather you want this page to be full width, left sidebar or right sidebar', 'wyzi-business-finder' ),
		'id' => $prefix . 'listing_page_sidebar',
		'type' => 'select',
		'default_cb' => 'full-width',
		'options' => array(
				'full-width' => esc_html__( 'Full Width', 'wyzi-business-finder' ),
				'left-sidebar' => esc_html__( 'Left Sidebar', 'wyzi-business-finder' ),
				'right-sidebar' => esc_html__( 'Right Sidebar', 'wyzi-business-finder' ),
			),
	));
	$wyz_cmb_listing_page->add_field( array(
		'name' => esc_html__( 'Grid view', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'If checked, the businesses list will be displayed as a grid.', 'wyzi-business-finder' ),
		'id' => $prefix . 'list_grid',
		'type' => 'checkbox',
	));
	$wyz_cmb_listing_page->add_field( array(
		'name' => esc_html__( 'Posts per page', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Maximum nubmber of Businesses to display per page', 'wyzi-business-finder' ),
		'id' => $prefix . 'listing_page_pagination',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'default_cb' => 10,
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	));
}
add_filter( 'cmb2_init', 'wyz_display_map_metaboxes' );


// Add google map api key to google map on page creation admin pages.
add_filter( 'pw-google-maps-api-key', function() {
	return get_option( 'wyz_map_api_key' );
});


/**
 * Save plugin's default options.
 */
function wyz_save_default_options() {
	require_once( WYZI_PLUGIN_DIR . 'businesses-and-offers/businesses/default-settings.php' );
	foreach ( $default_settings as $key => $value ) {
		add_option( $key, $value );
	}
}


/**
 * Function that fires on plugin deactivation
 */
function wyz_on_deactivation() {

	wp_clear_scheduled_hook( 'wyz_daily_event' );
}

register_deactivation_hook( __FILE__, 'wyz_on_deactivation' );

require_once( WYZI_PLUGIN_DIR . 'templates-and-shortcodes/shortcodes.php' );
require_once( WYZI_PLUGIN_DIR . 'templates-and-shortcodes/sliders/sliders.php' );

require_once( WYZI_PLUGIN_DIR . 'job-manager/job-manager.php' );

require_once( WYZI_PLUGIN_DIR . 'classes/cmb2-field-type-selectize.php' );
require_once( WYZI_PLUGIN_DIR . 'classes/cmb2-radio-image.php' );

require_once( WYZI_PLUGIN_DIR . 'businesses-and-offers/businesses-and-offers.php' );

require_once( WYZI_PLUGIN_DIR . 'front-end-registration/front-end-registration.php' );
require_once( WYZI_PLUGIN_DIR . 'front-end-registration/user-profile.php' );

require_once( WYZI_PLUGIN_DIR . 'hooks.php' );

require_once( WYZI_PLUGIN_DIR . 'settings.php' );

require_once( WYZI_PLUGIN_DIR . 'classes/helpers.php' );
require_once( WYZI_PLUGIN_DIR . 'classes/class-user-account.php' );
require_once( WYZI_PLUGIN_DIR . 'classes/class-query-vars.php' );

require_once( WYZI_PLUGIN_DIR . 'classes/class-post-share.php' );

require_once( WYZI_PLUGIN_DIR . 'classes/widgets/class-offers-tabs-widget.php' );
require_once( WYZI_PLUGIN_DIR . 'classes/widgets/class-business-filter-widget.php' );

require_once( WYZI_PLUGIN_DIR . 'templates-and-shortcodes/map-class.php' );

require_once( WYZI_PLUGIN_DIR . 'points/points.php' );

require_once( WYZI_PLUGIN_DIR . 'claim/claim_cpt_Creation.php' );

require_once( WYZI_PLUGIN_DIR . 'locations/locations.php' );

require_once( WYZI_PLUGIN_DIR . 'subscriptions/subscription-extra-options.php' );
require_once( WYZI_PLUGIN_DIR . 'wp-csv/wp-csv.php' );

//register bookings taxonomies
//require_once( WYZI_PLUGIN_DIR . 'booked/post-types/booked_appointments.php' );
//$booked_appointments_post_type = new booked_appointments_post_type();

//booking
require_once( WYZI_PLUGIN_DIR . 'booked/booked.php' );

add_action('init',function(){
	$product_settings = array(
                'inventory' => 'Enable',
                'shipping' => 'Enable',
                'linked_products' => 'Enable',
                'attribute' => 'Enable',
                'advanced' => 'Enable',
                'simple' => 'Enable',
                'variable' => 'Enable',
                'grouped' => 'Enable',
                'virtual' => 'Enable',
                'external' => 'Enable',
                'downloadable' => 'Enable',
                'booked_appointment' => 'Enable',
                'taxes' => 'Enable',
                'add_comment' => 'Enable',
                'comment_box' => 'Enable',
                'sku' => 'Enable',
            );
            update_option('wcmp_product_settings_name', $product_settings);
});


// Lets Disalow Backend Entering for Products in case the front end Product Submission is On
if ( 'on' == get_option('wyz_allow_front_end_submit','on')) 
add_action('admin_init', 'wyzi_disallowed_admin_pages_products',9);

function wyzi_disallowed_admin_pages_products(){
    global $pagenow;
    /* Check current admin page. */
    if(! current_user_can( 'manage_options' ) && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'product'){
       wp_redirect( home_url().'/user-account' );
        exit;
    }
}


///////////////////////////// WC Frontend Manager Compatibility \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// Add Listing Option in WCMP Frontend Manager
add_action('before_wcmp_fpm_template', function(){ 

	global $post;

	$temp_post = $post;

 	$curr_owner_bus_id = get_current_user_id();
 	
	$args = array(
		'post_type' => 'wyz_business',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'author' => $curr_owner_bus_id
	);
	
	$query = new WP_Query( $args );

	$output = '';
	// Loop through all available published businesses
	if ( $query->have_posts() ) { $output = '<div>' . '

		<select id="product_type" name="listing_id" class="regular-select">' ;
			 while ( $query->have_posts() ) {
				$query->the_post();
				$bus_id = get_the_ID();
				$output .= '<option value="' . $bus_id . '" ' . ( $curr_owner_bus_id == $bus_id ? 'selected="selected"' : '' ) . '>' . get_the_title() . " - $bus_id</option>";
			} ?>
		
	<?php ;
	}
	echo  '<h3>'.__('Listing to show Product in','wyzi-business-finder').'</h3>';
	echo $output . '</select></div>';
	wp_reset_postdata();
	
	$post = $temp_post;
	

}, 11
);

// Saving to which listing this product belongs
add_action('after_wcmp_fpm_meta_save','wyzi_listing_save_to_product',10,2);

function wyzi_listing_save_to_product ( $new_product_id, $product_manager_form_data ) { 
update_post_meta( $new_product_id, 'business_id', $product_manager_form_data['listing_id'] );

}

///////////////////////////// WC Frontend Manager Compatibility \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

///////////////////////////// Re-Assing Vendor Capabilities to Vendor Upon Saving in Store Front End \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

add_action('before_wcmp_vendor_dashboard', function(){
	$user_id = get_current_user_id();
	if ( function_exists( 'add_wcmp_users_caps' ) &&  $_SERVER['REQUEST_METHOD'] == 'POST') {
		global $WCMp;
		$user = new WP_User( $user_id );
		$user->remove_role( 'subscriber' );
		$user->remove_role( 'customer' );
		$user->remove_role( 'dc_pending_vendor' );
		$user->remove_role( 'client' );
		$user->add_role( 'business_owner' );
		$user->add_role( 'dc_vendor' );
		$WCMp->user->add_vendor_caps($user_id);
		$vendor = get_wcmp_vendor($user_id);
		$vendor->generate_term();
	}
	},
100
);
///////////////////////////// Re-Assing Vendor Capabilities to Vendor Upon Saving in Store Front End \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
