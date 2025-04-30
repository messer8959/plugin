jQuery( function ($) {

    let form = $( '#event-form' );
	let buttonSubmit = $( '.submit-event' );

	let options = {
		// url: afcp_ajax.url,
        url: 'http://front/wp-admin/admin-ajax.php',
		data: {
			action: 'created_event',
			nonce: afcp_ajax.nonce,
		},
		type: 'POST', 
		dataType: 'json',
		beforeSubmit: function(arr, form, options){
			buttonSubmit.text('Sending..');
		},
		success: function(response){
			console.log(response);
		}
    }

	form.ajaxForm( options );

});