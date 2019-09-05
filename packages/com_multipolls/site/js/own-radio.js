jQuery(document).ready(function($)
{	
	$( ".own-input" ).focusin(function() {
		$( this ).prev('.own-radio').attr('checked', true);
	});
})