"use strict";

jQuery.fn.isAboveScreen = function(){
	var viewport = {};
	viewport.top =jQuery(window).scrollTop();
	var bounds = {};
	bounds.bottom = bounds.top + jQuery(this).outerHeight();
	return (bounds.bottom < viewport.top);
}

var LoginDropdown = {
    getDropdown: function(a, b, c) {
        return this.x = a, this.y = b, this.dropdown = '<div class="login-dropdown list-group'+(c?' post-comm-dropdown':'')+'" style="display:block; position: absolute; '+(c?'right':'left')+': ' + a + "px; top: " + b + 'px;" >' + (!c?'<span>Like this post?</span>':'')+'<p>Login to make your opinion count</p><a class="wyz-button wyz-primary-color  wyz-prim-color icon action-btn btn-bg-blue btn-rounded" href="' + wall.loginPermalink + '" title="Sign in"> Sign in<i class="fa fa-angle-right" aria-hidden="true"></i></a></div>', jQuery.parseHTML(this.dropdown);
    }
};

var canScroll = true;
jQuery(document).ready(function() {
	if (true == wall.hasPosts) {
		var page = 1,DropDn, h = !1;

		if ( 'manual' != walll.pull_method ) {
			jQuery('#loadmoreajaxloader').bind('inview', function(event, visible){
				ajax_loadmore(visible);
			});
			//case page reload and loadmoreajaxloader is above screen
			if(jQuery('#loadmoreajaxloader').isAboveScreen())
				ajax_loadmore(true);
		} else
			ajax_loadmore(true);


		toastr.options.closeMethod = 'fadeOut';
		toastr.options.showEasing = 'swing';
		toastr.options.hideEasing = 'swing';
		toastr.options.closeDuration = 300;
		toastr.options.preventDuplicates = true;
		toastr.options.timeOut = 1000;

		jQuery("body").click(function() {
            h && DropDn!=undefined&&(DropDn.slideUp("fast"), DropDn.remove(), h = !1);
        });

        jQuery('.load-more-btn').live('click',function(){
    		this.parentElement.removeChild(this);
        	ajax_loadmore(true);
        });
		

		jQuery(".com-view-more").live({
			click: function (event) {
				event.preventDefault();
				jQuery(this).addClass('fade-loading');
				var This = jQuery(this);
				var offset = parseInt( This.data('offset') );
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=bus_load_comments&nonce=" + ajaxnonce + "&post-id=" + This.data('id') + '&offset=' + offset,
					success: function(result) {
						This.removeClass('fade-loading');
						if ( result ) {
							This.closest('.the-comment').before(result);
							This.closest('.the-comment').css({"display":"none"});
						} else {
							This.closest('.the-comment').css({"display":"none"});
						}
					}
				});
			}
		});

		var currentTarget, currentCommInput;
		jQuery(".post_footer_comment_btn, .post_footer_comment_btn").live("click", function() {
			if('false'==(wall.loggedInUser)){
				if(jQuery(this).hasClass('action-btn')){
					DropDn = LoginDropdown.getDropdown(70, 
	                	jQuery(this).position().top + 53, true
	                );
				} else {
	                DropDn = LoginDropdown.getDropdown(35, 
	                	jQuery(this).position().top + jQuery(this).parent().parent().position().top + 40, true
	                );
	            }
                jQuery("#postswrapper").append(DropDn);
                DropDn = jQuery("#postswrapper").find(".login-dropdown"), DropDn.slideDown("slow");
                h=1;
	            return;
	        }

			currentCommInput = jQuery(this).prev();
			var inputContent = currentCommInput.val(),
			non_c = jQuery(this).next().val();
			if(inputContent==''){
				toastr.warning("can't publish an empty comment");
			} else{
				var id = jQuery(this).data('id'),
				currentTarget = jQuery(this);
				currentTarget.prop("disabled", !0);
				currentTarget.addClass("busi_post_submit-dis");
				var label = currentTarget.text();
				currentTarget.text("Posting...");
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=bus_post_comm&nonce=" + non_c + "&id=" + id + "&comment=" + inputContent,
					success: function(msg) {
						if(!msg){
							toastr.error("Post comment failed");
						}
						else {
							currentTarget.closest('.post-footer-comments').find('.the-post-comments').prepend(msg);
							currentTarget.prop("disabled", 0);
							currentTarget.removeClass("busi_post_submit-dis");
							currentCommInput.val('');
							currentTarget.text(label);
						}
					}
				});
			}
		});

		//handle liking when user is not logged in
		jQuery(document).on("click", ".like-btn-no-log", function() {
        	if(h){DropDn.slideUp("fast")}
            event.preventDefault(), h = true, DropDn = LoginDropdown.getDropdown(35, jQuery(this).position().top + ( jQuery(this).find('.fa-heart-o').length ? jQuery(this).parent().position().top + 60 : jQuery(this).parent().parent().position().top + 40), false), jQuery("#postswrapper").append(DropDn), 
        	DropDn = jQuery("#postswrapper").find(".login-dropdown"), DropDn.slideDown("slow")
        });
	}

	function ajax_loadmore(visible) {
		if (visible && canScroll) {
			canScroll = false;

			jQuery("#loadmoreajaxloader").fadeTo("fast", 1);
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: "action=all_bus_inf_scrll&nonce=" + ajaxnonce + "&post_index=" + walll.ind + "&page=" + page + "&logged-in-user=" + wall.loggedInUser + '&posts_pull=' + walll.posts_pull+"&category="+walll.category,
				success: function(html) {
					if ('' !== html) {
						page++;
						canScroll = true;
						var spaceIndex = html.indexOf('wyz_space');
						walll.ind = parseInt(html.substring(0, spaceIndex));
						jQuery("#postswrapper").append(html.substring(spaceIndex + 9, html.length));
						jQuery("#loadmoreajaxloader").fadeTo("fast", 0);
						if ( 'manual' == walll.pull_method ) {
							jQuery("#postswrapper").append('<center><button class="load-more-btn wyz-primary-color-text">'+wall.loadMoreMsg+'</button></center>');
						}
					} else {
						jQuery('div#loadmoreajaxloader').html('<center>'+wall.noPostsMsg+'</center>');
					}
				}
			});
		}
	}
});

function getBaseUrl() {
	var re = new RegExp(/^.*\//);
	return re.exec(window.location.href);
}
