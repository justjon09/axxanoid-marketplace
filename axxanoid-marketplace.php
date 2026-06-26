<?php
/**
 * Plugin Name:       Axxanoid Marketplace
 * Plugin URI:        https://www.axxanoidstudios.com
 * Description:       Automated Indie Maker B2B Marketplace and Ego Trap.
 * Version:           1.0.0
 * Author:            Axxanoid Studios LLC
 * License:           GPL v2 or later
 * Text Domain:       axxanoid-marketplace
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin constants.
 */
define( 'AXX_MARKET_VERSION', '1.0.0' );
define( 'AXX_MARKET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AXX_MARKET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that initializes everything.
 */
final class Axxanoid_Marketplace_Init {
    
	public static function init() {
        add_action( 'init', array( __CLASS__, 'load_textdomain' ) );

        // Global Classes / CPTs must be registered on all requests
        require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-cpt.php';
        new Axxanoid_Marketplace_CPT();

        // Admin-facing functionality
		if ( is_admin() ) {
            // Load the meta box handler for the Maker editor.
			require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-meta-box.php';
			new Axxanoid_Marketplace_Meta_Box();

			// Register this plugin with the Axxanoid dashboard tab system.
			add_filter( 'axxanoid_register_plugin_tab', array( __CLASS__, 'register_plugin_tab' ) );
            
            // TO-DO: Load Admin Hub, Settings, and Cron when ready
		}
    }

	public static function load_textdomain() {
		load_plugin_textdomain( 'axxanoid-marketplace', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	public static function plugin_activation() {
		require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-cpt.php';
		$cpt = new Axxanoid_Marketplace_CPT();
        $cpt->register_marketplace_maker_cpt();
		flush_rewrite_rules();
	}

	public static function plugin_deactivation() {
        flush_rewrite_rules();
	}

	public static function register_plugin_tab( $plugins ) {
		$plugin_dir = plugin_dir_path( __FILE__ );

		$read_file = function( $path ) {
			return file_exists( $path ) ? file_get_contents( $path ) : '';
		};

		$plugins['market'] = [
			'name'             => __( 'Marketplace', 'axxanoid-marketplace' ),
			'template_content' => $read_file( $plugin_dir . 'axxanoid-marketplace-admin-info.html' ),
			'replacements'     => [
				'%%DOCUMENTATION_HTML%%' => $read_file( $plugin_dir . 'documentation.html' ),
				'%%README_MD%%'          => $read_file( $plugin_dir . 'README.md' ),
			],
		];
		return $plugins;
	}
}

register_activation_hook( __FILE__, array( 'Axxanoid_Marketplace_Init', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Axxanoid_Marketplace_Init', 'plugin_deactivation' ) );
add_action( 'plugins_loaded', array( 'Axxanoid_Marketplace_Init', 'init' ) );