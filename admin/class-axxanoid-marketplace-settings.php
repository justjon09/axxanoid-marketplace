<?php
/**
 * Handles the settings page for the Axxanoid Marketplace plugin.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Axxanoid_Marketplace_Settings
 * Manages the creation of the settings page and registration of settings.
 */
class Axxanoid_Marketplace_Settings {

	/**
	 * The option group for our settings.
	 */
	const OPTION_GROUP = 'axxanoid_marketplace_settings';

	/**
	 * The option name in the wp_options table.
	 */
	const OPTION_NAME  = 'axxanoid_marketplace_settings';
	const CATEGORY_TAG_MAP_OPTION = 'axx_market_category_tag_map';

	/**
	 * Constructor. Hooks into WordPress.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Registers the settings, section, and fields using the Settings API.
	 */
	public function register_settings() {
		register_setting( self::OPTION_GROUP, self::OPTION_NAME, array( 'sanitize_callback' => array( $this, 'sanitize_settings' ) ) );

		// SECTION URLs
		add_settings_section( 
			'axxanoid_marketplace_url_section',
			__( 'General Configuration', 'axxanoid-marketplace' ), 
			null, 
			'axxanoid-marketplace-settings' 
		);
		
		add_settings_field( 
			'maker_base_slug', 
			'Makers Base Slug', 
			array( $this, 'render_text_field' ),
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_url_section', 
			array( 
				'key' => 'maker_base_slug', 
				'default' => 'marketplace/makers',
				'desc'    => 'The base URL for the marketplace makers (e.g., averagestoner.com/<strong>marketplace/makers</strong>/). Changing this requires flushing rewrite rules (visit Settings > Permalinks).'
			)
		);

		add_settings_field( 
			'current_maker_profile_product', 
			'Current Maker Profile Product Woo ID', 
			array( $this, 'render_text_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_url_section', 
			array( 
				'key' => 'current_maker_profile_product', 
				'desc'    => 'The woocommerce ID for the product ccurrently sold as maker profile subscription.'
			)
		);

		add_settings_field( 
			'marketplace_scrape_keywords', 
			'Scraper Target Keywords', 
			array( $this, 'render_text_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_url_section', 
			array( 
				'key' => 'marketplace_scrape_keywords',
				// TO-DO use product categories include hiarchy -- ensure scapper priorities direct input over cats
				'default' => 'dab rig, rolling tray, bong, glass pipe',
				'desc'    => 'Comma-separated keywords for the Python drones.'
			)
		);

		// SECTION Email Templates
		add_settings_section( 
			'axxanoid_marketplace_email_section',
			__( 'Automated Email Templates', 'axxanoid-marketplace' ), 
			array( $this, 'render_email_description' ), 
			'axxanoid-marketplace-settings' 
		);

		// Trial Expiring Warnings -- second pitch
		add_settings_field( 
			'email_maker_trial_expire_subject', 
			'Maker Profile Trial Expiring - Subject', 
			array( $this, 'render_text_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_maker_trial_expire_subject', 
				'default' => 'Your Free Marketplace Trial is ending soon.' 
			) 
		);

		add_settings_field( 
			'email_maker_trial_expire_body', 
			'Maker Trial Expiring - Body', 
			array( $this, 'render_textarea_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_maker_trial_expire_body', 
				'default' => "Hi [maker_name],\n\nYour Avetage Stoner Marketplace portfolio expires on [expiration_date].\n\nDon't lose your spot at the top of the search results! Secure your permanent ranking by locking in an official subscription today:\n\n[checkout_url]" 
			) 
		);

		// Profile set to draft -- expired
		add_settings_field( 
			'email_draft_maker_profile_subject', 
			'Maker Profile to Draft - Subject', 
			array( $this, 'render_text_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_draft_maker_profile_subject', 
				'default' => 'Your Average Stoner Marketplace portfolio is not public.' 
			) 
		);

		add_settings_field( 
			'email_draft_maker_profile_body', 
			'Maker Profile to Draft - Body',
			array( $this, 'render_textarea_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_draft_maker_profile_body', 
				'default' => "Hi [maker_name],\n\nYour Avetage Stoner Marketplace portfolio expired on [expiration_date].\n\nDon't lose your spot at the top of the search results! Secure your permanent ranking by locking in an official subscription today:\n\n[checkout_url]" 
			) 
		);

		// Products removal warning -- expired for 45 days - final. 
		add_settings_field( 
			'email_maker_product_remove_subject', 
			'Maker Products Removal Warning - Subject', 
			array( $this, 'render_text_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_maker_product_remove_subject', 
				'default' => 'Your products to be reomved from Average Stoner.' 
			) 
		);

		add_settings_field( 
			'email_maker_product_remove_body', 
			'Maker Products Removal Warning - Body', 
			array( $this, 'render_textarea_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_maker_product_remove_body', 
				'default' => "Hi [maker_name],\n\nYour Avetage Stoner Marketplace portfolio expired on [expiration_date].\n\nDon't lose your spot at the top of the search results! Secure your permanent ranking by locking in an official subscription today:\n\n[checkout_url]" 
			) 
		);

		// 10-Day Warning -- renewal 
		add_settings_field( 
			'email_maker_profile_renewal_subject', 
			'10-Day Notice to Renew Maker Profile - Subject', 
			array( $this, 'render_text_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_maker_profile_renewal_subject', 
				'default' => 'NOTICE: You are about to lose your marketplace portfolio.' 
			)
		);

		add_settings_field( 
			'email_maker_profile_renewal_body', 
			'10-Day Notice to Renew Maker Profile - Body', 
			array( $this, 'render_textarea_field' ), 
			'axxanoid-marketplace-settings', 
			'axxanoid_marketplace_email_section', 
			array( 
				'key' => 'email_maker_profile_renewal_body', 
				'default' => "Hi [maker_name],\n\nThis is your friendly reminder, your lmker portfolio expires in 10 days on [expiration_date].\n\nIf your listing lapses to 'Free' status, your portfolio will not be publicly viewable. To reinstate your maker portfolio later, you will have to email us directly.\n\nRenew now to secure your spot:\n[checkout_url]" 
			) 
		);
	}

	public function render_email_description() {
		echo '<p>Configure the automated emails sent by the cron job. You can use the following dynamic variables:</p>';
		echo '<ul style="list-style: disc; margin-left: 20px; font-family: monospace;">';
		echo '<li><code>[maker_name]</code> - The title of the listing</li>';
		echo '<li><code>[expiration_date]</code> - When their status expires</li>';
		echo '<li><code>[locked_price]</code> - Their specific grandfathered price</li>';
		echo '<li><code>[checkout_url]</code> - The direct WooCommerce checkout link (generated from their locked_in_product_id)</li>';
		echo '</ul>';
	}

	public function render_text_field( $args ) {
		$options = get_option( self::OPTION_NAME, array() );
		$value   = isset( $options[ $args['key'] ] ) ? $options[ $args['key'] ] : $args['default'];

		printf(
			'<input type="text" name="%1$s[%2$s]" id="%2$s" value="%3$s" class="regular-text" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $args['key'] ),
			esc_attr( $value )
		);
		if ( isset( $args['desc'] ) ) {
			echo '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
		}
	}

	public function render_textarea_field( $args ) {
		$options = get_option( self::OPTION_NAME, array() );
		$value   = isset( $options[ $args['key'] ] ) ? $options[ $args['key'] ] : $args['default'];
		printf( 
			'<textarea name="%1$s[%2$s]" id="%2$s" rows="6" style="width:100%%; max-width:600px;">%3$s</textarea>', 
			esc_attr( self::OPTION_NAME ), 
			esc_attr( $args['key'] ), 
			esc_textarea( $value ) 
		);
		if ( isset( $args['desc'] ) ) {
			echo '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
		}
	}

	/**
	 * Get the saved category to tag mappings.
	 * @return array The mapping of category IDs to arrays of tag names.
	 */
	public static function get_category_tag_mappings() {
		$mappings = get_option( self::CATEGORY_TAG_MAP_OPTION, array() );
		return is_array( $mappings ) ? $mappings : array();
	}

	/**
	 * Sanitizes the settings array before saving.
	 *
	 * @param array $input The input array from the form.
	 * @return array The sanitized array.
	 */
	public function sanitize_settings( $input ) {
		$sanitized_input = array();
		if ( isset( $input['maker_base_slug'] ) ) {
			 $sanitized_input['maker_base_slug'] = sanitize_key( $input['maker_base_slug'] );
		}
		if ( isset( $input['current_maker_profile_product'] ) ) {
			$sanitized_input['current_maker_profile_product'] = sanitize_text_field( $input['current_maker_profile_product'] );
		}
		if ( isset( $input['marketplace_scrape_keywords'] ) ) {
			$sanitized_input['marketplace_scrape_keywords'] = sanitize_text_field( $input['marketplace_scrape_keywords'] );
		}
		if ( isset( $input['email_maker_trial_expire_subject'] ) ) {
			$sanitized_input['email_maker_trial_expire_subject'] = sanitize_text_field( $input['email_maker_trial_expire_subject'] );
		}
		if ( isset( $input['email_maker_trial_expire_body'] ) ) {
			$sanitized_input['email_maker_trial_expire_body'] = wp_kses_post( $input['email_maker_trial_expire_body'] );
		}
		if ( isset( $input['email_draft_maker_profile_subject'] ) ){
			$sanitized_input['email_draft_maker_profile_subject'] = sanitize_text_field( $input['email_draft_maker_profile_subject'] );
		}
		if ( isset( $input['email_draft_maker_profile_body'] ) ) {
			$sanitized_input['email_draft_maker_profile_body'] = wp_kses_post( $input['email_draft_maker_profile_body'] );
		}
		if ( isset( $input['email_maker_product_remove_subject'] ) ) {
			$sanitized_input['email_maker_product_remove_subject'] = sanitize_text_field( $input['email_maker_product_remove_subject'] );
		}
		if ( isset( $input['email_maker_product_remove_body'] ) ) {
			$sanitized_input['email_maker_product_remove_body'] = wp_kses_post( $input['email_maker_product_remove_body'] );
		}
		if ( isset( $input['email_maker_profile_renewal_subject'] ) ) {
			$sanitized_input['email_maker_profile_renewal_subject'] = wp_kses_post( $input['email_maker_profile_renewal_subject'] );
		}
		if ( isset( $input['email_maker_profile_renewal_body'] ) ) {
			$sanitized_input['email_maker_profile_renewal_body'] = wp_kses_post( $input['email_maker_profile_renewal_body'] );
		}
						
		return $sanitized_input;
	}
}