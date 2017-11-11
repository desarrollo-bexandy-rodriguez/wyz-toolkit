<?php
/**
 * Business creation backend form fields.
 *
 * @package wyz
 */

// Logo bg color.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Logo background color', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_logo_bg',
		'type' => 'colorpicker',
		'options' => array( 'url' => false ),
		'attributes' => array(
			'required' => 'required',
		),
	)
);

// Description.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Description', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'A small description about your business', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_excerpt',
		'type' => 'text_medium',
	)
);

// About.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'About Your Business', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'A full description about your business', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_description',
		'type' => 'wysiwyg',
	)
);

// Slogan.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Slogan', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Your business slogan', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_slogan',
		'type' => 'text_small',
	)
);


$_24_format = '24' == get_option( 'wyz_openclose_time_format_24' ) ? 'H:i' : 'h:i A';
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_monday',
		'type'        => 'group',
		'description' => esc_html__( 'Monday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),

		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_tuesday',
		'type'        => 'group',
		'description' => esc_html__( 'Tuesday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),
		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_wednesday',
		'type'        => 'group',
		'description' => esc_html__( 'Wednesday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),
		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_thursday',
		'type'        => 'group',
		'description' => esc_html__( 'Thursday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),
		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_friday',
		'type'        => 'group',
		'description' => esc_html__( 'Friday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),
		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_saturday',
		'type'        => 'group',
		'description' => esc_html__( 'Saturday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),
		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_sunday',
		'type'        => 'group',
		'description' => esc_html__( 'Sunday', 'wyzi-business-finder' ),
		'options'     => array(
			'group_title'   => '',
			'add_button'    => esc_html__( '+', 'wyzi-business-finder' ),
			'remove_button' => esc_html__( '-', 'wyzi-business-finder' ),
		),
		'fields'      => array(
			array(
				'name' => 'Open',
				'id' => 'open',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
			array(
				'name' => 'Close',
				'id' => 'close',
				'type' => 'text_time',
				'time_format' => $_24_format,
			),
	    ),
	)
);


// Address.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Address', 'wyzi-business-finder' ),
		'type' => 'title',
		'id' => $prefix . 'business_adress',
	)
);

// Bldg.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Bldg', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_bldg',
		'type' => 'text_small',
	)
);

// Street.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Street', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_street',
		'type' => 'text_small',
	)
);

// City.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'City', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_city',
		'type' => 'text_small',
	)
);

// Country.
$wyz_cmb_businesses->add_field(
	array(
		'name' => LOCATION_CPT,
		'id' => $prefix . 'business_country',
		'type' => 'select',
		'default_cb' => wyz_get_default_business_locations(),
		'options' => WyzHelpers::get_businesses_locations_options(),
	)
);

// Additional address line.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Addition Address Line', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_addition_address_line',
		'type' => 'text_small',
	)
);

// Phone number1.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Phone Number 1', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_phone1',
		'type' => 'text_small',
	)
);

// Phone number2.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Phone Number 2', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_phone2',
		'type' => 'text_small',
	)
);

// Email 1.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Email 1', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_email1',
		'type' => 'text_email',
	)
);

// Email 2.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Email 2', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_email2',
		'type' => 'text_email',
	)
);

// Business website.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Business website', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_website',
		'type' => 'text_url',
	)
);

// Facebook.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Facebook Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_facebook',
		'type' => 'text_medium',
	)
);

// Twitter.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Twitter Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_twitter',
		'type' => 'text_medium',
	)
);

// Linkedin.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Linkedin Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_linkedin',
		'type' => 'text_medium',
	)
);

// Google plus.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Google Plus Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_google_plus',
		'type' => 'text_medium',
	)
);

// Youtube.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Youtube Channel Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_youtube',
		'type' => 'text_medium',
	)
);

// Instagram.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Instagram Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_instagram',
		'type' => 'text_medium',
	)
);

// Flicker.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Flicker Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_flicker',
		'type' => 'text_medium',
	)
);

// Pinterest.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Pinterest Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_pinterest',
		'type' => 'text_medium',
	)
);



$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Header Image', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_header_image',
		'type' => 'file',
		'options' => array( 'url' => false, ),
		'attributes' => array(
			'required' => 'required',
		),
		'text'    => array(
			'add_upload_file_text' => esc_html__( 'ADD OR UPLOAD FILE', 'wyzi-business-finder' ),
		),
	)
);

// Longitude and latitude.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Location', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Choose Your Country then fine tune your location by moving the pointer', 'wyzi-business-finder' ),
		'default_cb' => '0',
		'id' => $prefix . 'business_location',
		'type' => 'pw_map',
		'split_values' => true,
	)
);

$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Posts Comments', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_comments',
		'type' => 'select',
		'default_cb' => 'on',
		'options' => array( 'on' => esc_html__( 'ON', 'wyzi-business-finder' ), 'off' => esc_html__( 'OFF', 'wyzi-business-finder' ) ),
		'attributes' => array(
			'required' => 'required',
			'class' => 'wyz-select',
		),
	)
);

$wyz_business_custom_form_data = get_option( 'wyz_business_custom_form_data', array() );
//Custom form fields
if ( ! empty( $wyz_business_custom_form_data ) ) {
	foreach ( $wyz_business_custom_form_data as $key => $value ) {
		$type = '';
		$attr = array( 'class' => 'wyz-input' );
		$options = array();
		$args = array();

		if ( ! empty( $value['required'] ) ) $attr['required'] = 'required';
		if ( ! empty( $value['cssClass'] ) ) $attr['class'] = $value['cssClass'];
		if ( ! empty( $value['placeholder'] ) ) $attr['placeholder'] = $value['placeholder'];

		switch ( $value['type'] ) {
			case 'textbox':
				$type = 'text';
			break;
			case 'email':
				$type = 'text_email';
			break;
			case 'textarea':
				$type = 'textarea';
			break;
			case 'url':
				$type = 'text_url';
			break;
			case 'selectbox':
				switch ($value['selecttype']) {
					case 'dropdown':
						$type = 'select';
						$attr['class'] = 'wyz-select';
						break;
					case 'radio':
						$type = 'radio';
						break;
					case 'checkboxes':
						$type = 'multicheck';
						break;
				}
				foreach( $value['options'] as $option ) {
					$options[ $option['value'] ] = $option['label'];
				}
			break;
			case 'file':
				$type = 'file';
	    		$options['url'] = false;
	    		$attr = array();
			break;
			case 'wysiwyg':
				$type = 'wysiwyg';
	    		$options['media_buttons'] = $value['mediaupload'];
			break;
		}


		if ( '' != $type ) {
			$args = array(
				'name' => $value['label'],
				'id' => "wyzi_claim_fields_$key",
				'type' => $type,
				'attributes' => $attr,
			);

			if ( 'file' == $type ) {
				$args['query_args'] = array(
	        		'type' => array(),
	    		);
	    		foreach ( $value['fileType'] as $file_type ) {
	    			if ( $file_type['selected'] == 1 ) {
	    				if('DOC' == $file_type['label'] ){
	    					$args['query_args']['type'][] = $file_type['value'][0];
	    					$args['query_args']['type'][] = $file_type['value'][1];
	    				}
	    				else
	    					$args['query_args']['type'][] = $file_type['value'];
	    			}
	    		}
			}

			if ( ! empty( $options ) ){
				$args['options'] = $options;
			}

			$wyz_cmb_businesses->add_field( $args );
		}
	}
}
?>
