"use strict";
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

function enableDisableSubmit(userRegister, userEmail, userFirst, userLast, submitBtn) {
	if ('' !== userRegister.val() && '' !== userEmail.val() && '' !== userFirst.val() && '' !== userLast.val() && passOK) {
		submitBtn.removeAttr('disabled');
		submitBtn.removeClass('wyz-btn-disabled');
		submitBtn.addClass('wyz-btn');
	} else {

		submitBtn.attr('disabled', 'disabled');
		submitBtn.addClass('wyz-btn-disabled');
		submitBtn.removeClass('wyz-btn');
	}
}

jQuery(document).ready(function() {
	// Binding to trigger checkPasswordStrength

	jQuery('body').on('keyup', 'input[name=wyz_user_pass], input[name=wyz_user_pass_confirm]',
		function() {
			enableDisableSubmit(
				jQuery('input[name=wyz_user_register]'),
				jQuery('input[name=wyz_user_email]'),
				jQuery('input[name=wyz_user_first]'),
				jQuery('input[name=wyz_user_last]'),
				jQuery('input[id=submit]')
			);

			checkPasswordStrength(
				jQuery('input[name=wyz_user_pass]'), // First password field
				jQuery('input[name=wyz_user_pass_confirm]'), // Second password field
				jQuery('#password-strength'), // Strength meter
				jQuery('input[id=submit]'), // Submit button
				['black', 'listed', 'word'] // Blacklisted words
			);
		});

	jQuery('body').on('keyup', 'input[name=wyz_user_register], input[name=wyz_user_email],' +
		'input[name=wyz_user_first], input[name=wyz_user_last]',
		function() {
			enableDisableSubmit(
				jQuery('input[name=wyz_user_register]'),
				jQuery('input[name=wyz_user_email]'),
				jQuery('input[name=wyz_user_first]'),
				jQuery('input[name=wyz_user_last]'),
				jQuery('input[id=submit]')
			);
		}
	);
});