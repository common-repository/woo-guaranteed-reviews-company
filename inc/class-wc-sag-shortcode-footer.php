<?php

class WC_SAG_Shortcode_Footer {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Constructor
     */
    public function __construct( $settings ) {
        $this->settings = $settings;

        add_shortcode( 'wcsag_footer', array( $this, 'render_shortcode' ) );

        if ( $this->settings->get( 'enable_widget_footer' ) == 1 ) {
            add_action( 'storefront_footer', array( $this, 'render_shortcode' ), 15 );
        }
    }

    /**
     * Render shortcode content
     */
    public function render_shortcode( $atts = array(), $content = null ) {
        $certificate_url = $this->settings->get( 'certificate_url' );

        if ( $certificate_url != '' ) {
            include( WC_SAG_PLUGIN_DIR . 'views/shortcode-footer.php' );
        }
        else {
            echo '<p>' . __( 'Error: cannot retrieve merchant certificate.', 'woo-guaranteed-reviews-company' ) . '</p>';
        }
    }
}