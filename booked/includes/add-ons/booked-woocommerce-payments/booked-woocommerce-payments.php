<?php
// Include the required class for plugin updates.
require_once('updates/plugin-update-checker.php');
$BookedWC_BoxyUpdateChecker = PucFactory::buildUpdateChecker('http://boxyupdates.com/get/?action=get_metadata&slug=booked-woocommerce-payments', __FILE__, 'booked-woocommerce-payments');

if ( class_exists('Booked_WC') ) {
	return;
}

// Global constants
define('BOOKED_WC_PLUGIN_PREFIX', 'booked_wc_');
define('BOOKED_WC_POST_TYPE', 'booked_appointments');
define('BOOKED_WC_TAX_CALENDAR', 'booked_custom_calendars');
define('BOOKED_WC_APPOINTMENTS_PAGE', 'booked-appointments');
define('BOOKED_WC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BOOKED_WC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('BOOKED_WC_PLUGIN_AJAX_URL', admin_url('admin-ajax.php'));

// Plugin WooCommerce Libraries
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-prevent-purchasing.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-meta-box-product.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-product.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-variation.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-order.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-order-item.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-cart.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-wc-helper.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/woocommerce/class-woocommerce.php');

// Default Plugin Libraries
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-settings.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-wp-cron.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-post-status.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-fragments.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-admin-notices.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-enqueue-scripts.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-wp-ajax.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-json-response.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-custom-fields.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-static-functions.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-appointment.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-appointment-payment-status.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/class-cleanup.php');
require_once(BOOKED_WC_PLUGIN_DIR . 'lib/core.php');

// setup the plugin
add_action('init',  array('Booked_WC', 'setup'));

// Localization
function bookedwc_local_init(){
	$locale = apply_filters('plugin_locale', get_locale(), 'booked-woocommerce-payments');
	load_textdomain('booked-woocommerce-payments', WP_LANG_DIR.'/plugins/booked-woocommerce-payments-'.$locale.'.mo');
	load_plugin_textdomain('booked-woocommerce-payments', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
}
//add_action('after_setup_theme', 'bookedwc_local_init');