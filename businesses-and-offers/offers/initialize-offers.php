<?php
/**
 * Main initializer of the Business part of the plugin
 *
 * @package wyz
 */

if ( ! post_type_exists( 'wyz_offers' ) ) {
	// Create the wyz_offer cpt.
	add_action( 'init', 'wyz_create_offer' );
	// Add user capabilities for wyz_offer.
	add_action( 'admin_init', 'wyz_add_offers_caps' );
	// Create wyz_offer categories.
	add_action( 'init', 'wyz_create_offers_taxonomy' );
}

/**
 * Creates the wyz_business cpt
 */
function wyz_create_offer() {
	update_option( 'offer-form-prefix', 'wyz_' );
	defined( 'WYZ_OFFERS_CPT' ) or define( 'WYZ_OFFERS_CPT', wyz_syntax_permalink( get_option( 'wyz_offers_old_single_permalink' ) ) );
	register_post_type(
		'wyz_offers',
		array(
			'public' => true,
			'map_meta_cap' => true,
			'capabilities' => array(
				'publish_posts' => 'publish_offers',
				'edit_posts' => 'edit_offers',
				'edit_others_posts' => 'edit_others_offers',
				'delete_posts' => 'delete_offers',
				'delete_published_posts' => 'delete_published_offers',
				'edit_published_posts' => 'edit_published_offers',
				'delete_others_posts' => 'delete_others_offers',
				'read_private_posts' => 'read_private_offers',
				'read_post' => 'read_offer',
			),
			'labels' => array(
				'name' => WYZ_OFFERS_CPT,
				'singular_name' => WYZ_OFFERS_CPT,
				'add_new' => esc_html__( 'Add New', 'wyzi-business-finder' ),
				'add_new_item' => esc_html__( 'Add New', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT,
				'edit' => esc_html__( 'Edit', 'wyzi-business-finder' ),
				'edit_item' => esc_html__( 'Edit', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT,
				'new_item' => esc_html__( 'New', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT,
				'view' => esc_html__( 'View', 'wyzi-business-finder' ),
				'view_item' => esc_html__( 'View', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT,
				'search_items' => esc_html__( 'Search', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT,
				'not_found' => esc_html__( 'No', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'found', 'wyzi-business-finder' ),
				'not_found_in_trash' => esc_html__( 'No', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'found in Trash', 'wyzi-business-finder' ),
				'parent' => esc_html__( 'Parent', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT,
			),
			'public' => true,
			'menu_position' => 56.2,
			'supports' => array( 'title', 'editor' ),
			'taxonomies' => array( '' ),
			'menu_icon' => plugins_url( 'images/offers-icon.png', __FILE__ ),
			'has_archive' => true,
			'rewrite' => array( 'slug' => esc_html( get_option( 'wyz_offers_old_single_permalink' ) ) ),
		)
	);
}

/**
 * Add custom roles to admins and business owners.
 */
function wyz_add_offers_caps() {
	// Gets the administrator role.
	$admins = get_role( 'administrator' );

	$admins->add_cap( 'publish_offers' );
	$admins->add_cap( 'edit_offers' );
	$admins->add_cap( 'edit_others_offers' );
	$admins->add_cap( 'delete_offers' );
	$admins->add_cap( 'delete_others_offers' );
	$admins->add_cap( 'read_private_offers' );
	$admins->add_cap( 'read_offer' );
	$admins->add_cap( 'delete_published_offers' );
	$admins->add_cap( 'edit_published_offers' );
}


/**
 * Register the form and fields for the front-end submission form.
 */
function wyz_register_offers_frontend_meta_boxes( $meta_boxes ) {
	$prefix = 'wyz_';

	$wyz_cmb_offers = new_cmb2_box( array(
		'id' => $prefix . 'frontend_offers',
		'title' => WYZ_OFFERS_CPT . ' ' . esc_html__( 'Fields', 'wyzi-business-finder' ),
		'object_types' => array( 'wyz_offers' ),
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true,
		'hookup' => false,
		'save_fields' => false,
	) );

	require( plugin_dir_path( __FILE__ ) . '/forms/offers-form-fields.php' );
}
add_filter( 'cmb2_init', 'wyz_register_offers_frontend_meta_boxes' );


/**
 * Register forms and fields for backend offer submission.
 */
function wyz_register_offers_backend_meta_boxes( $meta_boxes ) {
	$prefix = 'wyz_';

	$wyz_cmb_offers = new_cmb2_box( array(
		'id' => $prefix . 'backend_offers',
		'title' => WYZ_OFFERS_CPT . ' ' . esc_html__( 'Fields', 'wyzi-business-finder' ),
		'object_types' => array( 'wyz_offers' ),
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true,
	) );

	require( plugin_dir_path( __FILE__ ) . '/forms/offers-backend-form-fields.php' );
}
add_filter( 'cmb2_init', 'wyz_register_offers_backend_meta_boxes' );


/**
 * Filter to show offers on single-offers template.
 */
function wyz_include_offers_template_function( $template_path ) {
	// Display offer taxonomies/archives on offer-archive.php template.
	if ( is_post_type_archive( 'wyz_offers' ) || is_tax( 'offer-categories' ) ) {
		/*
		Checks if the file exists in the theme first,
		otherwise serve the file from the plugin
		*/
		if ( $theme_file = locate_template( array( 'offer-archive.php' ) ) ) {
			$template_path = $theme_file;
		} else {
			$template_path = plugin_dir_path( __FILE__ ) . 'offer-archive.php';
		}
	} elseif ( get_post_type() == 'wyz_offers' ) {
		if ( is_single() ) {
			if ( $theme_file = locate_template( array( 'single-offer.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . 'single-offer.php';
			}
		}
	}
	return $template_path;
}
add_filter( 'template_include', 'wyz_include_offers_template_function', 1 );


/**
 * Register offers taxonamies.
 */
function wyz_create_offers_taxonomy() {
	$labels = array(
		'name' => WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'singular_name' => WYZ_OFFERS_CPT . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'search_items' => WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'all_items' => esc_html__( 'All', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'edit_item' => esc_html__( 'Edit', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'update_item' => esc_html__( 'Update', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'add_new_item' => esc_html__( 'Add New', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'new_item_name' => esc_html__( 'New', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Category Name', 'wyzi-business-finder' ),
		'menu_name' => esc_html__( 'Categories', 'wyzi-business-finder' ),
		'view_item' => esc_html__( 'View', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'popular_items' => esc_html__( 'Popular', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'separate_items_with_commas' => esc_html__( 'Separate', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories with commas', 'wyzi-business-finder' ),
		'add_or_remove_items' => esc_html__( 'Add or remove', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'choose_from_most_used' => esc_html__( 'Choose from the most used', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'not_found' => esc_html__( 'No', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'Categories found', 'wyzi-business-finder' ),
	);

	register_taxonomy(
		'offer-categories',
		'wyz_offers',
		array(
			'label' => esc_html__( 'Category', 'wyzi-business-finder' ),
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'rewrite' => array( 'slug' => 'offer-categories' ),
		)
	);
}


/**
 * Sets the title field to current post title if exists.
 */
function wyz_set_default_offer_title( $field_args, $field ) {
	$taxonomy = 'offer-categories';
	$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

	global $WYZ_USER_ACCOUNT_TYPE;
	if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditOffer ) {
		return get_the_title( $_GET[ WyzQueryVars::EditOffer ] );
	}
	return '';
}

/*function wyz_offer_publish_hook( $post_id ) {
	add_post_meta( $post_id, 'business_id', WyzHelpers::wyz_get_user_business(), true);
	if ( isset( $_POST['wyz_offers_image_id'] ) && ! empty( $_POST['wyz_offers_image_id'] ) ) {
		update_post_meta( $post_id, 'wyz_offers_image_id', $_POST['wyz_offers_image_id'] );
		update_post_meta( $post_id, 'wyz_offers_image', $_POST['wyz_offers_image'] );
	}
}
add_action( 'publish_wyz_offers', 'wyz_offer_publish_hook' );
add_action( 'update_wyz_offers', 'wyz_offer_publish_hook' );*/

require_once( plugin_dir_path( __FILE__ ) . 'offer-class.php' );
?>
