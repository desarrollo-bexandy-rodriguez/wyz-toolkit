<?php

define('BOOKEDFEA_PLUGIN_DIR', dirname(__FILE__));
define('BOOKEDFEA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the required class for plugin updates.
require_once('updates/plugin-update-checker.php');
$BookedFEA_BoxyUpdateChecker = PucFactory::buildUpdateChecker('http://boxyupdates.com/get/?action=get_metadata&slug=booked-frontend-agents', __FILE__, 'booked-frontend-agents');

// Is Booked installed and active?

	if(!class_exists('BookedFEA_Plugin')) {
		class BookedFEA_Plugin {
			
			public function __construct() {
				
				add_action('init', array(&$this, 'booked_fea_init') );
				add_action('wp_enqueue_scripts', array(&$this, 'front_end_styles'));
				add_action('wp_enqueue_scripts', array(&$this, 'front_end_scripts'));
				
				require_once(sprintf("%s/includes/functions.php", BOOKEDFEA_PLUGIN_DIR));	
				require_once(sprintf("%s/includes/shortcodes.php", BOOKEDFEA_PLUGIN_DIR));			
				require_once(sprintf("%s/includes/ajax.php", BOOKEDFEA_PLUGIN_DIR));
				
				$bookedfea_ajax = new BookedFEA_Ajax();
			
			}
				
			public function booked_fea_init(){
				
				if (is_user_logged_in() && current_user_can('edit_booked_appointments')):
				
					add_filter('booked_profile_tab_content', array(&$this, 'booked_fea_tabs'),1);
					add_filter('booked_profile_tabs',array(&$this, 'booked_fea_tabs'),1);
				
				endif;
				
			}
			
			public static function front_end_styles() {
				wp_enqueue_style('booked-fea-styles', BOOKEDFEA_PLUGIN_URL . '/css/styles.css', array(), BOOKED_VERSION);
			}
			
			public static function front_end_scripts() {
				wp_register_script('booked-fea-js', BOOKEDFEA_PLUGIN_URL . '/js/functions.js', array(), BOOKED_VERSION, true);
				$booked_fea_vars = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'i18n_confirm_appt_delete' => __('Are you sure you want to cancel this appointment?','wyzi-business-finder'),
					'i18n_confirm_appt_approve' => __('Are you sure you want to approve this appointment?','wyzi-business-finder')
				);
				wp_localize_script( 'booked-fea-js', 'booked_fea_vars', $booked_fea_vars );
				wp_enqueue_script('booked-fea-js');
			}
			
			public function booked_fea_tabs($custom_tabs){
				
				$custom_tabs = array(
					'fea_appointments' => array(
						'title' => __('Upcoming Appointments','wyzi-business-finder'),
						'booked-icon' => 'booked-icon-calendar',
						'class' => false
					),
					'fea_pending' => array(
						'title' => __('Pending Appointments','wyzi-business-finder') . '<div class="counter"></div>',
						'booked-icon' => 'booked-icon-clock',
						'class' => false
					),
					'fea_history' => array(
						'title' => __('Appointment History','wyzi-business-finder'),
						'booked-icon' => 'booked-icon-calendar',
						'class' => false
					),
					'edit' => array(
						'title' => __('Edit Profile','wyzi-business-finder'),
						'booked-icon' => 'booked-icon-pencil',
						'class' => 'edit-button'
					)
				);
				
				return $custom_tabs;
				
			}

		}
	}
	
	add_action('plugins_loaded','init_bookedfea');
	
	
function init_bookedfea(){
	if(class_exists('BookedFEA_Plugin')) {
	
		$bookedfea_plugin = new BookedFEA_Plugin();
	
	}
}

function bookedfea_required_plugins_notice() {
			
	echo '<div class="update-nag">';
		echo sprintf( __('In order to use the %s plugin, you need to have %s installed and active.', 'wyzi-business-finder'), '<strong>Booked Front-End Agents</strong>', '<strong><a href="http://codecanyon.net/item/booked-appointment-booking-for-wordpress/9466968/?ref=boxystudio" target="_blank">Booked</a></strong>' );
	echo '</div>';
	
}

// Localization
function booked_fea_local_init(){
	$domain = 'booked-frontend-agents';
	$locale = apply_filters('plugin_locale', get_locale(), $domain);
	load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$domain.'/'.$domain.'-'.$locale.'.mo');
	load_plugin_textdomain($domain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
}
//add_action('after_setup_theme', 'booked_fea_local_init');