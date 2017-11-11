<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wp.timersys.com
 * @since      1.0.0
 *
 * @package    Facebook_Login
 * @subpackage Facebook_Login/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Facebook_Login
 * @subpackage Facebook_Login/admin
 * @author     Damian Logghe <info@timersys.com>
 */
class WYZFacebook_Login_Admin {
	/**
	 * @var     string  $views    location of admin views
	 */
	protected $views;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->views = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views' );
	}

	/**
	 *
	 * Register and enqueue scripts.
	 *
	 * @since     1.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function admin_scripts() {

		global $pagenow;
		if (  ( isset($_GET['page']) && 'facebook_login' == $_GET['page']  ) || $pagenow == 'profile.php' ) {

			wp_enqueue_script( 'fbl-public-js', plugins_url( 'public/js/facebook-login.js', dirname( __FILE__ ) ) , '', $this->version );
			wp_localize_script( 'fbl-public-js', 'fbl', apply_filters( 'fbl/js_vars', array(
				'ajaxurl'      => admin_url('admin-ajax.php'),
				'site_url'     => home_url(),
				'scopes'       => 'email,public_profile',
				'l18n'         => array(
					'chrome_ios_alert'      => __( 'Please login into facebook and then click connect button again', 'fbl' ),
				)
			)));
		}
	}

}
