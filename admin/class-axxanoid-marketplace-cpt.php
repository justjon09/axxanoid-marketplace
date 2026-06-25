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
    }

    /**
	 * Retrieves the dynamic base slug from settings.
	 */
	private function get_base_slug() {
		$options = get_option( 'axxanoid_marketplace_settings', array() );
		if ( ! is_array( $options ) ) { $options = array(); }
		return ! empty( $options['maker_base_slug'] ) ? $options['maker_base_slug'] : 'maker';
	}

    /**
	 * Registers the 'axxanoid_marketplace_maker' custom post type.
	 */
	public function register_marketplace_maker_cpt() {
		$base_slug = $this->get_base_slug();

		$labels = array(
			'name'               => _x( 'Marketplace Maker', 'post type general name', 'axxanoid-marketplace' ),
			'singular_name'      => _x( 'Marketplace Maker', 'post type singular name', 'axxanoid-marketplace' ),
			'menu_name'          => _x( 'Marketplace Makers', 'admin menu', 'axxanoid-marketplace' ),
			'name_admin_bar'     => _x( 'Marketplace Maker', 'add new on admin bar', 'axxanoid-marketplace' ),
			'add_new'            => _x( 'Add New', 'marketplace maker', 'axxanoid-marketplace' ),
			'add_new_item'       => __( 'Add New Marketplace Maker', 'axxanoid-marketplace' ),
			'new_item'           => __( 'New Marketplace Maker', 'axxanoid-marketplace' ),
			'edit_item'          => __( 'Edit Marketplace Maker', 'axxanoid-marketplace' ),
			'view_item'          => __( 'View Marketplace Maker', 'axxanoid-marketplace' ),
			'all_items'          => __( 'All Marketplace Makers', 'axxanoid-marketplace' ),
			'search_items'       => __( 'Search Marketplace Makers', 'axxanoid-marketplace' ),
			'not_found'          => __( 'No Marketplace Makers found.', 'axxanoid-marketplace' ),
			'not_found_in_trash' => __( 'No Marketplace Makers found in Trash.', 'axxanoid-marketplace' ),
		);

		$args = array(
			'labels'				=> $labels,
			'public'             	=> true,
			'publicly_queryable'	=> true,
			'show_ui'            	=> true,
			'show_in_menu'      	=> false, // Managed under the 'Axxanoid > Directory' hub
			'query_var'          	=> true,
			'capability_type'    	=> 'post',
			'has_archive'        	=> $base_slug, // This powers the main /directory/ index
			'hierarchical'       	=> false,
			'supports'           	=> array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
			'show_in_rest'       	=> true, // CRITICAL: Exposes CPT to REST API
		);

		register_post_type( 'axx_market_maker', $args );
	}
}