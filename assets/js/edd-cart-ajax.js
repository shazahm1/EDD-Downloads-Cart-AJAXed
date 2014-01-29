;jQuery(document).ready( function ($) {

	$.ajax({
		type: 'POST',
		data: { action: 'ajax_refresh_cart'},
		dataType: 'json',
		url: edd_scripts.ajaxurl,
		cache: false,
		success: function (response) {
			// console.log( response );

			$( response.widget ).slideUp('slow', function() {

				$( response.widget ).html( response.content ).slideDown('slow')
			});
		}
	}).fail( function (response) {

		if ( window.console && window.console.log ) {
			console.log( response );
		}

	}).done( function (response) {

	});

});
