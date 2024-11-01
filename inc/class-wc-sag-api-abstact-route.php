<?php 

/**
 * Api class
 */
abstract class WC_SAG_API_Abstract_Route {
    /** @var WC_SAG_Settings Plugin settings */
    protected $settings;

    /** @var string API namespace */
    protected $namespace = 'wcsag-api';

    /** @var string Route slug */
    protected $route;

    /** @var string Query var */
    protected $query_var;

    /**
     * Set hooks
     */
    public function __construct( $settings )
    {
        $this->settings = $settings;

        if ( !isset($this->route) ) {
          throw new Exception( get_class($this) . ' must have a $route' );
        }

        if ( !isset($this->query_var) ) {
          throw new Exception( get_class($this) . ' must have a $query_var' );
        }

        add_action( 'init', [ $this, 'add_rewrite_rule' ] );

        add_filter( 'query_vars', [ $this, 'query_vars' ] );

        add_filter( 'template_include', [ $this, 'get_template' ] );
    }

    /**
     * Add endpoint rewrite rule
     */
    public function add_rewrite_rule() {
        add_rewrite_rule( '^' . $this->namespace . $this->route . '/?$', 'index.php?' . $this->query_var . '=1', 'top' );
    }

    /**
     * Register endpoint query var
     */
    public function query_vars( $vars ) {
        $vars[] = $this->query_var;
        return $vars;
    }

    /**
     * Run our endpoint
     */
    public function get_template( $template ) {
        if ( get_query_var( $this->query_var ) ) {
            $this->run();
            exit;
        }
        return $template;
    }

    /**
     * Generate and return the actual data.
     */
    abstract protected function run();
}