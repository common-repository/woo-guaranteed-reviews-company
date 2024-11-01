<?php

class WC_SAG_API_Check extends WC_SAG_API_Abstract_Route {
    /** @var string Route slug */
    protected $route = '';

    /** @var string Query var */
    protected $query_var = 'wcsag_check';

    /**
     * Run the endpoint
     */
    protected function run() {
        echo 'STEAVISGARANTIS';
    }
}