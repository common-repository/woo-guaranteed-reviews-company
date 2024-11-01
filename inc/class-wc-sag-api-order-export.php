<?php

class WC_SAG_API_Order_Export extends WC_SAG_API_Abstract_Route {
    /** @var string Route slug */
    protected $route = '/orders/export';

    /** @var string Query var */
    protected $query_var = 'wcsag_orders_export';

    /**
     * Run the endpoint
     */
    protected function run() {
        $params = $this->validate_request();

        // Get orders between requested dates
        $orders = $this->get_orders( $params );
		
        // Format orders
        $formatted_orders = array_map( array( $this, 'format_order' ), $orders );

        // Build full URL
        $url = add_query_arg( array(
            'token'  => $params['token'],
            'apiKey' => $this->settings->guess_api_key_for_language( $params['lang'] )
        ), $this->settings->get_sag_api_url( $params['lang'] ) . 'bulkOrderInfos.php' );

        // Post orders to SAG endpoint
        wp_remote_post( esc_url_raw( $url ), array(
            'body'    => array( 'data' => base64_encode( json_encode( $formatted_orders ) ) ),
            'timeout' => 30,
        ) );
    }

    /**
     * Get local WPML languages based on API keys
     */
    protected function get_local_languages( $lang ) {
        $raw_api_key = $this->settings->get( 'api_key_raw' );
        $local_lang_codes = array();
        if ( is_array( $raw_api_key ) ) {
            // Looks like multilingual setup, returns key for current language
            foreach ( $raw_api_key as $lang_code => $api_key ) {
                if ( wcsag_get_lang_from_api_key( $api_key ) == $lang ) {
                    $local_lang_codes[] = $lang_code;
                }
            }
        }
        else {
            // Use api key language as default case
            $local_lang_codes[] = wcsag_get_lang_from_api_key( $api_key );
        }
        return $local_lang_codes;
    }

    /**
     * Get orders with retrocompat
     */
    protected function get_orders( $params ) {
        
        if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {

            $args = array(
                'post_type'      => 'shop_order',
                'post_status'    => $this->settings->get( 'wc_statuses' ),
                'posts_per_page' => -1,
                'date_query' => array(
                    array(
                        'after'     => array(
                            'year'  => date( 'Y', $params['date_from'] ),
                            'month' => date( 'n', $params['date_from'] ),
                            'day'   => date( 'j', $params['date_from'] ),
                        ),
                        'before'    => array(
                            'year'  => date( 'Y', $params['date_to'] ),
                            'month' => date( 'n', $params['date_to'] ),
                            'day'   => date( 'j', $params['date_to'] ),
                        ),
                        'inclusive' => true,
                        'column'    => 'post_modified',
                    ),
                ),
            );

            // Filter by lang if WPML is enabled
            if ( $params['lang'] && function_exists( 'icl_object_id' ) && class_exists( 'SitePress' ) ) {
                $args['meta_query'][] = array(
                    'key'     => 'wpml_language',
                    'value'   => $this->get_local_languages( $params['lang'] ),
                    'compare' => 'IN',
                );
            }
            
            if ( $params['lang'] && class_exists( 'Polylang_Woocommerce' )) {
                $args['meta_query'][] = array(
                    'key'     => 'lang',
                    'value'   => $this->get_local_languages( $params['lang'] ),
                    'compare' => 'IN',
                );
            }

            // Filter by lang if Weglot is enabled
			if ( $params['lang'] && class_exists( 'Context_Weglot' )) {
                $args['meta_query'][] = array(
                    'key'     => 'weglot_language',
                    'value'   => $this->get_local_languages( $params['lang'] ),
                    'compare' => 'IN',
                );
            }
			
			if ( $params['lang'] && class_exists( 'Context_Weglot' )) {
                echo "Weglot_up";
            }
            
            if ( $params['lang'] && class_exists( 'Polylang_Woocommerce' )) {
                echo "PL WC loaded";
            }


            return array_map( 'wc_get_order', get_posts( $args ) );
        }
        else {
			

            $args = array(
                'type'           => 'shop_order',
                'status'         => $this->settings->get( 'wc_statuses' ),
                'posts_per_page' => -1,
                'date_modified'  => "{$params['date_from']}...{$params['date_to']}",
            );

            // Filter by lang if WPML is enabled & WooCommerce multilingual is enabled
            if ( $params['lang'] && function_exists( 'icl_object_id' ) && class_exists( 'SitePress' ) && function_exists( 'wcml_loader' )) {
                add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'handle_custom_query_var' ), 10, 2 );
                $args['wpml_languages'] = $this->get_local_languages( $params['lang'] );
            }
            
            
            if ( $params['lang'] && class_exists( 'Polylang_Woocommerce' )) {
                $args['lang'] = $this->get_local_languages( $params['lang'] );
            }

            // Filter by lang if Weglot is enabled & WooCommerce multilingual is enabled
			if ( $params['lang'] && class_exists( 'Context_Weglot' )) {
				$args['meta_key'] = 'weglot_language';
				$args['meta_value'] = $this->get_local_languages( $params['lang'] );
            }
			
            $orders = wc_get_orders( $args );
            return $orders;
        }
    }

    /**
     * Handle WC wpml_languages query var to get orders with specific language
     */
    public function handle_custom_query_var( $query, $query_vars ) {
        if ( ! empty( $query_vars['wpml_languages'] ) ) {
            $query['meta_query'][] = array(
                'key'   => 'wpml_language',
                'value' => is_array($query_vars['wpml_languages']) ? $query_vars['wpml_languages'] : array(),
                'compare' => 'IN',
            );
        }

        return $query;
    }

    /**
     * Validate and sanitize request 
     */
    protected function validate_request() {
        $params = array();

        // Lang validation
        if ( isset( $_POST['lang'] ) ) {
            $params['lang'] = $_POST['lang'];
        }
        else {
            die( 'Missing Lang' );
        }

        // Token validation
        if ( isset( $_POST['token'] ) && $this->check_token( $_POST['token'], $params['lang'] ) ) {
            $params['token'] = $_POST['token'];
        }
        else {
            die( 'Invalid token' );
        }

        // From Date validation
        if ( isset( $_POST['fromDate'] ) && false !== $date_from = strtotime( $_POST['fromDate'] ) ) {
            $params['date_from'] = $date_from;
        }
        else {
            die( 'Invalid fromDate' );
        }

        // To Date validation
        if ( isset( $_POST['toDate'] ) && false !== $date_to = strtotime( $_POST['toDate'] ) ) {
            $params['date_to'] = $date_to;
        }
        else {
            die( 'Invalid toDate' );
        }

        return $params;
    }

    /**
     * Check a token
     */
    protected function check_token( $token, $lang = null ) {
        // Build SAG token checking URL
        $url = add_query_arg( array(
            'token'  => $token,
            'apiKey' => $this->settings->guess_api_key_for_language( $lang )
            ), $this->settings->get_sag_api_url( $lang ) . 'checkToken.php' );

        $response_body = wp_remote_retrieve_body( wp_remote_get( esc_url_raw( $url ), array( 'timeout' => 30 ) ) );

        // Check if token was validated
        return ( strpos( $response_body, 'ValidSagData' ) !== false ); 
    }

    /**
     * Format order values
     */
    protected function format_order( $order ) {
        $formatted_order = array(
            'id_order'            => version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id(),
            'reference'           => $order->get_order_number(),
            'order_date'          => version_compare( WC_VERSION, '3.0.0', '<' ) ? date( 'Y-m-d H:i:s', strtotime( $order->order_date ) ) : $order->get_date_created()->date( 'Y-m-d H:i:s' ),
            'total_paid_tax_incl' => wc_format_decimal( $order->get_total(), 2 ),
            'firstname'           => version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->billing_first_name : $order->get_billing_first_name(),
            'lastname'            => version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->billing_last_name : $order->get_billing_last_name(),
            'email'               => version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->billing_email : $order->get_billing_email(),
            'shipping_country'    => version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->shipping_country : $order->get_shipping_country()
        );

        foreach ( $order->get_items() as $item ) {
            $formatted_product = $this->format_product($item, $order);
            if ( $formatted_product ) {
                $formatted_order['products'][] = $formatted_product;
            }
        }

        return $formatted_order;
    }

    /**
     * Format product values 
     */
    protected function format_product( $item, $order ) {
        $product_id = $item instanceof WC_Order_Item_Product ? $item->get_product_id() : $item['product_id'];
		$variation_product_id = $item instanceof WC_Order_Item_Product ? $item->get_variation_id() : $item['variation_id'];
        // Apply filter for WPML
        if ( function_exists( 'icl_object_id' )  && class_exists( 'SitePress' ) ) {
            $order_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id();
            $order_lang = get_post_meta( $order_id, 'wpml_language', true );
            $product_id = function_exists( 'wpml_object_id_filter' ) ? apply_filters( 'wpml_object_id', $product_id, 'product', true, $order_lang ) : icl_object_id( $product_id, 'product', true, $order_lang );
        }

        $product = wc_get_product( $product_id );

        if ( $product ) {
			$idProduct = version_compare( WC_VERSION, '3.0.0', '<' ) ?  $product->id : $product->get_id();
			$ean13 = '';
            
			//Do we have a ean13 ? (SeoPress compatibility)
			$fieldType = get_post_meta( $idProduct, 'sp_wc_barcode_type_field', true );
			if ($fieldType && ($fieldType =="gtin13" or $fieldType =="none")) {
				$ean13 = get_post_meta( $idProduct, 'sp_wc_barcode_field', true );
			}
            
            //Do we have a ean13 ? (Cart Product Feed Additional Product Fields compatibility)
            $cpfEan = get_post_meta( $idProduct, '_cpf_ean', true );
			if ($cpfEan) {
				$ean13 = $cpfEan;
			}

            //Do we have a ean13 ? (Product GTIN (EAN, UPC, ISBN) for WooCommerce)
            $wpmEan = get_post_meta( $idProduct, '_wpm_gtin_code', true );
			if ($wpmEan) {
				$ean13 = $wpmEan;
			}
			
			$GtinProduct = get_post_meta( $idProduct, '_gtin_product', true );
			if ($GtinProduct) {
				$ean13 = $GtinProduct;
			}
            
            //custom Method from https://njengah.com/add-gtin-numbers-products-woocommerce/
            $customGtin = get_post_meta( $idProduct, '_gtin', true );
			if ($customGtin) {
				$ean13 = $customGtin;
			}
			
			$customGtin13 = get_post_meta( $idProduct, '_gtin_product', true );
			if ($customGtin13) {
				$ean13 = $customGtin13;
			}
			
			if ($variation_product_id) {
				$customGtin13var = get_post_meta( $variation_product_id, 'gtin_product_variable', true );
				if ($customGtin13var) {
					$ean13 = $customGtin13var;
				}
                $customGtin13var = get_post_meta( $variation_product_id, "wpseo_variation_global_identifiers_values", true );
				if ($customGtin13var) {
					$ean13 = $customGtin13var["gtin13"];
				}
			}
			
            return array(
                'id'              => $idProduct,
                'ean13'           => $ean13,
                'upc'             => '',
                'sku'             => $product->get_sku(),
                'name'            => version_compare( WC_VERSION, '3.0.0', '<' ) ? $product->post_title : $product->get_title(),
                'quantitySold'    => $item instanceof WC_Order_Item_Product ? $item->get_quantity() : $item['qty'],
                'unitPriceSoldHt' => wc_format_decimal( $order->get_item_total( $item ), 2 ),
                'url' 			  => get_permalink( $product_id )
            );
        }
        else {
            return false;
        }
    }

}