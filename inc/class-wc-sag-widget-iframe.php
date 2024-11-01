<?php

class WC_SAG_Widget_Iframe extends WP_Widget {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Sets up the widgets name etc
     */
    public function __construct( $settings ) {
        $this->settings = $settings;

        parent::__construct( 'wc_sag_iframe_widget',
                             __( 'GRC Widget iFrame', 'woo-guaranteed-reviews-company' ),
                             array( 'description' => __( 'Display vertical or horizontal widget with reviews (we recommend to place it in a column or in the footer)', 'woo-guaranteed-reviews-company' ) ) );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
        $iframe_height = isset( $instance[ 'height' ] ) ? $instance[ 'height' ] : 200;
        $endpoint = isset( $instance[ 'format' ] ) && $instance[ 'format' ] == 'vertical' ? 'v/' : 'h/';

        echo $args['before_widget'];

        // Display title
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ( $this->settings->get( 'site_id' ) !== 0 ) {
            // Display SAG iframe
            $iframe_url = $this->settings->get( 'sag_domain' ) . '/wp-content/plugins/ag-core/widgets/iframe/2/' . $endpoint . '?id=' . $this->settings->get( 'site_id' );

            echo '<div id="steavisgarantis" align="center" style="padding-top: 5px; width: 100%; background: white; border-radius: 8px;">
                    <iframe width="100%" height="' . esc_attr( $iframe_height ) . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' . esc_url( $iframe_url ) . '"></iframe>
                  </div>';
        }
        else {
            echo '<p>' . __( 'Please configure the plugin correctly to display this widget.', 'woo-guaranteed-reviews-company' ) . '</p>';
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
        $height = isset( $instance[ 'height' ] ) ? $instance[ 'height' ] : 200;
        $format = isset( $instance[ 'format' ] ) ? $instance[ 'format' ] : 'horizontal'; 

        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woo-guaranteed-reviews-company' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Iframe height:', 'woo-guaranteed-reviews-company' ); ?></label>
            <input class="small-text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="number" step="1" min="1" value="<?php esc_attr_e( $height ); ?>" size="5">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Format:', 'woo-guaranteed-reviews-company' ); ?></label>
            <label for="<?php echo $this->get_field_id( 'format-h' ); ?>"><input type="radio" id="<?php echo $this->get_field_id( 'format-h' ); ?>"
             name="<?php echo $this->get_field_name( 'format' ); ?>" value="horizontal" <?php if ( $format == 'horizontal' ) echo 'checked="checked"'; ?>> <?php _e( 'Horizontal', 'woo-guaranteed-reviews-company' ); ?></label>
            <label for="<?php echo $this->get_field_id( 'format-v' ); ?>"><input type="radio" id="<?php echo $this->get_field_id( 'format-v' ); ?>"
             name="<?php echo $this->get_field_name( 'format' ); ?>" value="vertical" <?php if ( $format == 'vertical' ) echo 'checked="checked"'; ?>> <?php _e( 'Vertical', 'woo-guaranteed-reviews-company' ); ?></label>
        </p>
        <?php
    }
}