(function ($) {
	$(document).on('submit', '.multipoll', function () {
		$(".poll-btn", this).attr("disabled", true);		
	});
})(jQuery);