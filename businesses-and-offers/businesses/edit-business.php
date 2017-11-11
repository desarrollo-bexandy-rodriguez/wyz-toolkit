<?php
/**
 * Called in 'businesses-and-offers.php' file when on 'edit business' page.
 *
 * @package wyz
 */

/**
 * Displays business frontend edit form
 *
 * @param array $atts shortcode attributes.
 */
function wyz_do_frontend_business_edit( $atts ) {
	$user_id = get_current_user_id();
	if( ( 'off' == get_option( 'wyz_allow_business_edit' ) || ! WyzHelpers::wyz_sub_can_bus_owner_do( $user_id , 'wyzi_sub_can_edit_business' ) ) && ! current_user_can( 'administrator' ) ) {
		return WyzHelpers::wyz_error( esc_html__( "You don't have the appropriate permissions to edit this post", 'wyzi-business-finder' ), true );
	}
	$id = $_GET['edit-business'];
	$query = new WP_Query( array(
		'post_type' => 'wyz_business',
		'posts_per_page' => '1',
		'post_status' => array( 'publish', 'pending' ),
		'author' => $user_id,
		'p' => $id,
	) );
	$can_edit = false;

	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();

			$curr_id = get_the_ID();
			$curr_post = get_post( $curr_id );

			$can_edit = true;
			if ( $_GET['edit-business'] == $curr_id ) {

				$current_post = $curr_id;

				// Get CMB2 metabox object.
				$cmb = wyz_frontend_business_cmb2_update_get( $current_post );

				// Get $cmb object_types.
				$post_types = $cmb->prop( 'object_types' );

				// Parse attributes.
				$atts = shortcode_atts( array(), $atts, 'business-data-display' );

				foreach ( $atts as $key => $value ) {
					$cmb->add_hidden_field( array(
						'field_args' => array(
							'id' => "atts[$key]",
							'type' => 'hidden',
							'default_cb' => $value,
						),
					) );
				}

				$cmb->add_hidden_field( array(
					'field_args' => array(
						'id' => "post_status",
						'type' => 'hidden',
						'default' => get_post_status( $curr_id ),
					),
				) );

				// Initiate our output variable.
				$output = '';//'<div class="section-title col-xs-12 margin-bottom-50"><h1>' . esc_html__( 'My', 'wyzi-business-finder' ) . ' ' . WYZ_BUSINESS_CPT . '</h1></div>';

				/*$prog_bar_pg_1_ttl = apply_filters( 'prog_bar_pg_1_ttl', esc_html__( 'Title and Description', 'wyzi-business-finder' ) );
				$prog_bar_pg_2_ttl = apply_filters( 'prog_bar_pg_2_ttl', esc_html__( 'Open/Close Times', 'wyzi-business-finder' ) );
				$prog_bar_pg_3_ttl = apply_filters( 'prog_bar_pg_3_ttl', esc_html__( 'Address and Contact', 'wyzi-business-finder' ) );
				$prog_bar_pg_4_ttl = apply_filters( 'prog_bar_pg_4_ttl', esc_html__( 'Extra Fields', 'wyzi-business-finder' ) );*/


				//progress bar
				$output .= wyz_get_business_form_header();

				// Get any submission errors.
				if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {

					// If there was an error with the submission, add it to our ouput.
					$output .= WyzHelpers::wyz_error( $error->get_error_message(), true );
				}

				// Get our form.
				if ( function_exists( 'cmb2_get_metabox_form' ) )
					$output .= '<div class="business-details-form col-md-12 col-xs-12">' . cmb2_get_metabox_form( $cmb, $current_post, array( 'save_button' => esc_html__( 'Update Business', 'wyzi-business-finder' ) ) ) . '</div>';

				break;
			}
		endwhile;
	endif;

	wp_reset_postdata();

	if ( ! $can_edit ) {
		return WyzHelpers::wyz_error( esc_html__( 'You don\'t have the appropriate permissions to edit this post', 'wyzi-business-finder' ), true );
	}
	if ( ! isset( $current_post ) ) {
		return WyzHelpers::wyz_error( esc_html__( 'An error occured, no business info to display', 'wyzi-business-finder' ), true );
	}

	return $output;
}

/**
 * Get frontend business edit cmb form.
 *
 * @param integer $id business id.
 */
function wyz_frontend_business_cmb2_update_get( $id ) {
	// Use ID of metabox in wyz_register_offers_frontend_meta_boxes.
	$metabox_id = 'wyz_frontend_businesses';

	// Get CMB2 metabox object.
	if ( function_exists( 'cmb2_get_metabox' ) )
		return cmb2_get_metabox( $metabox_id, $id );
}

/**
 * Get frontend business edit cmb form.
 */
function wyz_handle_frontend_business_update_form() {
	// If no form submission, bail.
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'] ) || ! isset( $_POST['object_id'] ) ) {
		return false;
	}

	// Get CMB2 metabox object.
	$cmb = wyz_frontend_business_cmb2_get( $_GET['edit-business'] );
	$post_data = array();


	// Get our shortcode attributes and set them as our initial post_data args.
	if ( isset( $_POST['atts'] ) ) {
		$_POST['atts']['post_status'] = $_POST['post_status'];
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

	$sanitized_values = $cmb->get_sanitized_values( $_POST );

	$post_data['post_title'] = wp_filter_nohtml_kses( $sanitized_values['wyz_business_name'] );
	$post_data['post_name'] = wp_filter_nohtml_kses( $sanitized_values['wyz_business_name'] );

	$post_data['ID'] = $_GET['edit-business'];

	$post_data['post_content'] = $sanitized_values['wyz_business_description'];

	// Create the new post.
	$new_submission_id = wp_update_post( $post_data, true );

	// If we hit a snag, update the user.
	if ( is_wp_error( $new_submission_id ) ) {
		return $cmb->prop( 'submission_error', $new_submission_id );
	}

	// Check if business has tags.
	if ( isset( $_POST['wyz_business_tags'] ) && '' !== $_POST['wyz_business_tags'] && ! empty( $_POST['wyz_business_tags'] ) ) {
		wp_set_object_terms( $new_submission_id, $_POST['wyz_business_tags'], 'wyz_business_tag', false );
	}

	unset( $post_data['post_type'] );
	unset( $post_data['post_status'] );
	unset( $sanitized_values['wyz_business_name'] );

	// Try to upload the featured image.
	$image_id = wyz_get_image_id( wp_filter_nohtml_kses( $sanitized_values['wyz_business_logo'] ) );

	// Set the featured image.
	if ( $image_id && ! is_wp_error( $image_id ) ) {
		set_post_thumbnail( $new_submission_id, $image_id );
	}
	update_post_meta( $new_submission_id, $sanitized_values['wyz_business_logo'], '' );
	if ( isset( $_POST['wyz_business_categories'] ) && '' !== $_POST['wyz_business_categories'] && ! empty( $_POST['wyz_business_categories'] ) ) {
		wp_set_object_terms( $new_submission_id, $_POST['wyz_business_categories'], 'wyz_business_category' );
		unset( $_POST['wyz_business_categories'] );
	}

	if ( isset( $_POST['wyz_business_category_icon'] ) ) {
		update_post_meta( $new_submission_id, 'wyz_business_category_icon', $_POST['wyz_business_category_icon'] );
		unset( $_POST['wyz_business_category_icon'] );
	}

	$optional_fields = array( 'wyz_business_open_monday', 'wyz_business_open_tuesday', 'wy_business_open_wednesday', 'wyz_business_open_thursday', 'wyz_business_open_friday', 'wyz_business_open_saturday', 'wyz_business_open_sunday', 'wyz_business_close_monday', 'wyz_business_close_tuesday', 'wy_business_close_wednesday', 'wyz_business_close_thursday', 'wyz_business_close_friday', 'wyz_business_close_saturday', 'wyz_business_close_sunday', 'wyz_business_addition_address_line', 'wyz_business_phone2', 'wyz_business_email2', 'wyz_business_website', 'wyz_business_facebook_id', 'wyz_business_twitter_id', 'wyz_business_linkedin_id', 'wyz_business_google_plus_id', 'wyz_business_facebook', 'wyz_business_twitter', 'wyz_business_linkedin', 'wyz_business_google_plus', 'wyz_business_youtube', 'wyz_business_instagram', 'wyz_business_flicker', 'wyz_business_pinterest' );

	foreach ( $optional_fields as $optional_field ) {
		if ( ! isset( $sanitized_values[ $optional_field ] ) ) {
			update_post_meta( $new_submission_id, $optional_field, '' );
		}
	}

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

	/*
	* Redirect back to the form page with a query variable with the new post ID.
	* This will help double-submissions with browser refreshes
	*/
	$url = '?business_updated=' . $new_submission_id;
	wp_redirect( esc_url_raw( $url ) );
	exit;
}

/**
 * Get cmb2 metaboxes
 */
function wyz_frontend_business_cmb2_get() {
	// Use ID of metabox in wyz_register_offers_frontend_meta_boxes.
	$metabox_id = 'wyz_frontend_businesses';

	// Post/object ID is not applicable since we're using this form for submission.
	$object_id = 'fake-object-id';

	// Get CMB2 metabox object.
	if ( function_exists( 'cmb2_get_metabox' ) )
		return cmb2_get_metabox( $metabox_id, $object_id );
}

/**
 * Get image id from image url
 *
 * @param string $image_url the image url.
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
