"use strict";
jQuery(document).ready(function() {

	var isMobile = window.matchMedia("only screen and (max-width: 760px)");
	// you want to enable the pointer events only on click;
	jQuery('#home-map, #business-map, #contact-map').addClass('scrolloff'); // set the pointer events to none on doc ready
	jQuery('#map-unlock').bind('click', mapClickHandler);
	if(!isMobile.matches)
		jQuery('.map-container').bind('click', mapClickHandler);
	


	function mapClickHandler(e) {
		
		if(!isMobile.matches){
			jQuery('.map-container').unbind('click', mapClickHandler);
		}
		jQuery('#map-unlock').unbind('click', mapClickHandler);
		jQuery('#home-map, #business-map, #contact-map').removeClass('scrolloff'); // set the pointer events true on click
		jQuery('#map-unlock').css({
			'background-image': "url(" + wyz_plg_ref + "templates-and-shortcodes/images/unlock-black.png)",
		});

		jQuery('#map-unlock').bind('click', mapUnclickHandler);
	}

	function mapUnclickHandler() {
		jQuery('#map-unlock').unbind('click', mapUnclickHandler);
		jQuery('#home-map, #business-map, #contact-map').addClass('scrolloff');
		jQuery('#map-unlock').css({
			'background-image': "url(" + wyz_plg_ref + "templates-and-shortcodes/images/lock-black.png)",
		});
		
		jQuery('#map-unlock').bind('click', mapClickHandler);
		if(!isMobile.matches)
			setTimeout(function(){
				if (!isMobile.matches)
					jQuery('.map-container').bind('click', mapClickHandler);
			}, 100);
			
	}
});
