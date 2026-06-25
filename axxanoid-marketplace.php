<?php
/**
 * Plugin Name:       Axxanoid Marketplace
 * Plugin URI:        https://www.axxanoidstudios.com
 * Description:       
 * Version:           1.1.0
 * Author:            Axxanoid Studios LLC
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
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
final class Axxanoid_Directory_Init {
    /**
	 * Initialize the plugin. Loads dependencies and instantiates classes.
	 */
	public static function init() {
        add_action( 'init', array( __CLASS__, 'load_textdomain' ) );

        // Global Classes / CPTs must be registered on all requests (admin, public, and REST API).
        require_once AXX_MARKET_PLUGIN_URL . 'admin/class-axxanoid-marketplace-cpt.php';
        new Axxanoid_Marketplace_CPT();

        // Load AJAX globally so wp-admin/admin-ajax.php requests can find it
		require_once AXX_MARKET_PLUGIN_DIR . 'public/class-axxanoid-marketplace-ajax.php';
		new Axxanoid_Marketplace_Ajax();

		// Load WooCommerce globally so webhooks and payment gateways can trigger it
		require_once AXX_MARKET_PLUGIN_DIR . 'public/class-axxanoid-marketplace-woocommerce.php';
		new Axxanoid_Marketplace_WooCommerce();

        // Load the settings page handler.
		require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-settings.php';
		new Axxanoid_Marketplace_Settings();

        // Load the Cron Job handler
		require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-cron.php';
		new Axxanoid_Marketplace_Cron();

        // Load admin-facing functionality only in the admin area
		if ( is_admin() ) {
			// Load the main admin page class.
			require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-admin.php';
			new Axxanoid_Marketplace_Admin();

            // Load the meta box handler for the Directory editor.
			require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-meta-box.php';
			new Axxanoid_Marketplace_Meta_Box();

			// Register this plugin with the Axxanoid dashboard tab system.
			add_filter( 'axxanoid_register_plugin_tab', array( __CLASS__, 'register_plugin_tab' ) );
		}
    }

    /**
	 * Loads the plugin text domain for translation.
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'axxanoid-marketplace', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

    /**
	 * Runs on plugin activation.
	 */
	public static function plugin_activation() {
		// Must register the CPT before flushing rules
		require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-cpt.php';
		$cpt = new Axxanoid_Marketplace_CPT();

		flush_rewrite_rules();
	}

    /**
	 * Runs on plugin deactivation.
	 */
	public static function plugin_deactivation() {

        flush_rewrite_rules();
	}

     /**
	 * Registers this plugin's information for the central Axxanoid dashboard.
	 *
	 * @param array $plugins Array of existing registered plugins.
	 * @return array Modified array of plugins.
	 */
	public static function register_plugin_tab( $plugins ) {
		$plugin_dir = plugin_dir_path( __FILE__ );

		$read_file = function( $path ) {
			return file_exists( $path ) ? file_get_contents( $path ) : '';
		};

		$plugins['dir'] = [
			'name'             => __( 'Marketplace', 'axxanoid-marketplace' ),
			'template_content' => $read_file( $plugin_dir . 'axxanoid-marketplace-admin-info.html' ),
			'replacements'     => [
				'%%DOCUMENTATION_HTML%%' => $read_file( $plugin_dir . 'documentation.html' ),
				'%%README_MD%%'          => $read_file( $plugin_dir . 'readme.md' ),
			],
		];
		return $plugins;
	}
}

// Register hooks that must run on plugin activation/deactivation.
// These must be in the global scope to be seen by WordPress during the activation process.
register_activation_hook( __FILE__, array( 'Axxanoid_Marketplace_Init', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Axxanoid_Marketplace_Init', 'plugin_deactivation' ) );

// Begins execution of the plugin on the 'plugins_loaded' hook.
add_action( 'plugins_loaded', array( 'Axxanoid_Marketplace_Init', 'init' ) );