<?php

/*
 * Reference : http://codex.wordpress.org/Settings_API
 * Reference : http://codex.wordpress.org/Function_Reference/add_settings_field
 */

add_filter('booked_admin_booked_screens', array('Booked_WC_Settings_Hooks', 'booked_admin_booked_screens'), 10, 1);

class Booked_WC_Settings {

	private function __construct() {

	}

	protected function _setup() {
		add_action('admin_menu', array($this, 'add_admin_menu_page') );
		add_action('admin_init', array('Booked_WC_Settings_Fields', 'register') );
	}

	public static function setup() {
		$settings = new self();
		$settings->_setup();
	}

	public function add_admin_menu_page () {
		add_submenu_page(
			BOOKED_WC_APPOINTMENTS_PAGE,
			__('WooCommerce', 'wyzi-business-finder'),
			__('WooCommerce', 'wyzi-business-finder'),
			'manage_booked_options',
			BOOKED_WC_PLUGIN_PREFIX . 'payment_options',
			array($this, 'render_payment_options_page')
		);
	}

	public function render_payment_options_page() {
		Booked_WC_Fragments::load('settings', 'payment-options');
	}

	public static function get_option($option_name) {
		$options = get_option(BOOKED_WC_PLUGIN_PREFIX . 'payment_options');

		if ( isset($options[$option_name]) ) {
			return $options[$option_name];
		}
	}
}

class Booked_WC_Settings_Hooks {

	public static function booked_admin_booked_screens( $screens ) {
		$screens[] = BOOKED_WC_PLUGIN_PREFIX . 'payment_options';

		return $screens;
	}
}

class Booked_WC_Settings_Fields {

	protected $options_name;

	private function __construct() {
		$this->options_name = BOOKED_WC_PLUGIN_PREFIX . 'payment_options';
		$this->register_fields();
	}

	public static function register() {
		return new self();
	}

	protected function register_fields() {

		// Sets the defaults if settings aren't saved yet.
		$settings = get_option($this->options_name);
		if ( !$settings ) {
			$default_settings['enable_thumbnails'] = 'enable';
			$default_settings['enable_auto_cleanup'] = 'disable';
			$default_settings['cleanup_mode'] = 'twicedaily';
			update_option($this->options_name, $default_settings);
		}

		add_settings_section(
			$this->options_name,
			'',
			array($this, 'field_section'),
			$this->options_name
		);

		add_settings_field(
			'enable_thumbnails',
			__('Enable Thumbnails', 'wyzi-business-finder'),
			array($this, 'enable_thumbnails'),
			$this->options_name,
			$this->options_name
		);
		
		add_settings_field(
			'enable_auto_cleanup',
			__('Enable Auto-Cleanup', 'wyzi-business-finder'),
			array($this, 'enable_auto_cleanup'),
			$this->options_name,
			$this->options_name
		);

		add_settings_field(
			'cleanup_mode',
			__('Cleanup Schedule', 'wyzi-business-finder'),
			array($this, 'field_modes'),
			$this->options_name,
			$this->options_name
		);
		
		add_settings_field(
			'redirect_page',
			__('Redirect After Booking', 'wyzi-business-finder'),
			array($this, 'redirect_page'),
			$this->options_name,
			$this->options_name
		);

		// Register our setting so that $_POST handling is done for us and
	 	// our callback function just has to echo the <input>
	 	register_setting($this->options_name, $this->options_name);
	}

	public function field_section() {
		//
	}

	public function enable_thumbnails() {
		$options = get_option($this->options_name);
		$field_value = isset($options['enable_thumbnails']) ? $options['enable_thumbnails'] : false;

		$radio_options = array(
			'enable' => __('Enable', 'wyzi-business-finder'),
			'disable' => __('Disable', 'wyzi-business-finder')
		);
		?>
		<p><?php echo __('Enable this to show product thumbnails for Booked items (on the cart/checkout screens).', 'wyzi-business-finder') ?></p>
		<?php foreach ($radio_options as $option_value => $option_label): ?>
			<div>
				<input name="<?php echo $this->options_name ?>[enable_thumbnails]" type="radio" value="<?php echo $option_value ?>" <?php echo checked($field_value, $option_value) ?> />
				<p for="<?php echo $this->options_name ?>" style="display:inline-block; margin:0; padding:0; position:relative; top:-1px;"><?php echo $option_label ?></p>
			</div>
		<?php endforeach ?>
		<?php
	}

	public function enable_auto_cleanup() {
		$options = get_option($this->options_name);
		$field_value = isset($options['enable_auto_cleanup']) ? $options['enable_auto_cleanup'] : false;

		$radio_options = array(
			'enable' => __('Enable', 'wyzi-business-finder'),
			'disable' => __('Disable', 'wyzi-business-finder')
		);
		?>
		<p><?php echo __('Automatically cleans up all non-paid appointments at a specific time interval. For example, when someone chooses a day and product but does not pay for the appointment, then it will continue to be "booked" unless removed manually. This removes that step with an automated option.', 'wyzi-business-finder') ?></p>
		<?php foreach ($radio_options as $option_value => $option_label): ?>
			<div>
				<input name="<?php echo $this->options_name ?>[enable_auto_cleanup]" type="radio" value="<?php echo $option_value ?>" <?php echo checked($field_value, $option_value) ?> />
				<p for="<?php echo $this->options_name ?>" style="display:inline-block; margin:0; padding:0; position:relative; top:-1px;"><?php echo $option_label ?></p>
			</div>
		<?php endforeach ?>
		<?php
	}
	
	public function redirect_page() {
		$options = get_option($this->options_name);
		$field_value = isset($options['redirect_page']) ? $options['redirect_page'] : false;

		$radio_options = array(
			'checkout' => __('Checkout Page (default)', 'wyzi-business-finder'),
			'cart' => __('Cart Page', 'wyzi-business-finder')
		);
		?>
		<p><?php echo __('You can choose to have Booked redirect to the Checkout page (default) or the Cart page instead.', 'wyzi-business-finder') ?></p>
		<?php foreach ($radio_options as $option_value => $option_label): ?>
			<div>
				<input name="<?php echo $this->options_name ?>[redirect_page]" type="radio" value="<?php echo $option_value ?>" <?php echo ($option_value == 'checkout' && !$field_value ? 'checked="checked"' : checked($field_value, $option_value)); ?> />
				<p for="<?php echo $this->options_name ?>" style="display:inline-block; margin:0; padding:0; position:relative; top:-1px;"><?php echo $option_label ?></p>
			</div>
		<?php endforeach ?>
		<?php
	}

	public function field_modes() {
		$options = get_option($this->options_name);
		$field_value = isset($options['cleanup_mode']) ? $options['cleanup_mode'] : false;

		$available_schedules = wp_get_schedules();

		$schedules = array();
		foreach ($available_schedules as $name => $data) {
			$schedules[(int) $data['interval']] = array_merge(
				array('name' => $name),
				$data
			);
		}

		usort($schedules, array($this, 'booked_schedule_sort'));

		?>
		<p><?php echo __('More schedule intervals can be added with a plugin like <a href="https://wordpress.org/plugins/wp-crontrol/" target="_blank">WP Crontrol</a>.', 'wyzi-business-finder') ?> <?php echo __('The currently available schedules are listed below.', 'wyzi-business-finder') ?></p>
		<?php foreach ($schedules as $index => $schedule_data): ?>
			<div>
				<input id="<?php echo $schedule_data['name'] ?>" name="<?php echo $this->options_name ?>[cleanup_mode]" type="radio" value="<?php echo $schedule_data['name'] ?>" <?php echo checked($field_value, $schedule_data['name']) ?> />
				<p for="<?php echo $schedule_data['name'] ?>" style="display:inline-block; margin:0; padding:0; position:relative; top:-1px;"><?php echo $schedule_data['display'] ?></p>
			</div>
		<?php endforeach ?>
		<?php
	}
	
	public function booked_schedule_sort($a, $b) {
		return $a['interval'] - $b['interval'];
	}

}