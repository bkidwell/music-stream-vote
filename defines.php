<?php
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
define( __NAMESPACE__ . '\\DB_VERSION', 3 );
