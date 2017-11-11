"use strict";

jQuery(document).ready(function() {
	addCatSliderData(catSlide.taxs);

	/*jQuery('#categories-search-text').bind("propertychange change click keyup input paste", function() {
		var txt = jQuery('#categories-search-text').val();
		if ('' === txt) {
			addCatSliderData(catSlide.taxs);
			return;
		}
		var taxs = new Array();

		var l = catSlide.taxs.length;
		for (var i = 0; i < l; i++) {
			if (null !== catSlide.taxs[i].name.match(new RegExp(txt, 'i')))
				taxs.push(catSlide.taxs[i]);
			else if (catSlide.taxs[i].has_children) {
				var k = catSlide.taxs[i].all_children.length;
				for (var j = 0; j < k; j++) {
					if (null !== catSlide.taxs[i].all_children[j].match(new RegExp(txt, 'i'))) {
						taxs.push(catSlide.taxs[i]);
						break;
					}
				}
			}
		}
		addCatSliderData(taxs);
	});*/


	function addCatSliderData(taxs) {

		var nOwl = jQuery('.category-search-slider');
		nOwl.empty();
		nOwl.trigger('destroy.owl.carousel');
		nOwl.html(nOwl.find('.owl-stage-outer').html()).removeClass('owl-loaded');
		var nav = (catSlide.nav == 'true') ? true : false;var autoplay = (catSlide.autoplay == 'true') ? true : false;
		var autoplay_timeout = (autoplay && catSlide.autoplay_timeout > 0 ) ? catSlide.autoplay_timeout : 0;
		var loop = autoplay ? true : ( (catSlide.loop == 'true') ? true : false );


		var l = taxs.length;
		if (l === 0) {
			nOwl.append('<div class="section-title"><h1>Nothing matched your search!</h1></div>');
			initOwlCat(nOwl, nav, loop, autoplay, autoplay_timeout, {});
			return;
		}
		var rows = catSlide.rows;
		var rcount = 0;
		var data = "";
		var i;
		for (i = 0; i < l; i++) {

			if (0 === rcount)
				data += '<div class="sin-cat-item">';
			var currTax = taxs[i];
			if(currTax.child_count == undefined || currTax.child_count == '') currTax.child_count = 0; 
			data += '<div class="col-xs-12"><div class="cat-slide-item"><div class="parent-cat text-center">';
			if (currTax.img !== '')
				data += '<img src="' + currTax.img + '"/>';
			else
				data += '<div></div>';
			data += '<h4>' + currTax.name + '</h4></div><div class="total-cat-info text-center"><p>('+currTax.child_count+')  Categories</p></div>';
			data += '<div class="cat-list"' + (currTax.color !== '' ? 'style="background-color:' + currTax.color + ';"' : '') + '><h4>'+currTax.name+'</h4>';
			if (currTax.has_children) {
				data += '<ul>';
				var currChildren = currTax.children;
				var len = currChildren.length;
				for (var j = 0; j < len; j++)
					data += '<li><a href="' + currChildren[j].link + '"><span>' + currChildren[j].name +'</span><span>'+ (currChildren[j].bus_count > 0 ? ' (' + currChildren[j].bus_count + ')' : '') + '</span></a></li>';
				data += '</ul>';
			}
			if (currTax.view_all) {
				data += '<a href="' + currTax.link + '" class="view-all-cat">View all <i class="fa fa-angle-double-right"></i></a>';
			}
				
			data += '</div></div></div>';

			
			if ((rows - 1) == rcount) {
				data += '</div>';
				rcount = -1;
			}
			rcount++;
		}

		if (rcount !== 0)
			data += '</div>';

		nOwl.append(data);

		var resp;
		var w = nOwl.parent().width();
		if(!isMobile.matches){
			if(w<450){
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
							items:1
						},
					};
				jQuery('#categories-search-text').parent().css('display','none');
			}
			else if(w<700){
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
			}
			else if(w<900){
				resp = {
						1200:{
							items:3
						},
						970:{
							items:2
						},
						768:{
							items:2
						},
						0:{
							items:1
						},
					};
			}
			else{
				resp = {
						1200:{
							items:catSlide.columns
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
			jQuery('#categories-search-text').parent().css('display','none');
			resp = {
					1200:{
						items:catSlide.columns
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
		initOwlCat(nOwl, nav, loop, autoplay, autoplay_timeout, resp);
	}
});

function initOwlCat(nOwl, nav, loop, autoplay, autoplay_timeout, resp){

	nOwl.owlCarousel({
		loop: loop,
		nav: nav,
		autoplay: autoplay,
		autoplayTimeout: autoplay_timeout,
		dots: false,
		navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
		margin: 10,
		responsive: resp
	});

	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
}