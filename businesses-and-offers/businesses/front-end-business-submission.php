<?php
/**
 * Front end business submission code
 *
 *  @package wyz
 */

/**
 * Displays the 'add new business' form.
 *
 * @param array $atts shortcode attributes.
 */
function wyz_display_add_new_business_form( $atts ) {
	$user_id = get_current_user_id();

	if ( ! is_user_logged_in() || ! current_user_can( 'publish_businesses' ) ) {
		return WyzHelpers::wyz_error( esc_html__( 'you don\'t have the permision to view this page\'s content', 'wyzi-business-finder' ), true );
	}
	
	if ( !WyzHelpers::user_can_create_business( $user_id ) ) {
		return WyzHelpers::wyz_warning( sprintf( esc_html__( 'You already have The maximum allowed number of %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),true);
	}


	// Get CMB2 metabox object.
	$cmb = wyz_frontend_business_cmb2_get();

	$post_status = ( 'off' != get_option( 'wyz_businesses_immediate_publish' ) ? 'publish' : 'pending' );
	
	$points_available = get_user_meta( $user_id, 'points_available', true );
	if ( '' == $points_available ) {
		$points_available = 0;
	} else {
		$points_available = intval( $points_available );
		if ( $points_available < 0 ){
			$points_available = 0;
		}
	}
	$registery_price = get_option( 'wyz_businesses_registery_price' );
	if ( '' == $registery_price ) {
		$registery_price = 0;
	} else {
		$registery_price = intval( $registery_price );
		if ( $registery_price < 0 ){
			$registery_price = 0;
		}
	}

	if ( $points_available < $registery_price ) {
		$post_status = 'pending_due_to_lack_of_points';
	}

	$atts = shortcode_atts( array(
		'post_author' => $user_id ? $user_id : 1,
		'post_status' => $post_status,
		'post_type' => 'wyz_business',
	), $atts, 'business-data-display' );

	/*
	* Let's add these attributes as hidden fields to our cmb form
	* so that they will be passed through to our form submission
	*/
	foreach ( $atts as $key => $value ) {
		$cmb->add_hidden_field( array(
			'field_args' => array(
				'id' => "atts[$key]",
				'type' => 'hidden',
				'default' => $value,
				),
		) );
	}

	// Initiate our output variable.
	$output = '<div class="section-title col-xs-12 margin-bottom-50"></div>';


	/*$prog_bar_pg_1_ttl = apply_filters( 'prog_bar_pg_1_ttl', esc_html__( 'Title and Description', 'wyzi-business-finder' ) );
	$prog_bar_pg_2_ttl = apply_filters( 'prog_bar_pg_2_ttl', esc_html__( 'Open/Close Times', 'wyzi-business-finder' ) );
	$prog_bar_pg_3_ttl = apply_filters( 'prog_bar_pg_3_ttl', esc_html__( 'Address and Contact', 'wyzi-business-finder' ) );
	$prog_bar_pg_4_ttl = apply_filters( 'prog_bar_pg_4_ttl', esc_html__( 'Extra Fields', 'wyzi-business-finder' ) );*/

	//progress bar
	$output .= wyz_get_business_form_header();

	// Get any submission errors.
	if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
		// If there was an error with the submission, add it to our ouput.
		$output .= WyzHelpers::wyz_error( esc_html__( $error->get_error_message(), 'wyzi-business-finder' ), true );
	}

	// Get our form.
	if ( function_exists( 'cmb2_get_metabox_form' ) )
		$output .= '<div class="business-details-form col-md-12 col-xs-12">' . cmb2_get_metabox_form( $cmb, 'fake-object-id', array( 'save_button' => esc_html__( 'Publish', 'wyzi-business-finder' ) ) ) . '</div>';
	wp_reset_postdata();
	return $output;
}

/**
 * Gets the front-end-post-form cmb instance.
 */
function wyz_frontend_business_cmb2_get() {
	// Use ID of metabox in wyz_register_offers_frontend_meta_boxes.
	$metabox_id = 'wyz_frontend_businesses';

	// Post/object ID is not applicable since we're using this form for submission.
	global $draft_id;
	$object_id = ! empty( $draft_id ) ? $draft_id : 'fake-object-id';

	if ( 'fake-object-id' != $object_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_business',
			'p' => $object_id,
		));
		if( $query->have_posts() )
			$query->the_post();
	}

	// Get CMB2 metabox object.
	if ( function_exists( 'cmb2_get_metabox' ) )
		return cmb2_get_metabox( $metabox_id, $object_id );
}

/**
 * Handles form submission on save. Redirects if save is successful, otherwise sets an error message as a cmb property.
 */
function wyz_handle_frontend_new_business_submission_form() {


	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;

	// If no form submission, bail.
	if ( ! isset( $_POST['submit-cmb'] ) || ! isset(  $_POST['object_id'] ) ) {
		return false;
	}

	$has_exceeded_businesses = !WyzHelpers::user_can_create_business( $user_id );
	if ( $has_exceeded_businesses ) {
		return '<h2>' . sprintf( esc_html__( 'You already have The maximum allowed number of %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ) . '</h2>';
	}

	// Get CMB2 metabox.
	$cmb = wyz_frontend_business_cmb2_get();
	$post_data = array();

	// Check for errors in submitted data.
	require_once( plugin_dir_path( __FILE__ ) . 'error-check.php' );

	if ( '' !== $errors ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', $errors ) );
	}

	// Get our shortcode attributes and set them as our initial post_data args.
	if ( isset( $_POST['atts'] ) ) {
		if ( isset( $_POST['atts']['post_status'] ) && 'pending_due_to_lack_of_points' == $_POST['atts']['post_status'] ) {
			$lack_of_points = true;
			$_POST['atts']['post_status'] = 'pending';
		} else {
			$lack_of_points = false;
		}

		foreach ( (array) $_POST['atts'] as $key => $value ) {
			$post_data[ $key ] = wp_filter_nohtml_kses( sanitize_text_field( $value ) );
		}
		unset( $_POST['atts'] );
	}

	$post_status = ( 'publish' == $post_data['post_status'] ? 'published' : 'pending' );


	// Check security nonce.
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'security_fail', esc_html__( 'Security check failed.', 'wyzi-business-finder' ) ) );
	}

	// Fetch sanitized values.
	$sanitized_values = $cmb->get_sanitized_values( $_POST );

	// Set our post data arguments
	if ( isset($sanitized_values['wyz_business_name'])){
		$business_title = wp_filter_nohtml_kses( $sanitized_values['wyz_business_name'] );
		$post_data['post_title'] = $business_title;
		unset( $sanitized_values['wyz_business_name'] );
	}

	if ( isset($sanitized_values['wyz_business_description']))
		$post_data['post_content'] = $sanitized_values['wyz_business_description'];

	// Create the new post.
	$post_data['post_type'] = 'wyz_business';

	$new_submission_id = wp_insert_post( $post_data, true );
	

	// If we hit a snag, update the user.
	if ( is_wp_error( $new_submission_id ) ) {
		return $cmb->prop( 'submission_error', $new_submission_id );
	}

	if ( ! $lack_of_points ) {
		$points_left = intval( get_the_author_meta( 'points_available', $user_id ) ) - intval( get_option( 'wyz_businesses_registery_price' ) );
		update_user_meta( $user_id, 'points_available', $points_left );
	}

	// Check if business has tags.
	if ( isset( $_POST['wyz_business_tags'] ) && ! empty( $_POST['wyz_business_tags'] ) && '' !== $_POST['wyz_business_tags'] ) {
		wp_set_object_terms( $new_submission_id, $_POST['wyz_business_tags'], 'wyz_business_tag', false );
	}

	/**
	* Other than post_type and post_status, we want
	* our uploaded attachment post to have the same post-data
	*/

	$image_id = wyz_get_image_id( $sanitized_values['wyz_business_logo'] );
	unset( $post_data['post_type'] );
	//unset( $post_data['post_status'] );

	// Set the featured image.
	if ( $image_id && ! is_wp_error( $image_id ) ) {
		set_post_thumbnail( $new_submission_id, $image_id );
	}

	if ( isset( $_POST['wyz_business_categories'] ) && '' !== $_POST['wyz_business_categories'] && ! empty( $_POST['wyz_business_categories'] ) ) {
		wp_set_object_terms( $new_submission_id, $_POST['wyz_business_categories'], 'wyz_business_category' );
	}

	if ( isset( $_POST['wyz_business_category_icon'] ) ) {
		update_post_meta( $new_submission_id, 'wyz_business_category_icon', $_POST['wyz_business_category_icon'] );
		unset( $sanitized_values['wyz_business_category_icon'] );
	}

	unset( $sanitized_values['wyz_business_categories'] );


	// Set opening and closing time values
	/*$time_form_ids = array(
		'wyz_open_close_monday',
		'wyz_open_close_tuesday',
		'wyz_open_close_wednesday',
		'wyz_open_close_thursday',
		'wyz_open_close_friday',
		'wyz_open_close_saturday',
		'wyz_open_close_sunday',
	);
	foreach ( $time_form_ids as $form_id ) {
		if ( isset( $sanitized_values[ $form_id ] ) ) {
			foreach ( $sanitized_values[ $form_id ] as $key => $value ) {
				update_post_meta( $new_submission_id, $key, $value );
				$wyz_cmb_businesses->get_field( $form_id )[ $key ]->args['attributes']['value'] = $value;
			}
		}
	}*/


	// Loop through remaining (sanitized) data, and save to post-meta.
	foreach ( $sanitized_values as $key => $value ) {
		if ( is_array( $value ) ) {
			$value = array_filter( $value );
			if ( ! empty( $value ) ) {
				update_post_meta( $new_submission_id, $key, $value );
			}
		} else {
			update_post_meta( $new_submission_id, $key, $value );
		}
	}

	update_user_meta( $user_id, 'has_business', true );
	WyzHelpers::add_business_to_user( $user_id, $new_submission_id, $post_status );
	//update_user_meta( $user_id, 'business_id', $new_submission_id );

	$email = get_option( 'admin_email' );

	if ( '' != $email ) {
		//send the admin an email of business registration
		$message = wyz_get_option( 'admin-noice-new-business-email' );
		//$business_title
		$username = $current_user->user_login;

		
		$message = str_replace( '%USERNAME%', $username, $message );
		$message = str_replace( '%BUSINESSNAME%', $business_title, $message );
		WyzHelpers::wyz_mail( $email, 'New Business Submission', $message, 'new_business' );
	}

	/*
	* Redirect back to the form page with a query variable with the new post ID.
	* This will help double-submissions with browser refreshes
	*/
	if ( $lack_of_points ) {
		$url = '?business_created=' . $new_submission_id . '&not_enough_points=true';
	} else {
		$url = '?business_created=' . $new_submission_id;
	}

	wp_redirect( esc_url_raw( $url ) );
	exit;
}

/**
 * Get image ID from url.
 *
 * @param string $image_url url of the image.
 */
function wyz_get_image_id( $image_url ) {
	global $wpdb;
	$image_url = preg_replace("/^http:/i", "https:", $image_url);
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
	if ( ! empty($attachment ) && ! empty($attachment[0] ) )
	    return $attachment[0];
	$image_url = preg_replace("/^https:/i", "http:", $image_url);
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
	if ( ! empty($attachment ) )
	    return $attachment[0];
	return array();
}
?>
