<?php

//Weglot Compatibility
if(class_exists( 'Context_Weglot' )) {
    include_once ABSPATH . 'wp-content/plugins/weglot/weglot-functions.php';
}

/**
 * Get WordPress Post ID from SAG review ID
 */
function wcsag_get_review_id( $review_id ) {
    $args = array(
       'fields'      => 'ids',
       'post_type'   => 'wcsag_review',
       'post_status' => 'any',
       'meta_query'  => array(
            array(
                'key'   => '_wcsag_id',
                'value' => $review_id
            )
        )
    );
    $query = new WP_Query( $args );
    return $query->have_posts() ? $query->posts[0] : false;
}

/**
 * Get Ratings data from product ID
 */
function wcsag_get_ratings( $product_id ) {
    $product_data = get_post_meta( $product_id, '_wcsag_rating', true );

   if ( empty( $product_data ) ) {
        $product_data = array();
    }

    return array_merge(
        array(
            'average'      => 0,
            'distribution' => array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 ),
            'count'        => 0
        ),
        $product_data
    );
}

/**
 * Update all products average ratings
 */
function wcsag_update_average_ratings() {
    global $wpdb;

    // Compute average in SQL
    $sql = "SELECT $wpdb->posts.post_parent as product_id, AVG($wpdb->postmeta.meta_value) as average
            FROM $wpdb->posts
            LEFT JOIN $wpdb->postmeta
            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
            AND $wpdb->postmeta.meta_key = '_wcsag_rating'
            WHERE $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = 'wcsag_review'
            GROUP BY $wpdb->posts.post_parent";

    $ratings = $wpdb->get_results( $sql );

    $updated_product_ids = array();

    foreach ( $ratings as $rating ) {

        $distributions = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
        $count = 0;

        // Compute rating disribution per product
        $sql = "SELECT FLOOR($wpdb->postmeta.meta_value) as note, COUNT(*) as count
                FROM $wpdb->posts
                LEFT JOIN $wpdb->postmeta
                ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                AND $wpdb->postmeta.meta_key = '_wcsag_rating'
                WHERE $wpdb->posts.post_status = 'publish'
                AND $wpdb->posts.post_type = 'wcsag_review'
                AND $wpdb->posts.post_parent = %d
                GROUP BY 1";

        $results = $wpdb->get_results( $wpdb->prepare( $sql, $rating->product_id ) );
        foreach ( $results as $result ) {
            $distributions[$result->note] = (int)$result->count;
            $count += (int)$result->count;
        }

        update_post_meta( $rating->product_id, '_wcsag_rating', array( 'average' => $rating->average, 'distribution' => $distributions, 'count' => $count ) );
        $updated_product_ids[] = $rating->product_id;
    }

    if ( count( $updated_product_ids ) > 0 ) {
        $ids_placeholder = implode( ',', array_fill( 0, count( $updated_product_ids ), '%d' ) );
        // Delete all previous average ratings data
        $sql = "DELETE $wpdb->postmeta
                FROM $wpdb->postmeta
                LEFT JOIN $wpdb->posts
                ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                WHERE $wpdb->postmeta.meta_key = '_wcsag_rating'
                AND $wpdb->posts.post_type = 'product'
                AND $wpdb->postmeta.post_id NOT IN ($ids_placeholder)";
        $wpdb->query( $wpdb->prepare( $sql, $updated_product_ids ) );
    }

}

/**
 * Get language code from api key
 */
function wcsag_get_lang_from_api_key( $api_key ) {
    $parts = explode( '/', $api_key );
    return isset($parts[1]) ? $parts[1] : false;
}

/**
 * Get custom answer ID from product id
 */
function wcsag_get_custom_answers( $product_id ) {
    $answers_data = get_post_meta( $product_id, '_wcsag_custom_answers', true );

    return $answers_data;
}

/**
 * Weglot Plugin Compatibility to know which language is currently chosen on front
 */
function weglot_current_language_comp() {
    if (class_exists( 'Context_Weglot' )) {
        $current_language = weglot_get_current_language();
        return $current_language;
    }
}

// CREATION CHAMP GTIN PERSONNALISE
// Champ que l'on va récupérer avec notre module.
// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_gtin_product',
            'placeholder' => 'GTIN Produit',
            'label' => __('GTIN Produit', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
    echo '</div>';
}

function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_gtin_product = $_POST['_gtin_product'];
    if (!empty($woocommerce_gtin_product))
        update_post_meta($post_id, '_gtin_product', esc_attr($woocommerce_gtin_product));
}