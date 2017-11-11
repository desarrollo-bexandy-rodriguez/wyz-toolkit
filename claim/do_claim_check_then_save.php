<?php
// Validate extra Data First
$is_there_an_error = false;
$new_claim_cpt_saved = false;


function wyz_current_user_affords_claim_submission() {

		$user_id = get_current_user_id();
		$points_available = get_user_meta( $user_id, 'points_available', true );
		if ( '' == $points_available ) {
			$points_available = 0;
		} else {
			$points_available = intval( $points_available );
		}
		$registery_price = get_option( 'wyz_claim_submission_price' );
		if ( '' == $registery_price ) {
			$registery_price = 0;
		} else {
			$registery_price = intval( $registery_price );
			if ( $registery_price < 0 ){
				$registery_price = 0;
			}
		}
		return $points_available >= $registery_price;
	}

 $wyz_claim_registration_form_data = get_option('wyz_claim_registration_form_data');
        
        
 
        if (isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response'])) {
            echo esc_html__('Please Verify  Recaptcha', 'wyzi-business-finder');
            $is_there_an_error = true;
        }
        if (isset($_FILES['wyzi_claim_fields'])) {
            $attacment_files = $_FILES['wyzi_claim_fields'];
            if (!empty($attacment_files) && is_array($attacment_files)) {
                foreach ($attacment_files['name'] as $key => $value) {
                    $file_type = array();
                    foreach ($wyz_claim_registration_form_data[$key]['fileType'] as $key1 => $value1) {
                        if ($value1['selected']) {
                            array_push($file_type, $value1['value']);
                        }
                    }
                    foreach ($attacment_files['type'][$key] as $file_key => $file_value) {
                        if(!empty($attacment_files['name'][$key][$file_key])){
                            if (!in_array($file_value, $file_type)) {
                                echo esc_html__('Please Upload a Valid File', 'wyzi-business-finder');
                                $is_there_an_error = true;
                            }
                        }
                    }
                    foreach ($attacment_files['size'][$key] as $file_size_key => $file_size_value) {
                        if(!empty($wyz_claim_registration_form_data[$key]['fileSize'])){
                            if ($file_size_value > $wyz_claim_registration_form_data[$key]['fileSize']) {
                                echo esc_html__('File upload limit exceeded', 'wyzi-business-finder');
                                $is_there_an_error = true;
                            }
                        } 
                    }
                }
            }
        }
// Check points 
 if (isset($_POST['wyzi_claim_fields']) && !$is_there_an_error ) {
if ('on' === get_option( 'wyz_claim_should_be_business_owner' ) ) {
        	$registery_price = get_option( 'wyz_claim_submission_price' );
        	if (wyz_current_user_affords_claim_submission()) {
        	$points_available = get_user_meta( get_current_user_id(), 'points_available', true );
        	
			if ( '' == $registery_price ) {
			$registery_price = 0;
			} else {
			$registery_price = intval( $registery_price );
			if ( $registery_price < 0 ){
				$registery_price = 0;
			}
		}
        	$points_available -= $registery_price;
        	update_user_meta (get_current_user_id(), 'points_available',$points_available);
        	}else {
        	echo esc_html__('You do not have enough Points, you need ' . $registery_price . ' points to claim', 'wyzi-business-finder');
            	$is_there_an_error = true;
        	}
        
        }
        }
 
// Save Data as a new Claim CPT
 if (isset($_POST['wyzi_claim_fields']) && !$is_there_an_error ) { 

            if (isset($_FILES['wyzi_claim_fields'])) {
                $attacment_files = $_FILES['wyzi_claim_fields'];
                $files = array();
                $count = 0;
                if (!empty($attacment_files) && is_array($attacment_files)) {
                    foreach ($attacment_files['name'] as $key => $attacment) {
                        foreach ($attacment as $key_attacment => $value_attacment) {
                            $files[$count]['name'] = $value_attacment;
                            $files[$count]['type'] = $attacment_files['type'][$key][$key_attacment];
                            $files[$count]['tmp_name'] = $attacment_files['tmp_name'][$key][$key_attacment];
                            $files[$count]['error'] = $attacment_files['error'][$key][$key_attacment];
                            $files[$count]['size'] = $attacment_files['size'][$key][$key_attacment];
                            $files[$count]['field_key'] = $key;
                            $count++;
                        }
                    }
                }
                $upload_dir = wp_upload_dir();
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                if (!function_exists('wp_handle_upload')) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                foreach ($files as $file) {
                    $uploadedfile = $file;
                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile && !isset($movefile['error'])) {
                        $filename = $movefile['file'];
                        $filetype = wp_check_filetype($filename, null);
                        $attachment = array(
                            'post_mime_type' => $filetype['type'],
                            'post_title' => $file['name'],
                            'post_content' => '',
                            'post_status' => 'inherit',
                            'guid' => $movefile['url']
                        );
                        $attach_id = wp_insert_attachment($attachment, $movefile['file']);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $_POST['wyzi_claim_fields'][$file['field_key']]['value'][] = $attach_id;
                    }
                }
            }
            $wyzi_claim_fields = $_POST['wyzi_claim_fields'];
            if ( is_user_logged_in() ) {
            $user_data = get_userdata(get_current_user_id());
            $user_name = $user_data->user_login;
            $user_email = $user_data->user_email;
            } else {
            $user_data = 0;
            $user_name = "Unknown";
            $user_email = "Unknown";
            }


            $my_post = array(
                'post_title' => $user_name,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'wyzi_claimrequest'
            );


            $claim_application_post_id = wp_insert_post($my_post);
            update_post_meta($claim_application_post_id, 'user_id', $user_data);
            update_post_meta($claim_application_post_id, 'username', $user_name);
            update_post_meta($claim_application_post_id, 'email', $user_email);
            update_post_meta($claim_application_post_id, 'wyzi_claim_fields', $wyzi_claim_fields);
            $new_claim_cpt_saved = true;
            WyzHelpers::wyz_mail(get_bloginfo('admin_email'),'New Claim Submission','Hello Admin <br> You have received a new Cliam on your website.', 'new_claim' );
            echo esc_html__('Thank you, we will review your claim and answer you as soon as possible','wyzi-business-finder');

}

?>