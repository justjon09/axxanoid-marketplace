<?php
/**
 * Creates the central "Marketplace" hub admin page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<h2>Marketplace Makers</h2>
<p>Manage scraped Etsy/IG makers, track their 10-day trials, and view active rent subscriptions.</p>
<form method="get">
	<input type="hidden" name="page" value="axxanoid-marketplace">
	<input type="hidden" name="tab" value="makers">
	<?php
	require_once AXX_MARKET_PLUGIN_DIR . 'admin/class-axxanoid-marketplace-list-table.php';
	$list_table = new Axxanoid_Marketplace_List_Table();
	$list_table->prepare_items();
	$list_table->display();
	?>
</form>