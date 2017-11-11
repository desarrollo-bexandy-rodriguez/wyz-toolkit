"use strict";
function initMap() {
	var lat = (!isNaN(contactMap.coor.latitude) ? contactMap.coor.latitude : '');
	var lon = (!isNaN(contactMap.coor.longitude) ? contactMap.coor.longitude : '');
	var latC = parseFloat(contactMap.coor.latitude);
	var scale = Math.pow(2, parseInt(contactMap.zoom));
	latC += ((parseInt(document.getElementById('contact-map').clientHeight / 5)) / scale);
	var latLng = new google.maps.LatLng(lat, lon);
	var latLngC = new google.maps.LatLng(latC, lon);
	var scrollwheel = 'on' == mapScrollZoom ? true : false;
	var map = new google.maps.Map(document.getElementById('contact-map'), {
		zoom: parseInt(contactMap.zoom),
		center: latLngC,
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});


	var marker = new google.maps.Marker({
		position: latLng,
		icon: contactMap.marker,
		scrollwheel : scrollwheel,
		map: map,
		anchor: new google.maps.Point(20, 27),
	});

}
google.maps.event.addDomListener(window, 'load', initMap);