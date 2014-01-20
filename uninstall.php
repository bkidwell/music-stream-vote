<?php
namespace GlumpNet\WordPress\MusicStreamVote;

require_once( dirname( __FILE__ ) . '/defines.php' );

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

/*
 * Delete opptions
 */
if ( !is_multisite() ) {
    // For Single site

    delete_option( PLUGIN_SLUG . '_options' );
    delete_option( PLUGIN_SLUG . '_state' );
} else {
    // For Multisite

    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );
        delete_site_option( PLUGIN_SLUG . '_options' );
        delete_site_option( PLUGIN_SLUG . '_state' );
    }
    switch_to_blog( $original_blog_id );
}

/*
 * Drop tables
 */
global $wpdb;
$p = $wpdb->prefix . PLUGIN_TABLESLUG;
$wpdb->query( "DROP TABLE ${p}_play;" );
$wpdb->query( "DROP TABLE ${p}_vote;" );
$wpdb->query( "DROP TABLE ${p}_track;" );
