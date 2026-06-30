<?php
/**
 * Handles the meta boxes for the Marketplace Maker editor.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Axxanoid_Marketplace_Meta_Box {

	public function __construct() {
		add_action( 'add_meta_boxes_axx_market_maker', array( $this, 'add_maker_meta_box' ) );
		add_action( 'save_post_axx_market_maker', array( $this, 'save_maker_meta_data' ) );
	}

	public function add_maker_meta_box() {
		add_meta_box(
			'axxanoid_maker_data',
			__( 'Indie Maker Details & Marketplace Rent', 'axxanoid-marketplace' ),
			array( $this, 'render_meta_box' ),
			'axx_market_maker',
			'normal',
			'high'
		);
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'axxanoid_maker_save_meta', 'axxanoid_maker_nonce' );
        
        // Fetch all the data to pass to the template
		$status       = get_post_meta( $post->ID, 'marketplace_status', true ) ?: 'Trial';
		$exp_date     = get_post_meta( $post->ID, 'trial_expiration_date', true );
		$sub_date     = get_post_meta( $post->ID, 'paid_expiration_date', true );
		$email        = get_post_meta( $post->ID, 'maker_email', true );
		$maker_url    = get_post_meta( $post->ID, 'maker_url', true );
		$pitch_date   = get_post_meta( $post->ID, 'pitch_sent_date', true );
		$follow_date  = get_post_meta( $post->ID, 'followup_sent_date', true );
		$brand_id     = get_post_meta( $post->ID, 'woo_brand_id', true );
		$product_id   = get_post_meta( $post->ID, 'locked_in_product_id', true );
		$order_id     = get_post_meta( $post->ID, 'subscription_order_id', true );

        // Include the template partial
        require AXX_MARKET_PLUGIN_DIR . 'admin/templates/meta-box-maker.php';
	}

	public function save_maker_meta_data( $post_id ) {
		if ( ! isset( $_POST['axxanoid_maker_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['axxanoid_maker_nonce'] ), 'axxanoid_maker_save_meta' ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$fields = array(
			'marketplace_status'    => 'sanitize_text_field',
			'trial_expiration_date' => 'sanitize_text_field',
			'paid_expiration_date' => 'sanitize_text_field',
			'maker_email'           => 'sanitize_email',
			'maker_url'             => 'esc_url_raw',
			'pitch_sent_date'       => 'sanitize_text_field',
			'followup_sent_date'    => 'sanitize_text_field',
			'woo_brand_id'          => 'absint',
			'locked_in_product_id'  => 'sanitize_text_field',
			'subscription_order_id' => 'sanitize_text_field',
		);

		foreach ( $fields as $field => $sanitizer ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $field, call_user_func( $sanitizer, wp_unslash( $_POST[ $field ] ) ) );
			}
		}
	}
}