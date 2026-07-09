<?php
/**
 * The template for displaying a single public Marketplace Maker profile.
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
// Retrieve the auth state passed from the Gatekeeper (defaults to false if missing)
$is_authenticated = get_query_var( 'axx_is_maker_auth', false );
?>

<div class="axx-market-profile-container wrap">
    
    <?php if ( $status === 'Trial' ) : ?>
        
        <?php 
        $exp_date  = get_post_meta( $maker_id, 'trial_expiration_date', true ); 
        $days_left = ( strtotime( $exp_date ) - time() ) / DAY_IN_SECONDS;
        $days_left = max( 0, floor( $days_left ) );
        ?>
        <?php // TO-DO enhance this text and layout ?>
        <div class="axx-market-banner axx-banner-warning">
            <div class="axx-banner-content">
                <h3>Free Portfolio Expires in <?php echo intval( $days_left ); ?> Days</h3>
                <ul class="axx-banner-list">
                    <li>Expand your protfolio with unlimited products</li>
                    <li>Link directly to your sales funnel</li>
                    <li>Shareable URL for social sales channels</li>
                    <li>Featured post broadcast to the Average Stoner social community</li>
                </ul>
                <p>Lock in your spot at the top of the community search results for just $2.99 / 30 Days.</p>
            </div>
            <div class="axx-banner-action">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Lock In Your Spot</a>
            </div>
        </div>
    <?php elseif ( $status === 'Active' ) : ?>
        
        <div class="axx-market-banner axx-banner-featured">
            <?php // TO-DO: fill content for featured (paid) maker // Featured <category> Maker ........ maybe a two line ai generated text -- additinal maker intput need to edit/ipdate this field. ?>
            <div class="axx-banner-content">
            </div>
        </div>
        <?php if ( $is_authenticated ) : ?>
            <?php // TO-DO: Content update, Dont like "rent", need to thank for early adoption, dont like grandfathered ?>
            <div class="axx-market-banner axx-banner-success">
                <div class="axx-banner-content">
                    <h3>Renew Early & Keep Your Grandfathered Price</h3>
                    <p>Because you used your secure email link, you can add 30 days to your rent right now at your locked-in rate.</p>
                </div>
                <div class="axx-banner-action">
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Renew Rent</a>
                </div>
            </div>

            <?php // TO-DO this is major functioanlity break -- if unlimited products could be unlimited emails need self managment asap ?>
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
        <h2>Products from the Maker</h2>
        <?php
        if ( ! empty( $brand_id ) && function_exists( 'WC' ) ) {
            // Query WooCommerce Products assigned to this Maker's Brand
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 12,
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'brand',
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