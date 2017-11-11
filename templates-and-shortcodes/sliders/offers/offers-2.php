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

if( ! class_exists( 'WYZIOffersSlider' ) ) {

	class WYZIOffersSlider {

		private $slider_attr;
		private $all_posts;

		public function __construct( $attr ) {
			$this->slider_attr = $attr;
			add_action( 'wp_footer', array( &$this, 'include_slider_script') , 6 );
			$this->setup_offers();
		}

		public function setup_offers() {
			$qry_args = array(
				'post_status' => 'publish',
				'post_type' => 'wyz_offers',
				'posts_per_page' => - 1,
			);

			$this->all_posts = new WP_Query( $qry_args );

			$offer_slide_data = array(
				'nav' => $this->slider_attr['nav'],
				'autoplay' => $this->slider_attr['autoplay'],
				'autoplay_timeout' => $this->slider_attr['autoplay_timeout'],
				'loop' => $this->slider_attr['loop'],
				'autoHeight' => $this->slider_attr['autoheight'],
			);
			wp_localize_script( 'wyz_offers_script', 'offerSlide', $offer_slide_data );
		}
			

		public function the_offers_slider() {
			ob_start();
			?>

			<div class="our-offer-area mb-50 section">
				<div class="row">
					<div class="col-xs-12">
						<!-- Offer Slider -->
						<?php 
						// Only show the slider if we have more than 1 offer (bug in owl).
						if ( $this->all_posts->post_count > 1 ) { ?>
						<div class="our-offer-slider">
						<?php } else { ?>
							<div class="single-owl-carousel">
						<?php }
						if ( class_exists( 'WyzOffer' ) ) {
							while ( $this->all_posts->have_posts() ) {
								$this->all_posts->the_post();
								WyzOffer::wyz_the_offer( get_the_ID(), false );
							}
							wp_reset_postdata();
						}
						?>
						</div>
					</div>
				</div>
			</div>

			<?php 
			return ob_get_clean();
		}

		public function include_slider_script() {
			wp_enqueue_script( 'wyz_offers_script' );
		}
	}
}
