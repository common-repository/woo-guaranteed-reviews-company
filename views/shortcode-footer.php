<?php

/**
 * displays the shortcode content
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div id="steavisgarantisFooterVerif">
    <a href="<?php echo $certificate_url; ?>" target="_blank">
        <img src="<?php echo WC_SAG_PLUGIN_URL; ?>assets/images/icon-<?php echo $this->settings->get( 'sag_lang' ); ?>.png" width="20px" height="20px" alt="' . __( 'Avis client', 'woo-guaranteed-reviews-company' ) . '">
    </a>
    <span id="steavisgarantisFooterText"><?php _e( 'Merchant approved by Guaranteed Reviews Company', 'woo-guaranteed-reviews-company' ); ?>, 
    <a href="<?php echo $certificate_url; ?>" target="_blank"><?php _e( 'click here to display our certification', 'woo-guaranteed-reviews-company' ); ?></a>.</span>
</div>