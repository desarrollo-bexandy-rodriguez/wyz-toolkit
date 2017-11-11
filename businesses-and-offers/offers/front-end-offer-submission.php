<?php
/**
 * Front end offer submission code
 *
 *  @package wyz
 */

/**
 * Displays the 'add new offer' form.
 *
 * @param array $atts shortcode attributes.
 */
function wyz_display_add_new_offer_form( $atts ) {
	$user_id = get_current_user_id();

	if ( wyz_check_for_pending() ) {
		WyzHelpers::wyz_error( sprintf( esc_html__( 'You can\'t add a new %s while having ones pending for review', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ));
		return;
	}

	if ( ! current_user_can( 'manage_options' ) && get_the_author_meta( 'points_available', $user_id ) < get_option( 'wyz_offer_point_price' ) ) {
		WyzHelpers::wyz_error( sprintf( esc_html__( 'You don\'t have enough points credit to publish a new %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ));
		return;
	}

	if ( ! WyzHelpers::wyz_has_business( $user_id ) ) {
		WyzHelpers::wyz_error( esc_html__( 'You don\'t have a business yet', 'wyzi-business-finder' ));
		return;
	}

	// Get CMB2 metabox object.
	$cmb = wyz_frontend_cmb2_get();

	// Get $cmb object_types.
	$post_types = $cmb->prop( 'object_types' );

	// Parse attributes.
	$post_status = ( 'on' == get_option( 'wyz_offer_immediate_publish' ) ? 'publish' : 'pending' );
	$atts = shortcode_atts( array(
		'post_author' => $user_id ? $user_id : 1,
		'post_status' => $post_status,
		'post_type' => reset( $post_types ),
	), $atts, 'offers-form-full-display' );

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
	$output = '';
	if ( 'on' != get_option( 'wyz_hide_points' ) ) {
		$offers_cost = get_option( 'wyz_offer_point_price', 0 );
		$output = WyzHelpers::wyz_info( sprintf( esc_html__( '%s cost: %d points', 'wyzi-business-finder' ), WYZ_OFFERS_CPT, $offers_cost ), true );
	}
	$output .= '<h3 class="single-post-title">' . sprintf( esc_html__( 'New %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</h3>';

	// Get any submission errors.
	if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {

		// If there was an error with the submission, add it to our ouput.
		$output .= WyzHelpers::wyz_error( $error->get_error_message(),true);
	}

	// Get our form.
	if ( function_exists( 'cmb2_get_metabox_form' ) )
		$output .= cmb2_get_metabox_form( $cmb, 'fake-object-id', array( 'save_button' => esc_html__( 'Publish', 'wyzi-business-finder' ) ) );

	return $output;
}

/**
 * Gets the front-end-post-form cmb instance.
 */
function wyz_frontend_cmb2_get() {
	// Use ID of metabox in wyz_register_offers_frontend_meta_boxes.
	$metabox_id = 'wyz_frontend_offers';

	// Post/object ID is not applicable since we're using this form for submission.
	global $draft_id;
	$object_id = ! empty( $draft_id ) ? $draft_id : 'fake-object-id';

	if ( 'fake-object-id' != $object_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
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
function wyz_handle_frontend_new_offer_submission_form() {
	// If no form submission, bail.
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return false;
	}

	if ( wyz_check_for_pending() ) {
		return false;
	}
	$user_id= get_current_user_id();
	$points_available = get_user_meta( $user_id, 'points_available', true );
	if ( '' == $points_available ) {
		$points_available = 0;
	} else {
		$points_available = intval( $points_available );
		if ( $points_available < 0 ){
			$points_available = 0;
		}
	}
	$registery_price = get_option( 'wyz_offer_point_price' );
	if ( '' == $registery_price ) {
		$registery_price = 0;
	} else {
		$registery_price = intval( $registery_price );
		if ( $registery_price < 0 ){
			$registery_price = 0;
		}
	}

	if ( ! current_user_can( 'manage_options' ) && $points_available < $registery_price ) {
		return WyzHelpers::wyz_error( sprintf( esc_html__( 'You don\'t have enough points credit to publish a new %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),true);
	}

	$bus_id = $_POST['wyz_business_id'];
	if ( ! WyzHelpers::user_owns_business( $bus_id, get_current_user_id() ) )
		return WyzHelpers::wyz_error( sprintf( esc_html__( '%s owner verification error', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT ),true);

	// Get CMB2 metabox object.
	$cmb = wyz_frontend_cmb2_get();
	$post_data = array();

	// Get our shortcode attributes and set them as our initial post_data args.
	if ( isset( $_POST['atts'] ) ) {
		foreach ( (array) $_POST['atts'] as $key => $value ) {
			$post_data[ $key ] = wp_filter_nohtml_kses( sanitize_text_field( $value ) );
		}
		unset( $_POST['atts'] );
	}

	// Check security nonce.
	if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'security_fail', esc_html__( 'Security check failed.', 'wyzi-business-finder' ) ) );
	}

	// Check for errors in submitted data.
	require_once( plugin_dir_path( __FILE__ ) . 'error-check.php' );

	if ( '' !== $errors ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', $errors ) );
	}

	// Check title submitted.
	if ( empty( $_POST['wyz_offers_title'] ) ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', sprintf( esc_html__( 'New %s requires a title.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) ) );
	}

	$sanitized_values = $cmb->get_sanitized_values( $_POST );

	// Set our post data arguments.
	$post_data['post_title'] = wp_filter_nohtml_kses( $sanitized_values['wyz_offers_title'] );
	unset( $sanitized_values['wyz_offers_title'] );

	$post_data['post_content'] = $sanitized_values['wyz_offers_description'];

	$post_data['post_author'] = get_current_user_id();
	$post_data['post_type'] = 'wyz_offers';
	$post_data['post_status'] = ( 'on' == get_option( 'wyz_offer_immediate_publish' ) ? 'publish' : 'pending' );

	//if the business os not published yet, offeres are published as pending
	if ( 'publish' != get_post_status( $bus_id ) ) {
		$post_data['post_status'] = 'pending';
	}

	// Create the new post.
	$new_submission_id = wp_insert_post( $post_data, true );

	// If we hit a snag, update the user.
	if ( is_wp_error( $new_submission_id ) ) {
		return $cmb->prop( 'submission_error', $new_submission_id );
	}
	
	update_post_meta( $new_submission_id, 'business_id', $bus_id );

	unset( $post_data['post_type'] );
	unset( $post_data['post_status'] );

	if ( ! empty( $sanitized_values['wyz_offers_category_check'] ) ) {
		wp_set_object_terms( $new_submission_id, $sanitized_values['wyz_offers_category_check'], 'offer-categories' );
	}

	unset( $sanitized_values['wyz_offers_category_check'] );

	//$image_id = wyz_get_image_id( $sanitized_values['wyz_offers_image'] );
	$image_id = $sanitized_values['wyz_offers_image_id'];
	$image_url = $sanitized_values['wyz_offers_image'];
	// Set the offer image.
	if ( $image_id && ! is_wp_error( $image_id ) ) {
		update_post_meta( $new_submission_id, 'wyz_offers_image_id', $image_id );
		update_post_meta( $new_submission_id, 'wyz_offers_image', $image_url );
	}
	unset( $sanitized_values['wyz_offers_image'] );
	unset( $sanitized_values['wyz_offers_image_id'] );

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
	
	$user_id = get_current_user_id();

	// Remove the cost of a post publish from his credit.
	$points_left = get_the_author_meta( 'points_available', $user_id ) - get_option( 'wyz_offer_point_price' );
	update_user_meta( $user_id, 'points_available', $points_left );

	/*
	* Redirect back to the form page with a query variable with the new post ID.
	* This will help double-submissions with browser refreshes
	*/
	$url = '?post_submitted=' . $new_submission_id;

	wp_redirect( esc_url_raw( $url ) );
	exit;
}

/**
 * Check if current user has any pending offers.
 */
function wyz_check_for_pending() {
	$query = new WP_Query( array(
		'post_type' => 'wyz_offers',
		'posts_per_page' => '-1',
		'post_status' => 'pending',
		'author' => get_current_user_id(),
	) );

	return $query->have_posts();
}

/**
 * Get image ID from url.
 *
 * @param string $image_url url of the image.
 */
function wyz_get_image_id( $image_url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
	return $attachment[0];
}
?>
