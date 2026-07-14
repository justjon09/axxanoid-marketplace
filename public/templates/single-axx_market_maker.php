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

// Fetch Portfolio Fields
$banner_id = get_post_meta( $maker_id, 'maker_header_banner', true );
$portrait_id = get_post_meta( $maker_id, 'maker_portrait', true );
$callout = get_post_meta( $maker_id, 'maker_callout_text', true );
$awards_json = get_post_meta( $maker_id, 'maker_awards', true );
$socials_json = get_post_meta( $maker_id, 'maker_social_urls', true );

$banner_url = $banner_id ? wp_get_attachment_image_url( $banner_id, 'full' ) : '';
$portrait_url = $portrait_id ? wp_get_attachment_image_url( $portrait_id, 'medium' ) : '';
$awards = $awards_json ? json_decode( $awards_json, true ) : array();
$socials = $socials_json ? json_decode( $socials_json, true ) : array();
?>

<div class="axx-market-profile-container wrap">
    
    <?php if ( $status === 'Trial' ) : ?>        
        <?php 
        $exp_date  = get_post_meta( $maker_id, 'trial_expiration_date', true );
        $days_left = max( 0, floor( ( strtotime( $exp_date ) - time() ) / DAY_IN_SECONDS ) );
        $days_left = ( strtotime( $exp_date ) - time() ) / DAY_IN_SECONDS;
        ?>
        <div class="axx-market-banner axx-banner-warning">
            <div class="axx-banner-content">
                <h3>Free Portfolio Expires in <?php echo intval( $days_left ); ?> Days</h3>
                <ul class="axx-banner-list">
                    <li>Expand your portfolio with unlimited products</li>
                    <li>Link directly to your sales funnel</li>
                    <li>Shareable URL for social sales channels</li>
                    <li>Featured post broadcast to the Average Stoner community</li>
                </ul>
                <p>Lock in your spot for just $2.99 / 30 Days.</p>
            </div>
            <div class="axx-banner-action">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Lock In Your Spot</a>
            </div>
        </div>
    <?php elseif ( $status === 'Active' ) : ?>
        <?php if ( $is_authenticated ) : ?>
            <div class="axx-market-banner axx-banner-success">
                <div class="axx-banner-content">
                    <h3>Thank You For Being A Featured Maker</h3>
                    <p>You can securely add 30 days to your active profile right now using this link.</p>
                </div>
                <div class="axx-banner-action">
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="axx-market-claim-btn button button-primary" data-maker-id="<?php echo esc_attr( $maker_id ); ?>">Extend Profile</a>
                </div>
            </div>
            <div class="axx-market-banner axx-banner-info">
                <div class="axx-banner-content">
                    <h3>Manage Your Portfolio</h3>
                    <p>Need to swap out a product or update your bio? Access your secure portal.</p>
                </div>
                <div class="axx-banner-action">
                    <a href="?marketplace_token=<?php echo esc_attr( $_GET['marketplace_token'] ); ?>&view=edit_bio" class="button button-secondary">Edit Bio & Visuals</a>
                    <a href="?marketplace_token=<?php echo esc_attr( $_GET['marketplace_token'] ); ?>&view=manage_products" class="button button-secondary">Manage Products</a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <section class="axx-maker-hero">
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
                <?php 
                if ( ! empty( $socials ) && is_array( $socials ) ) {
                    foreach ( $socials as $platform => $url ) {
                        if ( ! empty( $url ) ) {
                            echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="nofollow">' . esc_html( ucfirst( $platform ) ) . '</a>';
                        }
                    }
                }
                $maker_url = get_post_meta( $maker_id, 'maker_url', true );
                if ( $maker_url ) {
                    echo '<a href="' . esc_url( $maker_url ) . '" class="button button-primary" target="_blank" rel="nofollow">Official Store &rarr;</a>';
                }
                ?>
            </div>
        </div>
    </section>

    <div class="axx-maker-content-grid">
        <div class="axx-maker-main-bio">
            <?php if ( $callout ) : ?>
                <blockquote class="axx-maker-callout">
                    "<?php echo esc_html( $callout ); ?>"
                </blockquote>
            <?php endif; ?>
            
            <div class="axx-maker-bio-text">
                <?php the_content(); ?>
            </div>
        </div>

        <div class="axx-maker-sidebar">
            <?php if ( ! empty( $awards ) && is_array( $awards ) ) : ?>
                <h3>Accolades</h3>
                <div class="axx-awards-grid">
                    <?php foreach ( $awards as $award ) : ?>
                        <div class="axx-award-card">
                            <div class="axx-award-icon">
                                <?php if ( ! empty( $award['image'] ) ) : ?>
                                    <img src="<?php echo esc_url( $award['image'] ); ?>" style="width:100%; height:100%; object-fit:contain;" />
                                <?php else : ?>
                                    <img src="<?php echo esc_url( AXX_MARKET_PLUGIN_URL . 'public/assets/images/default-award.svg' ); ?>" style="width:100%; height:100%;" />
                                <?php endif; ?>
                            </div>
                            <div class="axx-award-details">
                                <h4><?php echo esc_html( $award['title'] ); ?></h4>
                                <span><?php echo esc_html( $award['place'] ); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <section class="axx-maker-products">
        <?php if ( $status === 'Expired' ) : ?>
            <div class="axx-market-empty-state">
                <h2>Portfolio Inactive</h2>
                <p>Your products have been removed from this directory listing. Please reactivate your rent to restore your public portfolio.</p>
            </div>
        <?php else :
            echo '<h2>Products from the Maker</h2>';
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
        endif ?>
    </section>
</div><?php
get_footer();