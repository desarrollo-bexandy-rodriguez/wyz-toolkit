<?php

require_once BOOKED_PLUGIN_DIR . '/class-tgm-plugin-activation.php';
add_action( 'bookedpa_register', 'booked_register_required_plugins' );

function booked_register_required_plugins(){

	$plugins = array(
		array(
			'name'               => 'Payments with WooCommerce',
			'slug'               => 'booked-woocommerce-payments',
			'source'             => BOOKED_PLUGIN_DIR . '/includes/add-ons/booked-woocommerce-payments.zip',
			'required'           => false,
			'version'            => BOOKED_WC_VERSION,
			'force_activation'   => false,
			'force_deactivation' => true
		),
		array(
			'name'      => 'WooCommerce',
			'slug'      => 'woocommerce',
			'required'  => false,
		),
		array(
			'name'               => 'Calendar Feeds',
			'slug'               => 'booked-calendar-feeds',
			'source'             => BOOKED_PLUGIN_DIR . '/includes/add-ons/booked-calendar-feeds.zip',
			'required'           => false,
			'version'            => BOOKED_CF_VERSION,
			'force_activation'   => false,
			'force_deactivation' => true
		),
		array(
			'name'               => 'Front-End Agents',
			'slug'               => 'booked-frontend-agents',
			'source'             => BOOKED_PLUGIN_DIR . '/includes/add-ons/booked-frontend-agents.zip',
			'required'           => false,
			'version'            => BOOKED_FEA_VERSION,
			'force_activation'   => false,
			'force_deactivation' => true
		),
	);

	$config = array(
		'id'           => 'booked',                		// Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      		// Default absolute path to bundled plugins.
		'menu'         => 'booked-install-addons', 		// Menu slug.
		'parent_slug'  => 'booked-appointments',        // Parent menu slug.
		'capability'   => 'manage_options',    			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    		// Show admin notices or not.
		'dismissable'  => true,                    		// If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      		// If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   		// Automatically activate plugins after installation or not.
		'message'      => '',                      		// Message to output right before the plugins table.
	);

	bookedpa( $plugins, $config );

}