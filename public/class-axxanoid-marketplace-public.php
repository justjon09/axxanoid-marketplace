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
        add_filter( 'template_include', array( $this, 'load_siloed_templates' ) );
    }

    public function enqueue_public_scripts() {
        // Fetch dynamic base slug to ensure we enqueue on the correct Hub page
        $options = get_option( 'axxanoid_marketplace_settings', array() );
        $base_slug = isset( $options['maker_base_slug'] ) ? $options['maker_base_slug'] : 'marketplace/makers';
        $parts = explode( '/', trim( $base_slug, '/' ) );
        $hub_slug = $parts[0];

        // Only load on Maker pages to save resources
        // Check if we are on ANY of the 3 Marketplace pages (Hub, Archive, Single Profile)
        if ( is_singular( 'axx_market_maker' ) || is_post_type_archive( 'axx_market_maker' ) || is_page( $hub_slug ) ) {

            // Load Marketplace CSS/JS
            wp_enqueue_style( 
                'axx-market-public-css', 
                AXX_MARKET_PLUGIN_URL . 'public/assets/css/axxanoid-marketplace-public.css', 
                array(), 
                AXX_MARKET_VERSION 
            );

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

            // THE WOOCOMMERCE BRIDGE: Force Woo assets to load on the custom templates
            if ( function_exists( 'WC' ) ) {
                // Forces Woo's JS (AJAX Add to Cart, variations, etc.)
                WC_Frontend_Scripts::load_scripts();
                
                // Forces Woo's native grid CSS so products look beautiful
                wp_enqueue_style( 'woocommerce-general' );
                wp_enqueue_style( 'woocommerce-layout' );
                wp_enqueue_style( 'woocommerce-smallscreen' );
            }
        }
    }

    /**
     * Intercepts the WordPress template hierarchy and forces our plugin templates.
     */
    public function load_siloed_templates( $template ) {

        $options = get_option( 'axxanoid_marketplace_settings', array() );
        $base_slug = isset( $options['maker_base_slug'] ) ? $options['maker_base_slug'] : 'marketplace/makers';
        $parts = explode( '/', trim( $base_slug, '/' ) );
        $hub_slug = $parts[0]; // Matches the auto-created page
        
        // Intercept the Single Vanity Profile (e.g., /marketplace/makers/joes-glass)
        if ( is_singular( 'axx_market_maker' ) ) {
            $plugin_template = AXX_MARKET_PLUGIN_DIR . 'public/templates/single-axx_market_maker.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        } 
        
        // Intercept the Search/Directory Archive (e.g., /marketplace/makers/)
        elseif ( is_post_type_archive( 'axx_market_maker' ) ) {
            $plugin_template = AXX_MARKET_PLUGIN_DIR . 'public/templates/archive-axx_market_maker.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        // Intercept the dynamically generated Explainer Hub
        elseif ( is_page( $hub_slug ) ) {
            $plugin_template = AXX_MARKET_PLUGIN_DIR . 'public/templates/page-marketplace-hub.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        // Fallback to the default theme template if ours are missing
        return $template;
    }
}