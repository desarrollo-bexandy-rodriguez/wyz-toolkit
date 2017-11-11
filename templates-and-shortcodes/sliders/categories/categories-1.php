<?php
/**
 * WYZI Categories Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZICategoriesSlider' ) ) {

	class WYZICategoriesSlider {

		private $cat_attr;

		public function __construct( $attr ) {
			$this->cat_attr = $attr;
			add_action( 'wp_footer', array( &$this, 'include_cat_script') , 4 );
			$this->setup_categories();
		}

		private function setup_categories() {
			$business_taxonomy = array();
			$taxonomy = 'wyz_business_category';
			$temp_link;
			$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
			$count = 0;
			$length = count( $tax_terms );

			for ( $i = 0; $i < $length; $i++ ) {
				if ( ! isset( $tax_terms[ $i ] ) )
					continue;
				$current = $tax_terms[ $i ];
				$tax_id = intval( $current->term_id );

				if ( 0 != $current->parent ) {
					continue;
				}

				$children = array();
				$tx_all_children = array();
				$tax = array();
				$tax['name'] = $current->name;
				$tax['has_children'] = false;
				$child_count = 0;
				$total_child_count = 0;
				$tax['color'] = get_term_meta( $tax_id, 'wyz_business_cat_bg_color', true );
				$temp_link = get_term_link( $current, $taxonomy );
				$tax['link'] = ( ! is_wp_error( $temp_link ) ? $temp_link : '' );
				$url = wp_get_attachment_url( get_term_meta( $tax_id, 'wyz_business_icon_upload', true ) );
				$tax['img'] = ( false != $url ? $url : '' );
				$tax['view_all'] = false;

				$length_2 = count( $tax_terms );

				for ( $j = 0; $j<$length_2; $j++ ) {
					if ( ! isset( $tax_terms[ $j ] ) ) continue;
					if ( $tax_terms[ $j ]->parent == $current->term_id ) {
						$child = $tax_terms[ $j ];
						$tax['has_children'] = true;
						$total_child_count++;
						$child_count++;
						if ( $child_count >4) {
							$tax['view_all'] = true;
							continue;
						}
						$bus_count = ( ( ! isset( $this->cat_attr['hide_count'] ) || !$this->cat_attr['hide_count'] ) ? $child->count : 0 );
						$temp_child = array();
						$temp_link = get_term_link( $child->term_id, $taxonomy );
						$tx_all_children[] = $child->name;
						$temp_child['name'] = $child->name;
						$temp_child['bus_count'] = $bus_count;
						$temp_child['link'] = ( ! is_wp_error( $temp_link ) ? $temp_link : '' );
						$children[] = $temp_child;

					}
				}
				$tax['children'] = $children;
				$tax['all_children'] = $tx_all_children;
				$tax['child_count'] = $total_child_count;
				$business_taxonomy[] = $tax;
				$count++;
			}

			$cat_slide_data = array(
				'taxs' => $business_taxonomy,
				'nav' => $this->cat_attr['nav'],
				'autoplay' => $this->cat_attr['autoplay'],
				'autoplay_timeout' => $this->cat_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->cat_attr['loop'] : false,
				'rows' => $this->cat_attr['rows'],
				'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
				'columns' => $this->cat_attr['columns'],
			);
			wp_localize_script( 'wyz_categories_script', 'catSlide', $cat_slide_data );

		}


		public function include_cat_script() {
			wp_enqueue_script( 'wyz_categories_script' );
		}

		public function the_categories_slider() {
			ob_start();
			?>
			<div class="category-search-area margin-bottom-50">
				<div class="row">
					<!-- Section Title & Search -->
					<div class="section-title section-title-search col-xs-12 margin-bottom-100">
						<h1><?php echo esc_html( $this->cat_attr['cat_slider_ttl'] );?></h1>
						<div class="wyz-search-form float-right">
							<input id="categories-search-text" type="text" placeholder="<?php esc_html_e( 'categories', 'wyzi-business-finder' );?>" name="q" />
							<button id="categories-search-submit" class="wyz-primary-color wyz-prim-color"><i class="fa fa-search"></i></button>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="category-search-slider">
						</div>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
	}
}