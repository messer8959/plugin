jQuery(function ($) {

	let form = $('#event-form');
	let buttonSubmit = $('.submit-event');

	let options = {
		// url: afcp_ajax.url,
		url: afcp_rest.root + 'afcp/v1/add',
		type: 'POST',
		dataType: 'json',
        headers: {
            'X-WP-Nonce': afcp_rest.nonce
        },
		beforeSubmit: function (arr, form, options) {
			buttonSubmit.text('Sending..');
		},
		success: function (response) {
			if (response.data.response == 'ERROR') {
				try {
					$.each(response.data.message, function ($key, $value) {
						console.log($key);
						console.log($value);
						$('#' + $key + '_field').append('<span class = "error">' + $value + '</span>');
					});
				} catch ( e ){
					add_message(response.data.message, 'danger');
				}
				
			}else {
				add_message(response.data.message, 'success');
				buttonSubmit.text('Add Event');
				form.resetForm();
			}

			console.log(response);
		}
	}

	form.ajaxForm(options);

});

function add_message($msg, $type) {
	var body = jQuery('body');
	var html = '<div class="alert alert-' + $type + '">' + $msg + '</div>';
	body.find(jQuery('.alert')).remove();
	body.fadeIn('slow').prepend(html);

	setTimeout( function(){
		jQuery( '.alert' ).fadeOut('slow');
		
	}, 5000 );
}