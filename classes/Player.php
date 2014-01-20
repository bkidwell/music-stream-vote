<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Serve player / voter HTML single-page app
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Player {
    /**
     * Patch the 'parse_request' action in WordPress to handle calls to this web service.
     */
    function __construct() {
        add_action("parse_request", function( $wp) {
            if ( $_GET[PLUGIN_SLUG . '_player'] == '1' ) {
                $opt = Options::get_instance();
                $site_root = esc_url( home_url( '/' ) );
                include( PLUGIN_DIR . 'views/player.php' );
                exit;
            }
        });
    }

}
