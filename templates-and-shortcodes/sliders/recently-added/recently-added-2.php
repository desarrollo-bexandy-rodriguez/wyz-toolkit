<?php
/**
 * WYZI Recently Added Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZIRecentlyAddedSlider' ) ) {

	class WYZIRecentlyAddedSlider {

		private $slider_attr;

		public function __construct( $attr ) {
			$this->slider_attr = $attr;
			//add_action( 'wp_footer', array( &$this, 'include_slider_script') , 6 );
		}

		public function the_rec_added_slider() {
			$args = array(
				'posts_per_page' => $this->slider_attr['count'],
				'offset' => 0,
				'orderby' => 'date',
				'order' => 'DESC',
				'fields' => 'ids',
				'post_type' => 'wyz_business',
				'post_status' => 'publish',
			);
			$posts = new WP_Query( $args ); 
			$count = 0;?>

			<div class="recently-added-area margin-bottom-50 mb-50">
				<div class="row">
					<?php if ( '' != $this->slider_attr['rec_added_slider_ttl'] ) {?>
					<div class="section-title mb-40">
						<h3><?php echo esc_html( $this->slider_attr['rec_added_slider_ttl'] );?></h3>
					</div>
					<?php };?>
					<div class="col-xs-12">
						<!-- Recently Added Slider -->
						<div class="recently-added-slider">
						<?php
						if ( function_exists( 'wyz_get_option' ) ) {
							$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
							if ( '' != $grid_alias )
								echo do_shortcode( '[ess_grid alias="' . $grid_alias .'" posts='.implode(',',$posts->posts).']' );
						}
						?>
						</div>
					</div>
				</div>
			</div>
			<?php $rec_add_slide_data = array(
				'nav' => $this->slider_attr['nav'],
				'autoplay' => $this->slider_attr['autoplay'],
				'autoplay_timeout' => $this->slider_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->slider_attr['loop'] : false,
			);
			wp_localize_script( 'wyz_rec_added_script', 'recAddSlide', $rec_add_slide_data );
		}

		public function include_slider_script() {
			wp_enqueue_script( 'wyz_rec_added_script' );
		}
	}
}
