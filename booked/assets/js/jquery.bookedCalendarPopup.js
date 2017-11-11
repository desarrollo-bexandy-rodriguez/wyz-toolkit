(function($){
 
    $.fn.bookedCalendarPopup = function( options ) {
 
		var settings = $.extend({
            classfinder	: 'bookedCalendarPopup',
            template : '<div class="booked-admin-calendar-wrap small"></div>'
        }, options );
        
        this.find('.'+settings.classfinder).each(function(){
	    	$(this).wrap( "<div class='bookedCalendarPopupWrapper'></div>" );
	    	$(this).click(function(){
		    	$(this).attr('disabled',true);	
		    	$('.booked-calendar-wrap').remove();
	        	var monthToLoad = $(this).attr('data-month');
				$(this).after(settings.template);
				init_booked_popup_calendar(monthToLoad);
			});
	    });
	    
	    function init_booked_popup_calendar(monthToLoad){
		    
		    $('.bookedCalendarPopupWrapper .booked-admin-calendar-wrap').spin('booked');
		    
		    var booked_ajaxURL	= $('#data-ajax-url').html(),
				calendar_id		= false;
			
			$('.bookedCalendarPopupWrapper .booked-admin-calendar-wrap').load(booked_ajaxURL, {'load':'calendar_picker','gotoMonth':monthToLoad}, function(){
				adjust_calendar_boxes();
			});
			
			return false;
			
	    }
	     
    };
 
}(jQuery));