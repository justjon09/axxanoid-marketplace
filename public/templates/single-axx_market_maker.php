<?php
/**
 * The template for displaying a single Marketplace Maker profile.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

get_header();

$maker_id = get_the_ID();
$status   = get_post_meta( $maker_id, 'marketplace_status', true ) ?: 'Trial';
$brand_id = get_post_meta( $maker_id, 'woo_brand_id', true );

// Token Authentication Logic
$provided_token   = isset( $_GET['marketplace_token'] ) ? sanitize_text_field( wp_unslash( $_GET['marketplace_token'] ) ) : '';
$saved_token      = get_post_meta( $maker_id, '_axx_market_claim_token', true );
$is_authenticated = ( ! empty( $provided_token ) && $provided_token === $saved_token );

?>

<div class="axx-market-profile-container wrap">
    
    <?php if ( $status === 'Trial' ) : ?>
        
        <?php 
        $exp_date  = get_post_meta( $maker_id, 'trial_expiration_date', true ); 
        $days_left = ( strtotime( $exp_date ) - time() ) / DAY_IN_SECONDS;
        $days_left = max( 0, floor( $days_left ) );
        ?>
        <div class="axx-market-banner axx-banner-warning">
            <div class="axx-banner-content">
                <h3>Your Free Portfolio Expires in <?php echo intval( $days_left ); ?> Days</h3>
                <p>Lock in your spot at the top of our community search results for just $2.99 / 30 Days.</p>
            </div>
            <div class="axx-banner-action">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Lock In Your Spot</a>
            </div>
        </div>

    <?php elseif ( $status === 'Expired' && $is_authenticated ) : ?>
        
        <div class="axx-market-banner axx-banner-danger">
            <div class="axx-banner-content">
                <h3>Profile Inactive</h3>
                <p>Your rent has expired and your profile has been removed from the public directory. Reactivate now at current market rates to restore your visibility.</p>
            </div>
            <div class="axx-banner-action">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Reactivate Profile</a>
            </div>
        </div>

    <?php elseif ( $status === 'Active' && $is_authenticated ) : ?>
        
        <div class="axx-market-banner axx-banner-success">
            <div class="axx-banner-content">
                <h3>Renew Early & Keep Your Grandfathered Price</h3>
                <p>Because you used your secure email link, you can add 30 days to your rent right now at your locked-in rate.</p>
            </div>
            <div class="axx-banner-action">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Renew Rent</a>
            </div>
        </div>

    <?php elseif ( $status === 'Active' && ! $is_authenticated && is_user_logged_in() && current_user_can('manage_options') ) : ?>
        
        <div class="axx-market-banner axx-banner-info">
            <div class="axx-banner-content">
                <h3>Manage Your Portfolio</h3>
                <p>Need to swap out a product or update your bio? Submit a priority request to the admin team.</p>
            </div>
            <div class="axx-banner-action">
                <button id="axx-open-update-modal" class="button button-secondary">Request Profile Update</button>
            </div>
        </div>

    <?php endif; ?>

    <header class="axx-maker-header">
        <h1 class="axx-maker-title"><?php the_title(); ?></h1>
        <div class="axx-maker-bio">
            <?php the_content(); ?>
        </div>
        
        <?php $maker_url = get_post_meta( $maker_id, 'maker_url', true ); ?>
        <?php if ( $maker_url ) : ?>
            <div class="axx-maker-outbound-wrap">
                <a href="<?php echo esc_url( $maker_url ); ?>" class="axx-maker-outbound button" target="_blank" rel="nofollow">Visit Official Store &rarr;</a>
            </div>
        <?php endif; ?>
    </header>

    <section class="axx-maker-products">
        <h2>Featured Indie Finds</h2>
        <?php
        if ( ! empty( $brand_id ) && function_exists( 'WC' ) ) {
            
            // Query WooCommerce Products assigned to this Maker's Brand
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 12,
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'brand', // Change this if you are using a different taxonomy for brands
                        'field'    => 'term_id',
                        'terms'    => $brand_id,
                    ),
                ),
            );

            $products_query = new WP_Query( $args );

            if ( $products_query->have_posts() ) {
                
                // We use native WooCommerce wrapper classes so the theme styles it properly
                echo '<div class="woocommerce">';
                echo '<ul class="products columns-4">';
                
                while ( $products_query->have_posts() ) {
                    $products_query->the_post();
                    // Load the native WooCommerce product card template
                    wc_get_template_part( 'content', 'product' ); 
                }
                
                echo '</ul>';
                echo '</div>';
                
                wp_reset_postdata();
            } else {
                echo '<p>No products are currently featured in this portfolio.</p>';
            }
        } else {
            echo '<p>Maker brand connection missing.</p>';
        }
        ?>
    </section>

</div><?php
get_footer();