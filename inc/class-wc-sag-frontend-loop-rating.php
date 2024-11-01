<?php

class WC_SAG_Frontend_Loop_Rating {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Constructor
     */
    public function __construct( $settings ) {
        $this->settings = $settings;
		add_shortcode( 'wcsag_category', array( $this, 'add_ratings' ) );
        if ( $this->settings->get( 'enable_loop_rating' ) == 1 ) {
            add_filter( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_ratings' ), 2 );
        }
    }

    /**
     * Add SAG ratings to product loop
     */
    public function add_ratings($atts = array()) {
        global $product;

        $product_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $product->id : $product->get_id();
        $ratings = wcsag_get_ratings( $product_id );
        $atts = shortcode_atts( array( 'id' => $product->get_id() ), $atts );

        $reviews_query = new WP_Query( array(
            'post_type'   => 'wcsag_review',
            'post_status' => 'publish',
            'post_parent' => $atts['id']
        ) );

        if ( $ratings['average'] && $reviews_query->found_posts !== 0 ) {
            include( WC_SAG_PLUGIN_DIR . 'views/loop-star-rating.php' );
        }
    }
}