jQuery(document).ready(function($)
{	
	$( ".ro-answers .own-input" ).focusin(function() {
		$( this ).prev('.own-radio').attr('checked', true);
	});
})