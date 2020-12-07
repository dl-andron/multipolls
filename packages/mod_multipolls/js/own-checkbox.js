jQuery(document).ready(function($)
{	
	$( ".cbo-answers .own-input" ).focusin(function() {
		$( this ).prev('.own-checkbox').attr('checked', true);
	});
})