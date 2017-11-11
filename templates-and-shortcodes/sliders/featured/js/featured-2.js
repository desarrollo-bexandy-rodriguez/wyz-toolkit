"use strict";
jQuery(document).ready( function(){
	var msGrid = jQuery('.featured-masonry-grid');
	/*-- Images Loaded --*/
	msGrid.imagesLoaded( function() {
		msGrid.masonry({
			itemSelector: '.masonry-item'
		});
	});
});
    