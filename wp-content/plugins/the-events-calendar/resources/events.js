jQuery(document).ready(function($) {
	// prepare calendar for popups
	$("table.calendar tbody tr").each(function(index) {
		// add a class of "right" to Friday & Saturday so tooltips stay onscreen
		$(this).find("td:gt(3)").addClass("right");
	});

	
	// popups
	$("table.calendar .event a").hover(function() {
		
		// one for IE6, one for everybody else
		if ($.browser.msie && $.browser.version == 6.0) {
			var bottomPad = $(this).parents("td").outerHeight() + 5;
		}
		else {
			var bottomPad = $(this).outerHeight() + 18;
		}
		
		$(this).next(".tooltip").css('bottom', bottomPad).fadeIn(300);
	}, function() {
		$(this).next(".tooltip").fadeOut(100);
	});
});
