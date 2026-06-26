<?php
/**
 * Creates the central "Marketplace" hub admin page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Axxanoid_Marketplace_Admin {

	const PAGE_SLUG = 'axxanoid-marketplace';
	const NONCE_ACTION = 'axx_market_admin_nonce';
    const AJAX_SEARCH_NONCE = 'axx_market_ajax_search_nonce';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
        add_action( 'admin_head', array( $this, 'set_admin_menu_highlight' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		// Action hooks for form saves will go here
	}

    /**
	 * Adds the "Marketplace" submenu page under the main "Axxanoid" menu.
	 * If Axxanoid Core is not active, it creates its own top-level menu.
	 */
	public function add_admin_page() {
		if ( class_exists( 'Axxanoid_Core_Init' ) ) {
			// Integrated mode: Add as a submenu to the Axxanoid Dashboard.
			add_submenu_page(
				Axxanoid_Plugin_Manager::TOP_LEVEL_SLUG,
				__( 'Marketplace', 'axxanoid-marketplace' ),
			    __( 'Marketplace', 'axxanoid-marketplace' ),
				'manage_options',
				self::PAGE_SLUG,
				array( $this, 'render_admin_page' )
			);
		} else {
			// Standalone mode: Create its own top-level menu.
			add_menu_page(
				__( 'Axxanoid Marketplace', 'axxanoid-marketplace' ),
				__( 'Marketplace', 'axxanoid-marketplace' ),
				'manage_options',
				self::PAGE_SLUG,
				array( $this, 'render_admin_page' ),
				'dashicons-editor-help'
			);
		}
	}

    /**
	 * Highlights the correct Axxanoid menu item when on Marketplace Makers edit screens.
	 */
	public function set_admin_menu_highlight() {
		global $parent_file, $submenu_file;

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		// When editing a single Marketplace Maker, highlight our custom hub page menu.
		if ( $screen->post_type == 'axx_market_maker' ) {
			if ( class_exists( 'Axxanoid_Core_Init' ) ) {
				// Integrated mode: Highlight under the main Axxanoid menu.
				$parent_file  = Axxanoid_Plugin_Manager::TOP_LEVEL_SLUG;
				$submenu_file = self::PAGE_SLUG;
			} else {
				// Standalone mode: Highlight our own top-level menu.
				$parent_file = self::PAGE_SLUG;
			}
		}
	}

    public function enqueue_admin_scripts( $hook_suffix ) {
		// Only load Vue on our specific admin page
		if ( strpos( $hook_suffix, self::PAGE_SLUG ) === false ) return;

		wp_enqueue_script( 'axx-vue-js', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '3', true );

		$category_mapping_data = $this->get_category_mapping_data_for_vue();

		wp_localize_script( 'jquery', 'axxMrkMappingData', array(
			'categories'  => $category_mapping_data,
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'searchNonce' => wp_create_nonce( self::AJAX_SEARCH_NONCE ),
		) );
	}

    public function get_category_mapping_data_for_vue() {
		$product_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		) );

		$saved_mappings = Axxanoid_Marketplace_Settings::get_category_tag_mappings();
		if ( is_wp_error( $product_categories ) || empty( $product_categories ) ) return array();

		$categories_by_id = array();
		foreach ( $product_categories as $category ) {
			$mapped_tags = isset( $saved_mappings[ $category->term_id ] ) ? $saved_mappings[ $category->term_id ] : array();
			$categories_by_id[ $category->term_id ] = array( 'id' => $category->term_id, 'name' => $category->name, 'parent' => $category->parent, 'mappedTags' => $mapped_tags, 'children' => array(), 'isExpanded' => false, 'breadcrumb' => '' );
		}

		$category_tree = array();
		foreach ( $categories_by_id as $id => &$category_node ) {
			$path = array();
			$current_cat = $category_node;
			while ( $current_cat['parent'] != 0 && isset( $categories_by_id[ $current_cat['parent'] ] ) ) {
				$parent_cat = $categories_by_id[ $current_cat['parent'] ];
				array_unshift( $path, $parent_cat['name'] );
				$current_cat = $parent_cat;
			}
			$category_node['breadcrumb'] = implode( ' › ', $path );

			if ( $category_node['parent'] ) {
				if ( isset( $categories_by_id[ $category_node['parent'] ] ) ) {
					$categories_by_id[ $category_node['parent'] ]['children'][] = &$category_node;
				}
			} else {
				$category_tree[] = &$category_node;
			}
		}
		return $category_tree;
	}

    /**
	 * Renders the main admin page for managing Axxanoid Marketplace.
	 */
	public function render_admin_page() {
        $admin_tabs = array( 'makers', 'category', 'settings' );
		$active_tab = isset( $_GET['tab'] ) && in_array( $_GET['tab'], $admin_tabs, true ) ? sanitize_key( $_GET['tab'] ) : 'makers';
		$template_path = AXX_MARKET_PLUGIN_DIR . 'admin/templates/tab-' . $active_tab . '.php';

		// Load the header and navigation
		require_once AXX_MARKET_PLUGIN_DIR . 'admin/templates/admin-header.php';
		
		// Load the active tab's HTML
		if ( file_exists( $template_path ) ) {
			require_once $template_path;
		}

		// Load the wrapper end
		require_once AXX_MARKET_PLUGIN_DIR . 'admin/templates/admin-footer.php';
	}
}