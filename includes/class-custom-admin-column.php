<?php

/**
 * This code adds the sticky expiration field column to the admin post list
 */

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) die;


if ( !class_exists( 'SPE_Custom_Admin_Column') ):

    class SPE_Custom_Admin_Column {

        /**
         * @var Object
         */
        public static $instance;


        /**
         * INSTANCE
         *
         * Ensures only one instance of Sticky_Post_Expiration is loaded or can be loaded.
         *
         * @since 1.0.0
         * @return Sticky_Post_Expiration - Main instance
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }



        /**
         *
         */
        private function __construct(){

            // Add a Sticky Expires admin column
            add_filter( 'manage_posts_columns', array( $this, 'add_sticky_expiration_column' ));

            // Add the admin column content
            add_action( 'manage_posts_custom_column',  array( $this, 'sticky_expiration_column_content' ), 10, 2);

            // This is to stop the Fatal Error for the is_plugin_active()
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            /** Admin Columns Pro Code */
            if( is_plugin_active( 'admin-columns-pro/admin-columns-pro.php' ) ):
                add_filter( 'cac/editable/is_column_editable/column=sticky_expiration', '__return_true' );
                add_filter( 'cac/editable/editables_data', array( $this,'column_editable_settings' ), 10, 2 );
                add_filter( 'cac/editable/column_value/column=sticky_expiration', array( $this, 'column_value' ), 10, 4 );
                add_filter( 'cac/editable/column_save/column=sticky_expiration', array( $this, 'column_save' ), 10, 5 );
            endif;
        }



        /**
         * Add the Sticky Expires admin column
         *
         * @since 1.0.0
         * @param $columns
         * @return mixed

         */
        public function add_sticky_expiration_column( $columns ) {
            $columns['sticky_expiration'] = __( 'Sticky Expires', 'sticky_post_expiration' );
            return $columns;
        }


        /**
         * Add the Sticky Expiration date to the custom column
         *
         * @since 1.0.0
         * @param $column
         * @param $post_id
         */
        public function sticky_expiration_column_content( $column, $post_id ) {

            if( $column == 'sticky_expiration' ):

                $sticky_date = get_post_meta( $post_id, 'sticky_expiration', true );

                if( !empty ( $sticky_date ) ){

                    $formatted_date = date_i18n( 'M d, Y', strtotime( $sticky_date ) );
                    echo "<span class=\"sticky-expiration\">$formatted_date</span>";
                }


            endif;
        }


        // Set the editable properties
        public function column_editable_settings( $editable_data, $model ) {
            $editable_data['sticky_expiration']['default_column'] = true; // Do not change this
            // Set the editability type.
            $editable_data['sticky_expiration']['type'] = 'date'; // Accepts 'text', 'select', 'textarea', 'media', 'float', 'togglable', 'select' and more.
            return $editable_data;
        }


        // Retrieve the value that will be used for editing
        public function column_value( $value, $column, $id, $model ) {
            // Retrieve the value that should be used for editing
            $value = get_post_meta( $id, 'sticky_expiration', true );
            return $value;
        }


        // Store the editable column value to the database
        public function column_save( $result, $column, $id, $value, $model ) {
            // Store the value that has been entered with inline-edit
             update_post_meta( $id, 'sticky_expiration', $value );
        }

    }

endif; // class exisits

SPE_Custom_Admin_Column::instance();


