<?php
/**
 * WYZI Locations Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZILocationsSlider' ) ) {

	class WYZILocationsSlider {

		private $loc_attr;

		public function __construct( $attr ) {
			$this->loc_attr = $attr;
			add_action( 'wp_footer', array( &$this, 'include_loc_script') , 6 );
			$this->setup_locations();
		}

		private function setup_locations() {
			$links = array();
			$names = array();
			$images = array();

			$qry_args = array(
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
			);

			$all_posts = new WP_Query( $qry_args );
			$business_permalink = get_post_type_archive_link( 'wyz_business' );

			$bus_count = array();
			$c;
			$count = 0;

			while ( $all_posts->have_posts() ) :
				$all_posts->the_post();

				$args = array(
					'post_type' => 'wyz_business',
					'posts_per_page' => '-1',
					'post_status' => 'publish',
					'fields' => 'ids',
					'meta_query' => array(
						array(
							'key' => 'wyz_business_country',
							'value' => get_the_ID(),
						),
					),
				);

				if ( $this->loc_attr['linking'] ) {
					$links[] = get_the_permalink();
				} else {
					$links[] = $business_permalink . '?location=' . get_the_ID();
				}

				$names[] = get_the_title();
				if (  has_post_thumbnail() ) {
					$thumb_id = get_post_thumbnail_id();
					$thumb_url = wp_get_attachment_image_src( $thumb_id,'medium', true );
					$tmp_img = $thumb_url[0];
				} else {
					$tmp_img = WyzHelpers::get_default_image( 'location' );
				}

				$images[] = $tmp_img;

				$query = new WP_Query( $args );
				$c = $query->found_posts;
				$bus_count[] = $c;
				$count++;
			endwhile;

			$loc_slide_data = array(
				'names' => $names,
				'images' => $images,
				'links' => $links,
				'nav' => $this->loc_attr['nav'],
				'autoplay' => $this->loc_attr['autoplay'],
				'autoplay_timeout' => $this->loc_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->loc_attr['loop'] : false,
				'busCount' => ( isset( $bus_count ) ? $bus_count : false ),
			);
			wp_localize_script( 'wyz_locations_script', 'locSlide', $loc_slide_data );
		}


		public function the_locations_slider() {
			ob_start();
			?>
			<div class="location-search-area margin-bottom-50">
				<div class="row">
					<!-- Section Title & Search -->
					<div class="section-title section-title-search col-xs-12 margin-bottom-100">
						<h1><?php echo esc_html( $this->loc_attr['loc_slider_ttl'] );?></h1>
						<div class="wyz-search-form float-right">
							<input id="locations-search-text" type="text" placeholder="<?php echo LOCATION_CPT;?>" />
							<button id="locations-search-submit" class="wyz-primary-color"><i class="fa fa-search"></i></button>
						</div>
					</div>
					<div class="col-xs-12">
						<!-- Location Search Slider -->
						<div class="location-search-slider"></div>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

		public function include_loc_script() {
			wp_enqueue_script( 'wyz_locations_script' );
		}
	}
}
?>