jQuery(document).ready(function($)
{		
	$('input[type="radio"]').click(function(){
		
		if ($(this).is(':checked')){			
			
			var className = $(this).closest('.answers').next().attr("class");

			if(className == 'answers') {
				$(this).closest('.answers').next().show();
			} else {
				$(this).closest('.multipoll').find('.poll-button input[type="submit"]').attr('disabled', false);
			}
		}
	});

	$( ".ro-answers .own-input" ).focusin(function() {

		var className = $(this).closest('.answers').next().attr("class");

		if(className == 'answers') {
			$(this).closest('.answers').next().show();
		} else {
			$(this).closest('.multipoll').find('.poll-button input[type="submit"]').attr('disabled', false);
		}
	
	});
});