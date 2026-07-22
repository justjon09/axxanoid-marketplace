<?php
/**
 * The wrapper template for the Maker's private Onboarding Portal.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

get_header();

$maker_id = get_the_ID();
$status   = get_post_meta( $maker_id, 'marketplace_status', true ) ?: 'Trial';
$networks = Axxanoid_Marketplace_Settings::get_social_networks();

// Fetch existing data for pre-filling
$data = array(
    'maker_id'      => $maker_id,
    'bio'           => get_post_meta( $maker_id, 'maker_bio', true ),
    'callout'       => get_post_meta( $maker_id, 'maker_callout_text', true ),
    'display_email' => get_post_meta( $maker_id, 'maker_display_email', true ),
    'awards'        => json_decode( get_post_meta( $maker_id, 'maker_awards', true ) ?: '[]', true ),
    'socials'       => json_decode( get_post_meta( $maker_id, 'maker_social_urls', true ) ?: '[]', true ),
    'networks'      => $networks
);
?>

<div class="axx-market-onboard-container wrap">
    <?php 
    if ( $status === 'Pending Review' ) {
        require AXX_MARKET_PLUGIN_DIR . 'public/templates/template-parts/onboard-pending.php';
    } else {
        // Extract data for the required scopes
        extract( $data );
        
        require AXX_MARKET_PLUGIN_DIR . 'public/templates/template-parts/onboard-header.php';
        require AXX_MARKET_PLUGIN_DIR . 'public/templates/template-parts/onboard-profile.php';
        require AXX_MARKET_PLUGIN_DIR . 'public/templates/template-parts/onboard-products.php';
        require AXX_MARKET_PLUGIN_DIR . 'public/templates/template-parts/onboard-submit.php';
    }
    ?>
</div>

<script>
    window.axxSocialNetworks = <?php echo wp_json_encode( $networks ); ?>;
</script>

<?php get_footer(); ?>