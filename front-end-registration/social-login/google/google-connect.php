<?php

define('NEW_GOOGLE_LOGIN', 1);
if (!defined('NEW_GOOGLE_LOGIN_PLUGIN_BASENAME')) define('NEW_GOOGLE_LOGIN_PLUGIN_BASENAME', plugin_basename(__FILE__));
$new_google_settings = maybe_unserialize(get_option('nextend_google_connect'));

if(!function_exists('nextend_uniqid')){
    function nextend_uniqid(){
        if(isset($_COOKIE['nextend_uniqid'])){
            if(get_site_transient('n_'.$_COOKIE['nextend_uniqid']) !== false){
                return $_COOKIE['nextend_uniqid'];
            }
        }
        $_COOKIE['nextend_uniqid'] = uniqid('nextend', true);
        setcookie('nextend_uniqid', $_COOKIE['nextend_uniqid'], time() + 3600, '/');
        set_site_transient('n_'.$_COOKIE['nextend_uniqid'], 1, 3600);
        
        return $_COOKIE['nextend_uniqid'];
    }
}

/*
Adding query vars for the WP parser
*/

function new_google_add_query_var() {

  global $wp;
  $wp->add_query_var('loginGoogle');
}
add_filter('init', 'new_google_add_query_var');

/* -----------------------------------------------------------------------------
Main function to handle the Sign in/Register/Linking process
----------------------------------------------------------------------------- */

/*
Compatibility for older versions
*/
add_action('parse_request', 'new_google_login_compat');

function new_google_login_compat() {

  global $wp;
  if ($wp->request == 'loginGoogle' || isset($wp->query_vars['loginGoogle'])) {
    new_google_login_action();
  }
}

/*
For login page
*/
add_action('login_init', 'new_google_login');

function new_google_login() {

  if (isset($_REQUEST['loginGoogle']) && $_REQUEST['loginGoogle'] == '1') {
    new_google_login_action();
  }
}

function new_google_login_action() {
  global $wp, $wpdb, $new_google_settings;
  
  include (dirname(__FILE__) . '/sdk/init.php');
  
  if (isset($_GET['code'])) {
    if (isset($new_google_settings['google_redirect']) && $new_google_settings['google_redirect'] != '' && $new_google_settings['google_redirect'] != 'auto') {
      $_GET['redirect'] = $new_google_settings['google_redirect'];
    }
    
    set_site_transient( nextend_uniqid().'_google_r', $_GET['redirect'], 3600);
    
    $client->authenticate();
    $access_token = $client->getAccessToken();
    set_site_transient( nextend_uniqid().'_google_at', $access_token, 3600);
    header('Location: ' . filter_var(new_google_login_url() , FILTER_SANITIZE_URL));
    exit;
  }
  
  $access_token = get_site_transient( nextend_uniqid().'_google_at');

  if ($access_token !== false) {
    $client->setAccessToken($access_token);
  }
  if (isset($_REQUEST['logout'])) {
    delete_site_transient( nextend_uniqid().'_google_at');
    $client->revokeToken();
  }
  if ($client->getAccessToken()) {
    $u = $oauth2->userinfo->get();

    // The access token may have been updated lazily.
    set_site_transient( nextend_uniqid().'_google_at', $client->getAccessToken(), 3600);

    // These fields are currently filtered through the PHP sanitize filters.
    
    // See http://www.php.net/manual/en/filter.filters.sanitize.php

    $email = filter_var($u['email'], FILTER_SANITIZE_EMAIL);

    $c_user = get_user_by( 'email', $email );

    if (!is_user_logged_in()) {
      if (!$c_user) { // Register

          require_once (ABSPATH . WPINC . '/registration.php');
          $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
          $new_google_settings['google_user_prefix'] = 'Google - ';
          $sanitized_user_login = sanitize_user($new_google_settings['google_user_prefix'] . $u['name']);
          if (!validate_username($sanitized_user_login)) {
            $sanitized_user_login = sanitize_user('google' . $user_profile['id']);
          }
          $defaul_user_name = $sanitized_user_login;
          $i = 1;
          while (username_exists($sanitized_user_login)) {
            $sanitized_user_login = $defaul_user_name . $i;
            $i++;
          }
          $ID = wp_create_user($sanitized_user_login, $random_password, $email);
          if (!is_wp_error($ID)) {
            $user_info = get_user_by('id',$ID);
            $user_role = 'client';
            if ( 'business_owner' == get_option( 'wyz_reg_def_user_role' ) ) $user_role = 'business_owner';
            wp_update_user(array(
              'ID' => $ID,
              'display_name' => $u['name'],
              'first_name' => $u['given_name'],
              'last_name' => $u['family_name'],
              'googleplus' => $u['link'],
              'role' => $user_role,
              'user_email' => $email,
            ));
            wp_new_user_notification($ID);
            update_user_meta($ID, 'new_google_default_password', $user_info->user_pass);
            do_action('nextend_google_user_registered', $ID, $u, $oauth2);

            // Give the user initial points value of zero.
              update_user_meta( $ID, 'points_available', 0 );
              update_user_meta( $ID, 'has_business', false );
              update_user_meta( $ID, 'business_id', -1 );

              do_action( 'wyz_after_user_register', $ID );

              $creds = array();
              $creds['user_login'] = $sanitized_user_login;
              $creds['user_password'] = $random_password;
              $creds['remember'] = true;
              $user = wp_signon( $creds, is_ssl() );

          } else {
            return;
          }
        if (isset($new_google_settings['google_redirect_reg']) && $new_google_settings['google_redirect_reg'] != '' && $new_google_settings['google_redirect_reg'] != 'auto') {
          set_site_transient( nextend_uniqid().'_google_r', $new_google_settings['google_redirect_reg'], 3600);
        }
      }
      elseif (!is_wp_error($c_user)) { // Login

        $secure_cookie = is_ssl();
        $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
        global $auth_secure_cookie;

        $auth_secure_cookie = $secure_cookie;
        wp_set_auth_cookie($c_user->ID, true, $secure_cookie);
        wp_set_current_user($c_user->ID, $c_user->user_login);
        update_user_meta($c_user->ID, 'identifier', $u['id']);

        $user_info = get_userdata($ID);
        do_action('wp_login', $c_user->user_login, $c_user);
        do_action('nextend_google_user_logged_in', $ID, $u, $oauth2);
      }
    }
  } else {
    if (isset($new_google_settings['google_redirect']) && $new_google_settings['google_redirect'] != '' && $new_google_settings['google_redirect'] != 'auto') {
      $_GET['redirect'] = $new_google_settings['google_redirect'];
    }
    if (isset($_GET['redirect'])) {
      set_site_transient( nextend_uniqid().'_google_r', $_GET['redirect'], 3600);
    }
    
    $redirect = get_site_transient( nextend_uniqid().'_google_r');
    
    if ($redirect || $redirect == new_google_login_url()) {
      $redirect = site_url();
      set_site_transient( nextend_uniqid().'_google_r', $redirect, 3600);
    }
    header('LOCATION: ' . $client->createAuthUrl());
    exit;
  }
  new_google_redirect();
}


/* -----------------------------------------------------------------------------
Miscellaneous functions
----------------------------------------------------------------------------- */

function new_google_sign_button() {

  global $new_google_settings;
  return '<a href="' . esc_url(new_google_login_url() . (isset($_GET['redirect_to']) ? '&redirect=' . urlencode($_GET['redirect_to']) : '')) . '" rel="nofollow">' . $new_google_settings['google_login_button'] . '</a><br />';
}




function new_google_login_url() {

  return site_url('wp-login.php') . '?loginGoogle=1';
}

function new_google_redirect() {
  
  $redirect = get_site_transient( nextend_uniqid().'_google_r');
  
  if (!$redirect || $redirect == '' || $redirect == new_google_login_url()) {
    if (isset($_GET['redirect'])) {
      $redirect = $_GET['redirect'];
    } else {
      $redirect = site_url();
    }
  }
  $redirect = wp_sanitize_redirect($redirect);
  $redirect = wp_validate_redirect($redirect, site_url());
  header('LOCATION: ' . $redirect);
  delete_site_transient( nextend_uniqid().'_google_r');
  exit;
}

/*
Session notices used in the profile settings
*/

function new_google_admin_notice() {
  $user_info = wp_get_current_user();
  $notice = get_site_transient($user_info->ID.'_new_google_admin_notice');
  if ($notice !== false) {
    echo '<div class="updated">
       <p>' . $notice . '</p>
    </div>';
    delete_site_transient($user_info->ID.'_new_google_admin_notice');
  }
}
add_action('admin_notices', 'new_google_admin_notice');
