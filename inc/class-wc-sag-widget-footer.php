<?php

class WC_SAG_Widget_Footer extends WP_Widget {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Sets up the widgets name etc
     */
    public function __construct( $settings ) {
        $this->settings = $settings;

        parent::__construct( 'wc_sag_footer_widget',
                             __( 'GRC Checking link', 'woo-guaranteed-reviews-company' ),
                             array( 'description' => __( 'Display a checking link in the footer pointing to your attestation page. (Important for your SEO)', 'woo-guaranteed-reviews-company' ) ) );
    }
 
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';

        echo $args['before_widget'];

        // Display title
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $certificate_url = $this->settings->get( 'certificate_url' );

        if ( $certificate_url != '' ) {

            include( WC_SAG_PLUGIN_DIR . 'views/shortcode-footer.php' );

        }
        else {
            echo '<p>' . __( 'Error: cannot retrieve merchant certificate.', 'woo-guaranteed-reviews-company' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';

        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woo-guaranteed-reviews-company' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $title, 'woo-guaranteed-reviews-company' ); ?>">
        </p>
        <?php 
    }
}