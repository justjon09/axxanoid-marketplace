<?php if ( ! defined( 'WPINC' ) ) die; ?>
<div class="axx-onboard-submit-wrap">
    <hr />
    <p>Are your profile and products ready for our team to review?</p>
    <form id="axx-maker-submit-review-form">
        <input type="hidden" name="action" value="axx_market_submit_for_review">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce('axx_market_public_nonce') ); ?>">
        <input type="hidden" name="maker_id" value="<?php echo esc_attr( $maker_id ); ?>">
        <button type="submit" class="button button-primary axx-btn-large">Submit Portfolio For Review</button>
    </form>
</div>