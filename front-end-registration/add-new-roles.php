<?php 
add_role( 'client', esc_html__( 'Client', 'wyzi-business-finder' ),
	array(
		'activate_plugins' => false,
		'delete_others_pages' => false,
		'delete_others_posts' => false,
		'delete_pages' => false,
		'delete_posts' => false,
		'delete_published_pages' => false,
		'delete_published_posts' => false,
		'edit_dashboard' => false,
		'edit_others_pages' => false,
		'edit_others_posts' => false,
		'edit_pages' => false,
		'edit_posts' => false,
		'edit_published_pages' => false,
		'edit_published_posts' => false,
		'edit_theme_options' => false,
		'manage_categories' => false,
		'manage_links' => false,
		'manage_options' => false,
		'publish_pages' => false,
		'publish_posts' => false,
		'read' => true,
		'remove_users' => false,
		'switch_themes' => false,
		'upload_files' => false,
	)
);

add_role( 'business_owner', esc_html__( 'Business Owner', 'wyzi-business-finder' ),
	array(
		'activate_plugins' => false,
		'delete_others_pages' => false,
		'delete_others_posts' => false,
		'delete_pages' => false,
		'delete_posts' => true,
		'delete_published_pages' => false,
		'delete_published_posts' => true,
		'edit_dashboard' => false,
		'edit_others_pages' => false,
		'edit_others_posts' => false,
		'edit_pages' => false,
		'edit_posts' => false,
		'edit_published_pages' => false,
		'edit_published_posts' => false,
		'edit_theme_options' => false,
		'manage_categories' => false,
		'manage_links' => false,
		'manage_options' => false,
		'publish_pages' => false,
		'publish_posts' => false,
		'read' => true,
		'remove_users' => false,
		'switch_themes' => false,
		'upload_files' => true,
		'publish_offers' => true,
		'edit_offers' => true,
		'edit_others_offers' => false,
		'delete_offers' => true,
		'delete_others_offers' => false,
		'read_offer' => true,
		'delete_published_offers' => true,
		'edit_published_offers' => true,
		'publish_businesses' => true,
		'edit_businesses' => true,
		'edit_others_businesses' => false,
		'delete_businesses' => true,
		'delete_others_businesses' => false,
		'read_private_businesses' => false,
		'read_business' => true,
		'delete_published_businesses' => true,
		'edit_published_businesses' => true,
	)
);


// Add Capabilities to User Roles (the below array can be filtered to include more or exclude any of the defaults)
$booked_user_roles = apply_filters( 'booked_user_roles', array('administrator','business_owner','booked_booking_agent') );
	
// Add the "Booking Agent" User Role
$booking_agent = add_role(
    'booked_booking_agent',
    esc_html__( 'Booking Agent','wyzi-business-finder' ),
    array(
        'read' => true,
    )
);

foreach($booked_user_roles as $role_name):
	$role_caps = get_role($role_name);
	$role_caps->add_cap('edit_booked_appointments');
endforeach;

$booked_admin_caps = get_role('administrator');
$booked_admin_caps->add_cap('manage_booked_options');

