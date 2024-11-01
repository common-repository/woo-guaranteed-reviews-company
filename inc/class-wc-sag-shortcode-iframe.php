<?php

class WC_SAG_Shortcode_Iframe {

    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Constructor
     */
    public function __construct( $settings ) {
        $this->settings = $settings;

        add_shortcode( 'wcsag_iframe', array( $this, 'render_shortcode' ) );
    }

    /**
     * Render shortcode content
     */
    public function render_shortcode( $atts = array(), $content = null ) {
        $atts = shortcode_atts( array(
            'width'  => '100%',
            'height' => 200,
            'format' => 'horizontal'
        ), $atts );

        // Display SAG iframe
        $iframe_url = $this->settings->get( 'sag_domain' ) . '/wp-content/plugins/ag-core/widgets/iframe/2/' . ( $atts[ 'format' ] == 'vertical' ? 'v/' : 'h/' ) . '?id=' . $this->settings->get( 'site_id' );

        echo '<div id="steavisgarantis" align="center" style="padding: 5px 0; width: 100%; background: white; border-radius: 8px;">
                <iframe width="' . esc_attr( $atts[ 'width' ] ) . '" height="' . esc_attr( $atts[ 'height' ] ) . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' . esc_url( $iframe_url )  . '"></iframe>
              </div>';
    }
}