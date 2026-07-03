<?php
/**
 * Registers the Custom Post Types, Taxonomies, and Meta for the Axxanoid Marketplace plugin.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Axxanoid_Marketplace_CPT
 *
 */
class Axxanoid_Marketplace_CPT {

	/**
	 * Constructor. Hooks the registration methods into WordPress.
	 */
	public function __construct() {
        // Registers the 'axxanoid_marketplace_maker' custom post type
		add_action( 'init', array( $this, 'register_marketplace_maker_cpt' ) );
		// Register Meta Fields for the REST API
		add_action( 'init', array( $this, 'register_marketplace_meta' ) );
		// Block Jetpack from auto-sharing the CPT by default
		add_filter( 'publicize_should_publicize_published_post', array( $this, 'restrict_cpt_auto_share' ), 10, 2 );
    }

    /**
	 * Retrieves the dynamic base slug from settings.
	 */
	private function get_base_slug() {
		$options = get_option( 'axxanoid_marketplace_settings', array() );
		return ! empty( $options['maker_base_slug'] ) ? $options['maker_base_slug'] : 'makers';
	}

    /**
	 * Registers the 'axxanoid_marketplace_maker' custom post type.
	 */
	public function register_marketplace_maker_cpt() {
		$base_slug = $this->get_base_slug();

		$labels = array(
			'name'               => _x( 'Marketplace Makers', 'post type general name', 'axxanoid-marketplace' ),
			'singular_name'      => _x( 'Marketplace Maker', 'post type singular name', 'axxanoid-marketplace' ),
			'menu_name'          => _x( 'Makers', 'admin menu', 'axxanoid-marketplace' ),
			'name_admin_bar'     => _x( 'Marketplace Maker', 'add new on admin bar', 'axxanoid-marketplace' ),
			'add_new'            => _x( 'Add New', 'marketplace maker', 'axxanoid-marketplace' ),
			'add_new_item'       => __( 'Add New Marketplace Maker', 'axxanoid-marketplace' ),
			'new_item'           => __( 'New Marketplace Maker', 'axxanoid-marketplace' ),
			'edit_item'          => __( 'Edit Marketplace Maker', 'axxanoid-marketplace' ),
			'view_item'          => __( 'View Marketplace Maker', 'axxanoid-marketplace' ),
			'all_items'          => __( 'All Makers', 'axxanoid-marketplace' ),
			'search_items'       => __( 'Search Makers', 'axxanoid-marketplace' ),
		);

		$args = array(
			'labels'				=> $labels,
			'public'             	=> true,
			'publicly_queryable'	=> true,
			'show_ui'            	=> true,
			'show_in_menu'      	=> false, // Managed under the 'Axxanoid > Directory' hub
			'query_var'          	=> true,
			'rewrite'            	=> array( 'slug' => $base_slug ),
			'capability_type'    	=> 'post',
			'has_archive'        	=> true,
			'hierarchical'       	=> false,
			'supports'           	=> array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
			'show_in_rest'       	=> true, // CRITICAL: Exposes CPT to REST API
		);

		register_post_type( 'axx_market_maker', $args );
	}

	/* Registers post meta fields and exposes them to the REST API for Python.
	 */
	public function register_marketplace_meta() {
		$meta_fields = array(
			// Scraped Data
			'maker_email'         => array( 'type' => 'string', 'default' => '' ),
			'maker_url'           => array( 'type' => 'string', 'default' => '' ), // Etsy/IG Outbound Link	
			// Subscription & Ego Trap Mechanics
			'marketplace_status'    => array( 'type' => 'string', 'default' => 'Trial' ), // 'Trial', 'Active', 'Expired'
			'trial_expiration_date' => array( 'type' => 'string', 'default' => '' ), // YYYY-MM-DD
			'paid_expiration_date'  => array( 'type' => 'string', 'default' => '' ),
			'pitch_sent_date'       => array( 'type' => 'string', 'default' => '' ), // YYYY-MM-DD
			'followup_sent_date'    => array( 'type' => 'string', 'default' => '' ), // YYYY-MM-DD
			'renewal_sent_date'     => array( 'type' => 'string', 'default' => '' ), // YYYY-MM-DD
			
			// WooCommerce Links
			'woo_brand_id' 			=> array( 'type' => 'integer', 'default' => 0 ), // Links CPT to Woo Taxonomy
			'locked_in_product_id'  => array( 'type' => 'string', 'default' => '' ), // Which woo sub they buy
			'subscription_order_id' => array( 'type' => 'string', 'default' => '' ), // Active order tracking
			// Comma-separated list of Woo Product IDs
            'maker_product_ids'               => array( 'type' => 'string', 'default' => '' ),

			// Secure Token
			'marketplace_renewal_claim_token' => array( 'type' => 'string', 'default' => '' ),
		);

		foreach ( $meta_fields as $meta_key => $args ) {
			register_post_meta( 'axx_market_maker', $meta_key, array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => $args['type'],
				'default'       => $args['default'],
				'auth_callback' => function() { return current_user_can( 'edit_posts' ); }
			) );
		}
	}
	
	/**
	 * Block Jetpack from auto-sharing the CPT unless they are a paying 'Active' Maker.
	 */
	public function restrict_cpt_auto_share( $should_publicize, $post ) {
		if ( 'axx_market_maker' === $post->post_type ) {
			$status = get_post_meta( $post->ID, 'marketplace_status', true );
			if ( $status !== 'Active' ) {
				return false; // Blocks Jetpack from broadcasting automatically
			}
		}
		return $should_publicize;
	}
}