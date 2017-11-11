<?php
/**
 * Add tags field to business cmb2 fields
 *
 * @package wyz
 */

define( 'CMB_TAGS_URL', plugin_dir_url( __FILE__ ) );

define( 'CMB_TAGS_VERSION', '1.0.0' );

// Render boxes.
function cmb_tags_render( $field, $value, $object_id, $object_type, $field_type ) { 
	cmb_tags_enqueue();
	$tax_terms = get_terms( 'wyz_business_tag', array( 'hide_empty' => false ) );
	$tmp_tags = get_the_terms( $object_id, 'wyz_business_tag' );
	$post_tags = array();
	if ( ! $tmp_tags || is_wp_error( $tmp_tags ) ) {
		$tmp_tags = array();
	}
	foreach ( $tmp_tags as $tag ) {
		array_push( $post_tags, $tag->name );
	}
	?>
	<select multiple name="wyz_business_tags[]" id="wyz-tag-select" data-selectator-keep-open="true">
		<?php
		foreach ( $tax_terms as $obj ) {
			echo '<option value="' . esc_attr( $obj->name ) . '"';
			if ( in_array( $obj->name, $post_tags ) ) {
				echo 'selected="selected"';
			}
			echo  '>'. $obj->name . '</option>';
		}
		?>
	</select>

	<?php echo $field_type->_desc( true ); ?>
	<div class="tagchecklist hide-if-no-js"></div>
	<?php
}
add_filter( 'cmb2_render_tags', 'cmb_tags_render', 10, 5 );
add_filter( 'cmb2_render_tags_sortable', 'cmb_tags_render', 10, 5 );





function cmb_tags_renderr( $field, $value, $object_id, $object_type, $field_type ) { 
	cmb_tags_enqueue();
	$tax_terms = get_terms( 'wyz_business_tag', array( 'hide_empty' => false ) );
	$tmp_tags = get_the_terms( $object_id, 'wyz_business_tag' );
	$post_tags = array();
	if ( ! $tmp_tags || is_wp_error( $tmp_tags ) ) {
		$tmp_tags = array();
	}
	foreach ( $tmp_tags as $tag ) {
		array_push( $post_tags, $tag->name );
	}
	?>
	<select multiple name="wyz_business_cats[]" id="wyz-cat-select" data-selectator-keep-open="true">
		<?php
		foreach ( $tax_terms as $obj ) {
			echo '<option value="' . esc_attr( $obj->name ) . '"';
			if ( in_array( $obj->name, $post_tags ) ) {
				echo 'selected="selected"';
			}
			echo  '>'. $obj->name . '</option>';
		}
		?>
	</select>

	<?php echo $field_type->_desc( true ); ?>
	<div class="tagchecklist hide-if-no-js"></div>
	<?php
}
add_filter( 'cmb2_render_cats', 'cmb_tags_renderr', 10, 5 );
add_filter( 'cmb2_render_cats_sortable', 'cmb_tags_renderr', 10, 5 );



/**
 * Enqueue scripts.
 */
function cmb_tags_enqueue() {
	wp_enqueue_script( 'cmb_tags_script', CMB_TAGS_URL . 'js/tags.js', array( 'jquery', 'jquery-ui-sortable' ), CMB_TAGS_VERSION );
	wp_enqueue_script( 'jQuery_tags_select', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), false, true );
	wp_enqueue_style( 'cmb_tags_style', CMB_TAGS_URL . 'css/tags.css', array(), CMB_TAGS_VERSION );
	wp_enqueue_style( 'jQuery_tags_select_style', plugin_dir_url( __FILE__ ) . 'css/selectize.default.css' );
}
?>
