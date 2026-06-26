<?php
/**
 * Creates the central "Marketplace" hub admin page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$options = get_option( 'axxanoid_marketplace_settings', array() );
?>

<h2>Marketplace Settings</h2>
<form method="post" action="admin-post.php">
	<?php wp_nonce_field( 'axx_market_save_settings', 'axx_market_settings_nonce' ); ?>
	<input type="hidden" name="action" value="axx_market_save_settings">
	
	<table class="form-table">
		<tr>
			<th scope="row"><label for="default_subscription_product_id">Default Subscription Product ID</label></th>
			<td>
				<input type="number" name="axxanoid_marketplace_settings[default_subscription_product_id]" id="default_subscription_product_id" value="<?php echo esc_attr( $options['default_subscription_product_id'] ?? '' ); ?>" class="regular-text">
				<p class="description">The WooCommerce Product ID for the "$2.99 / 30 Days" rent invoice. Python uses this when pitching new makers.</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="maker_base_slug">Maker Base Slug</label></th>
			<td>
				<input type="text" name="axxanoid_marketplace_settings[maker_base_slug]" id="maker_base_slug" value="<?php echo esc_attr( $options['maker_base_slug'] ?? 'makers' ); ?>" class="regular-text">
				<p class="description">The URL slug for maker profiles (e.g., averagestoner.com/<strong>makers</strong>/joes-glass). <em>Requires saving permalinks after changing.</em></p>
			</td>
		</tr>
	</table>
	<?php submit_button( 'Save Settings' ); ?>
</form>