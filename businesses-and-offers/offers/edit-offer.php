<?php
/**
 * Called in 'businesses-and-offers.php' file when on 'edit offer' page.
 *
 * @package wyz
 */

/**
 * Displays offer frontend edit form
 *
 * @param array $atts shortcode attributes.
 */
function wyz_do_frontend_offers_edit( $atts ) {
	if ( ! current_user_can( 'manage_options' ) && 'on' != get_option( 'wyz_offer_editable' ) ) {
		return '<h3>' . esc_html__( 'You don\'t have the right to access this page', 'wyzi-business-finder' ) . '</h3>';
	}

	$query = new WP_Query( array(
		'post_type' => 'wyz_offers',
		'posts_per_page' => '-1',
		'post_status' => array( 'publish', 'pending' ),
	) );
	$can_edit = false;

	global $WYZ_USER_ACCOUNT_TYPE;

	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();

			$curr_id = get_the_ID();
			$curr_post = get_post( $curr_id );
			if ( current_user_can( 'manage_options' ) || get_current_user_id() == $curr_post->post_author ) {

				$can_edit = true;
				if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditOffer ) {
					if ( $_GET[ WyzQueryVars::EditOffer ] == $curr_id ) {
						$current_post = $curr_id;

						// Get CMB2 metabox object.
						$cmb = wyz_frontend_cmb2_update_get( $current_post );

						// Get $cmb object_types.
						$post_types = $cmb->prop( 'object_types' );

						// Current user.
						$user_id = get_current_user_id();

						// Parse attributes.
						$atts = shortcode_atts( array(), $atts, 'offers-form-full-display' );

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
						
						$output = '';

						// Get any submission errors.
						if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
							// If there was an error with the submission, add it to our ouput.
							$output .= WyzHelpers::wyz_error( $error->get_error_message(), true );
						}

						// Get our form.
						if ( function_exists( 'cmb2_get_metabox_form' ) )
							$output .= cmb2_get_metabox_form( $cmb, $current_post, array( 'save_button' => esc_html__( 'Update Post', 'wyzi-business-finder' ) ) );
					}
				}
			}
		endwhile;
		wp_reset_postdata();
	endif;

	wp_reset_postdata();

	if ( ! $can_edit ) {
		return WyzHelpers::wyz_error( esc_html__( 'you don\'t have the appropriate permissions to edit this post', 'wyzi-business-finder' ), true );
	}
	if ( ! isset( $current_post ) ) {
		return WyzHelpers::wyz_info( esc_html__( 'No', 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT . ' ' . esc_html__( 'to display', 'wyzi-business-finder' ), true );
	}
	return $output;
}

/**
 * Get frontend offer edit cmb form.
 *
 * @param integer $id offer id.
 */
function wyz_frontend_cmb2_update_get( $id ) {
	$metabox_id = 'wyz_frontend_offers';
	// Get CMB2 metabox object.
	if ( function_exists( 'cmb2_get_metabox' ) )
		return cmb2_get_metabox( $metabox_id, $id );
}

/**
 * Get frontend offer edit cmb form.
 */
function wyz_handle_frontend_offer_update_form() {

	// If no form submission, bail.
	if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return false;
	}

	// Get CMB2 metabox object.
	$cmb = wyz_frontend_cmb2_get( $_GET[ WyzQueryVars::EditOffer ] );
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

	if ( '' != $errors ) {
		return $cmb->prop( 'submission_error', new WP_Error( 'post_data_missing', $errors ) );
	}

	$sanitized_values = $cmb->get_sanitized_values( $_POST );

	// Set our post data arguments.
	$post_data['post_title'] = wp_filter_nohtml_kses( $sanitized_values['wyz_offers_title'] );

	$post_data['post_content'] = $sanitized_values['wyz_offers_description'];

	$post_data['ID'] = $_GET[ WyzQueryVars::EditOffer ];

	// Create the new post.
	$new_submission_id = wp_update_post( $post_data, true );

	// If we hit a snag, update the user.
	if ( is_wp_error( $new_submission_id ) ) {
		return $cmb->prop( 'submission_error', $new_submission_id );
	}

	unset( $post_data['post_type'] );
	unset( $post_data['post_status'] );

	// Try to upload the featured image.
	//$image_id = wyz_get_image_id( $sanitized_values['wyz_offers_image'] );
	$image_id = $sanitized_values['wyz_offers_image_id'];
	$image_url = $sanitized_values['wyz_offers_image'];
	if ( $image_id && ! is_wp_error( $image_id ) ) {
		update_post_meta( $new_submission_id, 'wyz_offers_image_id', $image_id );
		update_post_meta( $new_submission_id, 'wyz_offers_image', $image_url );
	}
	unset( $sanitized_values['wyz_offers_image'] );
	unset( $sanitized_values['wyz_offers_image_id'] );
	
	wp_set_object_terms( $new_submission_id, wp_filter_nohtml_kses( $sanitized_values['wyz_offers_category_check'] ), 'offer-categories' );

	unset( $sanitized_values['wyz_offers_category_check'] );

	// Loop through remaining (sanitized) data, and save to post-meta.
	foreach ( $sanitized_values as $key => $value ) {
		if ( 'wyz_offers_title' !== $key && 'wyz_offers_category_check' !== $key ) {
			if ( is_array( $value ) ) {
				$value = array_filter( $value );
				if ( ! empty( $value ) ) {
					update_post_meta( $new_submission_id, $key, $value );
				}
			} else {
				update_post_meta( $new_submission_id, $key, $value );
			}
		}
	}

	/*
	* Redirect back to the form page with a query variable with the new post ID.
	* This will help double-submissions with browser refreshes
	*/
	$url = '?offer_updated=' . $new_submission_id;
	wp_redirect( esc_url_raw( $url ) );
	exit;
}

/**
 * Get cmb2 metaboxes
 */
function wyz_frontend_cmb2_get() {
	// Use ID of metabox in wyz_register_offers_frontend_meta_boxes.
	$metabox_id = 'wyz_frontend_offers';

	// Post/object ID is not applicable since we're using this form for submission.
	$object_id = 'fake-object-id';

	// Get CMB2 metabox object.
	if ( function_exists( 'cmb2_get_metabox_form' ) )
		return cmb2_get_metabox( $metabox_id, $object_id );
}

/**
 * Get image id from image url
 *
 * @param string $image_url the image url.
 */
function wyz_get_image_id( $image_url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
	return $attachment[0];
}
?>
