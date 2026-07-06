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

    <section class="axx-hub-section axx-hub-consumer-pitch" style="text-align: center; padding: 40px 20px; background: #fdf8e2; border-radius: 8px; margin-bottom: 40px;">
        <h1 style="font-size: 2.5em; margin-bottom: 15px;">Support Indie Glass & Gear</h1>
        <p style="font-size: 1.2em; color: #4b5563; max-width: 800px; margin: 0 auto 30px;">
            The Average Stoner Indie Marketplace is a curated, ever-changing collection of the best underground glassblowers, 3D printers, and accessory makers on the web. <br><br>
            <strong>These aren't mass-produced dropships.</strong> We hunt down real artists and give them a permanent home to showcase their craft. Inventory moves fast, so bookmark this page and check back often!
        </p>

        <div class="axx-hub-search-wrapper" style="max-width: 600px; margin: 0 auto;">
            <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="axx-market-search-form" style="display: flex; gap: 10px;">
                <input type="hidden" name="post_type" value="axx_market_maker" />
                <input type="text" name="s" placeholder="Search for makers, brands, or specific gear..." required style="flex-grow: 1; padding: 10px; font-size: 16px;" />
                <button type="submit" class="button button-primary" style="padding: 10px 20px; font-size: 16px;">Search the Market</button>
            </form>
        </div>
    </section>

    <section class="axx-hub-section axx-hub-maker-pitch" style="display: flex; flex-wrap: wrap; gap: 40px; align-items: flex-start;">
        
        <div class="axx-maker-pitch-content" style="flex: 1; min-width: 300px;">
            <h2>Are You an Indie Maker?</h2>
            <p>We are actively hunting for talented glassblowers, 3D printers, and cannabis accessory creators to feature to our 4,000+ community members.</p>
            <p><strong>Stop fighting the algorithm.</strong> List your top products directly on Average Stoner and let our traffic do the heavy lifting.</p>
            <ul style="list-style: disc; margin-left: 20px; margin-bottom: 20px;">
                <li>Claim your <strong>10-Day Free Trial</strong> immediately.</li>
                <li>No credit card required upfront.</li>
                <li>Keep 100% of your sales (we just link directly to your existing store).</li>
            </ul>
            <p><em>Note: Due to high volume, please expect a 7-day turnaround for our team to review your application, scrape your provided store, and build your custom portfolio.</em></p>
        </div>

        <div class="axx-maker-intake-form-wrapper" style="flex: 1; min-width: 300px; background: #fff; padding: 30px; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0;">Request a Portfolio</h3>
            
            <form id="axx-market-intake-form">
                <div style="margin-bottom: 15px;">
                    <label for="axx_intake_name" style="display: block; font-weight: bold; margin-bottom: 5px;">Maker / Brand Name *</label>
                    <input type="text" id="axx_intake_name" required style="width: 100%; padding: 8px;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="axx_intake_email" style="display: block; font-weight: bold; margin-bottom: 5px;">Contact Email *</label>
                    <input type="email" id="axx_intake_email" required style="width: 100%; padding: 8px;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="axx_intake_url" style="display: block; font-weight: bold; margin-bottom: 5px;">Etsy / Store URL (Optional but recommended)</label>
                    <input type="url" id="axx_intake_url" placeholder="https://" style="width: 100%; padding: 8px;" />
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="axx_intake_stock" style="display: block; font-weight: bold; margin-bottom: 5px;">Typical Stock Status *</label>
                    <select id="axx_intake_stock" required style="width: 100%; padding: 8px;">
                        <option value="">Select an option...</option>
                        <option value="Made on Demand">Made on Demand</option>
                        <option value="Limited Drops">Limited Drops / Small Batches</option>
                        <option value="Always Available">Always Available</option>
                    </select>
                </div>

                <div id="axx-intake-message" style="display: none; padding: 10px; margin-bottom: 15px; border-radius: 4px;"></div>

                <button type="submit" class="button button-primary" style="width: 100%; font-size: 16px; padding: 10px;">Submit Application</button>
            </form>
        </div>
    </section>

</div><?php
get_footer();