<?php

class WC_SAG_API_Config extends WC_SAG_API_Abstract_Route
{
    /** @var string Route slug */
    protected $route = '/config';

    /** @var string Query var */
    protected $query_var = 'wcsag_config';

    /**
     * Run the endpoint
     */
    protected function run() {

        // Token validation
        if ( !$this->check_token() ) return;

        // Get config
        $config = $this->get_config();

        // Build full URL
        $url = add_query_arg( array(
            'token'  => $token,
            'apiKey' => $this->settings->get( 'api_key' )
        ), $this->settings->get( 'sag_api_url' ) . 'configuration.php' );

        // Post content
        wp_remote_post( esc_url_raw( $url ), array(
          'body'    => base64_encode( json_encode( $config ) ),
          'timeout' => 30,
        ) );
    }

    /**
     * Format expected configuration
     */
    protected function get_config() {
        return  array(
            'order_statuses' => array()
          //  'iframeWidgetPosition'
          //  'javascriptWidget'
          //  'checkingLink'
          //  'multiShops'
          //  'allLast31DaysOrders'
          //  'incLast31DaysOrders'
        );

    }
}