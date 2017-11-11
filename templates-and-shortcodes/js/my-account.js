"use strict";

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

var passOK = false;
function checkPasswordStrength($pass1,
	$pass2,
	$strengthResult,
	$submitButton,
	blacklistArray) {
	var pass1 = $pass1.val();
	var pass2 = $pass2.val();

	// Reset the form & meter
	$strengthResult.removeClass('short bad good strong');

	// Extend our blacklist array with those from the inputs & site data
	blacklistArray = blacklistArray.concat(wp.passwordStrength.userInputBlacklist());

	// Get the password strength
	var strength = wp.passwordStrength.meter(pass1, blacklistArray, pass2);

	// Add the strength meter results
	switch (strength) {

		case 2:
			$strengthResult.addClass('bad').html(pwsL10n.bad);
			break;

		case 3:
			$strengthResult.addClass('good').html(pwsL10n.good);
			break;

		case 4:
			$strengthResult.addClass('strong').html(pwsL10n.strong);
			break;

		case 5:
			$strengthResult.addClass('short').html(pwsL10n.mismatch);
			break;

		default:
			$strengthResult.addClass('short').html(pwsL10n.short);

	}

	// The meter function returns a result even if pass2 is empty,
	// enable only the submit button if the password is strong and
	// both passwords are filled up
	if (4 === strength && '' !== pass2.trim()) {
		passOK = true;
	} else {
		passOK = false;
	}

	return strength;
}

function isEmptyPass() {
	return ('' === jQuery('input[name=wyz_user_pass]').val() && '' === jQuery('input[name=wyz_user_pass_confirm]').val());
}

function enableDisableSubmit(userFirst, userLast, userEmail, submitBtn) {
	if ('' !== userEmail.val() && '' !== userFirst.val() && '' !== userLast.val() && (isEmptyPass() || passOK)) {
		submitBtn.removeAttr('disabled');
		submitBtn.prop('disabled', false);
	} else {
		submitBtn.attr('disabled', 'disabled');
		submitBtn.prop('disabled', false);
	}
}


jQuery(document).ready(function() {

	if (jQuery('.user-acc-tabs').length) {

		jQuery('.booking').on('click',function(){
			jQuery(window).trigger('resize');
		});

		var t = jQuery('.page-content').height() + 50;
		var h;

		if (jQuery('#tab1').is(':checked')) {
			h = t + jQuery('#tab-content1').height();
			jQuery('.page-content').height(h);
		} else if (jQuery('#tab2').is(':checked')) {
			h = t + jQuery('#tab-content2').height();
			jQuery('.page-content').height(h);
		}

		jQuery('#tab1').click(function() {
			h = t + jQuery('#tab-content1').height();
			jQuery('.page-content').height(h);
			jQuery('#lbl1').addClass('wyz-tab-current');
			jQuery('#lbl2').removeClass('wyz-tab-current');
		});
		jQuery('#tab2').click(function() {
			h = t + jQuery('#tab-content2').height();
			jQuery('.page-content').height(h);
			jQuery('#lbl2').addClass('wyz-tab-current');
			jQuery('#lbl1').removeClass('wyz-tab-current');
		});

		
	}

	var previousActive;

	jQuery(function(){
		var hash = window.location.hash;
		hash && jQuery('a.profile-tab[href="' + hash + '"]').tab('show');

		if(hash)previousActive = jQuery('a.profile-tab[href="' + hash + '"]');
		else previousActive=jQuery('.profile-tab-list ul>li:first-child');

		previousActive.addClass('wyz-prim-color');

		//search for query arguments
		var url = window.location.search;
		if( (null != getParameterByName("add-job",url) && jQuery('#link-jobs').length ) ||
			('edit' == getParameterByName("action",url) && null!= getParameterByName("job_id",url) ) ) {
			previousActive.removeClass('wyz-prim-color');
			previousActive=jQuery('#link-jobs');
			previousActive.addClass('wyz-prim-color');
			previousActive.tab('show');
		}


		jQuery('a.profile-tab').click(function (e) {
			e.preventDefault();
			previousActive.removeClass('wyz-prim-color');
			previousActive=jQuery(this);
			jQuery(this).addClass('wyz-prim-color');
			jQuery(this).tab('show');
			var scrollmem = jQuery('body').scrollTop() || jQuery('html').scrollTop();
			window.location.hash = this.hash;
			jQuery('html,body').scrollTop(scrollmem);
		});
	});

	/*if (jQuery('.profile-tab-list').length) {
	    var $frame = jQuery('.profile-tab-list');
	    var $slidee = $frame.children('ul').eq(0);
	    var $wrap = $frame.parent();

	    // Call Sly on frame
	    $frame.sly({
	      horizontal: 1,
	      itemNav: 'basic',
	      smart: 1,
	      mouseDragging: 1,
	      touchDragging: 1,
	      releaseSwing: 1,
	      startAt: 0,
	      scrollBar: $wrap.find('.scrollbar'),
	      scrollBy: 1,
	      pagesBar: $wrap.find('.pages'),
	      activatePageOn: 'click',
	      speed: 300,
	      elasticBounds: 1,
	      easing: 'easeOutExpo',
	      dragHandle: 1,
	      dynamicHandle: 1,
	      clickBar: 1,

	    });

	    var c;
	    var sumWidth = 0;
	    var shown = false;
	    jQuery( '.profile-tab-list ul li' ).each(function(){
	    	sumWidth += jQuery(this).width();
	    });
	    jQuery(window).resize(function() {
	        clearTimeout(c), c = setTimeout(
	        	function(){
	        		if ( jQuery('.profile-tab-list').width() < sumWidth && ! shown ) {
	        			jQuery('.scrollbar').show();
	        			shown = true;
	        		} else if(jQuery('.profile-tab-list').width() > sumWidth && shown){
	        			jQuery('.scrollbar').hide();
	        			shown = false;
	        		}
	        		$frame.sly('reload');
	        	}
	        , 300
	        );
	    });
	    //jQuery(window).trigger('resize');
	}*/

	var profileDropdown = jQuery('#profile-tab-list-dropdown');
	if (jQuery('.profile-tab-list').length) {
		jQuery('.profile-tab-list a').click(function(){
			profileDropdown.val(jQuery(this).data('link'));
		});

		profileDropdown.on('change',function(){
			jQuery('#link-'+jQuery(this).val()).trigger('click');
		});
	}


	jQuery('body').on('keyup', 'input[name=wyz_user_pass], input[name=wyz_user_pass_confirm]',
		function(event) {
			enableDisableSubmit(
				jQuery('input[name=first-name]'),
				jQuery('input[name=last-name]'),
				jQuery('input[name=email]'),
				jQuery('#update-user')
			);

			checkPasswordStrength(
				jQuery('input[name=wyz_user_pass]'), // First password field
				jQuery('input[name=wyz_user_pass_confirm]'), // Second password field
				jQuery('#password-strength'), // Strength meter
				jQuery('#update-user'), // Submit button
				['black', 'listed', 'word'] // Blacklisted words
			);
		});

	if(jQuery('#amount-notif').length){
		jQuery('#transfer-points').val(parseInt(jQuery('#transfer-points').val())+parseInt(jQuery('#points-fee').html()));
		jQuery('body').on('keyup', '#transfer-points',function(){
			var points = parseInt(jQuery(this).val());
			if(points<0||isNaN(points)){
				jQuery('#amount-notif').html(myAccount.invalidText);
			} else{
				var cost = parseInt(jQuery('#points-fee').html());
				points += cost;
				if(myAccount.pointsAvailable < points){
					jQuery('#amount-notif').html('<span style="color:red;">'+points+' ' + myAccount.exceeds +'</span>');
				} else{
					jQuery('#amount-notif').html('<b>'+points+'</b> ' + myAccount.reduce + '.');
				}
			}
		});
	}

	toastr.options.closeMethod = 'fadeOut';
	toastr.options.showEasing = 'swing';
	toastr.options.hideEasing = 'swing';
	toastr.options.closeDuration = 300;
	toastr.options.preventDuplicates = true;
	toastr.options.timeOut = 0;

	jQuery('#logout-btn').click(function(e){
		e.preventDefault();
		toastr.warning('<p>'+myAccount.logoutText+'</p><div><a id="yeslogout">'+myAccount.logout+'</a><a id="nologout" style="float: right;">'+myAccount.cancel+'</a></div>');
	});

	jQuery('#yeslogout').live('click',function(e){
		e.preventDefault();
		window.location.href = jQuery('#logout-btn').attr('href');
	});

	jQuery('.profile-tab-list li.favorite').click(refreshFavorites);
	function refreshFavorites (){
		jQuery(this).unbind('click',refreshFavorites);
		jQuery(window).trigger('resize')
	}

	jQuery('body').on('keyup', 'input[name=first-name], input[name=last-name], input[name=email]',
		function() {
			enableDisableSubmit(
				jQuery('input[name=first-name]'),
				jQuery('input[name=last-name]'),
				jQuery('input[name=email]'),
				jQuery('#update-user')
			);
		}
	);
});