<?php
/**
 * The template for displaying the main Marketplace Hub / Explainer page.
 *
 * @package Axxanoid_Marketplace
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

get_header();
?>

<div class="axx-market-hub-container wrap">

    <section class="axx-hub-section axx-hub-consumer-pitch">
        <h1>Support Indie Glass & Gear</h1>
        <p class="axx-hub-subtitle">
            The Average Stoner Indie Marketplace is a curated, ever-changing collection of the best underground glassblowers, 3D printers, and accessory makers on the web. <br><br>
            <strong>These aren't mass-produced dropships.</strong> We hunt down real artists and give them a permanent home to showcase their craft. Inventory moves fast, so bookmark this page and check back often!
        </p>

        <div class="axx-hub-search-wrapper">
            <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="axx-market-search-form">
                <input type="hidden" name="post_type" value="axx_market_maker" />
                <input type="text" name="s" placeholder="Search for makers, brands, or specific gear..." required />
                <button type="submit" class="button button-primary">Search the Market</button>
            </form>
        </div>
    </section>

    <section class="axx-hub-section axx-hub-indie-finds">
        <h2 class="axx-indie-finds-title">Latest Indie Finds</h2>
        <?php 
        if ( function_exists( 'WC' ) ) {
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 4, // Show the 4 newest products
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat', // Querying the native Woo category
                        'field'    => 'slug',
                        'terms'    => 'indie-finds',
                    ),
                ),
            );

            $products_query = new WP_Query( $args );

            if ( $products_query->have_posts() ) {
                echo '<div class="woocommerce axx-indie-finds-woo">';
                echo '<ul class="products columns-4">';
                while ( $products_query->have_posts() ) {
                    $products_query->the_post();
                    wc_get_template_part( 'content', 'product' ); 
                }
                echo '</ul>';
                echo '</div>';
                wp_reset_postdata();
            } else {
                echo '<p class="axx-indie-finds-empty">New drops coming soon...</p>';
            }
        }
        ?>
    </section>

    <section class="axx-hub-section axx-hub-maker-pitch">
        
        <div class="axx-maker-pitch-content">
            <h2>Are You an Indie Maker?</h2>
            <p>We are actively hunting for talented glassblowers, 3D printers, and cannabis accessory creators to feature to our 4,000+ community members.</p>
            <p><strong>Stop fighting the algorithm.</strong> List your top products directly on Average Stoner and let our traffic do the heavy lifting.</p>
            <ul class="axx-maker-pitch-list">
                <li>Claim your <strong>10-Day Free Trial</strong> immediately.</li>
                <li>No credit card required upfront.</li>
                <li>Keep 100% of your sales (we just link directly to your existing store).</li>
            </ul>
            <p><em>Note: Due to high volume, please expect up to a 7-day turnaround for our team to review your application, scrape your provided store, and build your custom portfolio.</em></p>
        </div>

        <div class="axx-maker-intake-form-wrapper">
            <h3>Request a Portfolio Listing</h3>
            
            <form id="axx-market-intake-form">
                <input type="text" id="axx_intake_hp" name="axx_intake_hp" style="display:none !important;" tabindex="-1" autocomplete="off" />

                <div class="axx-form-row">
                    <label for="axx_intake_name">Maker / Brand Name *</label>
                    <input type="text" id="axx_intake_name" placeholder="Joe Makes Glass" required />
                </div>

                <div class="axx-form-row">
                    <label for="axx_intake_email">Contact Email *</label>
                    <input type="email" id="axx_intake_email" required />
                </div>

                <div class="axx-form-row">
                    <label for="axx_intake_url">Alt / Store URL (Optional but recommended)</label>
                    <input type="url" id="axx_intake_url" placeholder="https://" />
                </div>

                <div id="axx-intake-message" style="display: none;"></div>

                <button type="submit" class="button button-primary">Request Listing</button>
            </form>
        </div>
    </section>

</div>
<?php get_footer(); ?>