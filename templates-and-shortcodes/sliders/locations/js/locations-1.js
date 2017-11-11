"use strict";
jQuery(document).ready(function() {

	addLocSliderData(locSlide.links, locSlide.names, locSlide.images, locSlide.busCount);

	jQuery('#locations-search-text').bind("propertychange change click keyup input paste", function() {
		var txt = jQuery('#locations-search-text').val();
		if ('' === txt) {
			addLocSliderData(locSlide.links, locSlide.names, locSlide.images, locSlide.busCount);
			return;
		}
		var Links = [];
		var Names = [];
		var Flags = [];
		var BusCount = [];

		var l = locSlide.names.length;
		for (var i = 0; i < l; i++) {
			if (null !== locSlide.names[i].match(new RegExp(txt, 'i'))) {
				Names.push(locSlide.names[i]);
				Flags.push(locSlide.images[i]);
				Links.push(locSlide.links[i]);
				BusCount.push(locSlide.busCount[i]);
			}
		}

		addLocSliderData(Links, Names, Flags, BusCount);

	});

	function addLocSliderData(links, names, images, busCount) {

		var nOwl = jQuery('.location-search-slider');
		var nav = (locSlide.nav == 'true') ? true : false;
		var autoplay = (locSlide.autoplay == 'true') ? true : false;
		var autoplay_timeout = (autoplay && locSlide.autoplay_timeout > 0 ) ? locSlide.autoplay_timeout : 0;
		var loop = autoplay ? true : ( (locSlide.loop == 'true') ? true : false );

		nOwl.empty();
		nOwl.trigger('destroy.owl.carousel');
		nOwl.html(nOwl.find('.owl-stage-outer').html()).removeClass('owl-loaded');

		var l = names.length;
		if (l === 0) {
			nOwl.append('<div class="section-title"><h1>Nothing matched your search!</h1></div>');
			locInitOwl(nOwl, nav, loop,autoplay, autoplay_timeout, {});
			return;
		}
		
		var data = "";
		var link = '';

		for (var i = 0; i < l; i++) {
			data += '<div class="sin-location-item hovereffect">';
			data += '<img src="' + images[i] + '" alt="' + names[i] + '">';
			data += '<div class="overlay"><a href="' + links[i] + '"  class="loc-link"><h2 class="slider-item-title">' + names[i] + '</h2></a>';
			data += '<p class="loc-count"><span>' + busCount[i] + '</span></p></div></div>';
		}
                            

		nOwl.append(data);
		var resp;
		var w = nOwl.parent().width();
		if(!isMobile.matches){
			if(w<450)
				jQuery('#locations-search-text').parent().css('display','none');
			if(w<600)
				resp = {
					1200:{
						items:1
					},
					970:{
						items:1
					},
					768:{
						items:1
					},
					0:{
						items:1,
					},
				};
			else if(w<900)
				resp = {
						1200:{
							items:2
						},
						970:{
							items:1
						},
						768:{
							items:1
						},
						0:{
							items:1
						},
					};
			else{
				resp = {
						1200:{
							items:4
						},
						970:{
							items:3
						},
						768:{
							items:2,
						},
						0:{
							items:1,
						},
					}
			}
		}
		else{
			resp = {
				1200:{
					items:4
				},
				970:{
					items:3
				},
				768:{
					items:2,
				},
				0:{
					items:1,
				},
			}
			jQuery('#locations-search-text').parent().css('display','none');
		}
		locInitOwl(nOwl, nav, loop,autoplay, autoplay_timeout, resp);
	}
});

function locInitOwl(nOwl, nav,loop,autoplay, autoplay_timeout, resp){
	nOwl.owlCarousel({
		loop: loop,
		nav: nav,
		autoplay: autoplay,
		autoplayTimeout: autoplay_timeout,
		dots: false,
		margin: 15,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		responsive: resp,
		onInitialized : alighImages
	});

	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
}

function alighImages(){
	jQuery('.sin-location-item>img').each(function(){
		jQuery(this).on('load',function(){
			var imH = jQuery(this).height();
			imH -= 170;
			imH/=-2;
			jQuery(this).css('margin-top',imH+'px');
		});
		
	});
}
