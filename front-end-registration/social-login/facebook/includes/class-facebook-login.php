<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wp.timersys.com
 * @since      1.0.0
 *
 * @package    Facebook_Login
 * @subpackage Facebook_Login/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Facebook_Login
 * @subpackage Facebook_Login/includes
 * @author     Damian Logghe <info@timersys.com>
 */
class WYZFacebook_Login {
	/**
	 * Public class where all hooks are added
	 * @var Facebook_Login_Public   $fbl
	 */
	public $fbl;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Facebook_Login_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * @var array of plugin settings
	 */
	protected $opts;
	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Fbl plugin instance
	 */
	protected static $_instance = null;

	/**
	 * The plugin text domain for translations
	 *
	 * @since    1.1.3
	 * @access   protected
	 * @var      string    $text_domain    The string used to uniquely identify this plugin.
	 */
	protected $text_domain;

	private $shortcodes;

	/**
	 * Main Fbl Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WSI()
	 * @return Fbl - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param mixed $key
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name  = 'facebook-login';
		$this->text_domain  = 'wyzi_business_finder';
		$this->version      = FBL_VERSION;
		$this->opts         = get_option('fbl_settings');

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Facebook_Login_Loader. Orchestrates the hooks of the plugin.
	 * - Facebook_Login_i18n. Defines internationalization functionality.
	 * - Facebook_Login_Admin. Defines all hooks for the admin area.
	 * - Facebook_Login_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-facebook-login-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-facebook-login-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-facebook-login-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-facebook-login-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-facebook-login-public.php';
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fbl-notices.php';

		$this->loader = new WYZFacebook_Login_Loader();
		$this->shortcodes = new WYZFacebook_Login_Shortcodes( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Facebook_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WYZFacebook_Login_i18n();
		$plugin_i18n->set_domain( $this->text_domain );

		//$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WYZFacebook_Login_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->fbl = new WYZFacebook_Login_Public( $this->get_plugin_name(), $this->get_version() );

		if( '' != get_option( 'wyz_fb_app_id' ) ) {
			$this->loader->add_action( 'wp_enqueue_scripts', $this->fbl, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_ajax_fbl_facebook_login', $this->fbl, 'login_or_register_user' );
			$this->loader->add_action( 'wp_ajax_nopriv_fbl_facebook_login', $this->fbl, 'login_or_register_user' );
			$this->loader->add_action( 'facebook_login_button', $this->fbl, 'print_button' );
			$this->loader->add_action( 'facebook_login_button', $this->fbl, 'add_fb_scripts' );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Facebook_Login_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
