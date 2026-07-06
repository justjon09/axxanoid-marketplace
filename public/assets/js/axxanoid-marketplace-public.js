/**
 * Axxanoid Marketplace - Public JavaScript
 */
(function ($) {
	'use strict';
	$(function () {

		// --- Marketplace Direct Cart Push (Impulse Flow) ---
		$('.axx-market-claim-btn').on('click', function(e) {
			e.preventDefault(); 
			
			var $btn = $(this);
			var makerId = $btn.data('maker-id');
			var targetUrl = $btn.attr('href'); // e.g., "/checkout/"

			// Extract the secure token from the URL (for early renewals or authorized claims)
			var urlParams = new URLSearchParams(window.location.search);
			var secureToken = urlParams.get('marketplace_token');

			// Visual feedback
			$btn.text('Preparing Checkout...').css('opacity', '0.8').prop('disabled', true);

			// Ping the isolated Marketplace AJAX endpoint
			$.post(axxMarketAjax.ajax_url, {
				action: 'axx_market_set_claim_session',
				nonce: axxMarketAjax.nonce,
				maker_id: makerId,
				token: secureToken
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

		// --- Maker Intake Form Submission ---
		$('#axx-market-intake-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this);
			var $btn  = $form.find('button[type="submit"]');
			var $msg  = $('#axx-intake-message');

			$btn.text('Submitting...').prop('disabled', true);
			$msg.hide().removeClass('success error');

			$.post(axxMarketAjax.ajax_url, {
				action:      'axx_market_submit_intake',
				nonce:       axxMarketAjax.nonce,
				maker_name:  $('#axx_intake_name').val(),
				maker_email: $('#axx_intake_email').val(),
				maker_url:   $('#axx_intake_url').val(),
				maker_stock: $('#axx_intake_stock').val()
			}).done(function(response) {
				$msg.show();
				if (response.success) {
					$msg.css({'background': '#d1fae5', 'color': '#065f46', 'border': '1px solid #34d399'}).text(response.data);
					$form[0].reset();
					$btn.hide(); // Hide the button so they don't double submit
				} else {
					$msg.css({'background': '#fee2e2', 'color': '#991b1b', 'border': '1px solid #f87171'}).text(response.data);
					$btn.text('Submit Application').prop('disabled', false);
				}
			}).fail(function() {
				$msg.show().css({'background': '#fee2e2', 'color': '#991b1b', 'border': '1px solid #f87171'}).text('Server error. Please try again.');
				$btn.text('Submit Application').prop('disabled', false);
			});
		});

	});
})(jQuery);