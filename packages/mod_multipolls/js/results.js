(function ($) {
	$(document).on('click', 'input[name=results]', function () {
		var poll_body = $(this).closest('.poll-body');
		var value   = $(this).parent().siblings('input[name=id_poll]').val(),
			request = {
					'option' : 'com_ajax',
					'module' : 'multipolls',
					'data'   : value,
					'format' : 'raw'
				};
		$.ajax({
			type   : 'POST',
			data   : request,
			success: function (response) {
				poll_body.children('form').hide();
				poll_body.children('.results').html(response);
			}
		});
		return false;		
	});	

	$(document).on('click','input[name=back-to-poll]', function(){
		$(this).parent().siblings('form').show();
		$(this).parent().empty();
	});
})(jQuery);