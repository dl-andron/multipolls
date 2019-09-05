jQuery(document).ready(function($)
{	
	$('.active-title').siblings('.poll-body').hide();
	$('.active-title').on('click', function(){
		$(this).siblings('.poll-body').slideToggle(300);
	})
})