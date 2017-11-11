//toastr
!function(e){e(["jquery"],function(e){return function(){function t(e,t,n){return g({type:O.error,iconClass:m().iconClasses.error,message:e,optionsOverride:n,title:t})}function n(t,n){return t||(t=m()),v=e("#"+t.containerId),v.length?v:(n&&(v=d(t)),v)}function o(e,t,n){return g({type:O.info,iconClass:m().iconClasses.info,message:e,optionsOverride:n,title:t})}function s(e){C=e}function i(e,t,n){return g({type:O.success,iconClass:m().iconClasses.success,message:e,optionsOverride:n,title:t})}function a(e,t,n){return g({type:O.warning,iconClass:m().iconClasses.warning,message:e,optionsOverride:n,title:t})}function r(e,t){var o=m();v||n(o),u(e,o,t)||l(o)}function c(t){var o=m();return v||n(o),t&&0===e(":focus",t).length?void h(t):void(v.children().length&&v.remove())}function l(t){for(var n=v.children(),o=n.length-1;o>=0;o--)u(e(n[o]),t)}function u(t,n,o){var s=!(!o||!o.force)&&o.force;return!(!t||!s&&0!==e(":focus",t).length)&&(t[n.hideMethod]({duration:n.hideDuration,easing:n.hideEasing,complete:function(){h(t)}}),!0)}function d(t){return v=e("<div/>").attr("id",t.containerId).addClass(t.positionClass),v.appendTo(e(t.target)),v}function p(){return{tapToDismiss:!0,toastClass:"toast",containerId:"toast-container",debug:!1,showMethod:"fadeIn",showDuration:300,showEasing:"swing",onShown:void 0,hideMethod:"fadeOut",hideDuration:1e3,hideEasing:"swing",onHidden:void 0,closeMethod:!1,closeDuration:!1,closeEasing:!1,closeOnHover:!0,extendedTimeOut:1e3,iconClasses:{error:"toast-error",info:"toast-info",success:"toast-success",warning:"toast-warning"},iconClass:"toast-info",positionClass:"toast-top-right",timeOut:5e3,titleClass:"toast-title",messageClass:"toast-message",escapeHtml:!1,target:"body",closeHtml:'<button type="button">&times;</button>',closeClass:"toast-close-button",newestOnTop:!0,preventDuplicates:!1,progressBar:!1,progressClass:"toast-progress",rtl:!1}}function f(e){C&&C(e)}function g(t){function o(e){return null==e&&(e=""),e.replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/'/g,"&#39;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function s(){c(),u(),d(),p(),g(),C(),l(),i()}function i(){var e="";switch(t.iconClass){case"toast-success":case"toast-info":e="polite";break;default:e="assertive"}I.attr("aria-live",e)}function a(){E.closeOnHover&&I.hover(H,D),!E.onclick&&E.tapToDismiss&&I.click(b),E.closeButton&&j&&j.click(function(e){e.stopPropagation?e.stopPropagation():void 0!==e.cancelBubble&&e.cancelBubble!==!0&&(e.cancelBubble=!0),E.onCloseClick&&E.onCloseClick(e),b(!0)}),E.onclick&&I.click(function(e){E.onclick(e),b()})}function r(){I.hide(),I[E.showMethod]({duration:E.showDuration,easing:E.showEasing,complete:E.onShown}),E.timeOut>0&&(k=setTimeout(b,E.timeOut),F.maxHideTime=parseFloat(E.timeOut),F.hideEta=(new Date).getTime()+F.maxHideTime,E.progressBar&&(F.intervalId=setInterval(x,10)))}function c(){t.iconClass&&I.addClass(E.toastClass).addClass(y)}function l(){E.newestOnTop?v.prepend(I):v.append(I)}function u(){if(t.title){var e=t.title;E.escapeHtml&&(e=o(t.title)),M.append(e).addClass(E.titleClass),I.append(M)}}function d(){if(t.message){var e=t.message;E.escapeHtml&&(e=o(t.message)),B.append(e).addClass(E.messageClass),I.append(B)}}function p(){E.closeButton&&(j.addClass(E.closeClass).attr("role","button"),I.prepend(j))}function g(){E.progressBar&&(q.addClass(E.progressClass),I.prepend(q))}function C(){E.rtl&&I.addClass("rtl")}function O(e,t){if(e.preventDuplicates){if(t.message===w)return!0;w=t.message}return!1}function b(t){var n=t&&E.closeMethod!==!1?E.closeMethod:E.hideMethod,o=t&&E.closeDuration!==!1?E.closeDuration:E.hideDuration,s=t&&E.closeEasing!==!1?E.closeEasing:E.hideEasing;if(!e(":focus",I).length||t)return clearTimeout(F.intervalId),I[n]({duration:o,easing:s,complete:function(){h(I),clearTimeout(k),E.onHidden&&"hidden"!==P.state&&E.onHidden(),P.state="hidden",P.endTime=new Date,f(P)}})}function D(){(E.timeOut>0||E.extendedTimeOut>0)&&(k=setTimeout(b,E.extendedTimeOut),F.maxHideTime=parseFloat(E.extendedTimeOut),F.hideEta=(new Date).getTime()+F.maxHideTime)}function H(){clearTimeout(k),F.hideEta=0,I.stop(!0,!0)[E.showMethod]({duration:E.showDuration,easing:E.showEasing})}function x(){var e=(F.hideEta-(new Date).getTime())/F.maxHideTime*100;q.width(e+"%")}var E=m(),y=t.iconClass||E.iconClass;if("undefined"!=typeof t.optionsOverride&&(E=e.extend(E,t.optionsOverride),y=t.optionsOverride.iconClass||y),!O(E,t)){T++,v=n(E,!0);var k=null,I=e("<div/>"),M=e("<div/>"),B=e("<div/>"),q=e("<div/>"),j=e(E.closeHtml),F={intervalId:null,hideEta:null,maxHideTime:null},P={toastId:T,state:"visible",startTime:new Date,options:E,map:t};return s(),r(),a(),f(P),E.debug&&console&&console.log(P),I}}function m(){return e.extend({},p(),b.options)}function h(e){v||(v=n()),e.is(":visible")||(e.remove(),e=null,0===v.children().length&&(v.remove(),w=void 0))}var v,C,w,T=0,O={error:"error",info:"info",success:"success",warning:"warning"},b={clear:r,remove:c,error:t,getContainer:n,info:o,options:{},subscribe:s,success:i,version:"2.1.3",warning:a};return b}()})}("function"==typeof define&&define.amd?define:function(e,t){"undefined"!=typeof module&&module.exports?module.exports=t(require("jquery")):window.toastr=t(window.jQuery)});
"use strict";
jQuery(document).ready(function() {
	//show wyzi js elements.
	//for browsers with no js.
	jQuery('.map-container').show();

	jQuery('.category-search-area').show();
	jQuery('.location-search-area').show();
	jQuery('.our-offer-area').show();
	jQuery('.recently-added-area').show();
	jQuery('.featured-area').show();

	var useDimmer = 1 == wyz_template_type;
	
	//business category dropdown filter
	if (jQuery('#wyz-cat-filter').length) {
		jQuery('#wyz-cat-filter').selectator({
			labels: {
				search: general.searchText
			},
			useDimmer: useDimmer
		});
	}

	//locations dropdown filter
	if (jQuery('#wyz-loc-filter').length) {
		jQuery('#wyz-loc-filter').selectator({
			labels: {
				search: general.searchText
			},
			useDimmer: useDimmer
		});
	}

	if(jQuery('#header-image-content').length){
		var marg = (jQuery('#page-header-image').height()-jQuery('#header-image-content').height())/2;
		jQuery('#header-image-content').css('padding-top',marg+'px' );
	}
	

	jQuery(document).on('click', '.busi-post-share-btn', function (event) {
			event.preventDefault();
			jQuery(this).next(".business-post-share-cont").toggle();
	});
	/*jQuery(".busi-post-share-btn").live('click', function () {
			event.preventDefault();
			jQuery(this).nextAll(".business-post-share-cont").first().toggle();
		}
	);*/

	jQuery('.single-wyz_business_post .sin-blog .blog-image').bind('click', function(){
		var h = jQuery('img',this).height()
		jQuery(this).unbind().animate({maxHeight:h+'px'},500);
		jQuery('img', this).unbind().fadeTo(50,1);
	});
	jQuery('.single-wyz_business_post .sin-blog .blog-image>img').hover(function(){
		jQuery(this).fadeTo(50,0.6);
	},function(){
		jQuery(this).fadeTo(50,1);
	});

	var haveSliders = ( jQuery('.category-search-area').length || jQuery('.location-search-area').length || jQuery('.our-offer-area').length || jQuery('.recently-added-area').length || jQuery('.featured-area').length );
	var is_single_business = jQuery('.business-offers-area').length;

	if(haveSliders){
		var resizeId;
		var catExists = jQuery('.category-search-area').length;
		var locExists = jQuery('.location-search-area').length;
		var offExists = jQuery('.our-offer-area').length;
		var busOffExists = jQuery('.business-offers-area').length;
		var recExists = jQuery('.recently-added-area').length;
		var featExists = jQuery('.featured-area').length;

		refreshSlidersWidth(catExists,locExists,offExists,recExists,featExists,busOffExists);
	}

	if(is_single_business)
		refreshSlidersWidth(false,false,false,false,false,true);

	//Like functionality
	jQuery(".like-button").live("click", function(a) {
        a.preventDefault(), jQuery(this).removeClass("like-button"), jQuery(this).addClass("liked");
        var iElement = jQuery(this).find('i');
        if(iElement.hasClass('fa-heart-o')){
        	iElement.removeClass('fa-heart-o');
        	iElement.addClass('fa-heart');
        }
        var b = "#pl_" + jQuery(this).data("postid");
        jQuery(b).html(jQuery(this).data("likes") + 1), jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: "action=buslike&nonce=" + ajaxnonce + "&post-id=" + jQuery(this).data("postid"),
            success: function() {}
        })
    });

	function refreshSlidersWidth(catExists,locExists,offExists,recExists,featExists,busOffExists){
		var wid;
		if(catExists){
			var cat = jQuery('.category-search-area');
			wid = cat.width();
			if(wid<480){
				cat.addClass('category-search-area-sml');
			}
			else if(wid<768){
				cat.addClass('category-search-area-med');
			}
			else if(wid<992){
				cat.addClass('category-search-area-lrg');
			}
		}
		if(locExists){
			var loc = jQuery('.location-search-area');
			wid = loc.width();
			if(wid<480){
				loc.addClass('location-search-area-sml');
			}
			else if(wid<768){
				loc.addClass('location-search-area-med');
			}
			else if(wid<992){
				loc.addClass('location-search-area-lrg');
			}
		}
		if(offExists){
			
			jQuery('.our-offer-area').each(function(){
				wid = jQuery(this).width();
				if(wid<480){
				jQuery(this).addClass('our-offer-area-sml');
				}
				else if(wid<768){
					jQuery(this).addClass('our-offer-area-med');
				}
				else {
					jQuery(this).addClass('our-offer-area-lrg');
				}
			});
		}
		if(recExists){
			var cat = jQuery('.recently-added-area');
			wid = cat.width();
			if(wid<480){
				cat.addClass('recently-added-area-sml');
			}
			else if(wid<768){
				cat.addClass('recently-added-area-med');
			}
			else if(wid<992){
				cat.addClass('recently-added-area-lrg');
			}
		}
		if(recExists){
			var feat = jQuery('.featured-area');
			wid = feat.width();
			if(wid<480){
				feat.addClass('featured-area-sml');
			}
			else if(wid<768){
				feat.addClass('featured-area-med');
			}
			else if(wid<992){
				feat.addClass('featured-area-lrg');
			}
		}
		var busOffAreaName = ( 1 == wyz_template_type ? '.business-offers-area' : '.our-offer-area' );
		if(busOffExists){
			jQuery(busOffAreaName).each(function(){
				wid = jQuery(this).width();
				if(wid<480){
				jQuery(this).addClass('our-offer-area-sml');
				}
				else if(wid<768){
					jQuery(this).addClass('our-offer-area-med');
				}
				else {
					jQuery(this).addClass('our-offer-area-lrg');
				}
			});
		}
	}

	jQuery('.fav-bus').live('click',favoriteBus);
	function favoriteBus(event){
		event.preventDefault();
		var bus_id = jQuery(this).data('busid');
		if( '' == bus_id || undefined == bus_id ) return;
		var isFav = jQuery(this).data('fav');
		jQuery(this).parent().addClass('fade-loading');
		jQuery(this).unbind('favoriteBus');
		var favType = isFav == 1 ? 'unfav' : 'fav';
		var target = jQuery(this);

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=business_favorite&nonce=" + ajaxnonce + "&business_id=" + bus_id + "&fav_type=" + favType,
			success: function(result) {
				target.parent().removeClass('fade-loading');
				if(favType=='fav'){
					target.find('i').removeClass('fa-heart-o');
					target.find('i').addClass('fa-heart');
					target.data('fav',1 );
				} else {
					target.find('i').removeClass('fa-heart');
					target.find('i').addClass('fa-heart-o');
					target.data('fav',0 );
				}
			}
		});
	}
});

function WyzAjax (target) {
	target.appendChild('<div class="loading-spin"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></div>');

	this.done = function(){
		target.removeChild(target.getElementsByClassName('loading-spin'));
	}
}
