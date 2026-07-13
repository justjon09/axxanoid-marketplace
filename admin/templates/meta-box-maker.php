<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once AXX_MARKET_PLUGIN_DIR . 'admin/assets/css/axxanoid-admin-styles.css';
require_once AXX_MARKET_PLUGIN_DIR . 'admin/assets/js/axxanoid-meta-box.js';
?>
<h3>Internal Use Data</h3>
<div class="axx-market-meta-group">
    <h3>Maker Status</h3>
    <div class="axx-market-row">
        <label for="marketplace_status">Marketplace Status</label>
        <select name="marketplace_status" id="marketplace_status">
            <option value="Trial" <?php selected( $status, 'Trial' ); ?>>Trial (10-Day Promo)</option>
            <option value="Onboarding" <?php selected( $status, 'Onboarding' ); ?>>Onboarding (Maker Input)</option>
            <option value="Active" <?php selected( $status, 'Active' ); ?>>Active (Paying Rent)</option>
            <option value="Pending" <?php selected( $status, 'Pending' ); ?>>Pending (Admin Review)</option>
            <option value="Expired" <?php selected( $status, 'Expired' ); ?>>Expired (Unpublished)</option>
        </select>
    </div>
    <div class="axx-market-row">
        <label for="trial_expiration_date">Trial Expiration</label>
        <input type="text" name="trial_expiration_date" id="trial_expiration_date" value="<?php echo esc_attr( $trial_exp_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
    <div class="axx-market-row">
        <label for="paid_expiration_date">Paid Sub Expiration</label>
        <input type="text" name="paid_expiration_date" id="paid_expiration_date" value="<?php echo esc_attr( $sub_exp_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
</div>
<div class="axx-market-meta-group">
    <h3>Telemetry</h3>
    <div class="axx-market-row">
        <label for="pitch_sent_date">Day-1 Pitch Sent Date</label>
        <input type="text" name="pitch_sent_date" id="pitch_sent_date" value="<?php echo esc_attr( $pitch_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
    <div class="axx-market-row">
        <label for="followup_sent_date">Day-5 Follow-up Sent</label>
        <input type="text" name="followup_sent_date" id="followup_sent_date" value="<?php echo esc_attr( $follow_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
    <div class="axx-market-row">
        <label for="onboard_sent_date">Onboard Sent</label>
        <input type="text" name="onboard_sent_date" id="onboard_sent_date" value="<?php echo esc_attr( $onboard_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
    <div class="axx-market-row">
        <label for="renewal_sent_date">Renewal Sent</label>
        <input type="text" name="renewal_sent_date" id="renewal_sent_date" value="<?php echo esc_attr( $renewal_date ); ?>" placeholder="YYYY-MM-DD" />
    </div>
    <div class="axx-market-row">
        <label>Link Reset Requested</label>
        <input type="text" name="reset_link_requested_date" value="<?php echo esc_attr( $reset_date ); ?>" placeholder="YYYY-MM-DD" readonly />
        <button type="button" class="button axx-trigger-btn" id="axx-admin-trigger-reset" data-maker="<?php echo $post->ID; ?>">Regenerate & Email Link</button>
        <span id="axx-reset-msg" style="margin-left: 10px; color: green; display: none;">Triggered!</span>
    </div>
</div>
<div class="axx-market-meta-group">
    <h3>Contact Data</h3>
    <div class="axx-market-row">
        <label for="maker_email">Maker Email</label>
        <input type="email" name="maker_email" id="maker_email" value="<?php echo esc_attr( $maker_email ); ?>" placeholder="joe@joesglass.com" />
    </div>
    <div class="axx-market-row">
        <label for="maker_url">URL</label>
        <input type="url" name="maker_url" id="maker_url" value="<?php echo esc_url( $maker_url ); ?>" />
    </div>
</div>
<div class="axx-market-meta-group">
    <h3>Woocommerce Details</h3>
    <div class="axx-market-row">
        <label for="woo_brand_id">WooCommerce Brand ID</label>
        <input type="number" name="woo_brand_id" id="woo_brand_id" value="<?php echo esc_attr( $brand_id ); ?>" />
    </div>
    <div class="axx-market-row">
        <label for="locked_in_product_id">WooCommerce Locked Product ID</label>
        <input type="text" name="locked_in_product_id" id="locked_in_product_id" value="<?php echo esc_attr( $sub_product_id ); ?>" />
    </div>
    <div class="axx-market-row">
        <label for="subscription_order_id">WooCommerce Order ID</label>
        <input type="text" name="subscription_order_id" id="subscription_order_id" value="<?php echo esc_attr( $sub_order_id ); ?>" />
    </div>
</div>
<h3>Portfolio Visuals & Content</h3>
<div class="axx-market-meta-group">
    <h3>Templated Inputs</h3>
    <div class="axx-market-row">
        <label>Header Banner (Media ID)</label>
        <input type="number" name="maker_header_banner" class="axx-market-input-large" value="<?php echo esc_attr( $banner_id ); ?>" />
    </div>
    <div class="axx-market-row">
        <label>Portrait/Avatar (Media ID)</label>
        <input type="number" name="maker_portrait" class="axx-market-input-large" value="<?php echo esc_attr( $portrait_id ); ?>" />
    </div>
    <div class="axx-market-row">
        <label>Callout Quote</label>
        <textarea name="maker_callout_text" rows="3" class="axx-market-input-large"><?php echo esc_textarea( $callout ); ?></textarea>
    </div>
</div>




<div class="axx-market-meta-group">
    <h4>Social Links</h4>
    <div class="axx-market-row">
        <label>Instagram URL</label>
        <input type="url" name="socials[instagram]" class="axx-market-input-large" value="<?php echo esc_url( $socials['instagram'] ?? '' ); ?>" />
    </div>
    <div class="axx-market-row">
        <label>Website URL</label>
        <input type="url" name="socials[website]" class="axx-market-input-large" value="<?php echo esc_url( $socials['website'] ?? '' ); ?>" />
    </div>
</div>


<div class="axx-market-meta-group">
    <h4>Awards / Accolades</h4>
    <div id="axx-admin-awards-wrapper" style="max-width: 600px; margin-bottom: 15px;">
        <?php foreach ( $awards as $index => $award ) : ?>
            <div class="axx-award-row">
                <input type="text" name="awards[<?php echo $index; ?>][title]" placeholder="Award Title (e.g. Best Glass)" value="<?php echo esc_attr( $award['title'] ?? '' ); ?>" />
                <input type="text" name="awards[<?php echo $index; ?>][place]" placeholder="Place (e.g. 1st Place)" value="<?php echo esc_attr( $award['place'] ?? '' ); ?>" />
                <input type="text" name="awards[<?php echo $index; ?>][image]" placeholder="Image URL (Optional)" value="<?php echo esc_attr( $award['image'] ?? '' ); ?>" />
                <a href="#" class="axx-award-remove">&times; Remove</a>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button" id="axx-add-admin-award">Add Award</button>
</div>