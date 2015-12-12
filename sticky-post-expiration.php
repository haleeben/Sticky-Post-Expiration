<?php

/* Plugin Name:   Sticky Post Expiration
 * Plugin URI:   http://ebenhale.com/sticky-post-expiration
 * Version:      1.0.0
 * Description:  This WordPress plugin allows the user to set a sticky post expiration date
 * Author:       Eben Hale
 * Author URI:   http://ebenhale.com/
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


require_once 'includes/class-sticky-post-expiration.php';



Sticky_Post_Expiration::get_instance( '1.0.0' , plugin_dir_path(__file__) , plugin_dir_url( __FILE__ ) );

