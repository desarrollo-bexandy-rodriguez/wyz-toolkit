"use strict";
jQuery(document).ready(function() {
	var nav = (recAddSlide.nav == 'true') ? true : false;
	var autoplay = (recAddSlide.autoplay == 'true') ? true : false;
	var autoplay_timeout = (autoplay && recAddSlide.autoplay_timeout > 0 ) ? recAddSlide.autoplay_timeout : 0;
	var loop = autoplay ? true : ( (recAddSlide.loop == 'true') ? true : false );
	var nOwl = jQuery('.recently-added-slider');
	var resp;
	var w = nOwl.parent().width();
		if(!isMobile.matches){
			if(w<600){
				resp = {
					1200:{
						items:1
					},
					970:{
						items:1
					},
					768:{
						items:1,
					},
					0:{
						items:1,
					},
				};
			}
			else if(w<900){
				resp = {
					1200:{
						items:2
					},
					970:{
						items:1
					},
					768:{
						items:1,
					},
					0:{
						items:1,
					},
				};
			}
			else{
				resp = {
					1200:{
						items:3
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
		}else{
			resp = {
				1200:{
					items:3
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
		
	nOwl.owlCarousel({
		margin: 30,
		loop: loop,
		nav: nav,
		autoplay: autoplay,
		autoplayTimeout: autoplay_timeout,
		dots: false,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		responsive: resp,
		autoplayHoverPause:true
	});
	
	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
});
