jQuery(document).ready(function($)
{	
	$('.poll-button input[type="submit"]').attr('disabled', true);

	$('.answers:not(:first)').hide();		
	
	$('input[type="radio"]').click(function(){
		
		if ($(this).is(':checked')){			
			
			var className = $(this).closest('.answers').next().attr("class");

			if(className == 'answers') {
				$(this).closest('.answers').next().show();
			} else {
				$('.poll-button input[type="submit"]').attr('disabled', false);
			}
		}
	});

	$( ".ro-answers .own-input" ).focusin(function() {

		var className = $(this).closest('.answers').next().attr("class");

		if(className == 'answers') {
			$(this).closest('.answers').next().show();
		} else {
			$('.poll-button input[type="submit"]').attr('disabled', false);
		}
	
	});
});