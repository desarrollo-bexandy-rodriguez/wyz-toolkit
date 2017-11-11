<?php
/**
 * WIZY Shortcodes
 *
 * @package wyz
 */

static $locations = array();
static $categories = array();
static $taxonomies = array();

global $template_type;
function wyz_register_shortcodes() {
	global $template_type;

	$template_type = 1;
	if ( function_exists( 'wyz_get_theme_template' ) )
		$template_type = wyz_get_theme_template();

	add_shortcode( 'wyz_offers', 'wyz_get_offers' );
	add_shortcode( 'wyz_locations', 'wyz_get_locations' );
	add_shortcode( 'wyz_recently_added', 'wyz_get_recently_added' );
	add_shortcode( 'wyz_featured', 'wyz_get_featured' );
	add_shortcode( 'wyz_categories', 'wyz_get_categories' );
	add_shortcode( 'wyz_all_businesses', 'wyz_get_all_businesses' );
	add_shortcode( 'wyz_all_offers', 'wyz_get_all_offers' );
	add_shortcode( 'wyz_iframe', 'wyz_iframe' );
	//add_shortcode( 'wyz_info_section', 'wyz_info_section' );
	add_shortcode( 'wyz_claim_form_display', 'wyz_claim_form_func' );
	add_shortcode( 'wyz_header_filters','wyz_get_header_filters'  );

}
add_action( 'init', 'wyz_register_shortcodes' );

function wyz_get_categories( $wyz_cat_attr ) {
	$wyz_cat_attr = shortcode_atts( array( 'cat_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'autoplay_timeout' => 2000, 'rows' => 1, 'columns' => 4, 'loop' => false, 'hide_count' => false ), $wyz_cat_attr );

	return WYZISlidersFactory::the_categories_slider( $wyz_cat_attr );
}

function wyz_get_locations( $wyz_loc_attr ) {

	$wyz_loc_attr = shortcode_atts( array( 'loc_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'autoplay_timeout' => 2000, 'rows' => 1, 'loop' => false, 'linking' => false ), $wyz_loc_attr );

	return WYZISlidersFactory::the_locations_slider( $wyz_loc_attr );
}

function wyz_get_offers( $wyz_offrs_attr ) {

	$wyz_offrs_attr = shortcode_atts( array( 'nav' => false, 'autoplay' => false, 'autoplay_timeout' => 2000, 'loop' => false, 'autoheight' => true ), $wyz_offrs_attr );
	
	return WYZISlidersFactory::the_offers_slider( $wyz_offrs_attr );
}

function wyz_get_recently_added( $wyz_rec_add_attr ) {

	$wyz_rec_add_attr = shortcode_atts( array( 'rec_added_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'autoplay_timeout' => 2000, 'rows' => 1, 'loop' => false, 'count' => 10 ), $wyz_rec_add_attr );

	return WYZISlidersFactory::the_rec_added_slider( $wyz_rec_add_attr );
}

function wyz_get_featured( $wyz_featured_attr ) {
	global $template_type;
	$attr = array( 'featured_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'autoplay_timeout' => 2000, 'rows' => 1, 'loop' => false, 'count' => 10 );
	if ( 2 == $template_type ) {
		$attr['lg-desktop-col'] = '4';
		$attr['md-desktop-col'] = '3';
		$attr['tablet-col'] = '2';
		$attr['mobile-col'] = '1';
	}
	$wyz_featured_attr = shortcode_atts( $attr, $wyz_featured_attr );
	
	return WYZISlidersFactory::the_featured_slider( $wyz_featured_attr );
}

function wyz_get_all_businesses( $wyz_all_bus_attr ) {

	global $template_type;

	$wyz_all_bus_attr = shortcode_atts( array( 'count' => 10, 'country' => '', 'category' => '' ), $wyz_all_bus_attr );
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'post_type' => 'wyz_business',
		'posts_per_page' => $wyz_all_bus_attr['count'],
		'orderby'=> 'menu_order',
		'post_status' => array( 'publish' ),
		'paged'=>$paged,

	);

	if ( ! empty( $wyz_all_bus_attr['country'] ) ) {
		$args['meta_query'] = array(
			array(
				'key' => 'wyz_business_country',
				'value' => $wyz_all_bus_attr['country']
			)
		);
	}

	if ( ! empty( $wyz_all_bus_attr['category'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'wyz_business_category',
				'field' => 'term_id',
				'terms' => array( $wyz_all_bus_attr['category'] )
			)
		);
	}
	global $wp_query;

	$wp_query = WyzHelpers::query_businesses( $args, true );

	if ( 2 == $template_type ) {
		if ( $wp_query->have_posts() ) {

			if ( function_exists( 'wyz_get_option' ) ) {
				$ids = array();
				foreach ($wp_query->posts as $post) {
					$ids[] = $post->ID;
				}
				$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
				if ( '' != $grid_alias )
					echo do_shortcode( '[ess_grid alias="' . $grid_alias .'" posts='.implode(',',$ids).']' );
			}
		}
	} else {

		while( $wp_query->have_posts() ):
			$wp_query->the_post();
			echo WyzBusinessPost::wyz_create_business();
		endwhile;
	}
	wp_reset_postdata();
	if ( function_exists( 'wyz_pagination' ) ) wyz_pagination();

}


function wyz_iframe( $attr ) {
	$attr = shortcode_atts( array( 'src'=>'', 'allowfullscreen' => '' ), $attr );
	echo '<div class="wyz_iframe-container"><iframe src="' . $attr['src'] . '" ';
	if ( $attr['allowfullscreen'] )
		echo ' allowfullscreen';
	echo '></iframe></div>';
}

function wyz_get_all_offers( $wyz_all_offers_attr ) {

	$wyz_all_offers_attr = shortcode_atts( array( 'count' => 10 ), $wyz_all_offers_attr );
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'post_type' => 'wyz_offers',
		'posts_per_page' => $wyz_all_offers_attr['count'],
		'orderby'=> 'menu_order',
		'post_status' => array( 'publish' ),
		'paged'=>$paged,
	);

	global $wp_query;

	$wp_query = new WP_Query( $args );

	while( $wp_query->have_posts() ):
		$wp_query->the_post();
		echo WyzOffer::wyz_the_offer( get_the_ID(), true );
	endwhile;?>
	<?php if ( function_exists( 'wyz_pagination' ) ) wyz_pagination();?>
	<?php wp_reset_postdata();
}

function wyz_info_section( $attr ) {
	$attr = shortcode_atts( array( 'title' => '', 'content' => '' ), $attr );
	?>
	<div class="mb-50">
		<div class="container">
			<!-- Section Title -->
			<div class="row">
				<div class="section-title text-center col-xs-12 mb-50">
					<h2><?php echo $attr['title'];?></h2>
					<p><?php echo $attr['content'];?></p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function wyz_get_header_filters ( $attr ) {
	$attr = shortcode_atts( array( 'filters_order' => "1,2,3,4" ), $attr );
	$indexes = explode( ',', $attr['filters_order'] );
	for( $i=0;$i<count($indexes);$i++){
		$indexes[$i] = intval($indexes[$i]);
	}
	return WyzHelpers::wyz_get_business_filters( $indexes, false );
}


function wyz_claim_form_func() {
	$wyz_claim_registration_form_data = get_option( 'wyz_claim_registration_form_data' );
	require_once( plugin_dir_path( __FILE__ ) . '../claim/claim_registration_form_front_end.php' );
}

// Visual composer shortcodes map additions.
function wyz_vc_shortcodes_integrate() {
	global $locations;
	global $categories;
	global $template_type;

	if( empty( $locations ) ) {
		$locations = WyzHelpers::get_business_locations_dropdown_format(true);
	}
	
	if( empty( $taxonomies ) ) {

		$taxonomies = WyzHelpers::get_business_categories();
		$all_tax = array();
		foreach ( $taxonomies as $tax ) {
			$all_tax[] = array(
				'label' => $tax['name'],
				'value' => $tax['id']
			);
			foreach ($tax['children'] as $child) {
				$all_tax[] = array(
					'label' => $child['name'],
					'value' => $child['id']
				);
			}
		}


		$categories = WyzHelpers::get_business_categories_dropdown_format(true);
	}
	wp_reset_postdata();


	vc_map( array( 
		"name" => WYZ_OFFERS_CPT . ' ' . esc_html__( "Slider", 'wyzi-business-finder' ), 
		"base" => "wyz_offers", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '', 
		"params" => array(
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
				"param_name" => "nav", 
				"value" => '', 
				"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
				), 
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
				"param_name" => "autoplay", 
				"value" => '', 
				"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
				), 
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
			 	"param_name" => "autoplay_timeout", 
			 	"value" => 2000, 
			 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
			 	),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
				"param_name" => "loop",
				"value" => '', 
				"description" => esc_html__( "Loop through.", 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT 
				), 
			array( "type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Auto Height", 'wyzi-business-finder' ), 
				"param_name" => "autoheight", 
				"value" => '', 
				"description" => esc_html__( "Slider height automatically adjusts to content height.", 'wyzi-business-finder' ) 
				) 
			) 
		) );

	vc_map( array( 
		"name" => LOCATION_CPT . esc_html__( " Slider", 'wyzi-business-finder' ), 
		"base" => "wyz_locations", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '', 
		"params" => array( 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
				"param_name" => "loc_slider_ttl", 
				"value" => '', 
				"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
				),
			array("type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ),
				"param_name" => "nav", 
				"value" => '', 
				"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
				),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
				"param_name" => "autoplay", 
				"value" => '', 
				"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
				), 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
				"param_name" => "autoplay_timeout", 
				"value" => 2000, 
				"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
				),
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
				"param_name" => "loop", 
				"value" => '',
				"description" => esc_html__( "Loop through Locations.", 'wyzi-business-finder' ) 
				), 
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Linking", 'wyzi-business-finder' ), 
				"param_name" => "linking", 
				"value" => '', 
				"description" => esc_html__( "Each slide links to the corresponding Location CPT archives page.", 'wyzi-business-finder' )
				 ), 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Rows", 'wyzi-business-finder' ), 
				"param_name" => "rows", 
				"value" => 1, 
				"description" => esc_html__( "The number of rows you want this slider to have.", 'wyzi-business-finder' ) 
				) 
			) 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "Recently Added Businesses Slider", 'wyzi-business-finder' ), 
			"base" => "wyz_recently_added", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => '', 
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
					"param_name" => "rec_added_slider_ttl", 
					"value" => '', 
					"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
					), 
				array(
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
					"param_name" => "nav", 
					"value" => '', 
					"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
					),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
					"param_name" => "autoplay", 
					"value" => '', 
					"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
					), 
				array( 
				 	"type" => "textfield", 
				 	"holder" => "div", 
				 	"class" => "", 
				 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
				 	"param_name" => "autoplay_timeout", 
				 	"value" => 2000, 
				 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
				 	),
				array(
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
					"param_name" => "loop", 
					"value" => '', 
					"description" => esc_html__( "Loop through Businesses.", 'wyzi-business-finder' ) 
					), 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Count", 'wyzi-business-finder' ), 
					"param_name" => "count", 
					"value" => 10, 
					"description" => esc_html__( "The maximum number of businesses this slider has.", 'wyzi-business-finder' ) 
					) 
				) 
			) );

	vc_map( 
		array( 
			"name" => esc_html__( "Featured Businesses Slider", 'wyzi-business-finder' ), 
			"base" => "wyz_featured", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => '', 
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
					"param_name" => "featured_slider_ttl", 
					"value" => '', 
					"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
					), 
				array(
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
					"param_name" => "nav", 
					"value" => '', 
					"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
					),
				array( 
					"type" => "dropdown", 
					"value" => array('auto','manual'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts pull method", 'wyzi-business-finder' ), 
					"param_name" => "pull_method", 
					"description" => esc_html__( "Auto: posts are pulled when the end of the list is reached, Manual: user clicks 'Load More' to pull more posts", 'wyzi-business-finder' ),
				),
				array( 
					"type" => "dropdown", 
					"value" => array(6,4,3,2,1), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Number of columns for large desktops", 'wyzi-business-finder' ), 
					"param_name" => "lg-desktop-col",
				),
				array( 
					"type" => "dropdown", 
					"value" => array(6,4,3,2,1), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Number of columns for medium desktops", 'wyzi-business-finder' ), 
					"param_name" => "md-desktop-col",
				),
				array( 
					"type" => "dropdown", 
					"value" => array(6,4,3,2,1), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Number of columns for tablets", 'wyzi-business-finder' ), 
					"param_name" => "tablet-col",
				),
				array( 
					"type" => "dropdown", 
					"value" => array(6,4,3,2,1), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Number of columns for mobile", 'wyzi-business-finder' ), 
					"param_name" => "mobile-col",
				),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
					"param_name" => "autoplay", 
					"value" => '', 
					"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
					), 
				array( 
				 	"type" => "textfield", 
				 	"holder" => "div", 
				 	"class" => "", 
				 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
				 	"param_name" => "autoplay_timeout", 
				 	"value" => 2000, 
				 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
				 	),
				array(
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
					"param_name" => "loop", 
					"value" => '', 
					"description" => esc_html__( "Loop through Businesses.", 'wyzi-business-finder' ) 
					), 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Count", 'wyzi-business-finder' ), 
					"param_name" => "count", 
					"value" => 10, 
					"description" => esc_html__( "The maximum number of businesses this slider has.", 'wyzi-business-finder' ) 
					) 
				) 
			) );

	vc_map( 
		array( 
			"name" => esc_html__( "Business Categories Slider", 'wyzi-business-finder' ), 
			"base" => "wyz_categories", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => '', 
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
					"param_name" => "cat_slider_ttl", 
					"value" => '', 
					"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
					), 
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
					"param_name" => "nav", 
					"value" => '', 
					"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
					),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
					"param_name" => "autoplay", 
					"value" => '', 
					"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
					), 
				array( 
				 	"type" => "textfield", 
				 	"holder" => "div", 
				 	"class" => "", 
				 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
				 	"param_name" => "autoplay_timeout", 
				 	"value" => 2000, 
				 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
				 	),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
					"param_name" => "loop", 
					"value" => '', 
					"description" => esc_html__( "Loop through Categories.", 'wyzi-business-finder' ) 
					), 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Rows", 'wyzi-business-finder' ), 
					"param_name" => "rows", 
					"value" => 1, 
					"description" => esc_html__( "The number of rows you want this slider to have.", 'wyzi-business-finder' ) 
					),
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Columns", 'wyzi-business-finder' ), 
					"param_name" => "columns", 
					"value" => 4, 
					"description" => esc_html__( "The number of columns for wide screen", 'wyzi-business-finder' ) 
					),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Hide Businesses Count", 'wyzi-business-finder' ), 
					"param_name" => "hide_count", 
					"value" => '', 
					"description" => '' 
					), 
				) 
			) );

	vc_map( 
		array( 
			"name" => esc_html__( "Registration Form", 'wyzi-business-finder' ), 
			"base" => "wyz_signup_form", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display a registration form.", 'wyzi-business-finder' ), 
			"show_settings_on_create" => true 
		) );

	
	$taxonomies = WyzHelpers::get_business_categories();
	$all_tax = array();
	foreach ( $taxonomies as $tax ) {
		$all_tax[] = array(
			'label' => $tax['name'],
			'value' => $tax['id']
		);
		foreach ($tax['children'] as $child) {
			$all_tax[] = array(
				'label' => $child['name'],
				'value' => $child['id']
			);
		}
	}

	vc_map( 
		array( 
			"name" => esc_html__( "Wall", 'wyzi-business-finder' ), 
			"base" => "wyz_business_wall", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display businesses wall on this page.", 'wyzi-business-finder' ),
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts pull per page load", 'wyzi-business-finder' ), 
					"param_name" => "posts_pull",
					"value" => '10', 
					"description" => esc_html__( "The number of business posts to pull on each page load.", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => array('auto','manual'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts pull method", 'wyzi-business-finder' ), 
					"param_name" => "pull_method", 
					"description" => esc_html__( "Auto: posts are pulled when the end of the list is reached, Manual: user clicks 'Load More' to pull more posts", 'wyzi-business-finder' ),
				),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Display footer", 'wyzi-business-finder' ),
					"value" => '',
					"param_name" => "display_footer", 
					"description" => '', 
				),
				array( 
				 	"type" => "autocomplete", 
				 	"holder" => "div", 
				 	"class" => "", 
			 		'settings' => array(
			 			'multiple' => true,
						'values' => $all_tax
				 	),
				 	"heading" => esc_html__( "Category", 'wyzi-business-finder' ), 
				 	"param_name" => "category",
				 	"description" => __( 'Display only posts belonging to businesses of these categories (leave empty to display all)', 'wyzi-business-finder' ) 
			 	),
			),
			"show_settings_on_create" => true 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "All Businesses", 'wyzi-business-finder' ), 
			"base" => "wyz_all_businesses", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display all businesses on this page.", 'wyzi-business-finder' ), 
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts per page", 'wyzi-business-finder' ), 
					"param_name" => "count", 
					"value" => '10', 
					"description" => esc_html__( "The number of businesses to display per page.", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => $locations, 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Of specific Country", 'wyzi-business-finder' ), 
					"param_name" => "country", 
					"description" => '',
				),
				array( 
					"type" => "dropdown", 
					"value" => $categories, 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Of specific Category", 'wyzi-business-finder' ), 
					"param_name" => "category", 
					"description" => ''
				),
			), 
			"show_settings_on_create" => true 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "All", 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT, 
			"base" => "wyz_all_offers", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => sprintf( esc_html__( "Display all %s on this page.", 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts per page", 'wyzi-business-finder' ), 
					"param_name" => "count", 
					"value" => '10', 
					"description" => sprintf( esc_html__( "The number of %s to display per page.", 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) 
				) 
			), 
			"show_settings_on_create" => true 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "Subscription Tables", 'wyzi-business-finder' ), 
			"base" => "pmpro_advanced_levels", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display Pricing Tables of Paid Membership Pro", 'wyzi-business-finder' ), 
			"params" => array(
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Levels", 'wyzi-business-finder' ), 
					"param_name" => "levels", 
					"value" => '', 
					"description" => esc_html__( "Membership Levels Ids, comma Sparated", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => array('bootstrap'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Template", 'wyzi-business-finder' ), 
					"param_name" => "template", 
					"description" => '',
					'save_always'=>true,
				),
				array( 
					"type" => "dropdown", 
					"value" => array('div','table','2col','3col','4col'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Layout", 'wyzi-business-finder' ), 
					"param_name" => "layout", 
					"description" => esc_html__( "Tables Layout", 'wyzi-business-finder' ),
				),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Description", 'wyzi-business-finder' ), 
					"param_name" => "description", 
					"value" => "", 
					"description" => esc_html__( "Display Description", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Checkout Button", 'wyzi-business-finder' ), 
					"param_name" => "checkout_button", 
					"value" => '', 
					"description" => esc_html__( "Lable for checkout button", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => array(
						'full',
						'short',
						'hide'
						), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Price", 'wyzi-business-finder' ), 
					"param_name" => "price", 
					"description" => esc_html__( "How to display the level cost text", 'wyzi-business-finder' ),
				),
			), 
			"show_settings_on_create" => true , 
		) );

	vc_map( array( 
		"name" =>  esc_html__( "Header Filters", 'wyzi-business-finder' ), 
		"base" => "wyz_header_filters", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '',
		"params" => array(
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Filters Order", 'wyzi-business-finder' ), 
			 	"param_name" => "filters_order",
			 	"value" => '1,2,3,4',
			 	"description" => __( 'Enter the order of indexes to display the filters in, comma separated.<br/> 1: Keyword filer <br/> 2: Location filter <br/> 3: Category filter.<br/> 4: Open Days Filter.', 'wyzi-business-finder' ) 
		 	),
		 ),
			"show_settings_on_create" => true 
		) );

}
add_action( 'vc_before_init', 'wyz_vc_shortcodes_integrate' );