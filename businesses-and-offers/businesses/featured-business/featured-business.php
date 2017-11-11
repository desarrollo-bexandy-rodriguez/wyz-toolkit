<?php
if ( ! class_exists( 'WyzFeaturedBusiness' ) ) :

    class WyzFeaturedBusiness {

        protected static $instance = null;

        public function __construct() {

            include_once plugin_dir_path( __FILE__ ) . 'class-sticky-cpt-loader.php';
            include_once plugin_dir_path( __FILE__ ) . 'class-sticky-cpt-posts.php';
        }
        
        public static function get_instance() {

            if ( null == self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }
    }

    WyzFeaturedBusiness::get_instance();

endif;
