/**
 * Axxanoid Marketplace - Public JavaScript
 */
(function ($) {
	'use strict';
	$(function () {

		// --- Marketplace Direct Cart Push (Impulse Flow) ---
		$('.axx-market-claim-btn').on('click', function(e) {
			e.preventDefault(); // Stop the immediate redirect
			
			var $btn = $(this);
			var makerId = $btn.data('maker-id');
			var targetUrl = $btn.attr('href'); // e.g., "/checkout/"

			// Visual feedback
			$btn.text('Preparing Checkout...').css('opacity', '0.8').prop('disabled', true);

			// Ping the isolated Marketplace AJAX endpoint
			$.post(axxMarketAjax.ajax_url, {
				action: 'axx_market_set_claim_session',
				nonce: axxMarketAjax.nonce,
				maker_id: makerId
			}).done(function(response) {
				console.log("Marketplace Cart Push Response: ", response);
				if (response.success) {
					// Session is set and cart is prepped natively! Teleport to clean URL.
					window.location.href = targetUrl;
				} else {
					alert(response.data || 'Error starting the claim process. Please try again.');
					$btn.text('Claim Profile').css('opacity', '1').prop('disabled', false);
				}
			}).fail(function() {
				alert('Server error. Please try again.');
				$btn.text('Claim Profile').css('opacity', '1').prop('disabled', false);
			});
		});

	});
})(jQuery);