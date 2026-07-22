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
			
			// HONEYPOT CHECK: If filled, it's a bot. Stop execution silently.
			if ( $('#axx_intake_hp').val() !== '' ) {
				return false;
			}

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
				$btn.text('Request Listing').prop('disabled', false);
			});
		});

		// --- Onboarding Portal: Dynamic Socials Repeater ---
		const onboardSocialWrapper = document.getElementById('axx-onboard-socials-wrapper');
		if (onboardSocialWrapper) {
			document.getElementById('axx-add-onboard-social').addEventListener('click', function(e) {
				e.preventDefault();
				const index = onboardSocialWrapper.children.length;
				const row = document.createElement('div');
				row.className = 'axx-repeater-row';
				
				let optionsHtml = '<option value="">Select Platform...</option>';
				for (const [key, data] of Object.entries(window.axxSocialNetworks)) {
					optionsHtml += `<option value="${key}">${data.label}</option>`;
				}

				row.innerHTML = `
					<select name="socials[${index}][platform]" class="axx-input-full axx-social-select-auto">
						${optionsHtml}
					</select>
					<input type="text" name="socials[${index}][handle]" placeholder="Handle or URL" class="axx-input-full axx-flex-1" />
					<a href="#" class="axx-repeater-remove">&times;</a>
				`;
				onboardSocialWrapper.appendChild(row);
			});

			onboardSocialWrapper.addEventListener('click', function(e) {
				if(e.target.classList.contains('axx-repeater-remove')) {
					e.preventDefault();
					e.target.closest('.axx-repeater-row').remove();
				}
			});
		}

		// --- Onboarding Portal: Dynamic Awards Repeater ---
		const onboardAwardsWrapper = document.getElementById('axx-onboard-awards-wrapper');
		if (onboardAwardsWrapper) {
			document.getElementById('axx-add-onboard-award').addEventListener('click', function(e) {
				e.preventDefault();
				const index = onboardAwardsWrapper.children.length;
				const row = document.createElement('div');
				row.className = 'axx-repeater-row';
				row.innerHTML = `
					<input type="text" name="awards[${index}][title]" placeholder="Award Title" class="axx-input-full axx-flex-1" />
					<input type="text" name="awards[${index}][place]" placeholder="Place (e.g. 1st)" class="axx-input-small" />
					<a href="#" class="axx-repeater-remove">&times;</a>
				`;
				onboardAwardsWrapper.appendChild(row);
			});

			onboardAwardsWrapper.addEventListener('click', function(e) {
				if(e.target.classList.contains('axx-repeater-remove')) {
					e.preventDefault();
					e.target.closest('.axx-repeater-row').remove();
				}
			});
		}

		// --- Onboarding Portal: Save Profile (Allows File Uploads) ---
		$('#axx-maker-profile-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this);
			var $btn  = $form.find('button[type="submit"]');
			var $msg  = $('#axx-profile-msg');
			
			$btn.text('Saving...').prop('disabled', true);
			$msg.hide().removeClass('notice-success notice-error');

			// Using FormData because we are sending actual image files
			var formData = new FormData(this);

			$.ajax({
				url: axxMarketAjax.ajax_url,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(response) {
					$msg.show();
					if (response.success) {
						$msg.css({'color': 'green'}).text('Profile saved successfully!');
						$btn.text('Save Profile Data').prop('disabled', false);
					} else {
						$msg.css({'color': 'red'}).text(response.data);
						$btn.text('Save Profile Data').prop('disabled', false);
					}
				},
				error: function() {
					$msg.show().css({'color': 'red'}).text('Server error saving profile.');
					$btn.text('Save Profile Data').prop('disabled', false);
				}
			});
		});

		// --- Onboarding Portal: Add Product ---
		$('#axx-maker-product-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this);
			var $btn  = $form.find('button[type="submit"]');
			var $msg  = $('#axx-product-msg');
			
			$btn.text('Adding Product...').prop('disabled', true);
			$msg.hide();

			$.post(axxMarketAjax.ajax_url, $form.serialize())
			.done(function(response) {
				$msg.show();
				if (response.success) {
					$msg.css({'color': 'green'}).text('Product added successfully!');
					$form[0].reset();
					setTimeout(() => location.reload(), 1500); // Reload to show new grid item
				} else {
					$msg.css({'color': 'red'}).text(response.data);
					$btn.text('+ Add Product to Portfolio').prop('disabled', false);
				}
			}).fail(function() {
				$msg.show().css({'color': 'red'}).text('Server error adding product.');
				$btn.text('+ Add Product to Portfolio').prop('disabled', false);
			});
		});

		// --- Onboarding Portal: Submit for Review ---
		$('#axx-maker-submit-review-form').on('submit', function(e) {
			e.preventDefault();
			if (!confirm('Are you sure? Once submitted, you cannot edit your profile until it has been reviewed.')) return;

			var $form = $(this);
			var $btn  = $form.find('button[type="submit"]');
			$btn.text('Submitting...').prop('disabled', true);

			$.post(axxMarketAjax.ajax_url, $form.serialize())
			.done(function(response) {
				if (response.success) {
					location.reload(); // Reload to show the "Pending Review" screen
				} else {
					alert(response.data);
					$btn.text('Submit Portfolio For Review').prop('disabled', false);
				}
			});
		});

        // --- Onboarding Portal: Remove Product ---
        $(document).on('click', '.axx-remove-product-btn', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to permanently delete this product?')) return;

            var $btn = $(this);
            var productId = $btn.data('product-id');
            var makerId = $btn.data('maker-id');

            $btn.text('Removing...').css('opacity', '0.5');

            $.post(axxMarketAjax.ajax_url, {
                action: 'axx_market_remove_onboard_product',
                nonce: axxMarketAjax.nonce,
                product_id: productId,
                maker_id: makerId
            }).done(function(response) {
                if(response.success) {
                    $btn.closest('.product').fadeOut(300, function() { $(this).remove(); });
                } else {
                    alert(response.data);
                    $btn.text('Remove').css('opacity', '1');
                }
            });
        });

	});
})(jQuery);