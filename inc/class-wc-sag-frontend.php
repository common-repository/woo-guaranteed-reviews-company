<?php

include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-frontend-loop-rating.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-frontend-structured-data.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-widget-iframe.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-widget-footer.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-shortcode-iframe.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-shortcode-summary.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-shortcode-reviews.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-shortcode-footer.php' );

class WC_SAG_Frontend {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Constructor
     */
    public function __construct( $settings ) {
        $this->settings = $settings;
        
        if ( $this->settings->get( 'api_key' ) !== '' ) {
            $this->init();
        }
    }

    /**
     * Init frontend part of the plugin
     */
    protected function init() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_common_script_styles' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widget_js' ) );

        add_action( 'widgets_init', array( $this, 'widgets_init' ) );

        new WC_SAG_Frontend_Loop_Rating( $this->settings );
        new WC_SAG_Frontend_Structured_Data();
        new WC_SAG_Shortcode_Iframe( $this->settings );
        new WC_SAG_Shortcode_Summary( $this->settings );
        new WC_SAG_Shortcode_Reviews( $this->settings );
        new WC_SAG_Shortcode_Footer( $this->settings );
    }

    /**
     * Enqueue frontend js & css
     */
    public function enqueue_common_script_styles() {
        wp_enqueue_script( 'wcsag-main', WC_SAG_PLUGIN_URL . 'assets/js/main.js', array( 'jquery' ), WC_SAG_VERSION, true );
        wp_enqueue_style( 'wcsag-font', '//fonts.googleapis.com/css?family=Open+Sans:600,400,400i|Oswald:700' );
        wp_enqueue_style( 'wcsag-main', WC_SAG_PLUGIN_URL . 'assets/css/main.css',  array(), WC_SAG_VERSION );
    }

    /**
     * Display SAG JS widget
     */
    public function enqueue_widget_js() {
        if ( $this->settings->get( 'enable_widget_js' ) == 1 ) {
            wp_enqueue_script( 'wcsag-widget', $this->settings->get( 'sag_domain' ) . '/wp-content/plugins/ag-core/widgets/JsWidget.js', array( 'jquery' ), WC_SAG_VERSION, true );
            add_action( 'wp_footer', array( $this, 'enqueue_inline_config' ), 50 );
        }
    }

    /**
     * Add JS inline config in footer
     */
    public function enqueue_inline_config() {
        ?>
            <script type="text/javascript">
                var agSiteId="<?php echo $this->settings->get( 'site_id' ); ?>";
            </script>
        <?php
    }

    /**
     * Initiate widgets
     */
    public function widgets_init() {
        global $wp_widget_factory;

        $wp_widget_factory->widgets['WC_SAG_Widget_Iframe'] = new WC_SAG_Widget_Iframe( $this->settings );
        $wp_widget_factory->widgets['WC_SAG_Widget_Footer'] = new WC_SAG_Widget_Footer( $this->settings );
    }
}