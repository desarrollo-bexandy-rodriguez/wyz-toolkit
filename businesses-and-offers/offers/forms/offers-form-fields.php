<?php
/**
 * Offer creation frontend form fields.
 *
 * @package wyz
 */

// Title.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Title', 'wyzi-business-finder' ),
	'id'   => $prefix . 'offers_title',
	'type' => 'text',
	'default_cb' => 'wyz_set_default_offer_title',
	'attributes' => array( 'required' => 'required' ),
) );


// Excerpt.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Excerpt', 'wyzi-business-finder' ),
	'id'   => $prefix . 'offers_excerpt',
	'type' => 'text_medium',
	'attributes' => array( 'required' => 'required' ),
) );


// Description.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Description', 'wyzi-business-finder' ),
	'id'   => $prefix . 'offers_description',
	'type' => 'wysiwyg',
	'attributes' => array(/*
		'required' => 'required',*/
		'class' => 'wyz-input',
	),
	'options' => array(
		'media_buttons' => true,
	),
) );

// Image.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Main Image', 'wyzi-business-finder' ),
	'id'   => $prefix . 'offers_image',
	'type' => 'file',
	'options' => array(
		'url' => false,
	),
	'attributes' => array( 'required' => 'required' ),
	'text'    => array(
		'add_upload_file_text' => esc_html__( 'Add Or Upload File', 'wyzi-business-finder' ),
	),
) );


// Discount.
$wyz_cmb_offers->add_field( array(
	'name'        => esc_html__( 'Discount percentage', 'wyzi-business-finder' ),
	'id'          => $prefix . 'offers_discount',
	'type'        => 'own_slider',
	'min'         => '0',
	'max'         => '100',
	'default_cb'     => '50', // Start value.
	'value_label' => '%',
	'attributes' => array( 'required' => 'required' ),
) );

// Category.
$wyz_cmb_offers->add_field( array(
	'name'    => sprintf( esc_html__( '%s Categories', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),
	'id'      => $prefix . 'offers_category_check',
	'type'    => 'select',
	'default' => wyz_get_default_offers_categories(),
	'options' => wyz_get_offers_term_options(),
	'attributes' => array(
		'required' => 'required',
		'class' => 'wyz-select',
	),
) );

if ( isset( $_GET[ WyzQueryVars::BusinessId ] ) ) {
	//Owner Business id
	$wyz_cmb_offers->add_field( array(
		'id'   => $prefix . 'business_id',
		'type' => 'hidden',
		'attributes' => array(
			'value' => $_GET[ WyzQueryVars::BusinessId ],
		)
	) );
}

/**
 * Get all availablr offer categories.
 */
function wyz_get_offers_term_options() {
	$offer_taxonomy = array();
	$offer_taxonomy['0'] = '';
	$temp_name;
	$temp_slug;
	$taxonomy = 'offer-categories';
	$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
	foreach ( $tax_terms as $obj ) {
		foreach ( $obj as $key => $value ) {
			if ( 'name' === $key ) {
				$temp_name = $value;
			} elseif ( 'slug' === $key ) {
				$temp_slug = $value;
			}
		}
		if ( isset( $temp_name ) && isset( $temp_slug ) ) {
			$offer_taxonomy[ $temp_slug ] = $temp_name;
		}
	}
	return $offer_taxonomy;
}

/**
 * Get current offer category if exists.
 */
function wyz_get_default_offers_categories() {
	if ( !isset($_GET[WyzQueryVars::EditOffer]) ) {
		return '';
	}
	$terms = get_the_terms( $_GET[ WyzQueryVars::EditOffer ], 'offer-categories' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		if ( is_array( $terms ) ) {
			return $terms[0]->slug;
		}
		return $terms->slug;
	}
	return '';
}


$form_ids = array(
	$prefix . 'offers_title',
	$prefix . 'offers_excerpt',
	$prefix . 'offers_description',
	$prefix . 'offers_icon',
	$prefix . 'offers_image',
	$prefix . 'offers_discount',
);

/**
 * Override the default business form 'Submit' button.
 *
 * @param string  $form_format the default form format.
 * @param integer $object_id the form's id.
 * @param object  $cmb the current cmb form object.
 */
function wyz_offers_modify_cmb2_metabox_form_format( $form_format, $object_id, $cmb ) {
	if ( 'wyz_frontend_offers' === $cmb->cmb_id  ) {
		global $WYZ_USER_ACCOUNT_TYPE;
		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditOffer && get_the_ID() == $_GET[ WyzQueryVars::EditOffer ] ) {
			$lbl = sprintf( esc_html__( 'Update %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );
		} else {
			$lbl = sprintf( esc_html__( 'Create %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );
		}
		return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="submit-wrap"><input type="submit" name="submit-cmb" value="' . $lbl . '" class="wyz-prim-color-txt-hover wyz-primary-color wyz-prim-color btn-square button-primary"></div></form>';

	}

	return $form_format;
}
add_filter( 'cmb2_get_metabox_form_format', 'wyz_offers_modify_cmb2_metabox_form_format', 10, 3 );

foreach ( $form_ids as $form_id ) {
	$p = filter_input( INPUT_POST, $form_id );
	if ( $p ) {
		$wyz_cmb_offers->get_field( $form_id )->args['attributes']['value'] = $p;
	}
}

$d = filter_input( INPUT_POST, $prefix . 'offers_discount' );
if ( $d ) {

	$wyz_cmb_offers->get_field( $prefix . 'offers_discount' )->args['default_cb'] = $d;
}

$c = filter_input( INPUT_POST, $prefix . 'offers_category_check' );
if ( $c ) {
	$wyz_cmb_offers->get_field( $prefix . 'offers_category_check' )->args['default_cb'] = $c;
}
?>
