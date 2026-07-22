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

    </div>