<?php
/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) die;


if ( !class_exists('Sticky_Post_Expiration') ):

    /**
     * Class Sticky_Post_Expiration
     */
    class Sticky_Post_Expiration {

        /**
         * @var Sticky_Post_Expiration The single instance of the class
         * @since 1.0.0
         */
        protected static $instance = null;

        /**
         * @var version
         * @since 1.0.0
         */
        public static $version = null;

        /**
         * @var plugin_path
         * @since 1.0.0
         */
        public static $plugin_path = null;


        /**
         * @var plugin_url
         * @since 1.0.0
         */
        public static $plugin_url = null;


        /**
         * @var plugin_url
         * @since 1.0.0
         */
        public static $plugin_file = null;


        /**
         * INSTANCE
         *
         * Ensures only one instance of Sticky_Post_Expiration is loaded or can be loaded.
         *
         * @since 1.0.0
         * @static
         * @see Sticky_Post_Expiration()
         * @return Sticky_Post_Expiration - Main instance
         */
        public static function instance( $version, $plugin_path, $plugin_url, $plugin_file ) {

            if ( is_null( self::$instance ) ) {
                self::$instance = new self( $version, $plugin_path, $plugin_url, $plugin_file );
            }
            return self::$instance;
        }


        /**
         * CONSTRUCTOR
         *
         * This function automatically runs when the class is instantiated
         * All of the plugin setup kooks go here
         *
         * @since 1.0.0
         */
        private function __construct( $version, $plugin_path, $plugin_url, $plugin_file ) {

            //Assign the class variables
            self::$version = $version;
            self::$plugin_path = $plugin_path;
            self::$plugin_url = $plugin_url;
            self::$plugin_file = $plugin_file;


            // Include required files
            require_once ( $plugin_path . 'includes/class-metabox-field.php' );
            require_once ( $plugin_path . 'includes/class-custom-admin-column.php');


            // Register the deactivation hook
            register_deactivation_hook( $plugin_path.$plugin_file , array( $this, 'deactivation' ) );


            // Schedule the cron to check the sticky expiration
            if( !wp_next_scheduled( 'spe_sticky_expiration' ) ){
                wp_schedule_event( time(), 'twicedaily', 'spe_sticky_expiration' );
            }
            add_action( 'spe_sticky_expiration', array( $this, 'check_sticky_expiration' ));

            add_action('admin_footer', array( $this,'check_sticky_expiration' ) );

        }




        /**
         * This runs when the plugin is deactivated
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public static function deactivation() {
            wp_clear_scheduled_hook( 'spe_sticky_expiration' );
        }



        /**
         * This function is run by the cron schedule.
         * It gets all the stick posts, checks the sticky expiration,
         * and if the expiration date has past, it will remove the post id from the sticky post array,
         * then it will delete the sticky expiration postmeta
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public function check_sticky_expiration() {

            // Get all the sticky posts, will be array of post ID's
            $sticky_posts = get_option( 'sticky_posts' );

            foreach ( $sticky_posts as $sticky_post ):

                // Get the sticky post expiration date
                $spe_date = get_post_meta( $sticky_post, 'sticky_expiration', true );

                if ( !empty( $spe_date ) ){

                    // Get the current time and the post's expiration date and convert them to DateTime objects to compare
                    $current_time = new DateTime( current_time('Y-m-d') );
                    $expires_date = new DateTime( $spe_date );


                    // Determine if current time is greater than the expiration date
                    if ( $current_time > $expires_date ) {

                        unstick_post( $sticky_post );
                        delete_post_meta( $sticky_post, 'sticky_expiration' );

                        //DEVNOTE - debugging
                        $post_title = get_the_title( $sticky_post );
                        $link = get_the_permalink( $sticky_post );
                        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
                         $email = get_option( 'admin_email' );
                        wp_mail( $email, "Sticky Post Expired", "<p>Hi <br> The post <a href=\"$link\">$post_title</a> has expired</p>" );
                    }

                }

            endforeach;
        }

    } // end class

endif; // class_exists
