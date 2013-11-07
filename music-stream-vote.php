<?php
/*
Plugin Name: Music Stream Vote
Plugin URI: http://www.glump.net
Description: Collects and displays votes for the track currently playing on your IceCast music radio station. Votes are collected via a bot in your station's IRC channel. Current stats can be shown in WordPress or in IRC.
Version: 1.0
Author: Brendan Kidwell
Author URI: http://www.glump.net
License: GPL 3
*/

namespace GlumpNet\WordPress\MusicStreamVote;

define( __NAMESPACE__ . '\\REQUIRE_PHP_VER', '5.4.0' );
define( __NAMESPACE__ . '\\PLUGIN_DIR', dirname( __FILE__ ) . '/' );
define( __NAMESPACE__ . '\\PLUGIN_URL', plugins_url( basename( dirname( __FILE__ ) ) ) . '/' );
define( __NAMESPACE__ . '\\PLUGIN_NAME', 'Music Stream Vote' );
define( __NAMESPACE__ . '\\PLUGIN_SLUG', 'musicstreamvote' );
define( __NAMESPACE__ . '\\PLUGIN_TABLESLUG', 'musvote' );
define( __NAMESPACE__ . '\\BOT_DIR', PLUGIN_DIR . 'php-irc-2.2.1/' );

define( __NAMESPACE__ . '\\DB_STREAM_TITLE_LEN', 200 );
define( __NAMESPACE__ . '\\DB_NICK_LEN', 30 );
define( __NAMESPACE__ . '\\DB_USER_ID_LEN', 150 );
define( __NAMESPACE__ . '\\DB_ARTIST_LEN', 100 );
define( __NAMESPACE__ . '\\DB_TITLE_LEN', 100 );

spl_autoload_register(__NAMESPACE__ . '\\autoload');
function autoload( $cls ) {
    $c = ltrim( $cls, '\\' ); $l = strlen( __NAMESPACE__ );
    if ( strncmp( $c, __NAMESPACE__, $l ) !== 0 ) { return; }
    $c = str_replace( '\\', '/', substr( $c, $l ) ); $f = PLUGIN_DIR . 'classes' . $c . '.php';
    if ( !file_exists( $f ) ) {
        ob_clean(); echo "<br><br><pre><b>Error loading class $cls</b>\n"; debug_print_backtrace(); die();
    }
    require_once( $f );
}

function php_fail() {
    ?>
    <div class="error">
        <p>The plugin <strong>Music Stream Vote</strong> is installed but
        won't work because it requires PHP version <?php echo REQUIRE_PHP_VER; ?>
        or later and you are running PHP version <?php echo phpversion(); ?>.</p>
    </div>
    <?php
}

if (version_compare(phpversion(), REQUIRE_PHP_VER, ">=")) {

    new Settings();   // settings screen
    new BotService(); // web service for IRC bot
    new ShortCodes(); // shortcodes

    register_activation_hook( __FILE__, 'musicstreamvote_install' );
    function musicstreamvote_install() {
        VotePlugin::Installed();
    }
    register_deactivation_hook( __FILE__, 'musicstreamvote_remove' );
    function musicstreamvote_remove() {
        VotePlugin::Removed();
    }

} else {

    add_action( 'admin_notices', 'GlumpNet\\WordPress\\MusicStreamVote\\php_fail' );

} 
