<?php if ( ! defined( 'WPINC' ) ) die; ?>
<div class="axx-onboard-section">
    <h2 class="axx-onboard-section-title">2. Add Your Products</h2>
    <p class="axx-form-hint">Submit the products you want featured on the marketplace. Each submission will generate a listing that links directly out to your official store checkout.</p>
    
    <form id="axx-maker-product-form">
        <input type="hidden" name="action" value="axx_market_save_onboard_product">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce('axx_market_public_nonce') ); ?>">
        <input type="hidden" name="maker_id" value="<?php echo esc_attr( $maker_id ); ?>">

        <div class="axx-flex-row">
            <div class="axx-flex-2">
                <label>Product Title *</label>
                <input type="text" name="product_title" required class="axx-input-full" />
            </div>
            <div class="axx-flex-1">
                <label>Price (USD) *</label>
                <input type="number" step="0.01" name="product_price" required class="axx-input-full" />
            </div>
        </div>

        <div class="axx-flex-row">
            <div class="axx-flex-1">
                <label>Image URL *</label>
                <input type="url" name="product_image" placeholder="https://" required class="axx-input-full" />
            </div>
            <div class="axx-flex-1">
                <label>Checkout Link (Your Store) *</label>
                <input type="url" name="product_url" placeholder="https://" required class="axx-input-full" />
            </div>
        </div>

        <div id="axx-product-msg" style="display:none;" class="notice"></div>
        <button type="submit" class="button button-secondary">+ Add Product to Portfolio</button>
    </form>

    <hr style="border:0; height:1px; background:#e5e7eb; margin:30px 0;" />
    
    <h3 style="margin-bottom:15px;">Your Saved Products</h3>
    <?php
    // Query the products using the siloed product IDs attached to this Maker
    $saved_product_ids = get_post_meta( $maker_id, 'maker_product_ids', false );
    
    if ( ! empty( $saved_product_ids ) && function_exists('WC') ) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post__in'       => $saved_product_ids,
            'post_status'    => 'publish'
        );
        $products_query = new WP_Query( $args );

        if ( $products_query->have_posts() ) {
            echo '<div class="woocommerce"><ul class="products columns-4">';
            while ( $products_query->have_posts() ) {
                $products_query->the_post();
                
                // We wrap the native WC product template in a div so we can append our Remove button
                echo '<li class="product" style="position:relative;">';
                echo '<a href="#" class="axx-remove-product-btn" data-product-id="'.esc_attr(get_the_ID()).'" data-maker-id="'.esc_attr($maker_id).'" style="position:absolute; top:5px; right:5px; background:#d63638; color:#fff; border-radius:50%; width:24px; height:24px; text-align:center; line-height:24px; z-index:10; text-decoration:none; font-weight:bold;" title="Delete Product">&times;</a>';
                
                wc_get_template_part( 'content', 'product' ); 
                
                echo '</li>';
            }
            echo '</ul></div>';
            wp_reset_postdata();
        } else {
            echo '<p style="color:#6b7280;">No products have been added yet.</p>';
        }
    } else {
        echo '<p style="color:#6b7280;">No products have been added yet.</p>';
    }
    ?>

    </div>