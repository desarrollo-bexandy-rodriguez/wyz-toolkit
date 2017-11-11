<?php



/**
 * Add custom fields to job manager form
 *
 * @param string $items current menu items.
 * @param object $args used here to know theme location.
 */
function wyz_frontend_add_business_field( $fields ) {
	$businesses = WyzHelpers::get_user_businesses( get_current_user_id() );
	$values = array();
	$values[''] = sprintf( esc_html__( 'Not related to a %s', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT );
	foreach ($businesses['published'] as $id) {
		$values[ $id ] = get_the_title( $id );
	}
	$fields['job']['wyz_job_listing'] = array(
		'label'       => WYZ_BUSINESS_CPT,
		'type'        => 'select',
		'required'    => false,
		'placeholder' => '',
		'priority'    => 0,
		'options'		  => $values,
	);
  return $fields;
}


function wyz_admin_add_listing_field( $fields ) {
	$quer = new WP_Query(
		array(
			'post_type' => 'wyz_business',
			'posts_per_page' => -1,
			'post_status' => 'publish'
	));

	$values = array( '' => '' );

	while($quer->have_posts()){
		$quer->the_post();
		$values[ get_the_ID() ] = get_the_title();
	}
	wp_reset_postdata();

	$fields['_wyz_job_listing'] = array(
		'label'       => WYZ_BUSINESS_CPT,
		'type'        => 'select',
		'required'    => false,
		'placeholder' => '',
		'priority'    => 7,
		'options'		  => $values,
	);
	return $fields;
}
add_filter( 'submit_job_form_fields', 'wyz_frontend_add_business_field' );
add_filter( 'job_manager_job_listing_data_fields', 'wyz_admin_add_listing_field' );


/**
 * Chage template that displays business taxonomies.
 *
 * @param string $template_path path to our template file.
 */
function wyz_include_job_template_function( $template_path ) {

	if ( 'job_listing' === get_post_type() ) { // Display business on single-business.php template.
		if ( is_single() ) {
			if ( $theme_file = locate_template( array( 'single-job.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . 'single-job.php';
			}
		}
	}
	return $template_path;
}
add_filter( 'template_include', 'wyz_include_job_template_function', 1 );

add_filter( 'job_manager_locate_template', function( $template, $template_name, $template_path ) {
	if ( $template_name == 'job-application' )
		return plugin_dir_path( __FILE__ ) . 'templates/job-application.php';
	if ( $template_name == 'job-submitted' )
		return plugin_dir_path( __FILE__ ) . 'templates/job-submitted.php';
	if ( $template_name == 'content-single-job_listing-meta.php' )
		return plugin_dir_path( __FILE__ ) . 'templates/content-single-job_listing-meta.php';
	if ( $template_name == 'content-single-job_listing-company.php' )
		return plugin_dir_path( __FILE__ ) . 'templates/content-single-job_listing-company.php';
	if ( $template_name == 'job-preview.php' )
		return plugin_dir_path( __FILE__ ) . 'templates/job-preview.php';
	if ( $template_name == 'content-job_listing.php' )
		return plugin_dir_path( __FILE__ ) . 'templates/content-job_listing.php';
	return $template;
}, 10, 3);
?>