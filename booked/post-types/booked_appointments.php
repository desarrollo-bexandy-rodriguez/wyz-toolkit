<?php

if(!class_exists('booked_appointments_post_type')) {
	/**
	 * A booked_bookings_post_type class that provides 3 additional meta fields
	 */
	class booked_appointments_post_type {

		// META FIELDS
		// _appointment_timeslot
		// _appointment_timestamp
		// _appointment_user

		/**
		 * The Constructor
		 */
		public function __construct() {
			// register actions
			add_action('init', array(&$this, 'create_post_type'));
		} // END public function __construct()

		/**
		 * Create the post type
		 */
		public function create_post_type() {

			register_post_type('booked_appointments',
				array(
					'labels' => array(
						'name'               => esc_html__( 'Appointments', 'wyzi-business-finder'),
						'singular_name'      => esc_html__( 'Appointment', 'wyzi-business-finder'),
						'menu_name'          => esc_html__( 'Appointments', 'wyzi-business-finder'),
						'name_admin_bar'     => esc_html__( 'Appointment', 'wyzi-business-finder'),
						'add_new'            => esc_html__( 'Add New', 'wyzi-business-finder'),
						'add_new_item'       => esc_html__( 'Add New Appointment', 'wyzi-business-finder'),
						'new_item'           => esc_html__( 'New Appointment', 'wyzi-business-finder'),
						'edit_item'          => esc_html__( 'Edit Appointment', 'wyzi-business-finder'),
						'view_item'          => esc_html__( 'View Appointment', 'wyzi-business-finder'),
						'all_items'          => esc_html__( 'All Appointments', 'wyzi-business-finder'),
						'search_items'       => esc_html__( 'Search Appointments', 'wyzi-business-finder'),
						'parent_item_colon'  => esc_html__( 'Parent Appointments:', 'wyzi-business-finder'),
						'not_found'          => esc_html__( 'No Appointments found.', 'wyzi-business-finder'),
						'not_found_in_trash' => esc_html__( 'No Appointments found in Trash.', 'wyzi-business-finder')
					),
					'show_in_admin_bar' => false,
					'public' => false,
					'has_archive' => false,
					'description' => esc_html__('Appointments','wyzi-business-finder'),
					'supports' => array(
						'title','author'
					),
					'menu_icon' => 'dashicons-calendar-alt',
					'taxonomies'    => array(
				        'booked_custom_calendars'
				    )
				)
			);

			$labels = array(
				'name'                       => esc_html__( 'Custom Calendars', 'wyzi-business-finder'),
				'singular_name'              => esc_html__( 'Custom Calendar', 'wyzi-business-finder'),
				'search_items'               => esc_html__( 'Search Custom Calendars', 'wyzi-business-finder'),
				'popular_items'              => esc_html__( 'Popular Custom Calendars', 'wyzi-business-finder'),
				'all_items'                  => esc_html__( 'All Custom Calendars', 'wyzi-business-finder'),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => esc_html__( 'Edit Custom Calendar', 'wyzi-business-finder'),
				'update_item'                => esc_html__( 'Update Custom Calendar', 'wyzi-business-finder'),
				'add_new_item'               => esc_html__( 'Add New Custom Calendar', 'wyzi-business-finder'),
				'new_item_name'              => esc_html__( 'New Custom Calendar Name', 'wyzi-business-finder'),
				'separate_items_with_commas' => esc_html__( 'Separate custom calendars with commas', 'wyzi-business-finder'),
				'add_or_remove_items'        => esc_html__( 'Add or remove custom calendars', 'wyzi-business-finder'),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used custom calendars', 'wyzi-business-finder'),
				'not_found'                  => esc_html__( 'No custom calendars found.', 'wyzi-business-finder'),
				'menu_name'                  => esc_html__( 'Custom Calendars', 'wyzi-business-finder'),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'custom-calendar' ),
			);

			register_taxonomy( 'booked_custom_calendars', 'booked_appointments', $args );

		}

	} // END class booked_bookings_post_type
} // END if(!class_exists('booked_bookings_post_type'))


if (isset($_GET['flush_appointments'])):

	add_action('admin_init','booked_flush_demo_appts');

endif;

function booked_flush_demo_appts(){

	if (current_user_can('manage_booked_options')):

		$args = array(
			'post_type' => 'booked_appointments',
			'posts_per_page' => -1,
			'post_status' => 'any'
		);

		$total_appts = 0;

		$bookedAppointments = new WP_Query($args);
		if($bookedAppointments->have_posts()):
			while ($bookedAppointments->have_posts()):
				$bookedAppointments->the_post();
				global $post;
				wp_delete_post($post->ID,true);
				$total_appts++;
			endwhile;
		endif;
		echo $total_appts.' appointments have been flushed.';

	endif;

}

if (isset($_GET['create_appointments']) && isset($_GET['month']) && isset($_GET['year'])):

	add_action('admin_init','booked_create_demo_appts');

endif;

function booked_create_demo_appts($month = false,$year = false){
	global $user_ID;

	$month = $_GET['month'];
	$year = $_GET['year'];

	$calendars = get_terms('booked_custom_calendars','orderby=slug&hide_empty=0');
	if (!empty($calendars)):
		foreach($calendars as $calendar):
			$calendar_array[] = $calendar->term_id;
		endforeach;
	endif;

	$total_calendars = count($calendar_array);

	$days_in_month = date_i18n("t",strtotime($year.'-'.$month.'-01'));
	$current_day = 1;
	$appt_array = array();

	do {

		$random_calendar = rand(0,$total_calendars);
		if ($random_calendar): $random_calendar = $calendar_array[$random_calendar-1]; endif;

		if ($random_calendar):
			$booked_defaults = get_option('booked_defaults_'.$random_calendar);
			if (!$booked_defaults):
				$booked_defaults = get_option('booked_defaults');
			endif;
		else :
			$booked_defaults = get_option('booked_defaults');
		endif;

		$dayName = date('D',strtotime($year.'-'.$month.'-'.$current_day));
		if (isset($booked_defaults[$dayName]) && !empty($booked_defaults[$dayName])):

			// Create user array for this day's appointments
			$total_appts = rand(1,10);
			$done = false;
			$user_array = array();
			do {
				$random_user = rand(2,11);
				if (!in_array($random_user,$user_array)):
					$user_array[] = $random_user;
					$done = true;
				endif;
			} while (count($user_array) < $total_appts);

			foreach($user_array as $user){
				$random_timeslot = array_rand($booked_defaults[$dayName], 1);

				$timeslot_pieces = explode('-',$random_timeslot);
				$timestamp_time = $timeslot_pieces[0];

				$appt_array[$year.'-'.$month.'-'.$current_day][] = array(
					'user_id' 	=> $user,
					'timeslot'	=> $random_timeslot,
					'calendar'	=> $random_calendar,
					'timestamp'	=> strtotime($year.'-'.$month.'-'.$current_day.' '.$timestamp_time)
				);
			}

		endif;

		$current_day++;

	//} while ($current_day < 3);
	} while ($current_day <= $days_in_month);

	$total_appts = 0;

	foreach($appt_array as $date => $appt_day){

		foreach($appt_day as $appt):

			$status_rand = rand(1,50);
			if ($status_rand == 7): $status = 'draft'; else : $status = 'publish'; endif;

			$new_post = apply_filters('booked_new_appointment_args', array(
				'post_title' => date_i18n('F j, Y',$appt['timestamp']).' @ '.date_i18n('H:i',$appt['timestamp']).' (User: '.$appt['user_id'].')',
				'post_content' => '',
				'post_status' => $status,
				'post_date' => $year.'-'.$month.'-01 00:00:00',
				'post_author' => $appt['user_id'],
				'post_type' => 'booked_appointments'
			));
			$post_id = wp_insert_post($new_post);

			update_post_meta($post_id, '_appointment_timestamp', $appt['timestamp']);
			update_post_meta($post_id, '_appointment_timeslot', $appt['timeslot']);
			update_post_meta($post_id, '_appointment_user', $appt['user_id']);

			if ($appt['calendar']):

				$calendar_id = $appt['calendar'];
				$calendar_id = array($calendar_id);
				$calendar_id = array_map( 'intval', $calendar_id );
				$calendar_id = array_unique( $calendar_id );
				wp_set_object_terms($post_id,$calendar_id,'booked_custom_calendars');

			endif;

			do_action('booked_new_appointment_created', $post_id);

			$total_appts++;

		endforeach;

	}

	echo $total_appts.' appointments have been randomly generated.';

}