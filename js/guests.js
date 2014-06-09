(function($) {
	$(document).ready(function() {

		$('#guests').on("change",function() {
			var $guests = $(this);
			
			var guestVal = $guests.val();
			var product_id = $guests.data('product_id');
			var data = {
				action: 'add_new_price',
				product_id: product_id,
				guests: guestVal
			};

			var this_page = window.location.toString();

			if ( !guestVal.length ) {
				return;
			}

			$('form.cart').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'transparent url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );

			$.post(ajax_object.ajax_url, data, function( response ) {
				$('.woocommerce-error, .woocommerce-message').remove();
				fragments = response.fragments;
				errors = response.errors;

				if ( errors ) {
					$.each(errors, function(key, value) {
						$(key).replaceWith(value);
					});
				}

				if ( fragments ) {
					$.each(fragments, function(key, value) {
						$(key).replaceWith(value);
					});
				}

				// Unblock
				$('form.cart').stop(true).css('opacity', '1').unblock();
			
			});
		});

	});
})(jQuery);
