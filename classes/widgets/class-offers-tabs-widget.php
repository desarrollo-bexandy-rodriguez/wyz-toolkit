<?php
/**
 * Widget API: Wyz_Offers_Tabs_Widget class
 *
 * @package wyz
 * @since 1.0
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die('-1');
}
	
	
add_action( 'widgets_init', function(){
	register_widget( 'Wyz_Offers_Tabs_Widget' );
});
/**
 * Adds Wyz_Offers_Tabs_Widget widget.
 */
class Wyz_Offers_Tabs_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public static $count_ids;
	function __construct() {
		$off_cpt = ucwords( esc_html( get_option( 'wyz_offers_old_single_permalink' ) ) );
		$bus_cpt = ucwords( esc_html( get_option( 'wyz_business_old_single_permalink' ) ) );
		self::$count_ids =0;
		parent::__construct(
			'Wyz_Offers_Tabs_Widget',
			sprintf( esc_html__('%s Tabs Widget', 'wyzi-business-finder'),$off_cpt ),
			array( 'description' => sprintf( esc_html__( 'Displays a %s\'s related %s and all available %s', 'wyzi-business-finder' ), $bus_cpt, $off_cpt, $off_cpt ), )
		);
	}
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		self::$count_ids++;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( isset( $instance['max_offers_count'] ) && is_numeric( $instance['max_offers_count'] ) && $instance['max_offers_count'] > 0 ) {
			$max_count = $instance['max_offers_count'];
		} else {
			$max_count = 3;
		}

		$output = '<div class="tab-sidebar"><ul class="sidebar-tab-list fix">';

		if ( is_singular( 'wyz_business' ) ) {
			$output .= '<li class="active"><a href="#related-offers-' . self::$count_ids . '" data-toggle="tab">' . esc_html__( 'related', 'wyzi-business-finder') . '</a></li>';
			$output .= '<li><a href="#recent-offers-' . self::$count_ids . '" data-toggle="tab">' . esc_html__( 'recent', 'wyzi-business-finder') . '</a></li></ul>';
		} else {
			$output .= '<li class="active single"><a href="#recent-offers-' . self::$count_ids . '" data-toggle="tab">' . esc_html__( 'recent', 'wyzi-business-finder') . '</a></li></ul>';
		}

		$output .= '<div class="tab-content sidebar-tab-content fix">';

		if ( is_singular( 'wyz_business' ) ) {

			$queried_business = get_queried_object();
			if ( isset( $queried_business->ID ) ) {
				
				$bus_id = $queried_business->ID;

				$output .= '<div class="tab-pane active" id="related-offers-' . self::$count_ids . '">';
				$related_args = array(
					'posts_per_page' => -1,
					'post_type' => 'wyz_offers',
					'orderby' => 'post_date',
					'order' => 'desc',
					'post_status' => 'publish',
					'post_count' => $max_count
				);

				$related_offers =  get_posts( $related_args );

				$count = 0;

				foreach ( $related_offers as $post ) {

					if ( get_post_meta( $post->ID, 'business_id', true ) != $bus_id ) {
						continue;
					}

					if ( ++$count > $max_count ) {
						break;
					}

					$icon_id = get_post_meta( $post->ID, 'wyz_offers_image_id', true );
					$output .= '<div class="sin-tab-sidebar-post fix">';
					$output .= '<a href="' . esc_url( get_the_permalink( $post->ID ) ) . '" class="image">' . wp_get_attachment_image( $icon_id, 'thumbnail', true ) . '</a>';
					$output .= '<div class="content fix">';
					$output .= '<a href="' . esc_url( get_the_permalink( $post->ID ) ) . '">' . get_the_title( $post->ID ) . '</a>';
					$output .= '<span>' . get_the_time( get_option( 'date_format' ), $post->ID ) . '</span></div></div>';
				}
			}

			if ( ! isset( $count ) || 0 === $count ) {
				$output .= '<p class="no-rel-offers">' . sprintf( esc_html__( 'No Related %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p>';
			}
			$output .= '</div>';
		}

		$recent_args = array(
			'posts_per_page' => -1,
			'post_type' => 'wyz_offers',
			'orderby' => 'post_date',
			'order' => 'desc',
			'post_status' => 'publish',
			'post_count' => $max_count
		);

		$recent_offers =  get_posts( $recent_args );

		$output .= '<div class="tab-pane' . ( is_singular( 'wyz_business') ? '' : ' active' ) . '" id="recent-offers-' . self::$count_ids . '">';

		$count = 1;

		foreach ( $recent_offers as $post ) {

			if ( $count++ > $max_count ) {
				break;
			}

			$icon_id = get_post_meta( $post->ID, 'wyz_offers_image_id', true );
			$output .= '<div class="sin-tab-sidebar-post fix">';
			$output .= '<a href="' . esc_url( get_the_permalink( $post->ID ) ) . '" class="image">' . wp_get_attachment_image( $icon_id, 'thumbnail', true ) . '</a>';
			$output .= '<div class="content fix">';
			$output .= '<a href="' . esc_url( get_the_permalink( $post->ID ) ) . '">' . get_the_title( $post->ID ) . '</a>';
			$output .= '<span>' . get_the_time( get_option( 'date_format' ), $post->ID ) . '</span></div></div>';
		}
		$output .= '</div></div></div>';

		if ( array_key_exists( 'before_widget', $args ) ) {
			echo $args['before_widget'];
		}

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $output;

		if ( array_key_exists( 'after_widget', $args ) ) {
			echo $args['after_widget'];
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		if ( isset( $instance[ 'max_offers_count' ] ) && 0 < $instance[ 'max_offers_count' ] && 6 > $instance[ 'max_offers_count' ] ) {
			$max_count = $instance[ 'max_offers_count' ];
		}
		else {
			$max_count = 3;
		}
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'max_offers_count' ); ?>"><?php esc_html_e( 'Maximum number of offers to display:', 'wyzi-business-finder' ); ?></label> 
			
			<select id="<?php echo $this->get_field_id( 'max_offers_count' ); ?>" name="<?php echo $this->get_field_name( 'max_offers_count' ); ?>">
				<?php for ( $i = 1; $i < 6; $i++ ) {
					echo "<option value=\"$i\" " . ( $max_count == $i ? 'selected' : '' ) . ">$i</option>";
				}?>
			</select>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['max_offers_count'] = ( ( ! empty( $new_instance['max_offers_count'] ) ) && 0 < $new_instance['max_offers_count'] && 6 > $new_instance['max_offers_count'] ) ? strip_tags( $new_instance['max_offers_count'] ) : '3';
		return $instance;
	}
}