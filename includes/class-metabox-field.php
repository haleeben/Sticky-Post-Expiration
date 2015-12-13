<?php

/**
 * This code adds the sticky expiration field to the Publish metabox
 */

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Render the metabox options in the main Publish box
 *
 * @access public
 * @since 1.0
 * @return void
 */

class SPE_Metabox_Field {

    /**
     * @var object
     * @since 1.0
     */
    protected static $instance = null;


    /**
     * SPE_Metabox_Field constructor.
     *
     * @since 1.0
     */
    final function __construct() {

        // Add the expiration date field to the Publish metabox
        add_action( 'post_submitbox_misc_actions', array( $this, 'add_expiration_field' ));

        // Add the datepicker script and style to the post edit screen
        add_action( 'load-post-new.php', array( $this, 'admin_scripts' ));
        add_action( 'load-post.php', array( $this, 'admin_scripts' ));

        // Save the expiration date to the postmeta
        add_action( 'save_post',  array( $this, 'save_expiration' ));
    }


    /**
     * INSTANCE
     *
     * Ensures only one instance of Sticky_Post_Expiration is loaded or can be loaded.
     *
     * @since 1.0.0
     * @return Sticky_Post_Expiration - Main instance
     */
    public static function instance() {
        if ( is_null( self::$instance )) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Add the expiration date field to the Publish metabox
     *
     * @since 1.0.0
     * @return void
     */
    function add_expiration_field() {
        global $post;
        if( ! empty( $post->ID ) ) {
            $expires = get_post_meta( $post->ID, 'sticky_expiration', true );
        }
        $label = ! empty( $expires ) ? date_i18n( 'M d, Y', strtotime( $expires ) ) : __( 'Never', 'sticky_post_expiration' );
        $date  = ! empty( $expires ) ? date_i18n( 'M d, Y', strtotime( $expires ) ) : '';
        ?>
        <div id="spe-expiration-wrap" class="misc-pub-section hidden">
		<span>
			<span class="wp-media-buttons-icon dashicons dashicons-calendar"></span>&nbsp;
            <?php _e( 'Sticky Expires:', 'sticky_post_expiration' ); ?>
            <b id="spe-expiration-label"><?php echo $label; ?></b>
		</span>
            <a href="#" id="spe-edit-expiration" class="spe-edit-expiration hide-if-no-js">
                <span aria-hidden="true"><?php _e( 'Edit', 'sticky_post_expiration' ); ?></span>&nbsp;
                <span class="screen-reader-text"><?php _e( 'Edit date and time', 'sticky_post_expiration' ); ?></span>
            </a>
            <div id="spe-expiration-field" class="hide-if-js">
                <p>
                    <input type="text" name="spe-expiration" id="spe-expiration" value="<?php echo esc_attr( $date ); ?>" placeholder="Click to enter"/>
                </p>
                <p>
                    <a href="#" class="spe-hide-expiration button secondary"><?php _e( 'OK', 'sticky_post_expiration' ); ?></a>
                    <a href="#" class="spe-hide-expiration cancel"><?php _e( 'Cancel', 'sticky_post_expiration' ); ?></a>
                    <a href="#" class="spe-hide-expiration clear"><?php _e( 'Clear', 'sticky_post_expiration' ); ?></a>
                </p>
            </div>
            <?php wp_nonce_field( 'spe_edit_expiration', 'spe_expiration_nonce' ); ?>
        </div>
        <?php
    }


    /**
     * Save the posts's expiration date
     *
     * @since 1.0.0
     * @param $post_id
     * @return void
     */
    function save_expiration( $post_id ) {

        // Validation checks
        if( empty( $_POST['spe_expiration_nonce'] ) ) return;
        if( !wp_verify_nonce( $_POST['spe_expiration_nonce'], 'spe_edit_expiration' ) ) return;
        if( !current_user_can( 'edit_post', $post_id ) ) return;
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) return;

        $expiration = !empty( $_POST['spe-expiration'] ) ? sanitize_text_field( $_POST['spe-expiration'] ) : false;

        //Check for a valid date
        $date = date_parse( $expiration );
        ( $date['year'] || $date['month'] || $date['day'] ) ? $valid_date = true : $valid_date = false;

        if( $valid_date ) {
            update_post_meta( $post_id, 'sticky_expiration', $expiration );
        } else {
            delete_post_meta( $post_id, 'sticky_expiration' );
        }

//        DEVNOTE - debugging
//        $expiration = strtotime( $expiration, current_time( 'timestamp' ));
//        PC::debug($expiration,'$expiration');
//        PC::debug($date,'$date');

    }



    /**
     * Load the JS and CSS files
     *
     * @since 1.0.0
     * @return void
     */
    function admin_scripts() {
        wp_enqueue_style( 'jquery-ui-css', Sticky_Post_Expiration::$plugin_url . 'assets/css/datepicker.css' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'spe-expiration', Sticky_Post_Expiration::$plugin_url . 'assets/js/sticky-post-expiration.js', array( 'jquery-ui-datepicker','jquery-ui-slider' ), Sticky_Post_Expiration::$version , true );
    }


}// end class

SPE_Metabox_Field::instance();

