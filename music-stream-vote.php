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

require_once( dirname( __FILE__ ) . '/defines.php' );

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
