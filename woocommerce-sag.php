<?php
/**
 * Plugin Name: Woocommerce - Guaranteed Reviews Company
 * Plugin URI: https://www.guaranteed-reviews.com/
 * Description: Shop and/or product reviews, Google stars, Trusted certificate, automatic validation (option), review files importation…
 * Version: 1.2.6
 * Author: Guaranteed Reviews Company
 * Author URI: http://www.guaranteed-reviews.com/
 * License: GPLv3
 * Domain Path: /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WC_SAG_VERSION', '1.2.6' );
define( 'WC_SAG_MIN_PHP_VER', '5.3.0' );
define( 'WC_SAG_MIN_WC_VER', '2.2.0' );
define( 'WC_SAG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_SAG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_SAG_BASENAME', plugin_basename( __FILE__ ) );

include_once( WC_SAG_PLUGIN_DIR . 'inc/functions.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-settings.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-api-abstact-route.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-api-check.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-api-config.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-api-order-export.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-api-review-import.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-admin-page.php' );
include_once( WC_SAG_PLUGIN_DIR . 'inc/class-wc-sag-frontend.php' );

register_activation_hook( __FILE__, 'wcsag_activate' );
register_deactivation_hook( __FILE__, 'wcsag_deactivate' );

/**
 * The code that runs during plugin activation.
 */
function wcsag_activate() {
    // Add rewrite rules and flush
    $check_api = new WC_SAG_API_Check( new WC_SAG_Settings() );
    $check_api->add_rewrite_rule();
    $config_api = new WC_SAG_API_Config( new WC_SAG_Settings() );
    $config_api->add_rewrite_rule();
    $order_export_api = new WC_SAG_API_Order_Export( new WC_SAG_Settings() );
    $order_export_api->add_rewrite_rule();
    $review_import_api = new WC_SAG_API_Review_Import( new WC_SAG_Settings() );
    $review_import_api->add_rewrite_rule();
    flush_rewrite_rules();
    // disable native WooCommerce reviews & ratings
    update_option( 'woocommerce_enable_reviews', 'no' );
    update_option( 'woocommerce_enable_review_rating', 'no' );
}

/**
 * The code that runs during plugin deactivation.
 */
function wcsag_deactivate() {
    flush_rewrite_rules();
}

/**
 * Main plugin class
 */
class WC_SAG {
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ), 1 );
        add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
    }

    /**
     * Init plugin
     */
    public function init() {
        if ( $this->get_environment_warning() ) {
            return;
        }

        $this->load_textdomain();
        $this->register_post_types();

        $this->settings = new WC_SAG_Settings();
        
        // API
        new WC_SAG_API_Check( $this->settings );
        new WC_SAG_API_Config( $this->settings );
        new WC_SAG_API_Order_Export( $this->settings );
        new WC_SAG_API_Review_Import( $this->settings );

        // Frontend
        new WC_SAG_Frontend( $this->settings );

        // Admin
        if ( is_admin() ) {
            new WC_SAG_Admin_Page( $this->settings );
        }
    }

    /**
     * Display admin notice
     */
    public function admin_notices() {
        if ( $message = $this->get_environment_warning() ) {
            printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) ); 
        }
    }

    /**
     * Checks the environment for compatibility problems.  Returns a string with the first incompatibility
     * found or false if the environment has no problems.
     */
    protected function get_environment_warning() {
        if ( version_compare( phpversion(), WC_SAG_MIN_PHP_VER, '<' ) ) {
            $message = __( 'Guaranteed Reviews Company - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'woo-guaranteed-reviews-company' );
            return sprintf( $message, WC_SAG_MIN_PHP_VER, phpversion() );
        }

        if ( ! defined( 'WC_VERSION' ) ) {
            return __( 'Guaranteed Reviews Company requires WooCommerce to be activated to work.', 'woo-guaranteed-reviews-company' );
        }

        if ( version_compare( WC_VERSION, WC_SAG_MIN_WC_VER, '<' ) ) {
            $message = __( 'Guaranteed Reviews Company - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'woo-guaranteed-reviews-company' );
            return sprintf( $message, WC_SAG_MIN_WC_VER, WC_VERSION );
        }

        return false;
    }

    /**
     * Load translations
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'woo-guaranteed-reviews-company', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Register post types
     */
    public function register_post_types() {
        register_post_type( 'wcsag_review', array(
            'public'  => false,
            'rewrite' => false
        ));
    }
}

new WC_SAG();
