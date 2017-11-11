<?php
/**
 * Contains code to manage schedualed event of deleting offers.
 *
 * @package wyz
 */

add_action( 'pmpro_save_membership_level', 'wyzi_subscription_save_extra_options' );

/**
 * Saves extra Fields in Levels of Paid Memberships Pro Plugin to WordPress Option
 */
 function wyzi_subscription_save_extra_options($savedid) {

  $wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options');

  if ($wyzi_subscription_options === false) {
    $wyzi_subscription_options = array();
  }

if(!empty($_REQUEST['wyzi_sub_can_create_business'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_can_create_business'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_can_create_business'] = false;
}


$wyzi_subscription_options[$savedid]['wyzi_sub_can_create_job'] = !empty($_REQUEST['wyzi_sub_can_create_job']);



$wyzi_subscription_options[$savedid]['wyzi_sub_can_edit_business'] = !empty($_REQUEST['wyzi_sub_can_edit_business']);


if(!empty($_REQUEST['wyzi_sub_show_business_logo'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_show_business_logo'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_show_business_logo'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_map'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_map'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_map'] = false;
}


if(!empty($_REQUEST['wyzi_sub_business_show_description'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_description'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_description'] = false;
}


if(!empty($_REQUEST['wyzi_sub_business_show_opening_hours'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_opening_hours'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_opening_hours'] = false;
}


if(!empty($_REQUEST['wyzi_sub_business_show_contact_information_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_contact_information_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_contact_information_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_phone_1'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_phone_1'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_phone_1'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_phone_2'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_phone_2'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_phone_2'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_address'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_address'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_address'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_email_1'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_email_1'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_email_1'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_email_2'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_email_2'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_email_2'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_website_url'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_website_url'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_website_url'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_social_media'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_social_media'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_social_media'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_business_tags'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_business_tags'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_business_tags'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_social_shares'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_social_shares'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_social_shares'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_wall_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_wall_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_wall_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_photo_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_photo_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_photo_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_message_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_message_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_message_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_products_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_products_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_products_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_offers_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_offers_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_offers_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_show_ratings_tab'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_ratings_tab'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_show_ratings_tab'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_can_apply_vendor'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_apply_vendor'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_apply_vendor'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_can_create_bookings'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_create_bookings'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_create_bookings'] = false;
}

if(!empty($_REQUEST['wyzi_sub_max_businesses'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_max_businesses'] = $_REQUEST['wyzi_sub_max_businesses'];
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_max_businesses'] = false;
}

if(!empty($_REQUEST['wyzi_sub_max_jobs'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_max_jobs'] = $_REQUEST['wyzi_sub_max_jobs'];
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_max_jobs'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_can_custom_fields'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_custom_fields'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_custom_fields'] = false;
}

if(!empty($_REQUEST['wyzi_sub_business_can_create_offers'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_create_offers'] = true;
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_business_can_create_offers'] = false;
}

if(!empty($_REQUEST['wyzi_sub_points_added'])) {
  $wyzi_subscription_options[$savedid]['wyzi_sub_points_added'] = $_REQUEST['wyzi_sub_points_added'];
} else {
  $wyzi_subscription_options[$savedid]['wyzi_sub_points_added'] = 0;
}


  update_option( 'wyzi_pmpro_subscription_options', $wyzi_subscription_options );
 }



 add_action( 'pmpro_after_checkout', 'wyzi_subscription_update_user_priveleges_to_business_owner' );
/**
 * Upon Checkout change User Role to Business owner and add Points
 */
 function wyzi_subscription_update_user_priveleges_to_business_owner ($user_id) {

  if ( current_user_can( 'maage_options' ) )
    return;

  $wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options');

  $u = new WP_User( $user_id );

  $u->remove_role( 'client' );
  $u->add_role( 'business_owner' );

  $points_available = get_user_meta( $user_id, 'points_available', true );
  if ( '' == $points_available ) {
    $points_available = 0;
  } else {
      $points_available = intval( $points_available );
    }

  $membership_level = pmpro_getMembershipLevelForUser($user_id);
 
  $points_available = intval($points_available) + intval($wyzi_subscription_options[$membership_level->id]['wyzi_sub_points_added']);
  update_user_meta ($user_id, 'points_available',$points_available);

  WyzHelpers::add_extra_points( $user_id );

 }
