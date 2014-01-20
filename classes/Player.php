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

/*

-- How voting might work --

Login table:
* GUID
* nick
* last checkin UTC
* login token (two diceware words)
* login token expires UTC

Login:
1. On player startup, player gets a GUID or reads GUID from cookie
2. Player checks in with back-end as GUID.
    a. Back-end deletes GUIDs that haven't checked in in 30 days.
3. User clicks "login".
    a. Back-end sets new login token.
    b. Player displays login token and requests user logins in via IRC.
    c. Player polls back-end to see if login has succeeded.
    d. User PMs the login token to the bot.
    e. Back-end updates login info with nick,
    f. Player sees login.
4. User clicks "logout".
    a. Back-end erases nick.

*/