<?php
/**
 * Main initializer of the Business part of the plugin
 *
 * @package wyz
 */

if ( ! post_type_exists( 'wyz_business' ) ) {

	// Create business cpt.
	add_action( 'init', 'wyz_create_business', 5 );

	// Add admin user capabilities for business cpt.
	add_action( 'admin_init', 'wyz_add_businesses_caps', 6 );
	// Create business taxonomies.
	add_action( 'init', 'wyz_create_businesses_taxonomy', 6 );
}

require_once( plugin_dir_path( __FILE__ ) . 'ajax-handlers.php' );

//featured business
require_once( plugin_dir_path( __FILE__ ) . 'featured-business/featured-business.php' );



/**
 * Creates the wyz_business cpt
 */
function wyz_create_business() {
	defined( 'WYZ_BUSINESS_CPT' ) or define( 'WYZ_BUSINESS_CPT', wyz_syntax_permalink( get_option( 'wyz_business_old_single_permalink' ) ) );
	register_post_type( 'wyz_business',array(
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
			'name' => WYZ_BUSINESS_CPT,
			'singular_name' => WYZ_BUSINESS_CPT,
			'add_new' => esc_html__( 'Add New', 'wyzi-business-finder' ),
			'add_new_item' => sprintf( esc_html__( 'Add new %s item', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
			'edit' => esc_html__( 'Edit', 'wyzi-business-finder' ),
			'edit_item' => esc_html__( 'Edit', 'wyzi-business-finder' ) . ' ' . WYZ_BUSINESS_CPT,
			'new_item' => esc_html__( 'New', 'wyzi-business-finder' ) . ' ' . WYZ_BUSINESS_CPT,
			'view' => esc_html__( 'View', 'wyzi-business-finder' ),
			'view_item' => esc_html__( 'View', 'wyzi-business-finder' ) . ' ' . WYZ_BUSINESS_CPT,
			'search_items' => esc_html__( 'Search', 'wyzi-business-finder' ) . ' ' . WYZ_BUSINESS_CPT,
			'not_found' => sprintf( esc_html__( 'No %s found', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
			'not_found_in_trash' => sprintf( esc_html__( 'No %s found in trash', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
			'parent' => esc_html__( 'Parent', 'wyzi-business-finder' ) . ' ' . WYZ_BUSINESS_CPT,
		),
		'menu_position' => 55.1,
		'supports' => array( 'title', 'thumbnail', 'editor' ),
		'taxonomies' => array( '' ),
		'menu_icon' => plugins_url( 'images/business-icon.png', __FILE__ ),
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'rewrite' => array( 'slug' => esc_html( get_option( 'wyz_business_old_single_permalink' ) ) ),
	) );

	if ( true == get_option( 'just_activated', false ) ) {
		flush_rewrite_rules();
		update_option( 'just_activated', false );
	}
}

include_once( plugin_dir_path( __FILE__ ) . 'business-post.php' );
include_once( plugin_dir_path( __FILE__ ) . 'ratings/ratings.php' );

// Add cusom column to business table.
add_filter( 'manage_wyz_business_posts_columns', 'wyz_manage_business_owner_column', 10 );
add_action( 'manage_wyz_business_posts_custom_column', 'wyz_add_business_owner_column', 10, 2 );

/**
 * Add 'Owner' column to all businesses page in backend.
 *
 * @param array $defaults The default parameters of business owner column.
 */
function wyz_manage_business_owner_column( $defaults ) {
	$defaults['business_owner'] = 'Owner';
	return $defaults;
}

/**
 * Add 'Owner' column to all businesses page in backend.
 *
 * @param string  $column_name column name.
 * @param integer $post_ID id of current business.
 */
function wyz_add_business_owner_column( $column_name, $post_ID ) {
	if ( 'business_owner' === $column_name ) {
		$post = get_post( $post_ID );
		$auth_ID = $post->post_author;
		echo '<p>' . esc_html( get_the_author_meta( 'display_name', $auth_ID ) ) . '</p>';
	}
}

/**
 * Add extra column for business category admin page, for category icon.
 *
 * @param array $defaults default column data.
 */
function wyz_cat_type_columns( $defaults ) {
	$defaults['category_icon'] = esc_html__( 'Icon', 'wyzi-business-finder' );
	$defualts['map_icon'] = esc_html__( 'Map Icon', 'wyzi-business-finder' );
	return $defaults;
}
add_filter( 'manage_edit-wyz_business_category_columns', 'wyz_cat_type_columns', 5 );

/**
 * Add extra column for business category admin page, for category icon.
 *
 * @param array   $value value.
 * @param string  $column_name current business category column name.
 * @param integer $id current business category id.
 */
function wyz_cat_type_custom_columns( $value, $column_name, $id ) {
	if ( 'category_icon' === $column_name ) {
		$data = wp_get_attachment_url( get_term_meta( $id, 'wyz_business_icon_upload', true ) );
		if ( false != $data ) {
			echo '<img src="' . esc_url( $data ) . '" width=100 height=100 />';
		} else {
			echo esc_html__( 'No Image', 'wyzi-business-finder' );
		}
	} elseif ( 'map_icon' === $column_name ) {
		// Nothing to see here.
	}
}
add_action( 'manage_wyz_business_category_custom_column', 'wyz_cat_type_custom_columns', 5, 3 );

/**
 * Auto generate map icon from the category icon once a new category has been made. icons are saved in wp library.
 *
 * @param integer $term_id the term id.
 * @param integer $tt_id the term id.
 */
function wyz_generate_map_icons( $term_id, $tt_id ) {

	$icon_uploaded = get_term_meta( $term_id, 'wyz_business_cat_icon_upload', true );
	$icon_2_uploaded = get_term_meta( $term_id, 'wyz_business_cat_icon_2_upload', true );

	if ( isset( $icon_2_uploaded['id'] ) )
		update_term_meta( $term_id, 'wyz_business_icon_2_upload', $icon_2_uploaded['id'] );

	if ( isset( $icon_uploaded ) && ! empty( $icon_uploaded ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'image-resize/smart_resize_image.function.php' );

		// Get the original category icon upon category creation.
		$url = $icon_uploaded['url'];
		$url_arr = explode( '/', $url );
		$ct = count( $url_arr );
		$name = $url_arr[ $ct - 1 ];
		$name_div = explode( '.', $name );
		$img_name = $name_div[0];

		// Path and url to where we save icons.
		$uploads_path = plugin_dir_path( __FILE__ ) . 'map-icons';
		$uploads_url = plugin_dir_url( __FILE__ ) . 'map-icons';

		// Path and url of saving directory + image name.
		$location_url_25 = $uploads_url . '/x25_' . $name;
		$location_path_25 = $uploads_path . '/x25_' . $name;
		$location_url_32 = $uploads_url . '/x32_' . $name;
		$location_path_32 = $uploads_path . '/x32_' . $name;
		$location_url = $uploads_url . '/' . $name;
		$location_path = $uploads_path . '/' . $name;

		// Load source and mask.
		$mask = imagecreatefrompng( $uploads_path . '/marker.png' );
		$mask2 = imagecreatefrompng( $uploads_path . '/marker-2.png' );
		$mask3 = imagecreatefrompng( $uploads_path . '/marker-3.png' );
		$mask4 = imagecreatefrompng( $uploads_path . '/marker-4.png' );
		$source = imagecreatetruecolor( 32, 44 );
		$source2 = imagecreatetruecolor( 60, 60 );
		$source3 = imagecreatetruecolor( 40, 64 );
		$source4 = imagecreatetruecolor( 64, 77 );


		$bg_color = get_term_meta( $term_id, 'wyz_business_cat_bg_color', true );
		list( $r, $g, $b ) = sscanf( $bg_color, '#%02x%02x%02x' );

		// Sets background category color.
		$color = imagecolorallocate( $source, $r, $g, $b );
		$color2 = imagecolorallocate( $source2, $r, $g, $b );
		$color3 = imagecolorallocate( $source3, $r, $g, $b );
		$color4 = imagecolorallocate( $source4, $r, $g, $b );
		imagefill( $source, 0, 0, $color );
		imagefill( $source2, 0, 0, $color2 );
		imagefill( $source3, 0, 0, $color3 );
		imagefill( $source4, 0, 0, $color4 );

		// Apply mask to source.
		WyzHelpers::wyz_imagealphamask( $source, $mask );
		WyzHelpers::wyz_imagealphamask( $source2, $mask2 );
		WyzHelpers::wyz_imagealphamask( $source3, $mask3 );
		WyzHelpers::wyz_imagealphamask( $source4, $mask4 );
		//Source has now a 40x55 image with category color as bg color.

		// Resize icon to 25 by 25 for map marker icon and save it to map-icons folder.
		wyz_smart_resize_image( null, file_get_contents( $url ), 32, 32, false, $location_path_32, false, false, 100 );
		wyz_smart_resize_image( null, file_get_contents( $url ), 25, 25, false, $location_path_25, false, false, 100 );
		// Now $location_url_25 holds the 25x25 image url.

		// Resize icon to 50 by 50 for category icon and save it to map-icons folder.
		wyz_smart_resize_image( null, file_get_contents( $url ), 50, 50, false, $location_path, false, false, 100 );
		// Now $location_url holds the 50x50 image url.

		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = 'image/png';
		$ext = '.png';
		// Get image type, and use appropriate function to generate temp image.
		$type = exif_imagetype( $location_url_25 );
		switch ( $type ) {
			case IMAGETYPE_JPEG:
				$image_2 = imagecreatefromjpeg( $location_url_25 );
				$image_2_2 = imagecreatefromjpeg( $location_url_32 );
				$image_2_3 = imagecreatefromjpeg( $location_url_25 );
				$image_2_4 = imagecreatefromjpeg( $location_url );
				$filetype = 'image/jpeg';
				$ext = '.jpeg';
			break;
			case IMAGETYPE_GIF:
				$image_2 = imagecreatefromgif( $location_url_25 );
				$image_2_2 = imagecreatefromgif( $location_url_32 );
				$image_2_3 = imagecreatefromgif( $location_url_25 );
				$image_2_4 = imagecreatefromgif( $location_url );
				$filetype = 'image/gif';
				$ext = '.gif';
			break;
			case IMAGETYPE_PNG:
				$image_2 = imagecreatefrompng( $location_url_25 );
				$image_2_2 = imagecreatefrompng( $location_url_32 );
				$image_2_3 = imagecreatefrompng( $location_url_25 );
				$image_2_4 = imagecreatefrompng( $location_url );
				$filetype = 'image/png';
				$ext = '.png';
		}

		// Solve black background issues when copying small image ontop of larger one.
		$temp_img = imagecreatetruecolor( 32, 44 );
		imagealphablending( $temp_img, true );
		imagesavealpha( $temp_img, true );
		imagefill( $temp_img, 0, 0, 0x7fff0000 );
		imagecopy( $temp_img, $image_2, 0, 0, 0, 0, 32, 44 );
		imagefill( $temp_img, 25, 25, 0x7fff0000 );
		$temp_img_2 = imagecreatetruecolor( 60, 60 );
		imagealphablending( $temp_img_2, true );
		imagesavealpha( $temp_img_2, true );
		imagefill( $temp_img_2, 0, 0, 0x7fff0000 );
		imagecopy( $temp_img_2, $image_2_2, 0, 0, 0, 0, 60, 60 );
		imagefill( $temp_img_2, 32, 32, 0x7fff0000 );
		$temp_img_3 = imagecreatetruecolor( 40, 64 );
		imagealphablending( $temp_img_3, true );
		imagesavealpha( $temp_img_3, true );
		imagefill( $temp_img_3, 0, 0, 0x7fff0000 );
		imagecopy( $temp_img_3, $image_2_3, 0, 0, 0, 0, 40, 64 );
		imagefill( $temp_img_3, 25, 25, 0x7fff0000 );
		$temp_img_4 = imagecreatetruecolor( 64, 77 );
		imagealphablending( $temp_img_4, true );
		imagesavealpha( $temp_img_4, true );
		imagefill( $temp_img_4, 0, 0, 0x7fff0000 );
		imagecopy( $temp_img_4, $image_2_4, 0, 0, 0, 0, 64, 77 );
		imagefill( $temp_img_4, 50, 50, 0x7fff0000 );

		// Copy icon ontop of marker and place them inside image 1.
		imagecopy( $source, $temp_img, 4, 5, 0, 0, 32, 44 );
		imagecopy( $source2, $temp_img_2, 13, 13, 0, 0, 60, 60 );
		imagecopy( $source3, $temp_img_3, 8, 4, 0, 0, 40, 64 );
		imagecopy( $source4, $temp_img_4, 7, 13, 0, 0, 64, 77 );

		// Save the finished marker with icon.
		imagepng( $source, $uploads_path . '/mapicon-' . $img_name . '.png' );
		imagepng( $source2, $uploads_path . '/mapicon-' . $img_name . '-2.png' );
		imagepng( $source3, $uploads_path . '/mapicon-' . $img_name . '-3.png' );
		imagepng( $source4, $uploads_path . '/mapicon-' . $img_name . '-4.png' );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Move map marker (25x25) to wp upload directory.
		$upload_25 = wp_upload_bits( 'mapicon-' . $img_name . '.png', null, file_get_contents( $uploads_path . '/mapicon-' . $img_name . '.png' ) );
		$upload_25_2 = wp_upload_bits( 'mapicon-' . $img_name . '-2.png', null, file_get_contents( $uploads_path . '/mapicon-' . $img_name . '-2.png' ) );
		$upload_25_3 = wp_upload_bits( 'mapicon-' . $img_name . '-3.png', null, file_get_contents( $uploads_path . '/mapicon-' . $img_name . '-3.png' ) );
		$upload_25_4 = wp_upload_bits( 'mapicon-' . $img_name . '-4.png', null, file_get_contents( $uploads_path . '/mapicon-' . $img_name . '-4.png' ) );

		// Prepare an array of post data for the attachment.
		$attachment_25 = array(
			'guid' => $upload_25['url'],
			'post_mime_type' => 'image/png',
			'post_title' => preg_replace( '/\.[^.]+$/', '', 'Map Icon: ' . $img_name . '.png' ),
			'post_content' => '',
			'post_status' => 'inherit',
		);
		$attachment_25_2 = array(
			'guid' => $upload_25_2['url'],
			'post_mime_type' => 'image/png',
			'post_title' => preg_replace( '/\.[^.]+$/', '', 'Map Icon: ' . $img_name . '-2.png' ),
			'post_content' => '',
			'post_status' => 'inherit',
		);
		$attachment_25_3 = array(
			'guid' => $upload_25_3['url'],
			'post_mime_type' => 'image/png',
			'post_title' => preg_replace( '/\.[^.]+$/', '', 'Map Icon: ' . $img_name . '-3.png' ),
			'post_content' => '',
			'post_status' => 'inherit',
		);
		$attachment_25_4 = array(
			'guid' => $upload_25_4['url'],
			'post_mime_type' => 'image/png',
			'post_title' => preg_replace( '/\.[^.]+$/', '', 'Map Icon: ' . $img_name . '-4.png' ),
			'post_content' => '',
			'post_status' => 'inherit',
		);

		// Insert the map marker attachment.
		$attach_id_25 = wp_insert_attachment( $attachment_25, $upload_25['file'], $term_id );
		$attach_data_25 = wp_generate_attachment_metadata( $attach_id_25, $upload_25['file'] );
		$update_25 = wp_update_attachment_metadata( $attach_id_25, $attach_data_25 );
		$attach_id_25_2 = wp_insert_attachment( $attachment_25_2, $upload_25_2['file'], $term_id );
		$attach_data_25_2 = wp_generate_attachment_metadata( $attach_id_25_2, $upload_25_2['file'] );
		$update_25_2 = wp_update_attachment_metadata( $attach_id_25_2, $attach_data_25_2 );
		$attach_id_25_3 = wp_insert_attachment( $attachment_25_3, $upload_25_3['file'], $term_id );
		$attach_data_25_3 = wp_generate_attachment_metadata( $attach_id_25_3, $upload_25_3['file'] );
		$update_25_3 = wp_update_attachment_metadata( $attach_id_25_3, $attach_data_25_3 );
		$attach_id_25_4 = wp_insert_attachment( $attachment_25_4, $upload_25_4['file'], $term_id );
		$attach_data_25_4 = wp_generate_attachment_metadata( $attach_id_25_4, $upload_25_4['file'] );
		$update_25_4 = wp_update_attachment_metadata( $attach_id_25_4, $attach_data_25_4 );

		// Update the category ' map_icon ' meta data.
		update_term_meta( $term_id, 'map_icon', $attach_id_25 );
		update_term_meta( $term_id, 'map_icon2', $attach_id_25_2 );
		update_term_meta( $term_id, 'map_icon3', $attach_id_25_3 );
		update_term_meta( $term_id, 'map_icon4', $attach_id_25_4 );

		// Move category icon to wp upload directory.
		$upload = wp_upload_bits( $img_name . $ext, null, file_get_contents( $location_path ) );

		$attachment = array(
			'guid'           => $upload['url'],
			'post_mime_type' => $filetype,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $img_name . $ext ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert the category icon attachment.
		$attach_id = wp_insert_attachment( $attachment, $upload['file'], $term_id );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		$update = wp_update_attachment_metadata( $attach_id, $attach_data );

		// Update the category meta data to have the new smaller image as category icon.
		update_term_meta( $term_id, 'wyz_business_icon_upload', $attach_id );

	}
}
add_action( 'created_wyz_business_category', 'wyz_generate_map_icons', 11, 2 );
add_action( 'edited_wyz_business_category', 'wyz_generate_map_icons', 11, 2 );

/**
 * Reorder columns in businesses table.
 *
 * @param array $columns the cpt display columns.
 */
function wyz_custom_cpt_columns( $columns ) {
	$date = $columns['date'];
	unset( $columns['date'] );
	$columns['business_owner'] = esc_html__( 'Owner', 'wyzi-business-finder' );
	$columns['date'] = $date;
	return $columns;
}
add_filter( 'manage_edit-wyz_business_columns', 'wyz_custom_cpt_columns' );

/**
 * Add custom roles to admins and business owners.
 */
function wyz_add_businesses_caps() {
	// Get the administrator role.
	$admins = get_role( 'administrator' );

	$admins->add_cap( 'publish_businesses' );
	$admins->add_cap( 'edit_businesses' );
	$admins->add_cap( 'edit_others_businesses' );
	$admins->add_cap( 'delete_businesses' );
	$admins->add_cap( 'delete_others_businesses' );
	$admins->add_cap( 'read_private_businesses' );
	$admins->add_cap( 'read_business' );
	$admins->add_cap( 'delete_published_businesses' );
	$admins->add_cap( 'edit_published_businesses' );

	$admins = get_role( 'business_owner' );

	$admins->add_cap( 'publish_businesses' );
	$admins->add_cap( 'edit_businesses' );
	$admins->add_cap( 'delete_businesses' );
	$admins->add_cap( 'read_business' );
	$admins->add_cap( 'delete_published_businesses' );
	$admins->add_cap( 'edit_published_businesses' );
}

/**
 * Related to business cpt permalink name change.
 * Once business permalink name has been changed in settings, update the 'wyz_business_old_single_permalink', 'wyz_business_post_old_single_permalink' and '' options.
 */
function wyz_check_for_permalink_change() {
	if ( get_option( 'wyz_business_new_single_permalink' ) != get_option( 'wyz_business_old_single_permalink' ) ||
		get_option( 'wyz_business_post_new_single_permalink' ) != get_option( 'wyz_business_post_old_single_permalink' ) ||
		get_option( 'wyz_offers_new_single_permalink' ) != get_option( 'wyz_offers_old_single_permalink' ) ||
		get_option( 'wyz_location_new_single_permalink' ) != get_option( 'wyz_location_old_single_permalink' ) ) {

		update_option( 'wyz_business_old_single_permalink', esc_html( get_option( 'wyz_business_new_single_permalink' ) ) );
		update_option( 'wyz_business_post_old_single_permalink', esc_html( get_option( 'wyz_business_post_new_single_permalink' ) ) );
		update_option( 'wyz_offers_old_single_permalink', esc_html( get_option( 'wyz_offers_new_single_permalink' ) ) );
		update_option( 'wyz_location_old_single_permalink', esc_html( get_option( 'wyz_location_new_single_permalink' ) ) );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'wyz_check_for_permalink_change', 4 );

/**
 * Related to business cpt permalink name change.
 *
 * @param string $old_value old business cpt permalink name.
 * @param string $new_value new business cpt permalink name.
 */
function wyz_check_business_permalink( $old_value, $new_value ) {
	if ( $old_value != $new_value ) {
		update_option( 'wyz_business_new_single_permalink', $new_value );
		header( 'Refresh:0' );
	}
}
add_action( 'update_option_wyz_business_new_single_permalink', 'wyz_check_business_permalink', 10, 2 );
/**
 * Related to business post cpt permalink name change.
 *
 * @param string $old_value old business post cpt permalink name.
 * @param string $new_value new business post cpt permalink name.
 */
function wyz_check_business_post_permalink( $old_value, $new_value ) {
	if ( $old_value != $new_value ) {
		update_option( 'wyz_business_post_new_single_permalink', $new_value );
		header( 'Refresh:0' );
	}
}
add_action( 'update_option_wyz_business_post_new_single_permalink', 'wyz_check_business_post_permalink', 10, 2 );
/**
 * Related to offer cpt permalink name change.
 *
 * @param string $old_value old offer cpt permalink name.
 * @param string $new_value new offer cpt permalink name.
 */
function wyz_check_offer_permalink( $old_value, $new_value ) {
	if ( $old_value != $new_value ) {
		update_option( 'wyz_location_new_single_permalink', $new_value );
		header( 'Refresh:0' );
	}
}
add_action( 'update_option_wyz_location_new_single_permalink', 'wyz_check_offer_permalink', 10, 2 );

/**
 * Related to location cpt permalink name change.
 *
 * @param string $old_value old location cpt permalink name.
 * @param string $new_value new location cpt permalink name.
 */
function wyz_check_location_permalink( $old_value, $new_value ) {
	if ( $old_value != $new_value ) {
		update_option( 'wyz_offers_new_single_permalink', $new_value );
		header( 'Refresh:0' );
	}
}
add_action( 'update_option_wyz_offers_new_single_permalink', 'wyz_check_location_permalink', 10, 2 );


/**
 * Register the form and fields for our front-end submission form
 */
function wyz_register_businesses_frontend_meta_boxes( $meta_boxes ) {
	$prefix = 'wyz_';

	$wyz_cmb_businesses = new_cmb2_box( array(
		'id' => $prefix . 'frontend_businesses',
		'title' => esc_html__( 'Business Fields', 'wyzi-business-finder' ),
		'object_types' => array( 'wyz_business' ),
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true,
		'hookup' => false,
		'save_fields' => false,
	) );
	require( plugin_dir_path( __FILE__ ) . '/forms/businesses-form-fields.php' );
}
add_filter( 'cmb2_init', 'wyz_register_businesses_frontend_meta_boxes' );

/**
 * Register forms and fields for backend offer submission.
 */
function wyz_register_businesses_backend_meta_boxes( $meta_boxes ) {
	$prefix = 'wyz_';
	$wyz_cmb_businesses = new_cmb2_box( array(
		'id' => $prefix . 'backend_businesses',
		'title' => esc_html__( 'Business Fields', 'wyzi-business-finder' ),
		'object_types' => array( 'wyz_business' ),
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true,
	) );
	require( plugin_dir_path( __FILE__ ) . '/forms/businesses-backend-form-fields.php' );
}
add_filter( 'cmb2_init', 'wyz_register_businesses_backend_meta_boxes' );

/**
 * Add phone cmb2.
 */
function wyz_render_phone( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	echo $field_type_object->input( array(
		'class' => 'cmb2-text-medium',
		'type' => 'number',
	) );
}
add_action( 'cmb2_render_phone', 'wyz_render_phone', 10, 5 );

/**
 * Sanitize the field.
 */
function wyz_sanitize_phone( $null, $new ) {
	$new = preg_replace( '/[^0-9]/', '', $new );
	return $new;
}
add_filter( 'cmb2_sanitize_phone', 'wyz_sanitize_phone', 10, 2 );

/**
 * Chage template that displays business taxonomies.
 *
 * @param string $template_path path to our template file.
 */
function wyz_include_business_template_function( $template_path ) {

	//change template path to business page in case we used the one-page template
	if ( is_front_page() ) {

		if ( ! function_exists( 'wyz_get_option' ) ) return $template_path;

		if ( 'on' == wyz_get_option( 'one_page_template') ) {
			$bus_id = wyz_get_option( 'one-page-business-cpt' );
			global $wp_query;
			if ( ! is_string( get_post_status( $bus_id ) ) ) { //check if the post doesn't exists
				$args = array(
					'post_type' => 'wyz_business',
					'posts_per_page' => 1,
				);
				$post_object = wp_get_recent_posts( $args, OBJECT );
			} else {
				$post_object = get_post( $bus_id, OBJECT );
			}
			setup_postdata( $GLOBALS['post'] =& $post_object );
			$wp_query = new Wp_Query( array(
				'post_type' => 'wyz_business',
				'p' => $post_object->ID,
			));
			return plugin_dir_path( __FILE__ ) . 'single-business.php';
		}
	}
	// Display business taxonomies/archives on business-archive.php template.
	if ( is_post_type_archive( 'wyz_business' ) || is_tax( 'wyz_business_category' ) || is_tax( 'wyz_business_tag' ) ) {
		/*
		Checks if the file exists in the theme first,
		otherwise serve the file from the plugin.
		*/
		if ( $theme_file = locate_template( array( 'business-archive.php' ) ) ) {
			$template_path = $theme_file;
		} else {
			$template_path = plugin_dir_path( __FILE__ ) . 'business-archive.php';
		}
	} elseif ( 'wyz_business' === get_post_type() ) { // Display business on single-business.php template.
		if ( is_single() ) {
			if ( $theme_file = locate_template( array( 'single-business.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . 'single-business.php';
			}
		}
	}
	return $template_path;
}
add_filter( 'template_include', 'wyz_include_business_template_function', 1 );

/**
 * Register business taxonomies.
 */
function wyz_create_businesses_taxonomy() {
	$labels = array(
		'name' => WYZ_BUSINESS_CPT . ' ' . esc_html__( 'Categories', 'wyzi-business-finder' ),
		'singular_name' => WYZ_BUSINESS_CPT . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
		'search_items' => sprintf( esc_html__( 'Search %s Categories', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'all_items' => sprintf( esc_html__( 'All %s Categories', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'edit_item' => sprintf( esc_html__( 'Edit %s Category', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'update_item' => sprintf( esc_html__( 'Update %s Category', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'add_new_item' => sprintf( esc_html__( 'Add New %s Category', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'new_item_name' => sprintf( esc_html__( 'New %s Category Name', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'menu_name' => esc_html__( 'Categories', 'wyzi-business-finder' ),
		'view_item' => sprintf( esc_html__( 'View %s Category', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'popular_items' => sprintf( esc_html__( 'Popular %s Categories', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'separate_items_with_commas' => sprintf( esc_html__( 'Separate %s Categories with commas', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'add_or_remove_items' => sprintf( esc_html__( 'Add or Remove %s Categories', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'choose_from_most_used' => sprintf( esc_html__( 'Choose from the most used %s Categories', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'not_found' => sprintf( esc_html__( 'No %s Categories found', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
	);

	register_taxonomy(
		'wyz_business_category',
		'wyz_business',
		array(
			'label' => esc_html( ucwords( get_option( 'wyz_business_old_single_permalink' ) ) ) . ' ' . esc_html__( 'Category', 'wyzi-business-finder' ),
			'hierarchical' => true,
			'capabilities' => array (
				'manage_terms' => 'manage_options',
				'edit_terms' => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'edit_posts',
			),
			'labels' => $labels,
			'show_ui' => true,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'rewrite' => array( 'slug' => get_option( 'wyz_business_old_single_permalink' ) . '_category' ),
		)
	);

	// Business tags.
	$labels = array(
		'name' => WYZ_BUSINESS_CPT . ' ' . esc_html__( 'Tags', 'wyzi-business-finder' ),
		'singular_name' => WYZ_BUSINESS_CPT . ' ' .esc_html__( 'Tag', 'wyzi-business-finder' ),
		'search_items' => sprintf( esc_html__( 'Search %s Tags', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'popular_items' => sprintf( esc_html__( 'Popular %s Tags', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'all_items' => sprintf( esc_html__( 'All %s Tags', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => sprintf( esc_html__( 'Edit %s Tag', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'update_item' => sprintf( esc_html__( 'Update %s Tag', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'add_new_item' => sprintf( esc_html__( 'Add New %s Tag', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'new_item_name' => sprintf( esc_html__( 'New %s Tag name', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'separate_items_with_commas' => sprintf( esc_html__( 'Separate %s Tags with commas', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'add_or_remove_items' => sprintf( esc_html__( 'Add or remove %s Tags', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'choose_from_most_used' => sprintf( esc_html__( 'Choose from the most used %s Tags', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'not_found' => sprintf( esc_html__( 'No %s Tags found', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),
		'menu_name' => esc_html__( 'Tags', 'wyzi-business-finder' ),
	);

	$args = array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => array( 'slug' => get_option( 'wyz_business_old_single_permalink' ) . '_tags' ),
	);

	register_taxonomy( 'wyz_business_tag', 'wyz_business', $args );
}

require_once( plugin_dir_path( __FILE__ ) . 'Tax-meta-class/migration/tax_to_term_meta.php' );

require_once( plugin_dir_path( __FILE__ ) . 'Tax-meta-class/Tax-meta-class.php' );

function wyz_init_tax_meta() {
	if ( is_admin() ) {
		$template_type = 1;
		if ( function_exists( 'wyz_get_theme_template' ) )
			$template_type = wyz_get_theme_template();
		new tax_to_term_meta();
		/*
		 * configure taxonomy custom fields
		*/
		
		$config = array(
			'id' => 'wyz_business_icon',
			'title' => 'Category Icon',
			'pages' => array( 'wyz_business_category' ),
			'context' => 'normal',
			'fields' => array(),
			'local_images' => true,
			'use_with_theme' => false,
		);

		$business_cat_meta = new Tax_Meta_Class( $config );
		$business_cat_meta->addImage( 'wyz_business_cat_icon_upload', array( 'name' => esc_html__( 'Category Icon', 'wyzi-business-finder' ) ) );
		if ( 2 == $template_type )
			$business_cat_meta->addImage( 'wyz_business_cat_icon_2_upload', array( 'name' => esc_html__( 'Slider Category Icon', 'wyzi-business-finder' ) ) );
		$business_cat_meta->addColor( 'wyz_business_cat_bg_color', array( 'name' => esc_html__( 'Business Category Background Color','wyzi-business-finder' ) ) );
		$business_cat_meta->Finish();
		
		add_action( 'wyz_business_category_add_form_fields', 'wyz_business_category_form_custom_field_add', 10 );
		add_action( 'wyz_business_category_edit_form_fields','wyz_business_category_form_custom_field_add' );
	}
}
add_action( 'init', 'wyz_init_tax_meta' );


/**
 * Add business category custom color picker in business category admin page
 *
 * @param array $taxonomy taxonomy.
 */
function wyz_business_category_form_custom_field_add( $taxonomy ) {
?>
	<div class="form-field">
		<div id="canvasloader-container" style="position: absolute;bottom: 8px;left: 240px;"></div>
	</div>
<?php
}


/**
 * Enqueue Color Picker.
 *
 * @param string $hook_suffix hook suffix.
 */
function wyz_colorpicker_enqueue( $hook_suffix ) {
	if ( ( 'edit-tags.php' === $hook_suffix || 'term.php' === $hook_suffix ) && filter_input( INPUT_GET, 'taxonomy' ) && 'wyz_business_category' === filter_input( INPUT_GET, 'taxonomy' ) ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'colorpicker-js', plugin_dir_url( __FILE__ ) . 'js/colorpicker.js', array( 'wp-color-picker' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'wyz_colorpicker_enqueue' );


/**
 * Sets the title field to current post title if exists.
 */
function wyz_set_default_business_name( $field_args, $field ) {
	global $WYZ_USER_ACCOUNT_TYPE;

	if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditBusiness ) {
		return get_the_title( filter_input( INPUT_GET, WyzQueryVars::EditBusiness ) );
	}
	return '';
}

