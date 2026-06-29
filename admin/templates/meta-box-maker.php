<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once AXX_MARKET_PLUGIN_DIR . 'admin/assets/css/axxanoid-admin-styles.css';
?>
<div class="axx-market-highlight">
    <div class="axx-market-row">
        <label for="marketplace_status">Marketplace Status</label>
        <select name="marketplace_status" id="marketplace_status">
            <option value="Trial" <?php selected( $status, 'Trial' ); ?>>Trial (10-Day Promo)</option>
            <option value="Active" <?php selected( $status, 'Active' ); ?>>Active (Paying Rent)</option>
            <option value="Expired" <?php selected( $status, 'Expired' ); ?>>Expired (Unpublished)</option>
        </select>
    </div>
    <div class="axx-market-row">
        <label for="trial_expiration_date">Trial Expiration</label>
        <input type="text" name="trial_expiration_date" id="trial_expiration_date" value="<?php echo esc_attr( $exp_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
    <div class="axx-market-row">
        <label for="locked_in_product_id">Woo Sub Product ID</label>
        <input type="text" name="locked_in_product_id" id="locked_in_product_id" value="<?php echo esc_attr( $product_id ); ?>" placeholder="e.g., 100 for '$2.99 Rent'" />
    </div>
    <div class="axx-market-row">
        <label for="subscription_order_id">Woo Order ID</label>
        <input type="text" name="subscription_order_id" id="subscription_order_id" value="<?php echo esc_attr( $order_id ); ?>" />
    </div>
</div>

<h3>Scraped Recon Data</h3>
<div class="axx-market-row">
    <label for="woo_brand_id">WooCommerce Brand ID</label>
    <input type="number" name="woo_brand_id" id="woo_brand_id" value="<?php echo esc_attr( $brand_id ); ?>" />
</div>
<div class="axx-market-row">
    <label for="maker_email">Maker Email</label>
    <input type="email" name="maker_email" id="maker_email" value="<?php echo esc_attr( $email ); ?>" />
</div>
<div class="axx-market-row">
    <label for="maker_url">Etsy/IG Store URL</label>
    <input type="url" name="maker_url" id="maker_url" value="<?php echo esc_url( $maker_url ); ?>" />
</div>

<h3>Drone Telemetry</h3>
<div class="axx-market-row">
    <label for="pitch_sent_date">Day-1 Pitch Sent Date</label>
    <input type="text" name="pitch_sent_date" id="pitch_sent_date" value="<?php echo esc_attr( $pitch_date ); ?>" placeholder="YYYY-MM-DD" />
</div>
<div class="axx-market-row">
    <label for="followup_sent_date">Day-5 Follow-up Sent</label>
    <input type="text" name="followup_sent_date" id="followup_sent_date" value="<?php echo esc_attr( $follow_date ); ?>" placeholder="YYYY-MM-DD" />
</div>