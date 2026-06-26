<?php
/**
 * Handles custom REST API endpoints for the headless Python drones.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Axxanoid_Marketplace_API {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_api_endpoints' ) );
	}

	public function register_api_endpoints() {
		// Day 1: Unpitched Makers
		register_rest_route( 'axx_market/v1', '/pending-pitches', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_pending_pitches' ),
			'permission_callback' => function() { return current_user_can( 'edit_posts' ); }
		) );

		// Day 5: Follow-up Reminders
		register_rest_route( 'axx_market/v1', '/pending-followups', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_pending_followups' ),
			'permission_callback' => function() { return current_user_can( 'edit_posts' ); }
		) );

		// Day 10: Executioner (Unpaid Trials)
		register_rest_route( 'axx_market/v1', '/expiring-trials', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_expiring_trials' ),
			'permission_callback' => function() { return current_user_can( 'edit_posts' ); }
		) );
	}

	/**
	 * Returns Makers on Trial who haven't received the Day 1 Qwen Pitch.
	 */
	public function get_pending_pitches( $request ) {
		$args = array(
			'post_type'      => 'axx_market_maker',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'meta_query'     => array(
				'relation' => 'AND',
				array( 'key' => 'marketplace_status', 'value' => 'Trial' ),
				array( 'key' => 'maker_email', 'value' => '@', 'compare' => 'LIKE' ), // Must have email
				array(
					'relation' => 'OR',
					array( 'key' => 'pitch_sent_date', 'value' => '', 'compare' => '=' ),
					array( 'key' => 'pitch_sent_date', 'compare' => 'NOT EXISTS' ),
				),
			),
		);

		$query = new WP_Query( $args );
		return new WP_REST_Response( $this->format_maker_response( $query->posts ), 200 );
	}

	/**
	 * Returns Makers who were pitched >= 5 days ago and haven't paid or been followed up with.
	 */
	public function get_pending_followups( $request ) {
		$five_days_ago = gmdate( 'Y-m-d', strtotime( '-5 days' ) );

		$args = array(
			'post_type'      => 'axx_market_maker',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'meta_query'     => array(
				'relation' => 'AND',
				array( 'key' => 'marketplace_status', 'value' => 'Trial' ),
				array( 'key' => 'pitch_sent_date', 'value' => $five_days_ago, 'compare' => '<=', 'type' => 'DATE' ), // Pitched 5+ days ago
				array(
					'relation' => 'OR',
					array( 'key' => 'followup_sent_date', 'value' => '', 'compare' => '=' ),
					array( 'key' => 'followup_sent_date', 'compare' => 'NOT EXISTS' ),
				),
			),
		);

		$query = new WP_Query( $args );
		return new WP_REST_Response( $this->format_maker_response( $query->posts ), 200 );
	}

	/**
	 * Returns Makers whose 10-day trial has expired so the Executioner Drone can draft them.
	 */
	public function get_expiring_trials( $request ) {
		$today = gmdate( 'Y-m-d' );

		$args = array(
			'post_type'      => 'axx_market_maker',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'meta_query'     => array(
				'relation' => 'AND',
				array( 'key' => 'marketplace_status', 'value' => 'Trial' ),
				array( 'key' => 'trial_expiration_date', 'value' => $today, 'compare' => '<=', 'type' => 'DATE' ), // Expired
			),
		);

		$query = new WP_Query( $args );
		return new WP_REST_Response( $this->format_maker_response( $query->posts ), 200 );
	}

	/**
	 * Helper function to cleanly format the JSON response for Python.
	 */
	private function format_maker_response( $posts ) {
		$response = array();
		foreach ( $posts as $post ) {
			$response[] = array(
				'id'    => $post->ID,
				'name'  => html_entity_decode( get_the_title( $post->ID ) ),
				'email' => get_post_meta( $post->ID, 'maker_email', true ),
				'url'   => get_post_meta( $post->ID, 'maker_url', true ),
			);
		}
		return $response;
	}
}