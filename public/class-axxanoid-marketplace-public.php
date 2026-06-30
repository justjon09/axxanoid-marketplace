<?php
/**
 * Enqueues the public-facing Javascript and styles for the Marketplace.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Axxanoid_Marketplace_Public {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
    }

    public function enqueue_public_scripts() {
        // Only load on Maker pages to save resources
        if ( is_singular( 'axx_market_maker' ) ) {
            wp_enqueue_script( 
                'axx-market-public-js', 
                AXX_MARKET_PLUGIN_URL . 'public/assets/js/axxanoid-marketplace-public.js', 
                array( 'jquery' ), 
                AXX_MARKET_VERSION, 
                true 
            );

            // Notice the specific 'axxMarketAjax' object name to avoid Directory plugin collisions
            wp_localize_script( 'axx-market-public-js', 'axxMarketAjax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'axx_market_public_nonce' ),
            ));
        }
    }
}