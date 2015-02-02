(function($) {
	$(document).ready(function() {
		var $guests = $('#pa_guests');
		
		var checkGuests = function() {
			var guestVal = $guests.val();
			var product_id = $guests.data('product_id');
			var data = {
				action: 'cruise_check_avail',
				product_id: product_id,
				guests: guestVal
			};

			var this_page = window.location.toString();

			if ( !guestVal.length ) {
				return;
			}

			$('form.cart').fadeTo('400', '0.6').block({message: "Checking Availability...", overlayCSS: {background: 'transparent url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );

			$.post(ajax_object.ajax_url, data, function( response ) {
				if (response) {
					$(".single_variation_wrap").fadeIn();
					$(".single_add_to_cart_button").show();
					$(".out-of-stock").addClass('hide');
					$(".price").show().text('$' + response['price_' + guestVal] + ' / person');
					$("#variation_id").val(response.ID);
				} else {
					$(".out-of-stock").removeClass('hide');
					$(".single_add_to_cart_button").hide();
				}

				
				$('form.cart').stop(true).css('opacity', '1').unblock();
			});

		};

		$guests.on("change",function() {
			checkGuests();
		});
		checkGuests();
	});
})(jQuery);
