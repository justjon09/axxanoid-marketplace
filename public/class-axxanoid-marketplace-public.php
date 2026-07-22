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
        add_action( 'template_redirect', array( $this, 'enforce_private_maker_profiles' ) );
        add_filter( 'template_include', array( $this, 'load_siloed_templates' ) );
        // Intake Form Handlers
        add_action( 'wp_ajax_axx_market_submit_intake', array( $this, 'ajax_handle_maker_intake' ) );
        add_action( 'wp_ajax_nopriv_axx_market_submit_intake', array( $this, 'ajax_handle_maker_intake' ) );
        // Onboarding Form Handlers
        add_action( 'wp_ajax_axx_market_save_onboard_profile', array( $this, 'ajax_save_onboard_profile' ) );
        add_action( 'wp_ajax_nopriv_axx_market_save_onboard_profile', array( $this, 'ajax_save_onboard_profile' ) );
        
        add_action( 'wp_ajax_axx_market_save_onboard_product', array( $this, 'ajax_save_onboard_product' ) );
        add_action( 'wp_ajax_nopriv_axx_market_save_onboard_product', array( $this, 'ajax_save_onboard_product' ) );
        
        add_action( 'wp_ajax_axx_market_submit_for_review', array( $this, 'ajax_submit_for_review' ) );
        add_action( 'wp_ajax_nopriv_axx_market_submit_for_review', array( $this, 'ajax_submit_for_review' ) );

        add_action( 'wp_ajax_axx_market_remove_onboard_product', array( $this, 'ajax_remove_onboard_product' ) );
        add_action( 'wp_ajax_nopriv_axx_market_remove_onboard_product', array( $this, 'ajax_remove_onboard_product' ) );
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

    public function enforce_private_maker_profiles() {
        if ( ! is_singular( 'axx_market_maker' ) ) {
            return;
        }

        $maker_id = get_the_ID();
        $status   = get_post_meta( $maker_id, 'marketplace_status', true ) ?: 'Trial';

        $provided_token   = isset( $_GET['marketplace_token'] ) ? sanitize_text_field( wp_unslash( $_GET['marketplace_token'] ) ) : '';
        $saved_token      = get_post_meta( $maker_id, 'marketplace_claim_token', true );
        $is_authenticated = ( ! empty( $provided_token ) && $provided_token === $saved_token ) || current_user_can( 'manage_options' );

        // Pass the auth state to the template for this specific page load only
        set_query_var( 'axx_is_maker_auth', $is_authenticated );

        if ( in_array( $status, array( 'Trial', 'Active' ), true ) ) {
            return;
        }

        if ( ! $is_authenticated ) {
            $options = get_option( 'axxanoid_marketplace_settings', array() );
            $base_slug = isset( $options['maker_base_slug'] ) ? $options['maker_base_slug'] : 'marketplace/makers';
            $parts = explode( '/', trim( $base_slug, '/' ) );
            wp_safe_redirect( home_url( '/' . $parts[0] . '/' ) );
            exit;
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
            $status = get_post_meta( get_the_ID(), 'marketplace_status', true ) ?: 'Trial';
            
            if ( in_array( $status, array( 'Onboarding', 'Pending Review' ), true ) ) {
                $plugin_template = AXX_MARKET_PLUGIN_DIR . 'public/templates/onboard-single-axx_market_maker.php';
            } elseif ( $status === 'Expired') {
                $plugin_template = AXX_MARKET_PLUGIN_DIR . 'public/templates/expired-single-axx_market_maker.php';
            } else {
                $plugin_template = AXX_MARKET_PLUGIN_DIR . 'public/templates/single-axx_market_maker.php';
            }

            if ( file_exists( $plugin_template ) ) return $plugin_template;
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

    /**
     * Catches the inbound Maker application form.
     */
    public function ajax_handle_maker_intake() {
        check_ajax_referer( 'axx_market_public_nonce', 'nonce' );

        $name  = isset( $_POST['maker_name'] ) ? sanitize_text_field( wp_unslash( $_POST['maker_name'] ) ) : '';
        $email = isset( $_POST['maker_email'] ) ? sanitize_email( wp_unslash( $_POST['maker_email'] ) ) : '';
        $url   = isset( $_POST['maker_url'] ) ? esc_url_raw( wp_unslash( $_POST['maker_url'] ) ) : '';

        if ( empty( $name ) || empty( $email ) ) {
            wp_send_json_error( 'Please provide your Maker Name and Email.' );
        }

        // Prevent duplicate applications
        $existing = get_posts( array(
            'post_type'   => 'axx_market_maker',
            'meta_key'    => 'maker_email',
            'meta_value'  => $email,
            'fields'      => 'ids',
            'post_status' => 'any'
        ) );

        if ( ! empty( $existing ) ) {
            wp_send_json_error( 'An application with this email already exists.' );
        }

        // Create the Maker Profile ('publish' is required so the Token URL resolves through the Gatekeeper)
        $maker_id = wp_insert_post( array(
            'post_title'   => $name,
            'post_status'  => 'publish', 
            'post_type'    => 'axx_market_maker'
        ) );

        if ( is_wp_error( $maker_id ) ) {
            wp_send_json_error( 'System error. Please try again.' );
        }

        // Map the initial data
        update_post_meta( $maker_id, 'maker_email', $email );
        if ( ! empty( $url ) ) update_post_meta( $maker_id, 'maker_url', $url );
        
        // Flag for Onboarding & Generate Secure Token
        update_post_meta( $maker_id, 'marketplace_status', 'Onboarding' );
        update_post_meta( $maker_id, 'marketplace_claim_token', wp_generate_password( 20, false ) );
        
        // Blank date tells Python to send the magic link
        update_post_meta( $maker_id, 'onboard_sent_date', '' );

        wp_send_json_success( 'Application received! Check your email in a few minutes for your secure setup link.' );
    }

    /**
     * Secures and authenticates incoming Maker AJAX requests.
     */
    private function authenticate_maker_ajax() {
        check_ajax_referer( 'axx_market_public_nonce', 'nonce' );
        $maker_id = isset( $_POST['maker_id'] ) ? absint( $_POST['maker_id'] ) : 0;
        if ( ! $maker_id || get_post_type( $maker_id ) !== 'axx_market_maker' ) {
            wp_send_json_error( 'Invalid Profile.' );
        }
        return $maker_id;
    }

    /**
     * Saves the Maker's bio, text, JSON repeaters, and securely handles image uploads.
     */
    public function ajax_save_onboard_profile() {
        $maker_id = $this->authenticate_maker_ajax();

        // Save Basic Text Fields
        if ( isset( $_POST['maker_callout_text'] ) ) update_post_meta( $maker_id, 'maker_callout_text', sanitize_textarea_field( wp_unslash( $_POST['maker_callout_text'] ) ) );
        if ( isset( $_POST['maker_bio'] ) ) update_post_meta( $maker_id, 'maker_bio', wp_kses_post( wp_unslash( $_POST['maker_bio'] ) ) );
        if ( isset( $_POST['maker_display_email'] ) ) update_post_meta( $maker_id, 'maker_display_email', sanitize_email( wp_unslash( $_POST['maker_display_email'] ) ) );

        // Handle JSON Array for Socials
        if ( isset( $_POST['socials'] ) && is_array( $_POST['socials'] ) ) {
            $clean_socials = array();
            foreach ( $_POST['socials'] as $social ) {
                if ( ! empty( $social['platform'] ) && ! empty( $social['handle'] ) ) {
                    $clean_socials[] = array(
                        'platform' => sanitize_text_field( $social['platform'] ),
                        'handle'   => sanitize_text_field( $social['handle'] )
                    );
                }
            }
            update_post_meta( $maker_id, 'maker_social_urls', wp_json_encode( $clean_socials ) );
        } else {
            update_post_meta( $maker_id, 'maker_social_urls', '[]' );
        }

        // Handle JSON Array for Awards
        if ( isset( $_POST['awards'] ) && is_array( $_POST['awards'] ) ) {
            $clean_awards = array();
            foreach ( $_POST['awards'] as $award ) {
                if ( ! empty( $award['title'] ) ) {
                    $clean_awards[] = array(
                        'title' => sanitize_text_field( $award['title'] ),
                        'place' => sanitize_text_field( $award['place'] )
                    );
                }
            }
            update_post_meta( $maker_id, 'maker_awards', wp_json_encode( $clean_awards ) );
        } else {
            update_post_meta( $maker_id, 'maker_awards', '[]' );
        }

        // Native WP Media File Upload Handler
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        if ( ! empty( $_FILES['maker_header_banner']['name'] ) ) {
            $banner_id = media_handle_upload( 'maker_header_banner', $maker_id );
            if ( ! is_wp_error( $banner_id ) ) {
                update_post_meta( $maker_id, 'maker_header_banner', $banner_id );
            }
        }

        if ( ! empty( $_FILES['maker_portrait']['name'] ) ) {
            $portrait_id = media_handle_upload( 'maker_portrait', $maker_id );
            if ( ! is_wp_error( $portrait_id ) ) {
                update_post_meta( $maker_id, 'maker_portrait', $portrait_id );
            }
        }

        wp_send_json_success( 'Saved successfully.' );
    }

    /**
     * Accepts a product submission, dynamically creates a WooCommerce External product,
     * sideloads the image URL, and ties it to the Maker.
     */
    public function ajax_save_onboard_product() {
        $maker_id = $this->authenticate_maker_ajax();

        if ( ! function_exists( 'WC' ) ) wp_send_json_error( 'WooCommerce is not active.' );

        $title = isset( $_POST['product_title'] ) ? sanitize_text_field( wp_unslash( $_POST['product_title'] ) ) : '';
        $price = isset( $_POST['product_price'] ) ? floatval( $_POST['product_price'] ) : 0;
        $image_url = isset( $_POST['product_image'] ) ? esc_url_raw( wp_unslash( $_POST['product_image'] ) ) : '';
        $checkout_url = isset( $_POST['product_url'] ) ? esc_url_raw( wp_unslash( $_POST['product_url'] ) ) : '';

        if ( empty( $title ) || empty( $image_url ) || empty( $checkout_url ) ) {
            wp_send_json_error( 'Please fill out all required product fields.' );
        }

        // Create the External Product
        $product = new WC_Product_External();
        $product->set_name( $title );
        $product->set_regular_price( $price );
        $product->set_product_url( $checkout_url );
        $product->set_button_text( 'Buy from Maker' );
        $product->set_status( 'publish' );

        // Tag to parent "Indie Finds" category
        $term = get_term_by( 'slug', 'indie-finds', 'product_cat' );
        if ( $term ) {
            $product->set_category_ids( array( $term->term_id ) );
        }

        // Tag to Maker's specific Brand ID
        $brand_id = get_post_meta( $maker_id, 'woo_brand_id', true );
        if ( $brand_id ) {
            wp_set_object_terms( $product->get_id(), array( intval( $brand_id ) ), 'brand' );
        }

        $product_id = $product->save();

        if ( $product_id ) {
            // Sideload the external image so WooCommerce can use it locally
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            
            $tmp = download_url( $image_url );
            if ( ! is_wp_error( $tmp ) ) {
                $file_array = array(
                    'name'     => basename( wp_parse_url( $image_url, PHP_URL_PATH ) ),
                    'tmp_name' => $tmp
                );
                $attach_id = media_handle_sideload( $file_array, $product_id );
                if ( ! is_wp_error( $attach_id ) ) {
                    set_post_thumbnail( $product_id, $attach_id );
                } else {
                    @unlink( $file_array['tmp_name'] );
                }
            }

            // Save the child product ID natively to the Maker CPT (Multiple Rows = 'single' => false)
            add_post_meta( $maker_id, 'maker_product_ids', $product_id, false );

            wp_send_json_success();
        }

        wp_send_json_error( 'Failed to create product.' );
    }

    /**
     * Flips the Maker status to Pending Review and locks the frontend.
     */
    public function ajax_submit_for_review() {
        $maker_id = $this->authenticate_maker_ajax();
        update_post_meta( $maker_id, 'marketplace_status', 'Pending Review' );
        wp_send_json_success();
    }

    /**
     * Allows Maker to permanently delete a product.
     */
    public function ajax_remove_onboard_product() {
        $maker_id = $this->authenticate_maker_ajax();
        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

        if ( ! $product_id ) wp_send_json_error( 'Invalid Product.' );

        // Security Check: Ensure this product actually belongs to this Maker
        $owned_products = get_post_meta( $maker_id, 'maker_product_ids', false );
        if ( ! in_array( $product_id, $owned_products ) ) {
            wp_send_json_error( 'Permission Denied.' );
        }

        // Trash the WooCommerce Product
        wp_trash_post( $product_id );

        // Remove the specific row from the Maker CPT
        delete_post_meta( $maker_id, 'maker_product_ids', $product_id );

        wp_send_json_success();
    }
}