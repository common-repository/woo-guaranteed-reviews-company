<?php

class WC_SAG_Shortcode_Reviews {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Constructor
     */
    public function __construct( $settings ) {
        $this->settings = $settings;

        add_shortcode( 'wcsag_reviews', array( $this, 'render_shortcode' ) );

        add_action( 'wp_ajax_wcsag_more_reviews', array( $this, 'ajax_more_review' ) );
        add_action( 'wp_ajax_nopriv_wcsag_more_reviews', array( $this, 'ajax_more_review' ) );

        if ( $this->settings->get( 'enable_widget_product' ) == 1 ) {
            add_action( 'woocommerce_after_single_product_summary', array( $this, 'render_shortcode' ), 15 );
        }
    }

    /**
     * Render shortcode content
     */
    public function render_shortcode( $atts = array(), $content = null ) {
        $atts = shortcode_atts( array( 'id' => get_the_ID() ), $atts );

        $ratings = wcsag_get_ratings( $atts['id'] );

        $reviews_query = new WP_Query( array(
            'post_type'      => 'wcsag_review',
            'post_status'    => 'publish',
            'post_parent'    => $atts['id'],
            'posts_per_page' => $this->settings->get( 'posts_per_page' )
        ) );

        if ($reviews_query->found_posts == 0 || $reviews_query->found_posts < $this->settings->get( 'minReviews' )) return;
        //ob_start();
        include( WC_SAG_PLUGIN_DIR . 'views/shortcode-reviews.php' );
        //return ob_get_clean();
    }

    /**
     * Render shortcode content
     */
    public function ajax_more_review() {
        // AJAX check
        if ( ( empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest' ) ) die;
        // Params check
        if ( !isset( $_POST['currentPage'] ) || !isset( $_POST['id_product'] ) ) die;

        $paged = (int) $_POST['currentPage'];
        $product_id = (int) $_POST['id_product'];

        $reviews_query = new WP_Query( array(
            'post_type'      => 'wcsag_review',
            'post_status'    => 'publish',
            'post_parent'    => $product_id,
            'posts_per_page' => $this->settings->get( 'posts_per_page' ),
            'paged'          => $paged
        ) );

        include( WC_SAG_PLUGIN_DIR . 'views/shortcode-reviews-list.php' );
        exit;
    }
}