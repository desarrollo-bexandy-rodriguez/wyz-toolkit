<?php
/**
 * Contains code to manage schedualed event of deleting offers.
 *
 * @package wyz
 */

 add_action( 'pmpro_membership_level_after_other_settings', 'wyzi_subscription_add_extra_options' );
 /**
 * Adds extra Fields in Levels of Paid Memberships Pro Plugin
 */
 function wyzi_subscription_add_extra_options() {

   if( isset ( $_REQUEST['edit'] ) ) {
     $edit = intval( $_REQUEST['edit'] ); }
 	else {
 		$edit = false;
  }
  $all_options = get_option( 'wyzi_pmpro_subscription_options' );
  ?>
  <h3 class="topborder"><?php esc_html_e('Business Settings', 'wyzi-business-finder');?></h3>
  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Create Business', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_can_create_business" name="wyzi_sub_can_create_business" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_can_create_business']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_can_create_business"><?php esc_html_e( 'Check to allow subscriber to create basic business. This option need to be on for any of the following capabilities to have meaning.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Edit Business', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_can_edit_business" name="wyzi_sub_can_edit_business" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_can_edit_business']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_can_edit_business"><?php esc_html_e( 'Check to allow subscriber to edit his businesses from the front end' , 'wyzi-business-finder' );?></label></td>
      </tr>


      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Business Logo', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_show_business_logo" name="wyzi_sub_show_business_logo" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_show_business_logo']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_show_business_logo"><?php esc_html_e( 'Check to allow Business to show Logo in Single Business Page.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Business Map', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_map" name="wyzi_sub_business_show_map" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_map']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_map"><?php esc_html_e( 'Check to allow map to appear on Single Business Pages.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Business About', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_description" name="wyzi_sub_business_show_description" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_description']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_description"><?php esc_html_e( 'Check to allow Business description to appear in Business Sidebar.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Opening Hours', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_opening_hours" name="wyzi_sub_business_show_opening_hours" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_opening_hours']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_opening_hours"><?php esc_html_e( 'Check to allow Business Opening Hours to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Contact Information Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_contact_information_tab" name="wyzi_sub_business_show_contact_information_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_contact_information_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_contact_information_tab"><?php esc_html_e( 'Check to allow Business Contact Information Tab. You can manage each sub-contact information below. If off, all contact information will not show ( Phone 1, Phone 2, Email, Address, Website ..) even if turned on' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Phone 1', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_phone_1" name="wyzi_sub_business_show_phone_1" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_phone_1']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_phone_1"><?php esc_html_e( 'Check to allow Business first phone number to appear in contact information.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Phone 2', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_phone_2" name="wyzi_sub_business_show_phone_2" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_phone_2']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_phone_2"><?php esc_html_e( 'Check to allow Business second phone number to appear. Requires first Phone.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Address', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_address" name="wyzi_sub_business_show_address" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_address']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_address"><?php esc_html_e( 'Check to allow Business address to appear in contact information.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Email 1', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_email_1" name="wyzi_sub_business_show_email_1" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_email_1']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_email_1"><?php esc_html_e( 'Check to allow Business first Email to appear in contact information.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Email 2', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_email_2" name="wyzi_sub_business_show_email_2" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_email_2']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_email_2"><?php esc_html_e( 'Check to allow Business second Email to appear in contact information. Requires first Email.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Website', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_website_url" name="wyzi_sub_business_show_website_url" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_website_url']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_website_url"><?php esc_html_e( 'Check to allow Business Website URL to appear in contact information.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Social Media', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_social_media" name="wyzi_sub_business_show_social_media" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_social_media']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_social_media"><?php esc_html_e( 'Check to allow Business Social Media Links to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Tags', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_business_tags" name="wyzi_sub_business_show_business_tags" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_business_tags']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_business_tags"><?php esc_html_e( 'Check to allow Business Tags Links to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Social Shares', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_social_shares" name="wyzi_sub_business_show_social_shares" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_social_shares']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_social_shares"><?php esc_html_e( 'Check to allow Business Share Links to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Wall Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_wall_tab" name="wyzi_sub_business_show_wall_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_wall_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_wall_tab"><?php esc_html_e( 'Check to allow Business Wall Tab to appear for Business to post statuses.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Photo Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_photo_tab" name="wyzi_sub_business_show_photo_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_photo_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_photo_tab"><?php esc_html_e( 'Check to allow Business Photo Tab to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Message Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_message_tab" name="wyzi_sub_business_show_message_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_message_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_message_tab"><?php esc_html_e( 'Check to allow Business Message Tab to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Products Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_products_tab" name="wyzi_sub_business_show_products_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_products_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_products_tab"><?php esc_html_e( 'Check to allow Business Products Tab to appear if Business Owner is a Vendor.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Offers Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_offers_tab" name="wyzi_sub_business_show_offers_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_offers_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_offers_tab"><?php esc_html_e( 'Check to allow Business Offers Tab to appear.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Show Ratings Tab', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_show_ratings_tab" name="wyzi_sub_business_show_ratings_tab" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_show_ratings_tab']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_show_ratings_tab"><?php esc_html_e( 'Check to allow Business Ratings Tab to appear so clients can rate business.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Can become Vendor', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_can_apply_vendor" name="wyzi_sub_business_can_apply_vendor" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_can_apply_vendor']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_can_apply_vendor"><?php esc_html_e( 'Check to allow Business Owner to apply to become a Vendor.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Can create Offers', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_can_create_offers" name="wyzi_sub_business_can_create_offers" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_can_create_offers']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_can_create_offers"><?php esc_html_e( 'Check to allow Business Owner to create Offers.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Can create Custom Listing fields', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_can_custom_fields" name="wyzi_sub_business_can_custom_fields" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_can_custom_fields']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_can_custom_fields"><?php esc_html_e( 'Check to allow Business Owner to display custom forms in his single listing page.' , 'wyzi-business-finder' );?></label></td>
      </tr>

      <tr>
        <th scope="row" valign="top"><label><?php esc_html_e('Can create booking calendar', 'wyzi-business-finder');?>:</label></th>
        <td><input id="wyzi_sub_business_can_create_bookings" name="wyzi_sub_business_can_create_bookings" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif ($all_options[$edit]['wyzi_sub_business_can_create_bookings']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_business_can_create_bookings"><?php esc_html_e( 'Can create a calendar for his businesses for clients to book from' , 'wyzi-business-finder' );?></label></td>
      </tr>


      <tr>
        <th scope="row" valign="top"><label for="wyzi_sub_max_businesses"><?php esc_html_e('Maximum number of allowed businesses','wyzi-business-finder');?>:</label></th>
        <td>
          <?php $limit = intval( get_option( 'wyz_max_allowed_businesses', 1 ) );
          $index = isset( $all_options[$edit]['wyzi_sub_max_businesses'] ) ? $all_options[$edit]['wyzi_sub_max_businesses'] : 1?>
          <select name="wyzi_sub_max_businesses">
          <?php for( $i=0; $i<=$limit; $i++) {
            echo '<option value="' . $i . '" ' . ( $index == $i ? 'selected="selected"' : '' ) . '>' . $i . '</option>';
          }?>
          </select> 
          <br /><small>
            <?php esc_html_e('Maximum number of listings that the business owner can create. To change the upper limit, edit \'Maximum allowed Businesses per user\' in Toolkit Options.', 'wyzi-business-finder');?>
          </small>
        </td>
      </tr>


      <tr>
        <th scope="row" valign="top"><label for="wyzi_sub_max_jobs"><?php esc_html_e('Can create Jobs','wyzi-business-finder');?>:</label></th>
        
          <?php $limit = intval( get_option( 'wyz_max_allowed_businesses', 1 ) );
          $index = isset( $all_options[$edit]['wyzi_sub_max_jobs'] ) ? $all_options[$edit]['wyzi_sub_max_jobs'] : 1;?>
          <td><input id="wyzi_sub_can_create_job" name="wyzi_sub_can_create_job" type="checkbox" value="yes" <?php if ( $edit == -1 ) {} elseif (isset($all_options[$edit]['wyzi_sub_can_create_job'])&&$all_options[$edit]['wyzi_sub_can_create_job']) { ?>checked="checked"<?php } ?> /> <label for="wyzi_sub_can_create_job"><?php esc_html_e( 'Check to allow subscriber to create Jobs.' , 'wyzi-business-finder' );?></label></td>
          <td><input type="number" name="wyzi_sub_max_jobs" min="0" value="<?php echo $index;?>"/><br /><small>
            <?php esc_html_e('Maximum number of Jobs that the business owner can create.', 'wyzi-business-finder');?>
          </small>
          </td>
        </td>
      </tr>


      <tr>
        <th scope="row" valign="top"><label for="wyzi_sub_points_added"><?php esc_html_e('Points','wyzi-business-finder');?>:</label></th>
        <td>
          <input name="wyzi_sub_points_added" type="text" size="20" value="<?php if ( $edit == -1 ) { echo '0'; } else { echo $all_options[$edit]['wyzi_sub_points_added'] ; } ?>" />
          <br /><small>
            <?php _e('Number of Points to be added to User once he subscribes successfully to this Subscription Level.', 'wyzi-business-finder');?>
          </small>
        </td>
      </tr>

    </tbody>
      </table>
<?php 
}



require_once( plugin_dir_path( __FILE__ ) . 'subscription-save-extra-options.php' ); 

