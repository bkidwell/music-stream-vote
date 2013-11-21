<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * DB: Methods for Vote database objects
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Vote {

    /**
     * Get table name for Vote objects.
     * @return string
     */
    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . PLUGIN_TABLESLUG . '_vote';
    }

    /**
     * Get latest (1) 'vote_id' for this 'track_id' and 'nick' in the last 60 minutes or NULL.
     * @param  int $track_id
     * @param  string $nick
     * @return int
     */
    public static function get_recent_id( $track_id, $nick ) {
        global $wpdb;

        return $wpdb->get_var( $wpdb->prepare(
            "
                SELECT id FROM ".Vote::table_name()."
                WHERE track_id=%d
                AND nick=%s
                AND timestampdiff(minute, time_utc, utc_timestamp()) < 60
                AND deleted=0
            ",
            $track_id, substr( $nick, 0, DB_NICK_LEN )
        ) );
    }

    /**
     * Get latest (1) Vote object for 'nick' in the last 10 minutes or NULL.
     * @param  string $nick
     * @return object
     */
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
            substr( $nick, 0, DB_NICK_LEN )
        ), ARRAY_A );
    }

    /**
     * Record a Vote.
     * @param  string $time_utc (YYYY-MM-DD HH:MM:SS)
     * @param  int $track_id
     * @param  string $stream_title
     * @param  int $value
     * @param  string $nick
     * @param  string $user_id
     * @param  boolean $is_authed
     * @return void
     */
    public static function new_vote(
        $time_utc, $track_id, $stream_title, $value, $nick, $user_id, $is_authed, $comment
    ) {
        global $wpdb;

        $values = array( 
            'time_utc' => $time_utc,
            'track_id' => $track_id,
            'stream_title' => substr( $stream_title, 0, DB_STREAM_TITLE_LEN ),
            'value' => $value,
            'nick' => substr( $nick, 0, DB_NICK_LEN ),
            'user_id' => substr( $user_id, 0, DB_USER_ID_LEN ),
            'is_authed' => $is_authed
        );
        $formats = array( '%s', '%s', '%s', '%d', '%s', '%s', '%d' );

        if ( $comment !== '' ) {
            $values['comment'] = substr( $comment, 0, DB_COMMENT_LEN );
            $formats[] = '%s';
        }

        $wpdb->insert( Vote::table_name(), $values, $formats );
    }

    /**
     * Delete a Vote.
     * @param  int $vote_id
     * @return void
     */
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

    /**
     * Get last 1000 votes by nick
     * @param  string $nick
     * @return int
     */
    public static function get_votes_by_nick( $nick, $start_date=null, $end_date=null ) {
        global $wpdb;

        if ( $start_date ) {
            $start_date = $wpdb->prepare(
                "AND v.time_utc >= %s", $start_date
            );
        }
        if ( $end_date ) {
            $end_date = $wpdb->prepare(
                "AND time_utc < DATE_ADD(STR_TO_DATE(%s, %s), INTERVAL 1 DAY)", array($end_date, '%m/%d/%Y')
            );
        }

        $sql = $wpdb->prepare(
            "
                SELECT v.time_utc, v.stream_title, t.title, t.artist, v.track_id, v.value
                FROM ".Vote::table_name()." v
                LEFT JOIN ".Track::table_name()." t ON t.id = v.track_id
                WHERE nick=%s
                AND deleted=0
                $start_date
                $end_date
                ORDER BY time_utc DESC
                LIMIT 1000
            ",
            substr( $nick, 0, DB_NICK_LEN )
        );

        return $wpdb->get_results( $sql, ARRAY_A );
    }

    /**
     * Get last 1000 votes by nick
     * @param  string $nick
     * @return int
     */
    public static function get_votes_by_track_id( $track_id ) {
        global $wpdb;

        return $wpdb->get_results( $wpdb->prepare(
            "
                SELECT time_utc, nick, value
                FROM ".Vote::table_name()."
                WHERE track_id=%d
                AND deleted=0
                ORDER BY time_utc DESC
                LIMIT 1000
            ",
            $track_id
        ), ARRAY_A );
    }

}