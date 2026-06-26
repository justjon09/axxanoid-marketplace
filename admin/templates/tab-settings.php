<?php
/**
 * Creates the central "Marketplace" hub admin page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$options = get_option( 'axxanoid_marketplace_settings', array() );

echo '<h2>Marketplace Settings</h2>';
echo '<form action="' . esc_url( admin_url( 'options.php' ) ) . '" method="post">';
	settings_fields( 'axxanoid_marketplace_settings' );
	do_settings_sections( 'axxanoid-marketplace-settings' );
	submit_button();
echo '</form>';
?>