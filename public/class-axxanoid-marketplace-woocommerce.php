<?php
/**
 * Handles WooCommerce AJAX cart interception, session management, and order fulfillment.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Axxanoid_Marketplace_WooCommerce {

	public function __construct() {
		// 1. Isolated AJAX Handlers to prep the cart and session
		add_action( 'wp_ajax_axx_market_set_claim_session', array( $this, 'ajax_set_market_claim_session' ) );
		add_action( 'wp_ajax_nopriv_axx_market_set_claim_session', array( $this, 'ajax_set_market_claim_session' ) );

		// 2. Stamp the WooCommerce Order with the Maker ID upon checkout
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'stamp_order_with_market_maker_id' ) );

		// 3. Fulfill the Order: Flip the Maker Status to 'Active' when paid
		add_action( 'woocommerce_payment_complete', array( $this, 'process_market_maker_payment' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'process_market_maker_payment' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'process_market_maker_payment' ) );
	}

	/**
	 * AJAX: Intercepts the click, natively clears cart, adds Rent product, and sets isolated session.
	 */
	public function ajax_set_market_claim_session() {
		check_ajax_referer( 'axx_market_public_nonce', 'nonce' );

		if ( ! function_exists( 'WC' ) ) {
			wp_send_json_error( 'WooCommerce is not active.' );
		}

		$maker_id = isset( $_POST['maker_id'] ) ? absint( $_POST['maker_id'] ) : 0;
        $provided_token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';

		if ( ! $maker_id || get_post_type( $maker_id ) !== 'axx_market_maker' ) {
			wp_send_json_error( 'Invalid Maker Profile.' );
		}

        $status = get_post_meta( $maker_id, 'marketplace_status', true ) ?: 'Trial';
		$saved_token = get_post_meta( $maker_id, '_axx_market_claim_token', true );

        $options = get_option( 'axxanoid_marketplace_settings', array() );
		$current_default_product = isset( $options['current_maker_profile_product'] ) ? absint( $options['current_maker_profile_product'] ) : 0;
		$product_id = 0;

        // --- THE SECURE PRICING ENGINE ---
		if ( $status === 'Expired' || empty( $provided_token ) || $provided_token !== $saved_token ) {
			// Penalty: They expired, or lack authorization token. Force current market rate.
			$product_id = $current_default_product;
			
			// Lock them into the new, higher price going forward
			if ( $product_id ) {
				update_post_meta( $maker_id, 'locked_in_product_id', $product_id );
			}
		} else {
			// Reward: They are Trial or Active AND have a valid token. Honor the grandfathered price.
			$product_id = get_post_meta( $maker_id, 'locked_in_product_id', true );
			
			if ( empty( $product_id ) ) {
				$product_id = $current_default_product;
				if ( $product_id ) {
					update_post_meta( $maker_id, 'locked_in_product_id', $product_id );
				}
			}
		}

        if ( ! $product_id ) {
			wp_send_json_error( 'Marketplace subscription product not configured.' );
		}

		// Initialize Woo Session
		if ( isset( WC()->session ) ) {
			if ( ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}

			// Lock the Maker ID into their backend session natively (Isolated naming)
			WC()->session->set( 'axx_market_claiming_maker_id', $maker_id );

			// Empty the cart to prevent double-billing and add the rent invoice
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $product_id );

            // Telemetry
			$clicks = (int) get_post_meta( $maker_id, 'checkout_clicks', true );
			update_post_meta( $maker_id, 'checkout_clicks', $clicks + 1 );

			wp_send_json_success( 'Session set and cart prepared.' );
		}

		wp_send_json_error( 'Failed to initialize session.' );
	}

	/**
	 * Stamps the WooCommerce order with the Maker ID before checkout finishes.
     * Updated for HPOS compatibility.
	 */
	public function stamp_order_with_market_maker_id( $order_id ) {
		if ( isset( WC()->session ) ) {
			$maker_id = WC()->session->get( 'axx_market_claiming_maker_id' );
			
			if ( $maker_id ) {
				$order = wc_get_order( $order_id );
				if ( $order ) {
                    // Use native Woo methods for HPOS support instead of update_post_meta
                    $order->update_meta_data( '_axx_market_maker_id', $maker_id );
					$order->add_order_note( 'Marketplace Inbound: Maker ID ' . $maker_id . ' is paying their digital rent.' );
                    $order->save();
				}
			}
		}
	}

	/**
	 * When the order is successfully paid, mark the Maker Profile as 'Active' and extend trial.
     * Updated for HPOS compatibility.
	 */
	public function process_market_maker_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) return;

        // Check if already processed using Woo methods
		$already_processed = $order->get_meta( '_axx_market_claim_processed' );
		if ( $already_processed ) return;

		$maker_id = $order->get_meta( '_axx_market_maker_id' );
		
		if ( $maker_id ) {
			// Check previous status BEFORE update
            $previous_status = get_post_meta( $maker_id, 'marketplace_status', true ) ?: 'Trial';
			// Flip their status
			update_post_meta( $maker_id, 'marketplace_status', 'Active' );
			// Log the active order ID
			update_post_meta( $maker_id, 'subscription_order_id', $order_id );

			// --- DATE STACKING LOGIC ---
            // Query Maker for existing expiration date to stack time safely
            $trial_expiration = get_post_meta( $maker_id, 'trial_expiration_date', true );
			$sub_expiration   = get_post_meta( $maker_id, 'paid_expiration_date', true );
            $base_time = time(); // Default baseline is right now

            // If renewing an existing active sub, stack on top of it
			if ( ! empty( $sub_expiration ) && strtotime( $sub_expiration ) > $base_time ) {
				$base_time = strtotime( $sub_expiration );
			} 
			// Otherwise, if converting from an active Trial, stack on top of the remaining trial days
			elseif ( ! empty( $trial_expiration ) && strtotime( $trial_expiration ) > $base_time ) {
				$base_time = strtotime( $trial_expiration );
			}

			// Add 30 days to their paid expiration
			$expiration_date = gmdate( 'Y-m-d', strtotime( '+30 days', $base_time ) );
			update_post_meta( $maker_id, 'paid_expiration_date', $expiration_date );
			
			// --- THE JETPACK TRIGGER ---
            // Only trigger social sharing if they are converting from Trial for the first time
            if ( $previous_status === 'Trial' ) {
                // Clear Jetpack's 'already shared' flag and queue it for broadcast
				delete_post_meta( $maker_id, '_publicize_done' );
				update_post_meta( $maker_id, '_publicize_pending', 1 );
            }
				
			// Ensure the profile is actually published
			wp_update_post( array(
				'ID'          => $maker_id,
				'post_status' => 'publish'
			) );

            // Mark order as processed via HPOS methods
            $order->update_meta_data( '_axx_market_claim_processed', true );
            $order->save();
			
			if ( isset( WC()->session ) ) {
				WC()->session->__unset( 'axx_market_claiming_maker_id' );
			}
		}
	}
}