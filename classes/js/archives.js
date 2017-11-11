jQuery(document).ready(function(){
	// business tags filteer
/*	if (jQuery('#wyz-tag-filter').length) {

		jQuery('#wyz-tag-filter').selectize({
			create: false,
			plugins: ['remove_button']
		});
	}*/

	// business day filteer
	if (jQuery('#wyz-day-filter').length) {

		jQuery('#wyz-day-filter').selectize({
			create: false,
			plugins: ['remove_button']
		});
	}

	if ( undefined !== WyzLocFilter && undefined !== WyzLocFilter.filterType && 'text' == WyzLocFilter.filterType
		&& jQuery('#wyz-loc-filter-txt').length) {

		function intialize() {

			var input = document.getElementById('wyz-loc-filter-txt');
			var autocomplete = new google.maps.places.Autocomplete(input);
			google.maps.event.addListener(autocomplete, 'place_changed', function () {
				var place = autocomplete.getPlace();
				document.getElementById('loc-filter-txt').value = place.name;
				document.getElementById('loc-filter-lat').value = place.geometry.location.lat();
				document.getElementById('loc-filter-lon').value = place.geometry.location.lng();

			});
		}
		
		google.maps.event.addDomListener(window, 'load', intialize);

	}

});