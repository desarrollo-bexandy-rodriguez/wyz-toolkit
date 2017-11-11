//show/hide map metabox according to map checkbox and page template
"use strict";
(function() {
	jQuery(document).ready(function() {

		jQuery('#mymetabox_revslider_0').hide();

		if(jQuery('.cmb2-id-wyz-business-description')){
			jQuery('.cmb2-id-wyz-business-description').hide();
		}
		if(jQuery('.cmb2-id-wyz-offers-description')){
			jQuery('.cmb2-id-wyz-offers-description').hide();
		}

		var page_template = jQuery('#page_template');
		var template = page_template.val();

		page_template.change(function() {
			template = jQuery(this).val();
			if (template == 'templates/full-width-page.php' || template == 'templates/left-sidebar-page.php' ||
				 template == 'templates/right-sidebar-page.php' ) {
				jQuery('#wyz_maps_on_pages').show();
				jQuery('#wyz_header_map').hide();
				jQuery('#wyz_header_image').hide();
				jQuery('#wyz_listing_page_template').hide();
				jQuery('#wyz_header_rev_slider').hide();
			} else if( template == 'templates/contact-page.php' ) {
				jQuery('#wyz_maps_on_pages').hide();
				jQuery('#wyz_header_image').hide();
				jQuery('#wyz_header_map').show();
				jQuery('#wyz_listing_page_template').hide();
				jQuery('#wyz_header_rev_slider').hide();
			} else if(template == 'templates/business-listing-page.php'){
				jQuery('#wyz_maps_on_pages').hide();
				jQuery('#wyz_header_map').show();
				jQuery('#wyz_listing_page_template').show();
				jQuery('#wyz_header_image').hide();
				jQuery('#wyz_header_rev_slider').hide();
			}else{
				jQuery('#wyz_maps_on_pages').hide();
				jQuery('#wyz_header_map').hide();
				jQuery('#wyz_header_image').hide();
				jQuery('#wyz_listing_page_template').hide();
				jQuery('#wyz_header_rev_slider').hide();
			}
			update_template(template, jQuery("#wyz_page_header_content").val());
		}).change();

		update_template(template, jQuery("#wyz_page_header_content").val());

		jQuery("#wyz_page_header_content").change(function() {
			update_template(template,jQuery(this).val());
		}).change();

	});
})(jQuery);


function update_template(template, headerType) {
	if (headerType == 'map' || template == 'templates/contact-page.php' || template == 'templates/business-listing-page.php') {
		jQuery('#wyz_header_map').show();
		jQuery('#wyz_header_image').hide();
		jQuery('#wyz_header_rev_slider').hide();
		if (template == 'templates/contact-page.php'){
			jQuery('.cmb2-id-wyz-contact-page-map').show();
			jQuery('.cmb2-id-wyz-page-map').hide();
			jQuery('.cmb2-id-wyz-page-autozoom').hide();
			jQuery('.cmb2-id-wyz-map-scroll-zoom-checkbox').hide();
		}else{
			jQuery('.cmb2-id-wyz-contact-page-map').hide();
			jQuery('.cmb2-id-wyz-page-map').show();
			jQuery('.cmb2-id-wyz-page-autozoom').show();
			jQuery('.cmb2-id-wyz-map-scroll-zoom-checkbox').show();
		}
		google.maps.event.trigger(window, 'resize', {});
	} else if(headerType == 'revslider'){
		jQuery('#wyz_header_rev_slider').show();
		jQuery('#wyz_header_image').hide();
		jQuery('#wyz_header_map').hide();
	}else {
		jQuery('#wyz_header_map').hide();
		if ( headerType == 'image' ) {
			jQuery('#wyz_header_image').show();
		}else
			jQuery('#wyz_header_image').hide();
		jQuery('#wyz_header_rev_slider').hide();
	}

}