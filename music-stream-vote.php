<?php
/*
Plugin Name: Music Stream Vote
Plugin URI: http://www.glump.net
Description: Collects and displays votes for the track currently playing on your IceCast music radio station. Votes are collected via a bot in your station's IRC channel. Current stats can be shown in WordPress or in IRC.
Version: 1.1
Author: Brendan Kidwell
Author URI: http://www.glump.net
License: GPL 3
*/

namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Must be running at least this PHP version.
 */
define( __NAMESPACE__ . '\\REQUIRE_PHP_VER', '5.4.0' );
/**
 * Absolute filesystem path to Music Stream Vote plugin, with trailing slash
 */
define( __NAMESPACE__ . '\\PLUGIN_DIR', dirname( __FILE__ ) . '/' );
/**
 * Absolute URL to Music Stream Vote plugin files, with trailing slash
 */
define( __NAMESPACE__ . '\\PLUGIN_URL', plugins_url( basename( dirname( __FILE__ ) ) ) . '/' );
/**
 * Application name
 */
define( __NAMESPACE__ . '\\PLUGIN_NAME', 'Music Stream Vote' );
/**
 * Application code name
 */
define( __NAMESPACE__ . '\\PLUGIN_SLUG', 'musicstreamvote' );
/**
 * Database table prefix
 */
define( __NAMESPACE__ . '\\PLUGIN_TABLESLUG', 'musvote' );
/**
 * Absolute filesystem path to IRC bot, with trailing slash
 */
define( __NAMESPACE__ . '\\BOT_DIR', PLUGIN_DIR . 'php-irc-2.2.1/' );

/**
 * Max length in DB for stream_title
 */
define( __NAMESPACE__ . '\\DB_STREAM_TITLE_LEN', 200 );
/**
 * Max length in DB for nick
 */
define( __NAMESPACE__ . '\\DB_NICK_LEN', 30 );
/**
 * Max length in DB for user_id
 */
define( __NAMESPACE__ . '\\DB_USER_ID_LEN', 150 );
/**
 * Max length in DB for track artist
 */
define( __NAMESPACE__ . '\\DB_ARTIST_LEN', 100 );
/**
 * Max length in DB for track title
 */
define( __NAMESPACE__ . '\\DB_TITLE_LEN', 100 );
/**
 * Max length in DB for comment
 */
define( __NAMESPACE__ . '\\DB_COMMENT_LEN', 200 );
/**
 * Database schema version -- increment this every time you change Db::update_schema() .
 */
define( __NAMESPACE__ . '\\DB_VERSION', 2 );

spl_autoload_register( __NAMESPACE__ . '\\autoload' );
/**
 * Class loader for the plugin (called by PHP)
 * @param  string $cls Class name
 * @return void
 */
function autoload( $cls ) {
    $c = ltrim( $cls, '\\' ); $l = strlen( __NAMESPACE__ );
    if ( strncmp( $c, __NAMESPACE__, $l ) !== 0 ) { return; }
    $c = str_replace( '\\', '/', substr( $c, $l ) ); $f = PLUGIN_DIR . 'classes' . $c . '.php';
    if ( !file_exists( $f ) ) {
        ob_clean(); echo "<br><br><pre><b>Error loading class $cls</b>\n"; debug_print_backtrace(); die();
    }
    require_once( $f );
}

/**
 * Wrong version of PHP found.
 * @return void
 */
function php_fail() {
    ?>
    <div class="error">
        <p>The plugin <strong>Music Stream Vote</strong> is installed but
        won't work because it requires PHP version <?php echo REQUIRE_PHP_VER; ?>
        or later and you are running PHP version <?php echo phpversion(); ?>.</p>
    </div>
    <?php
}

/**
 * Plugin was activated.
 * @return void
 */
function activate() {
}
/**
 * Plugin was deactivated.
 * @return void
 */
function deactivate() {
}

if (version_compare(phpversion(), REQUIRE_PHP_VER, ">=")) {

    new Db();         // database structure
    new Settings();   // settings screen
    new BotService(); // web service for IRC bot
    new ShortCodes(); // shortcodes

    //register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );
    //register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );

} else {

    add_action( 'admin_notices', __NAMESPACE__ . '\\php_fail' );

} 
