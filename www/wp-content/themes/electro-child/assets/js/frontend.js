(function($){
	"use strict";

	jQuery(document).ready(function($){

		$('form.woocommerce-checkout').on('click', '.wc_payment_methods input[type="radio"]', function(){
			var wc_payment_methods = $(this).val();

			if(wc_payment_methods == 'cheque'){
				$('.woocommerce-remove-coupon').trigger('click');
				$('#yith-par-message-reward-cart, #yith-par-message-cart').hide();
			}else{
				$('#yith-par-message-reward-cart, #yith-par-message-cart').show();
			}


			var form_data = {
				'action': globals.checkout_payment_action,
				'nonce': globals.nonce,
				'wc_payment_methods': wc_payment_methods
			};

			$.ajax({
				type: "POST",
				url: globals.ajax_url,
				data: form_data,
				dataType: "json"
			}).done(function(responce){
				if(responce.error == 0){

				}else{
					console.log(responce);
				}
			}).fail(function(){
				console.log("SYSTEM TECHNICAL ERROR");
			});

		});

		$(document).on('click', '.shop-view-switcher .nav-link', function(){
			$.cookie('product-view-type', $(this).data('archiveClass'), {path: '/', expires: new Date(Date.now() + (365 * 86400 * 1000))});
			$('[data-toggle="shop-products"]').attr('data-view', $(this).data('archiveClass'));
		});

	});

})(jQuery);
