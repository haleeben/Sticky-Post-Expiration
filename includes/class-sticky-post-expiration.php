<?php

if ( !class_exists( 'Sticky_Post_Expiration' ) ){

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
         * INSTANCE
         *
         * Ensures only one instance of Sticky_Post_Expiration is loaded or can be loaded.
         *
         * @since 1.0.0
         * @static
         * @see Sticky_Post_Expiration()
         * @return Sticky_Post_Expiration - Main instance
         */
        public static function get_instance( $version, $plugin_path, $plugin_url ) {
            if (is_null(self::$instance)) {
                self::$instance = new self($version, $plugin_path, $plugin_url);
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
        private function __construct( $version, $plugin_path, $plugin_url ) {

            //Assign the class variables
            $this::$version = $version;
            $this::$plugin_path = $plugin_path;
            $this::$plugin_url = $plugin_url;

            // Include required files
            require_once( $this::$plugin_path . 'includes/class-metabox-field.php' );

            // Register the deactivation hook
            register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

            // Register the uninstall hook - delete the post_meta field
            register_uninstall_hook( __FILE__, array( __CLASS__, 'delete_plugin_options' ) );

            // Schedule the cron to check the sticky expiration
            if( !wp_next_scheduled( 'spe_sticky_expiration' ) )
                wp_schedule_event( time(), 'twicedaily', 'spe_sticky_expiration' );

            add_action( 'spe_sticky_expiration', array( $this, 'check_sticky_expiration' ));

            // Display the admin notification
            //add_action( 'admin_notices', array( $this, 'check_sticky_expiration' ) ) ;

        }


        /**
         * This runs when the plugin is deactivated
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public function deactivation() {
            //TODO get this to work
            wp_clear_scheduled_hook( 'spe_sticky_expiration' );
        }


        /**
         * Delete options table entries ONLY when plugin deactivated AND deleted
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public function delete_plugin_options() {

            delete_post_meta_by_key( 'sticky_expiration' );
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

            $sticky_posts = get_option( 'sticky_posts' );

            foreach ( $sticky_posts as $sticky_post ):

                $spe_date = get_post_meta( $sticky_post, 'sticky_expiration', true );
                $spe_date = !empty( $spe_date ) ? date_i18n( 'Y-n-d', strtotime( $spe_date ) ) : '';

                if ( !empty( $spe_date ) ):

                    // Get the current time and the post's expiration date
                    $current_time = current_time( 'timestamp' );
                    $expiration = strtotime( $spe_date, current_time( 'timestamp' ));

                    // Determine if current time is greater than the expiration date
                    if ( $current_time >= $expiration ) {

                        unstick_post( $sticky_post );
                        delete_post_meta( $sticky_post, 'sticky_expiration' );
                        $post_title = get_the_title( $sticky_post );
                        wp_mail( "haleeben@gmail.com","Sticky Post Expired", "<p>Hi \n\n The post $post_title has expired</p>" );
                    }

                endif;

            endforeach;
        }


    } // end class

} // end class_exists
