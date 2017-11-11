<?php
/**
 * Widget API: Wyz_Business_Filter_Widget class
 *
 * @package wyz
 * @since 1.0
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	die('-1');
}
	

add_action( 'widgets_init', function(){
	register_widget( 'Wyz_Business_Filter_Widget' );
});
/**
 * Adds Wyz_Business_Filter_Widget widget.
 */
class Wyz_Business_Filter_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$bus_cpt = ucwords( esc_html( get_option( 'wyz_business_old_single_permalink' ) ) );
		parent::__construct(
			'Wyz_Business_Filter_Widget',
			sprintf( esc_html__('%s Filter Widget', 'wyzi-business-finder'), $bus_cpt ),
			array( 'description' => sprintf( esc_html__( 'Displays a filter in the %s listing page. Only use on the listing sidebar.', 'wyzi-business-finder' ), $bus_cpt ), )
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

		if( ! is_post_type_archive('wyz_business') && ! is_tax( 'wyz_business_tag' ) && ! is_tax( 'wyz_business_category' ) ) return;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( array_key_exists( 'before_widget', $args ) ) {
			echo $args['before_widget'];
		}

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}?>

		<div id="sidebar-filters"><?php WyzHelpers::wyz_get_business_filters();?></div>

		<?php 
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

		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Refine Your Search') );
		$title = $instance['title'];
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
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
		return $instance;
	}
}