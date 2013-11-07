<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * DB: Methods for Play database objects
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Play {

    /**
     * Get table name for Play objects.
     * @return string
     */
    public static function table_name() {
        return $wpdb->prefix . PLUGIN_TABLESLUG . '_play';
    }

    /**
     * Record new Play object.
     * @param  string $time_utc (YYYY-MM-DD HH:MM:SS)
     * @param  int $track_id
     * @param  string $stream_title
     * @return void
     */
    public static function new_play( $time_utc, $track_id, $stream_title ) {
        global $wpdb;

        // Get last track played
        $last_title = $wpdb->get_var(
            "SELECT stream_title FROM $table_name ORDER BY time_utc DESC LIMIT 1"
        );

        // Only record a new play if stream_title has changed
        if ( $last_title != $stream_title ) {
            $wpdb->insert(
                Play::table_name(),
                array( 
                    'time_utc' => $time_utc,
                    'track_id' => $track_id,
                    'stream_title' => substr( $stream_title, 0, DB_STREAM_TITLE_LEN )
                )
            );
        }
    }

}