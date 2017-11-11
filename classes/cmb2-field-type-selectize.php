<?php
/**
 * Add tags field to business cmb2 fields
 *
 * @package wyz
 */

define( 'WYZ_SELECTIZE_URL', plugin_dir_url( __FILE__ ) );

define( 'WYZ_SELECTIZE_VERSION', '1.0.1' );

// Render tags.
function wyz_tags_render( $field, $value, $object_id, $object_type, $field_type ) { 
	wyz_tags_enqueue();
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
add_filter( 'cmb2_render_tags', 'wyz_tags_render', 10, 5 );
add_filter( 'cmb2_render_tags_sortable', 'wyz_tags_render', 10, 5 );




// Render categories.
function wyz_categories_render( $field, $value, $object_id, $object_type, $field_type ) {

	$wyz_business_form_data = get_option( 'wyz_business_form_builder_data', array() );
	$parents_selectable = false;
	$separate_categories = true;
	foreach ($wyz_business_form_data as $w) {
		if( $w['type'] == 'category' ){
			$parents_selectable = ( isset( $w['parentSelectable'] ) && $w['parentSelectable'] ) ;
			if ( isset( $w['separateCategories'] ) )
				$separate_categories = $w['separateCategories'];
			break;
		}
	}
	wyz_tags_enqueue();
	$business_taxonomy = array();
	$temp_name;
	$temp_slug;
	$taxonomy = 'wyz_business_category';
	$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
	$tmp_cats = get_the_terms( $object_id, 'wyz_business_category' );
	$post_cats = array();
	$js_data = array();
	if ( ! $tmp_cats || is_wp_error( $tmp_cats ) ) {
		$tmp_cats = array();
	}
	foreach ( $tmp_cats as $cat ) {
		array_push( $post_cats, $cat->slug );
	}

	if ( $parents_selectable )
		wyz_categories_render_parents_selectable( $tax_terms, $post_cats, $field_type, $separate_categories );
	else
		wyz_categories_render_parents_non_selectable( $tax_terms, $post_cats, $field_type, $separate_categories );
}

function wyz_categories_render_parents_selectable( $tax_terms, $post_cats, $field_type, $separate_categories ) {
	$taxonomy = 'wyz_business_category';
?>
	<select multiple name="wyz_business_categories[]" id="wyz-cat-select" data-selectator-keep-open="true">
		<?php
		foreach ( $tax_terms as $obj ) {
			if ( 0 !== $obj->parent ) {
				continue;
			}
			if ( $separate_categories )
				echo '<optgroup label="'. $obj->name .'">';
			$id = intval( $obj->term_id );
			$js_data[ $id ] = 0;
			echo '<option value="' . esc_attr( $obj->slug ) . '" data-data=\'{"id": ' . $id  . ', "class" : "parent"}\' ';
			if ( in_array( $obj->slug, $post_cats ) ) {
				echo 'selected="selected"';
				$js_data[ $id ]++;
			}
			echo  '>'. $obj->name . '</option>';
			$children = get_term_children( $id, $taxonomy );
			if ( ! empty( $children ) ) {
				foreach ( $children as $child ) {
					$child = get_term_by( 'id', $child, $taxonomy );
					echo '<option class="child" value="' . esc_attr( $child->slug ) . '" data-data=\'{"id": ' . $id  . ', "class" : "child"}\' ';
					if ( in_array( $child->slug, $post_cats ) ) {
						echo 'selected="selected"';
						$js_data[ $id ]++;
					}
					echo  '>'. $child->name . '</option>';

				}
			}
			if ( $separate_categories )
				echo '</optgroup>';
		}
		?>
	</select>

	<?php echo $field_type->_desc( true ); 
	wp_localize_script( 'wyz_tags_script', 'catIconCount', $js_data );
	?>
	<div class="tagchecklist hide-if-no-js"></div>

<?php
}

function wyz_categories_render_parents_non_selectable( $tax_terms, $post_cats, $field_type, $separate_categories ) {
	$taxonomy = 'wyz_business_category';
?>
	<select multiple name="wyz_business_categories[]" id="wyz-cat-select" data-selectator-keep-open="true">
		<?php
		foreach ( $tax_terms as $obj ) {
			if ( 0 !== $obj->parent ) {
				continue;
			}
			if ( $separate_categories )
				echo '<optgroup label="'. $obj->name .'">';
			$id = intval( $obj->term_id );
			$js_data[ $id ] = 0;
			$children = get_term_children( $id, $taxonomy );
			if ( ! empty( $children ) ) {
				foreach ( $children as $child ) {
					$child = get_term_by( 'id', $child, $taxonomy );
					echo '<option value="' . esc_attr( $child->slug ) . '" data-data=\'{"id": ' . $id  . ', "class" : "child"}\' ';
					if ( in_array( $child->slug, $post_cats ) ) {
						echo 'selected="selected"';
						$js_data[ $id ]++;
					}
					echo  '>'. $child->name . '</option>';

				}
			}
			if ( $separate_categories )
				echo '</optgroup>';	
		}
		?>
	</select>

	<?php echo $field_type->_desc( true ); 
	wp_localize_script( 'wyz_tags_script', 'catIconCount', $js_data );
	?>
	<div class="tagchecklist hide-if-no-js"></div>

<?php
}

add_filter( 'cmb2_render_cats', 'wyz_categories_render', 10, 5 );
add_filter( 'cmb2_render_cats_sortable', 'wyz_categories_render', 10, 5 );



function wyz_categories_icons_render( $field, $value, $object_id, $object_type, $field_type ) {
	wyz_tags_enqueue();

	$taxonomies = array();
	$taxonomy = 'wyz_business_category';
	$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

	$object_terms = get_the_terms( $object_id, 'wyz_business_category' );
	$post_cats = array();
	if ( ! $object_terms || is_wp_error( $object_terms ) ) {
		$object_terms = array();
	}
	foreach ( $object_terms as $cat ) {
		if ( $cat->parent ) {
			$post_cats[ $cat->parent ] = true;
		}
	}

	$length = count( $tax_terms );
	$js_data = array();
	$len = 0;
	for ( $i = 0; $i < $length; $i++ ) {
		if ( ! isset( $tax_terms[ $i ] ) ) {
			continue;
		}
		$temp_tax = array();
		$obj = $tax_terms[ $i ];
		if ( 0 == $obj->parent ) {
			$temp_icon = wp_get_attachment_url( get_term_meta( $obj->term_id, 'wyz_business_icon_upload', true ) );
			if ( '' == $temp_icon ) {
				continue;
			}
			$len++;
			$temp_tax['id'] = $obj->term_id;
			$temp_tax['icon'] = $temp_icon;
			$temp_tax['bg'] = get_term_meta( $obj->term_id, 'wyz_business_cat_bg_color', true );
			$taxonomies[] = $temp_tax;
			unset( $tax_terms[ $i ] );
		}
	}

	$cat_icon_id = get_post_meta( $object_id, 'wyz_business_category_icon', true );
	?>

	<select name="wyz_business_category_icon" id="wyz-cat-icon-select" class="wyz-input wyz-select">
		<option value=""></option>
		<?php for ( $i = 0; $i < $len; $i++ ) {
			$img = $taxonomies[ $i ]['icon'];
			$bgc = $taxonomies[ $i ]['bg'];
			$js_data[ $taxonomies[ $i ]['id'] ] =  '<option ' . ( $taxonomies[ $i ]['id'] == $cat_icon_id ? 'selected ' : '' ) . 'value="' . $taxonomies[ $i ]['id'] . '" ' . ( false != $img ? 'data-left="<div class=\'cat-prnt-icn\' ' . ( '' != $bgc ? 'style=\'background-color:' . esc_attr( $bgc ) . ';\' ' : '' ) .'><img src=\'' . $img . '\'/></div>"' : '') .'></option>';
			if ( isset( $post_cats[ $taxonomies[ $i ]['id'] ] ) ){
				echo $js_data[ $taxonomies[ $i ]['id'] ];
			}
		}?>

	</select>

	<?php
	wp_localize_script( 'wyz_tags_script', 'catIconOptions', $js_data );
}

add_filter( 'cmb2_render_cats_icons', 'wyz_categories_icons_render', 10, 5 );

static $script_enqueued = false;
/**
 * Enqueue scripts.
 */
function wyz_tags_enqueue() {
	global $script_enqueued;
	if ( $script_enqueued ) return;
	$script_enqueued = true;
	wp_enqueue_script( 'wyz_tags_script', WYZ_SELECTIZE_URL . 'js/tags.js', array( 'jquery', 'jquery-ui-sortable' ), WYZ_SELECTIZE_VERSION );

	// Lets check if Category icon is Included and pass to JS
	$wyz_business_form_data = get_option( 'wyz_business_form_builder_data', array() );
	$cat_icon_exits = false;
	foreach ( $wyz_business_form_data as $key => $value ) {
		if ( 'categoryIcon' == $value['type'] ){
			$cat_icon_exits = true;
			break;
		}
	}
	$categories_info = array(
        	'catIconExits' => $cat_icon_exits
    	);
    wp_localize_script( 'wyz_tags_script', 'categoriesInfo', $categories_info );
    
	wp_enqueue_script( 'jQuery_tags_select', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), false, true );
	wp_enqueue_style( 'wyz_tags_style', WYZ_SELECTIZE_URL . 'css/tags.css', array(), WYZ_SELECTIZE_VERSION );
	wp_enqueue_style( 'jQuery_tags_select_style', plugin_dir_url( __FILE__ ) . 'css/selectize.default.css' );
}
?>
