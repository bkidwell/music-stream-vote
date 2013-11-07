<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Vote {

    public static function table_name() {
        return $wpdb->prefix . PLUGIN_TABLESLUG . '_vote';
    }

    public static function get_recent_id( $track_id, $nick ) {
        global $wpdb;

        return $wpdb->get_var( $wpdb->prepare(
            "
                SELECT id FROM $table_name
                WHERE track_id=%d
                AND nick=%s
                AND timestampdiff(minute, time_utc, utc_timestamp()) < 60
                AND deleted=0
            ",
            $track_id, $nick
        ) );
    }

    public static function get_undoable_vote( $nick ) {
        global $wpdb;

        return $wpdb->get_row( $wpdb->prepare(
            "
                SELECT id, track_id, stream_title, deleted FROM ".Vote::table_name()."
                WHERE nick=%s
                AND timestampdiff(minute, time_utc, utc_timestamp()) < 10
                ORDER BY time_utc DESC
                LIMIT 1
            ",
            $nick
        ) );
    }


    public static function new_vote(
        $time_utc, $track_id, $stream_title, $value, $nick, $user_id, $is_authed
    ) {
        global $wpdb;

        $wpdb->insert(
            Vote::table_name(),
            array( 
                'time_utc' => $time_utc,
                'track_id' => $track_id,
                'stream_title' => $stream_title,
                'value' => $num,
                'nick' => $nick,
                'user_id' => $user_id,
                'is_authed' => $is_authed
            ),
            array( '%s', '%s', '%s', '%d', '%s', '%s', '%d' )
        );
    }

    public static function delete( $vote_id ) {
        global $wpdb;

        $wpdb->update(
            Vote::table_name(),
            array( 
                'deleted' => 1
            ),
            array(
                'id' => $vote_id
            ),
            array( '%d' ),
            array( '%d' )
        );
    }

}