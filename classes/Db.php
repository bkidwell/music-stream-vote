<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Database structure
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Db {

    /**
     * Check schema version and upgrade if necessary
     */
    public function __construct() {
        $state = State::get_instance();
        if ( $state->db_version == '' || $state->db_version < DB_VERSION ) {
            self::update_schema();
            $state->db_version = DB_VERSION;
            $state->save();
        }
    }

    /**
     * Create/update schema in database.
     */
    public static function update_schema() {
        global $wpdb;

        $sql = "

        CREATE TABLE ".Track::table_name()." (
            id int(11) NOT NULL AUTO_INCREMENT,
            stream_title varchar(200) NOT NULL,
            track_key varchar(200) NOT NULL,
            artist varchar(100) NOT NULL,
            title varchar(100) NOT NULL,
            play_count int(11) NOT NULL,
            vote_count int(11) NOT NULL,
            vote_total int(11) DEFAULT NULL,
            vote_average double DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY (track_key),
            KEY (vote_average),
            KEY (play_count),
            KEY (vote_count),
            KEY (vote_total)
        );

        CREATE TABLE ".Play::table_name()." (
            id int(11) NOT NULL AUTO_INCREMENT,
            time_utc datetime NOT NULL,
            track_id int(11) NOT NULL,
            stream_title varchar(200) NOT NULL,
            PRIMARY KEY (id),
            KEY (track_id),
            KEY (time_utc),
            CONSTRAINT ".Play::table_name()."_ibfk_1 FOREIGN KEY (track_id) REFERENCES ".Track::table_name()." (id)
        );

        CREATE TABLE ".Vote::table_name()." (
            id int(11) NOT NULL AUTO_INCREMENT,
            time_utc datetime NOT NULL,
            track_id int(11) NOT NULL,
            stream_title varchar(200) NOT NULL,
            value tinyint(4) NOT NULL,
            nick varchar(30) NOT NULL,
            user_id varchar(150) NOT NULL,
            is_authed bit(1) NOT NULL,
            deleted tinyint(4) NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            KEY (track_id),
            KEY (time_utc),
            KEY (nick),
            CONSTRAINT ".Vote::table_name()."_ibfk_1 FOREIGN KEY (track_id) REFERENCES ".Track::table_name()." (id)
        );

        ";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}