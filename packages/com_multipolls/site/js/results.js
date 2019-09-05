jQuery(document).ready(function($)
{	
	$(document).on('click', 'input[name=check-results]', function () {
		var poll_body = $(this).closest('form');
		var id_poll  = $(this).parent().siblings('input[name=id_poll]').val();
		var token = $("#token").attr("name");
	    $.ajax({
	        data: { [token]: "1", task: "getResults", format: "json", id_poll: id_poll },
	        success: function(response) {   
	        	poll_body.hide();          	
	        	poll_body.siblings('.results').html(response.data); 	        	
	        },
	        error: function() { 
	        	console.log('error'); 
	        },
	    });
	}); 

	$(document).on('click','input[name=back-to-poll]', function(){
		$(this).parent().siblings('form').show();
		$(this).parent().empty();
	});   
})