<?php if ( ! defined( 'WPINC' ) ) die; ?>
<div class="axx-onboard-section">
    <h2 class="axx-onboard-section-title">1. Brand Visuals & Bio</h2>
    <form id="axx-maker-profile-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="axx_market_save_onboard_profile">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce('axx_market_public_nonce') ); ?>">
        <input type="hidden" name="maker_id" value="<?php echo esc_attr( $maker_id ); ?>">

        <div class="axx-form-row">
            <label>Header Banner Image</label>
            <p class="axx-form-hint">Landscape format (e.g., 1200x300px). JPG or PNG.</p>
            <input type="file" name="maker_header_banner" accept="image/jpeg, image/png, image/webp" />
        </div>

        <div class="axx-form-row">
            <label>Portrait / Logo</label>
            <p class="axx-form-hint">Square format (e.g., 500x500px). JPG or PNG.</p>
            <input type="file" name="maker_portrait" accept="image/jpeg, image/png, image/webp" />
        </div>

        <div class="axx-form-row">
            <label>Callout Quote</label>
            <input type="text" name="maker_callout_text" value="<?php echo esc_attr( $callout ); ?>" placeholder="A short, catchy quote about your craft..." class="axx-input-full" />
        </div>

        <div class="axx-form-row">
            <label>Maker Bio</label>
            <textarea name="maker_bio" rows="6" placeholder="Tell the community about yourself and your process..." class="axx-input-full"><?php echo esc_textarea( $bio ); ?></textarea>
        </div>

        <div class="axx-form-row">
            <label>Public Display Email (Optional)</label>
            <input type="email" name="maker_display_email" value="<?php echo esc_attr( $display_email ); ?>" placeholder="sales@yourshop.com" class="axx-input-full" />
        </div>

        <div class="axx-form-row">
            <label>Social & External Links</label>
            <div id="axx-onboard-socials-wrapper">
                <?php foreach ( $socials as $index => $social ) : ?>
                    <div class="axx-repeater-row">
                        <select name="socials[<?php echo $index; ?>][platform]" class="axx-input-full axx-social-select-auto">
                            <option value="">Select Platform...</option>
                            <?php foreach ( $networks as $key => $data ) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected( $social['platform'] ?? '', $key ); ?>><?php echo esc_html( $data['label'] ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="socials[<?php echo $index; ?>][handle]" value="<?php echo esc_attr( $social['handle'] ?? '' ); ?>" placeholder="Handle or URL" class="axx-input-full" />
                        <a href="#" class="axx-repeater-remove">&times;</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="axx-add-onboard-social">+ Add Social Link</button>
        </div>

        <div class="axx-form-row">
            <label>Awards & Accolades (Optional)</label>
            <div id="axx-onboard-awards-wrapper">
                <?php foreach ( $awards as $index => $award ) : ?>
                    <div class="axx-repeater-row">
                        <input type="text" name="awards[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $award['title'] ?? '' ); ?>" placeholder="Award Title" class="axx-input-full" />
                        <input type="text" name="awards[<?php echo $index; ?>][place]" value="<?php echo esc_attr( $award['place'] ?? '' ); ?>" placeholder="Place (e.g. 1st)" class="axx-input-small" />
                        <a href="#" class="axx-repeater-remove">&times;</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="axx-add-onboard-award">+ Add Award</button>
        </div>

        <div id="axx-profile-msg" style="display:none;" class="notice"></div>
        <button type="submit" class="button button-primary">Save Profile Data</button>
    </form>
</div>