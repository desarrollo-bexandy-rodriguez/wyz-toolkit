<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'FBL_VERSION', '1.1.6');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-facebook-login-activator.php
 */
function wyz_activate_facebook_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-facebook-login-activator.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fbl-upgrader.php';

	WYZFacebook_Login_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-facebook-login-deactivator.php
 */
function wyz_deactivate_facebook_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-facebook-login-deactivator.php';
	WYZFacebook_Login_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wyz_activate_facebook_login' );
register_deactivation_hook( __FILE__, 'wyz_deactivate_facebook_login' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-facebook-login.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wyz_run_facebook_login() {

	$plugin = WYZFacebook_Login::instance();
	$plugin->run();
	return $plugin;
}
$GLOBALS['fbl'] = wyz_run_facebook_login();
