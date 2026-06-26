<?php
/**
 * Creates the central "Marketplace" hub admin page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<style>
	<?php require_once AXX_MARKET_PLUGIN_DIR . 'admin/assets/css/axxanoid-admin-vue-styles.css'; ?>
</style>

<h2>Etsy Keyword to WooCommerce Category Mapping</h2>
<p>Map incoming scraped Etsy tags directly to your native WooCommerce categories. The "Run Now" function retroactively applies these rules to all products currently in the Indie Finds category.</p>

<div id="axx-market-vue-mapper-root">
	<p><em>[ Vue.js Interface Placeholder ]</em></p>
</div>