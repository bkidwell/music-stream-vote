<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Play {

    public static function table_name() {
        return $wpdb->prefix . PLUGIN_TABLESLUG . '_play';
    }

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
                    'stream_title' => $stream_title
                )
            );
        }
    }

}