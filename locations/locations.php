<?php
/**
 * Main Locations initializer
 *
 * @package wyz
 */


if ( ! post_type_exists( 'wyz_location' ) ) {

	// Create business cpt.
	add_action( 'init', 'wyz_create_locations', 5 );
}


/**
 * Creates the wyz_location cpt
 */
function wyz_create_locations() {
	defined( 'LOCATION_CPT' ) or define( 'LOCATION_CPT', wyz_syntax_permalink( get_option( 'wyz_location_old_single_permalink' ) ) );
	register_post_type( 'wyz_location',array(
		'public' => true,
		'map_meta_cap' => true,
		'capabilities' => array(
			'publish_posts' => 'publish_businesses',
			'edit_posts' => 'edit_businesses',
			'edit_others_posts' => 'edit_others_businesses',
			'delete_posts' => 'delete_businesses',
			'delete_published_posts' => 'delete_published_businesses',
			'edit_published_posts' => 'edit_published_businesses',
			'delete_others_posts' => 'delete_others_businesses',
			'read_private_posts' => 'read_private_businesses',
			'read_post' => 'read_business',
		),
		'labels' => array(
			'name' => LOCATION_CPT,
			'singular_name' => LOCATION_CPT,
			'add_new' => esc_html__( 'Add New', 'wyzi-business-finder' ),
			'add_new_item' => esc_html__( 'Add New', 'wyzi-business-finder' ) . ' ' . LOCATION_CPT,
			'edit' => esc_html__( 'Edit', 'wyzi-business-finder' ),
			'edit_item' => esc_html__( 'Edit', 'wyzi-business-finder' ) . ' ' . LOCATION_CPT,
			'new_item' => esc_html__( 'New', 'wyzi-business-finder' ) . ' ' . LOCATION_CPT,
			'view' => esc_html__( 'View', 'wyzi-business-finder' ),
			'view_item' => esc_html__( 'View', 'wyzi-business-finder' ) . ' ' . LOCATION_CPT,
			'search_items' => esc_html__( 'Search', 'wyzi-business-finder' ) . ' ' . LOCATION_CPT,
			'not_found' => sprintf( esc_html__( 'No %s found', 'wyzi-business-finder' ), LOCATION_CPT ),
			'not_found_in_trash' => sprintf( esc_html__( 'No %s found in Trash', 'wyzi-business-finder' ), LOCATION_CPT ),
			'parent' => esc_html__( 'Parent', 'wyzi-business-finder' ) . ' ' . LOCATION_CPT,
		),
		'public' => true,
		'menu_position' => 56.1,
		'supports' => array( 'title', 'thumbnail', 'editor' ),
		'menu_icon' => plugins_url( 'images/locations.png', __FILE__ ),
		'has_archive' => false,
		'rewrite' => array( 'slug' => get_option( 'wyz_location_old_single_permalink' ) ),
	) );
}

/**
 * Chage template that displays business taxonomies.
 *
 * @param string $template_path path to our template file.
 */
function wyz_include_location_template_function( $template_path ) {
	// Display location archives on location-archive.php template.
	if ( 'wyz_location' === get_post_type() ) {
		if ( is_single() ) {
			if ( $theme_file = locate_template( array( 'location-archive.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . 'location-archive.php';
			}
		}
	}
	return $template_path;
}
add_filter( 'template_include', 'wyz_include_location_template_function', 1 );


/**
 * Register Location map field.
 */
function wyz_register_location_meta_box( $meta_boxes ) {
	$prefix = 'wyz_';
	$wyz_cmb_location = new_cmb2_box( array(
		'id' => $prefix . 'location_metaboxes',
		'title' => esc_html__( 'Map Coordinates', 'wyzi-business-finder' ),
		'object_types' => array( 'wyz_location' ),
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true,
	) );

	$wyz_cmb_location->add_field(
		array(
			'name' => '',
			'desc' => esc_html__( 'Choose Your Country then fine tune your location by moving the pointer', 'wyzi-business-finder' ),
			'default_cb' => '0',
			'id' => $prefix . 'location_coordinates',
			'type' => 'pw_map',
			'split_values' => true,
		)
	);
}
add_filter( 'cmb2_init', 'wyz_register_location_meta_box' );

