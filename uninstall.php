<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

if ( !is_multisite() ) {

    delete_post_meta_by_key( 'sticky_expiration' );

} else {

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    if( is_wp_error( $blog_ids ) || !is_array( $blog_ids )) wp_die('There was an error removing the postmeta from the multisite network');

    foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );

        delete_post_meta_by_key( 'sticky_expiration' );

        restore_current_blog();
    }

}


