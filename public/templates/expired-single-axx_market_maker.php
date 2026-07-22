<?php
/**
 * The template for displaying an Expired Maker profile.
 * Only accessible to the Maker via their secure token.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

get_header();

$maker_id = get_the_ID();

// Fetch Visuals so they see what they are missing
$banner_id = get_post_meta( $maker_id, 'maker_header_banner', true );
$portrait_id = get_post_meta( $maker_id, 'maker_portrait', true );

$banner_url = $banner_id ? wp_get_attachment_image_url( $banner_id, 'full' ) : '';
$portrait_url = $portrait_id ? wp_get_attachment_image_url( $portrait_id, 'medium' ) : '';
?>

<div class="axx-market-profile-container wrap">
    
    <div class="axx-market-banner axx-banner-danger">
        <h2>Your Portfolio is Inactive</h2>
        <p class="axx-lead-text">
            Your Marketplace Rent has expired. Your products and public storefront have been removed from the directory. 
        </p>
        <p>
            Reactivate your spot right now to instantly restore your public portfolio, reactivate your product grid, and start receiving traffic again.
        </p>
        
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary axx-btn-large" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">
            Reactivate Portfolio
        </a>
    </div>

    <section class="axx-maker-hero axx-maker-hero-expired">
        <?php if ( $banner_url ) : ?>
            <img src="<?php echo esc_url( $banner_url ); ?>" class="axx-maker-banner" alt="Maker Banner" />
        <?php else : ?>
            <img src="<?php echo esc_url( AXX_MARKET_PLUGIN_URL . 'public/assets/images/default-banner.svg' ); ?>" class="axx-maker-banner" />
        <?php endif; ?>

        <div class="axx-maker-portrait-wrap">
            <?php if ( $portrait_url ) : ?>
                <img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php the_title_attribute(); ?>" />
            <?php else : ?>
                <img src="<?php echo esc_url( AXX_MARKET_PLUGIN_URL . 'public/assets/images/default-avatar.svg' ); ?>" alt="Avatar" />
            <?php endif; ?>
        </div>

        <div class="axx-maker-info-bar">
            <h1 class="axx-maker-title" style="margin: 0; font-size: 2em;"><?php the_title(); ?></h1>
            <div class="axx-maker-socials">
                <span class="axx-socials-disabled-text">Links Disabled</span>
            </div>
        </div>
    </section>

    <section class="axx-maker-products">
        <div class="axx-market-empty-state">
            <h2>Products Offline</h2>
            <p>Your products are currently hidden from this directory. Please reactivate your rent to restore your public portfolio.</p>
        </div>
    </section>

</div>

<?php get_footer(); ?>