<?php

/**
 * Admin class
 */
class WC_SAG_Admin_Page {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /**
     * Constructor
     */
    public function __construct( $settings ) {
        $this->settings = $settings;

        add_action( 'admin_menu', array( $this, 'add_menu' ), 100 );

        add_filter( 'plugin_action_links_' . WC_SAG_BASENAME, array( $this, 'plugin_action_links' ) );
    }

    /**
     * Add setting page as WooCommerce submenu
     */
    public function add_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Guaranteed Reviews Company', 'woo-guaranteed-reviews-company' ),
            __( 'Guaranteed Reviews Company', 'woo-guaranteed-reviews-company' ),
            'manage_options',
            'wc-sag-settings',
            array( $this, 'admin_page' )
        );
    }

    /**
     * Show action links on the plugin screen.
     */
    public function plugin_action_links( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=wc-sag-settings' ) . '" aria-label="' . esc_attr__( 'View Guaranteed Reviews Company settings', 'woo-guaranteed-reviews-company' ) . '">' . esc_html__( 'Settings', 'woo-guaranteed-reviews-company' ) . '</a>',
        );

        return array_merge( $action_links, $links );
    }

    /**
     * Render admin page
     */
    public function admin_page() {
        if ( isset( $_POST['wp-sag-settings-submit'] ) && check_admin_referer( 'wp-sag-settings-form' ) ) {
            $this->update_settings();
        }
        elseif ( isset( $_POST['wp-sag-registered-submit'] ) && check_admin_referer( 'wp-sag-registered-form' ) ) {
            $this->save_apikey();
        }
        elseif ( isset( $_POST['wp-sag-reset-submit'] ) && check_admin_referer( 'wp-sag-reset-form' ) ) {
            $messages = $this->reset_data();
        }

        if ( ( $this->settings->get( 'api_key' ) !== '' ) || ( isset( $_GET['bypass_account'] ) ) ) {
            include_once( WC_SAG_PLUGIN_DIR . 'views/settings-page.php' );
        }
        else {
            $user = wp_get_current_user();
            include_once( WC_SAG_PLUGIN_DIR . 'views/account-page.php' );
        }
    }

    /**
     * Update settings based on form submission
     */
    protected function update_settings() {
        if ( isset( $_POST['api_key'] ) ) {
            $this->settings->set( 'api_key_raw', $_POST['api_key'] );
        }
        if ( isset( $_POST['wc_statuses'] ) && is_array( $_POST['wc_statuses'] ) ) {
            $this->settings->set( 'wc_statuses', array_intersect( $_POST['wc_statuses'], array_keys(wc_get_order_statuses()) ) );
        }

        $this->settings->set( 'enable_widget_js', isset( $_POST['enable_widget_js'] ) ? 1 : 0 );
        $this->settings->set( 'enable_widget_product', isset( $_POST['enable_widget_product'] ) ? 1 : 0 );
        $this->settings->set( 'widget_style', $_POST['widget_style'] );
        $this->settings->set( 'minReviews', $_POST['minReviews'] );
        $this->settings->set( 'enable_widget_footer', isset( $_POST['enable_widget_footer'] ) ? 1 : 0 );
        $this->settings->set( 'enable_loop_rating', isset( $_POST['enable_loop_rating'] ) ? 1 : 0 );
        $this->settings->set( 'posts_per_page', $_POST['posts_per_page'] );
        $this->settings->set( 'source_lang_flags', $_POST['source_lang_flags'] );
		$this->settings->set( 'star_color', $_POST['star_color'] );

        $this->settings->save();
    }

    /**
     * Update API key based on form submission
     */
    protected function save_apikey() {
        if ( isset( $_POST['api_key'] ) ) {
            $this->settings->set( 'api_key_raw', $_POST['api_key'] );
            $this->settings->save();
        }
    }

    /**
     * Reset Data
     */
    protected function reset_data() {
        // Delete all reviews
        $reviews_query = new WP_Query( array(
            'post_type'      => 'wcsag_review',
            'post_status'    => 'any',
            'posts_per_page' => -1
        ) );
        foreach( $reviews_query->posts as $post ) {
            wp_delete_post( $post->ID, true );
        }
        // Delete all review count
        global $wpdb;
        $wpdb->delete( $wpdb->postmeta, array('meta_key' => '_wcsag_rating') );
    }
}