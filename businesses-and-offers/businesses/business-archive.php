<?php
/**
 * Template Name: Business Archive
 *
 * @package wyz
 */
get_header();

if ( 'on' != get_option( 'wyz_business_map_hide_in_single_bus' ) ) {
	WyzMap::wyz_get_archives_map();
}

$location = -1;
$category = -1;
$keyword = '';
$days_get = array();
global $wp_query;
global $template_type;
$temp_query = '';
if ( isset( $_GET['location'] ) && 0 < $_GET['location'] ) {
	$location = $_GET['location'];
}
if ( isset( $_GET['category'] ) && 0 < $_GET['category'] ) {
	$category = $_GET['category'];
}
if ( is_tax( 'wyz_business_category' ) ) {
	$category = get_queried_object_id();
}
if ( isset( $_GET['keyword'] ) ) {
	$keyword = $_GET['keyword'];
}
if ( isset( $_GET['open_days'] ) ) {
	$days_get = $_GET['open_days'];
}

$map_skin = get_option( 'wyz_business_archives_map_skin' );
if ( '' == $map_skin ) $map_skin = 0;
$js_data = array(
	'GPSLocations' => array(),
	'markersWithIcons' => array(),
	'businessNames' => array(),
	'businessLogoes' => array(),
	'businessPermalinks' => array(),
	'businessCategories' => array(),
	'businessCategoriesColors' => array(),
	'defLogo' => WyzHelpers::get_default_image( 'business' ),
	'defCoor' => array(
		'latitude' => esc_attr( get_option( 'wyz_businesses_default_lat' ) ),
		'longitude' => esc_attr( get_option( 'wyz_businesses_default_lon' ) ),
		'zoom' => get_option( 'wyz_archives_map_zoom', 12 ),
	),
	'mapSkin' => WyzMap::get_map_skin( $map_skin, -1 ),
	'viewDetails' =>esc_html__( 'View Details', 'wyzi-business-finder' ),
	'templateType' => $template_type,
);

add_action( 'wp_footer', function(){
	global $js_data;
	wp_localize_script( 'wyz_archives_map', 'archivesMap', $js_data );
}, 11 );
?>

<div class="wall-collection-area margin-bottom-100 margin-top-50">
	<div class="container">
		<div class="row">

			<!-- Left sidebar. -->
			<?php if ( 'right-sidebar' !== wyz_get_option( 'sidebar-layout' ) && 'full-width' !== wyz_get_option( 'sidebar-layout' ) ) :?>
				
				<div class="sidebar-container<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-3 col-md-4 col-xs-12<?php } else { ?>col-xs-4 <?php } ?>">
						
					<?php if ( is_active_sidebar( 'wyz-business-listing-sb' ) ) : ?>

						<div class="widget-area sidebar-widget-area" role="complementary">
							
							<?php dynamic_sidebar( 'wyz-business-listing-sb' ); ?>
						
						</div>

					<?php endif; ?>

				</div>
			<?php endif; ?>

			<!-- Wall Collection -->
			<div class="wall-collections<?php if ( 'full-width' === wyz_get_option( 'sidebar-layout' ) ) { ?> col-lg-12 col-md-12 col-xs-12"<?php } elseif ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-9 col-md-8 col-xs-12<?php } else { ?> col-xs-8<?php } ?>">

				<?php 
				$count = 0;
				$paged = 1;
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				}

				if ( ! is_tax('wyz_business_tag') && ! is_tax('wyz_business_category') ) {
					$args = array();

					if ( '' != $keyword )
						$keywords = explode( ' ', $keyword );
					else
						$keywords = array();

					$meta_query = '';
			

					if ( '' != $location && 0 < $location ) {
						$meta_query = array( // Include excerpt and slogan in global map search.
							'relation' => 'AND',
							array( 'key' => 'wyz_business_country', 'value' => $location )
						);
						if ( ! empty( $keyword ) ) {
							$meta_query[] = array( 
								'relation' => 'OR',
								array( 'key' => 'wyz_business_excerpt', 'value' => $keyword, 'compare' => 'LIKE' ),
								array( 'key' => 'wyz_business_slogan', 'value' => $keyword, 'compare' => 'LIKE' ),
							);
						}
					} elseif( ! empty( $keywords ) ) {
						$meta_query = array( // Include excerpt and slogan in global map search.
							'relation' => 'OR',
							array( 'key' => 'wyz_business_excerpt', 'value' => $keyword, 'compare' => 'LIKE' ),
							array( 'key' => 'wyz_business_slogan', 'value' => $keyword, 'compare' => 'LIKE' ),
						);
					}



					if ( ! empty( $keywords ) ) {
						$tax_query = array(
							array(
								'taxonomy' => 'wyz_business_tag',
								'field'    => 'name',
								'terms' => $keywords,
							),
						);
						if ( '' !== $category && 0 < $category ){
							$args['cat_query'] = $category;
						}
					} elseif ( '' !== $category && 0 < $category ) {
						$tax_query = array(
							array(
								'taxonomy' => 'wyz_business_category',
								'field'    => 'term_id',
								'terms' => $category,
							),
						);
					}

					if ( '' != $meta_query ) {
						$args['meta_query'] = $meta_query;
					}

					if ( ! empty( $keywords ) ) {

						$args['_meta_or_title'] = $keywords;
						$args['my_tax_query'] = $tax_query;
						$args['_meta_or_tax'] = true;
					} elseif ( isset( $tax_query ) && ! empty( $tax_query) ) {
						$args['tax_query'] = $tax_query;
					}
				}

				else {

					if ( is_tax('wyz_business_tag') ) {
						$tag = single_tag_title( '', false );
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'wyz_business_tag',
								'field'    => 'slug',
								'terms'    => $tag,
							),
						);
					}
					elseif ( is_tax('wyz_business_category') ) {
						$qo =get_queried_object();
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'wyz_business_category',
								'field'    => 'slug',
								'terms'    => $qo->slug,
							),
						);
					} else {
						if ( ! empty( $keyword ) ) {
							$args['post_title_like'] = $keyword;
						}
						if ( 0 < $category ) {
							if ( ! isset( $args['tax_query'] ) ) {
								$args['tax_query'] = array();
							}
							$args['tax_query'][] = array(
								'taxonomy' => 'wyz_business_category',
								'field'    => 'term_id',
								'terms'    => $category,
							);
						}
						if ( 0 < $location ) {
							$args['meta_query'] = array(
								array( 'key' => 'wyz_business_country', 'value' => $location ),
							);
						}
					}
				}

				do_action( 'wyz_before_business_search_display' );

				$post_ids = array();
				if ( ! empty( $args ) ) {
					$args['post_type'] = 'wyz_business';
					$args['paged'] = $paged; 
					$the_query = WyzHelpers::query_businesses( $args );
					$temp_query = $wp_query;
					$wp_query = $the_query;
					if ( $the_query->have_posts() ) :
					
						while ( $the_query->have_posts() ) :
							$the_query->the_post();

				 			wyz_get_archives_filtered();
				 			$post_ids[] = get_the_ID();

				 			$count++;

						endwhile;
						
					endif;
				} else {
					$the_query = WyzHelpers::query_businesses( array( 'paged' => $paged,'post_type'=>'wyz_business','post_status'=>'publish') );
					$post_ids = array();
					if ( $the_query->have_posts() ) :
						$post_ids = wp_list_pluck( $the_query->posts, 'ID' );
						while ( $the_query->have_posts() ) :
							$the_query->the_post();

				 			wyz_get_archives_filtered();
				 			$post_ids[] = get_the_ID();

				 			$count++;

						endwhile;
					endif;
				}
				if( 0 === $count ) {
					if ( $location < 1 ) {
						WyzHelpers::wyz_info( esc_html__( 'No Businesses to show', 'wyzi-business-finder' ) );
					} else {
						WyzHelpers::wyz_info( esc_html__( 'No Businesses match your search', 'wyzi-business-finder' ) );
					}
				} else {
					if ( 2 == $template_type ) {
						if ( function_exists( 'wyz_get_option' ) ) {
							$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
							if ( '' != $grid_alias )
								echo do_shortcode( '[ess_grid alias="' . $grid_alias .'" posts='.implode(',',$post_ids).']' );
						}
					}
				}

				do_action( 'wyz_after_business_search_display' );

				if ( function_exists( 'wyz_pagination' ) ) wyz_pagination();
				
				if ( '' != $temp_query ) {
					$wp_query = $temp_query;
				}
				wp_reset_postdata();?>
			</div>

			<!-- Right sidebar. -->
			<?php if ( 'right-sidebar' === wyz_get_option( 'sidebar-layout' ) ) :?>
				
				<div class="sidebar-container<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-3 col-md-4 col-xs-12<?php } else { ?>col-xs-4 <?php } ?>">
						
					<?php if ( is_active_sidebar( 'wyz-business-listing-sb' ) ) : ?>

						<div class="widget-area sidebar-widget-area" role="complementary">
							
							<?php dynamic_sidebar( 'wyz-business-listing-sb' ); ?>
						
						</div>

					<?php endif; ?>

				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php 
function wyz_get_archives_filtered() {
	global $days_get;
	global $count;
	global $template_type;
	if ( ! empty( $days_get ) ) {
		$keys = array( 'wyz_open_close_monday', 'wyz_open_close_tuesday', 'wyz_open_close_wednesday', 'wyz_open_close_thursday'
				, 'wyz_open_close_friday', 'wyz_open_close_saturday', 'wyz_open_close_sunday');
		$ds = array( 'mon','tue','wed','thur','fri','sat','sun' );
		$day = '';
		$len = count( $days_get );
		$c = 0;
		for ( $i = 0; $i<7; $i++ ) {
			if ( in_array( $ds[ $i ], $days_get ) ) {
				$day = get_post_meta( get_the_ID(), $keys[ $i ], true );
				if ( ! empty( $day[0] ) ) {
					$c++;
				}
			}
		}
		if( $c == $len ){
			if ( 1 == $template_type )
				echo WyzBusinessPost::wyz_create_business();
			$count++;
			wyz_add_archives_js();
		}
	} else {
		if ( 1 == $template_type )
			echo WyzBusinessPost::wyz_create_business();
		$count++;
		wyz_add_archives_js();
	}	
}

function wyz_add_archives_js() {
	global $js_data;
	global $template_type;
	$temp_loc = get_post_meta( get_the_ID(), 'wyz_business_location', true );

	$def_marker_coor = array('latitude' => get_option( 'wyz_businesses_default_lat', 0 ), 'longitude' => get_option( 'wyz_businesses_default_lon', 0 ) );
	if ( empty( $temp_loc ) || '' == $temp_loc['latitude'] || '' == $temp_loc['longitude'] ) {
		$temp_loc = array(
			'latitude' => $def_marker_coor['latitude'],
			'longitude' => $def_marker_coor['longitude'],
		);
	}

	array_push( $js_data['GPSLocations'], $temp_loc );

	array_push( $js_data['businessNames'], get_the_title() );

	array_push( $js_data['businessPermalinks'], esc_url( get_the_permalink() ) );

	if ( has_post_thumbnail() ) {
		array_push( $js_data['businessLogoes'], get_the_post_thumbnail( get_the_ID(), 'medium', array( 'class' => 'business-logo-marker' ) ) );
	} else {
		array_push( $js_data['businessLogoes'], '' );
	}

	$temp_term_id = WyzHelpers::wyz_get_representative_business_category_id( get_the_ID() );

	if ( '' != $temp_term_id ) {
		$icon_meta_key = 'map_icon';
		if(2==$template_type) $icon_meta_key .= '2';
		$holder = wp_get_attachment_url( get_term_meta( $temp_term_id, $icon_meta_key, true ) );
		$col = get_term_meta( $temp_term_id, 'wyz_business_cat_bg_color', true );
		array_push( $js_data['businessCategories'], intval( $temp_term_id ) );
		array_push( $js_data['businessCategoriesColors'], $col );

		if ( ! isset( $holder ) || false == $holder || '' == $holder ) {
			array_push( $js_data['markersWithIcons'], '' );
			array_push( $js_data['businessCategories'], -1 );
			array_push( $js_data['businessCategoriesColors'], '' );
		} else {
			array_push( $js_data['markersWithIcons'], $holder );
		}
	} else {
		array_push( $js_data['markersWithIcons'], '' );
		array_push( $js_data['businessCategories'], -1 );
		array_push( $js_data['businessCategoriesColors'], '' );
	}
}
?>
<?php get_footer();?>
