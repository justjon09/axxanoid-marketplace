<?php
/**
 * Creates the central "Marketplace" hub admin page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once AXX_MARKET_PLUGIN_DIR . 'admin/assets/css/axxanoid-admin-styles.css';

if ( isset( $_GET['message'] ) && $_GET['message'] === 'mappings_saved' ) {
    echo '<div class="notice notice-success is-dismissible"><p>Category mappings saved successfully.</p></div>';
}

if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) {
    echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
}
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Indie Marketplace Engine', 'axxanoid-marketplace' ); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=axxanoid-marketplace&tab=makers" class="nav-tab <?php echo 'makers' === $active_tab ? 'nav-tab-active' : ''; ?>">Makers</a>
        <a href="?page=axxanoid-marketplace&tab=category" class="nav-tab <?php echo 'category' === $active_tab ? 'nav-tab-active' : ''; ?>">Category Mapping</a>
        <a href="?page=axxanoid-marketplace&tab=settings" class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>">Settings</a>
    </h2>
    <div class="axx-market-tab-content">