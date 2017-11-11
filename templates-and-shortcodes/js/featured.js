"use strict";
jQuery(document).ready(function() {
	var nav = (featuredSlide.nav == 'true') ? true : false;
	var loop = (featuredSlide.loop == 'true') ? true : false;
	var nOwl = jQuery('.featured-slider');
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
		dots: false,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		responsive: resp
	});
});
