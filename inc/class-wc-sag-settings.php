<?php

/**
 * Plugin Options class
 */
class WC_SAG_Settings {
    protected $option_slug = 'wc-sag-settings';

    protected $settings;

    protected $default_settings = array(
        'api_key_raw'            => '',
        'wc_statuses'            => array( 'wc-completed' ),
        'enable_widget_js'       => 1,
        'enable_widget_product'  => 1,
        'widget_style'           => 1,
        'minReviews'=> 1,
        'enable_widget_footer'   => 1,
        'enable_loop_rating'     => 1,
        'posts_per_page'         => '6',
        'source_lang_flags'      => 1,
		'star_color'			 => '#f5d700'
    );

    /**
     * Constructor
     */
    public function __construct() {
        $this->load();
    }

    /**
     * Load settings
     */
    public function load() {
        $saved_options = get_option( $this->option_slug, array() );
        $this->settings = array_merge( $this->default_settings, is_array( $saved_options ) ? $saved_options : array() );
    }

    /**
     * Save settings
     */
    public function save() {
        update_option( $this->option_slug, $this->settings );
    }

    /**
     * Get a setting
     */
    public function get( $key ) {
        if ( method_exists( $this, "get_$key" ) ) {
            return $this->{"get_$key"}();
        }
        return array_key_exists( $key, $this->settings ) ? $this->settings[ $key ] : false;
    }

    /**
     * Set a setting
     */
    public function set( $key, $value ) {
        if ( array_key_exists( $key, $this->settings ) ) {
            $this->settings[ $key ] = $value;
            return true;
        }
        return false;
    }

    /**
     * Get Current API Key
     */
    protected function get_api_key() {
        $raw_api_key = $this->settings['api_key_raw'];
        if ( is_array( $raw_api_key ) ) {
            // Looks like multilingual setup, returns key for current language
            if ( defined( 'ICL_LANGUAGE_CODE' ) && array_key_exists( ICL_LANGUAGE_CODE, $raw_api_key ) ) {
                return $raw_api_key[ICL_LANGUAGE_CODE];
            }
            elseif (class_exists( 'Context_Weglot' )) {
                //Weglot Plugin Compatibility, returns current language on front
                $current_language =  weglot_current_language_comp();
                return $raw_api_key[$current_language];
            }
            else {
                // Return first found key as fallback
                return array_shift($raw_api_key);
            }
        }
        else {
            // Only one language
            return $raw_api_key;
        }
    }

    public function guess_api_key_for_language( $lang ) {
        $raw_api_key = $this->settings['api_key_raw'];
        if ( is_array( $raw_api_key ) ) {
            foreach ( $raw_api_key as $api_key) {
                if ( wcsag_get_lang_from_api_key( $api_key ) == $lang ) {
                    return $api_key;
                }
            }
        }
        else {
            return $raw_api_key;
        }
    }

    /**
     * Get SAG Domain
     */
    protected function get_sag_domain( $lang = null ) {
        if ( is_null( $lang ) ) {
            // Get current language, from API Key or site locale
            $api_key = $this->get( 'api_key' );
            $lang = !empty($api_key) ? wcsag_get_lang_from_api_key( $api_key ) : substr( get_locale(), 0, 2);
        }

		//Domain
		switch (strtolower( $lang )) {
			case 'fr' : $url = 'https://www.societe-des-avis-garantis.fr';			break;
			case 'en' : $url = 'https://www.guaranteed-reviews.com';				break;
			case 'it' : $url = 'https://www.societa-recensioni-garantite.it';		break;
			case 'es' : $url = 'https://www.sociedad-de-opiniones-contrastadas.es';	break;
			case 'de' : $url = 'https://www.g-g-b.de';								break;
			case 'nl' : $url = 'https://www.g-b-n.nl';								break;
			default   : $url = 'https://www.societe-des-avis-garantis.fr';			break;
		}

        // Returns url based on language
        return $url;
    }

    /**
     * Get SAG API URL
     */
    public function get_sag_api_url( $lang = null ) {
        return $this->get_sag_domain( $lang ) . '/wp-content/plugins/ag-core/api/';
    }

    /**
     * Get Site ID from API key
     */
    protected function get_site_id() {
        $api_key = $this->get( 'api_key' );
        $parts = explode( '/', $api_key );
        return isset($parts[0]) ? $parts[0] : false;
    }

    /**
     * Get Certificate URL
     */
    protected function get_certificate_url() {

        if ( false === ( $certificate_url = get_transient( 'wcsag_certificate_url' ) ) ) {

            // Build full URL
            $url = add_query_arg( array(
                'method' =>'certificateUrl',
                'apiKey' => $this->get( 'api_key' )
            ), $this->get_sag_domain() . '/wp-content/plugins/ag-core/api/getInfos.php' );

            $response = wp_remote_get( $url, array( 'timeout' => 30 ) );
            $url = wp_remote_retrieve_body( $response );
            $certificate_url = filter_var( $url, FILTER_VALIDATE_URL ) !== false ? $url : '';

            set_transient( 'wcsag_certificate_url', $certificate_url, 12 * HOUR_IN_SECONDS );
        }

        return $certificate_url;
    }


    /**
     * Get SAG Lang
     */
    protected function get_sag_lang() {
		$api_key = $this->get( 'api_key' );
		$sag_lang = (!empty($api_key) ? wcsag_get_lang_from_api_key( $api_key ) : substr( get_locale(), 0, 2) );
        return $sag_lang;
    }
}