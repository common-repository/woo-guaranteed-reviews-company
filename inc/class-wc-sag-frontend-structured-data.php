<?php

class WC_SAG_Frontend_Structured_Data {
    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'woocommerce_structured_data_product', array( $this, 'add_ratings_data' ), 10, 2 );
    }

    /**
     * Add SAG ratings to product structured data
     */
    public function add_ratings_data( $markup, $product ) {
        unset($markup['aggregateRating']);

        $product_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $product->id : $product->get_id();        
        $ratings = wcsag_get_ratings( $product_id );

        $reviews_query = new WP_Query( array(
            'post_type'   => 'wcsag_review',
            'post_status' => 'publish',
            'post_parent' => $product_id
        ) );

        if ($reviews_query->found_posts > 0) {
            $markup['aggregateRating'] = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => wc_format_decimal( $ratings['average'], 2 ),
                'ratingCount' => $reviews_query->found_posts,
                'reviewCount' => $reviews_query->found_posts,
                'bestRating' => '5.00'
            );
        }

        return $markup;
    }
}