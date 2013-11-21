<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * DB: Methods for Track database objects
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Track {

    /**
     * Get table name for Track objects.
     * @return string
     */
    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . PLUGIN_TABLESLUG . '_track';
    }

    /**
     * Create ID for 'stream_title' or get existing ID for this value.
     * @param  string $stream_title
     * @return int
     */
    public static function create_or_get_id( $stream_title ) {
        global $wpdb;

        $parts = explode( ' - ', $stream_title, 2 );
        if ( count( $parts ) < 2 ) { $parts[1] = ''; }
        $track_key = substr(
            self::key_cleanup( $parts[0] ) . ' - ' . self::key_cleanup( $parts[1] ),
            0, DB_STREAM_TITLE_LEN
        );

        $id = $wpdb->get_var( $wpdb->prepare(
            "
                SELECT id
                FROM " . Track::table_name() . "
                WHERE track_key = %s
            ", 
            $track_key
        ) );

        if ( $id === NULL ) {
            $wpdb->insert( 
                Track::table_name(), 
                array( 
                    'stream_title' => substr( $stream_title, 0, DB_STREAM_TITLE_LEN ) ,
                    'track_key' => $track_key,
                    'artist' => substr( $parts[0], 0, DB_ARTIST_LEN ) ,
                    'title' => substr( $parts[1], 0, DB_TITLE_LEN )
                ), 
                array( '%s', '%s', '%s', '%s')
            );
            $id = $wpdb->insert_id;
        }

        return $id;
    }

    public static function get_id( $stream_title ) {
        global $wpdb;

        $parts = explode( ' - ', $stream_title, 2 );
        if ( count( $parts ) < 2 ) { $parts[1] = ''; }
        $track_key = substr(
            self::key_cleanup( $parts[0] ) . ' - ' . self::key_cleanup( $parts[1] ),
            0, DB_STREAM_TITLE_LEN
        );

        $id = $wpdb->get_var( $wpdb->prepare(
            "
                SELECT id
                FROM " . Track::table_name() . "
                WHERE track_key = %s
            ", 
            $track_key
        ) );
        
        return $id;
    }

    public static function get( $track_id ) {
        global $wpdb;

        return $wpdb->get_row( $wpdb->prepare(
            "
                SELECT *
                FROM " . Track::table_name() . "
                WHERE id = %d
            ", 
            $track_id
        ), OBJECT );
    }

    public static function search( $artist, $title ) {
        global $wpdb;

        if ( strlen($artist) == 0 && strlen($title) == 0 ) {
            return NULL;
        }

        $cond = array();
        $cond_parm = array();
        if ( $artist ) {
            $cond[] = 'artist LIKE %s';
            $cond_parm[] = '%' . $artist . '%';
        }
        if ( $title ) {
            $cont[] = 'title LIKE %s';
            $cond_parm[] = '%' . $title . '%';
        }

        $sql = $wpdb->prepare(
            "
                SELECT stream_title, artist, title, play_count, vote_count, vote_total
                FROM " . Track::table_name() . "
                WHERE " . implode( ' AND ', $cond ) . "
                ORDER BY vote_total DESC
            ",
            $cond_parm
        );

        return $wpdb->get_results( $sql, ARRAY_A );
    }

    /**
     * Update aggregate vote values for this 'track_id'.
     * @param  int $track_id
     * @return void
     */
    public static function update_vote( $track_id ) {
        global $wpdb;

        $wpdb->query( $wpdb->prepare(
            "
                UPDATE ".Track::table_name()." t
                LEFT JOIN (
                    SELECT track_id, count(id) vote_count,
                    sum(value) vote_total, avg(value) vote_average
                    FROM ".Vote::table_name()."
                    WHERE track_id=%d AND deleted=0
                ) v ON v.track_id = t.id
                SET t.vote_count = v.vote_count,
                t.vote_total = v.vote_total,
                t.vote_average = v.vote_average
                WHERE t.id=%d
            ",
            $track_id, $track_id
        ) );
    }

    /**
     * Update play count for this 'track_id'.
     * @param  int $track_id
     * @return void
     */
    public static function update_play_count( $track_id ) {
        global $wpdb;

        $wpdb->query( $wpdb->prepare(
            "
                UPDATE ".Track::table_name()."
                SET play_count=(
                  SELECT count(*)
                  FROM ".Play::table_name()."
                  WHERE track_id=%d
                )
                WHERE id=%d
            ",
            $track_id, $track_id
        ) );
    }

    /**
     * Was this 'track_id' played in the last 60 minutes?
     * @param  int  $track_id
     * @return boolean
     */
    public static function is_recently_played( $track_id ) {
        global $wpdb;

        $count = $wpdb->get_var( $wpdb->prepare(
            "
                SELECT count(*) FROM ".Play::table_name()."
                WHERE track_id=%d
                AND timestampdiff(minute, time_utc, utc_timestamp()) < 60
            ",
            $track_id
        ) );
        return $count > 0;
    }

    /**
     * Get top n stats for IRC
     * @return object[]
     */
    public static function irc_stats( $limit ) {
        global $wpdb;

        return $wpdb->get_results(
            "
                SELECT stream_title, vote_total, artist, title
                FROM ".Track::table_name()."
                WHERE vote_total IS NOT NULL
                ORDER BY vote_total DESC LIMIT $limit
            "
        );
    }

    /**
     * Get top 100 tracks by vote.
     * @return object[]
     */
    public static function top_hundred_by_vote( $start_time = NULL, $end_time = NULL) {
        global $wpdb;

        if ( (! $start_time) && (! $end_time) ) {
            return $wpdb->get_results(
                "
                    SELECT stream_title, artist, title, vote_total
                    FROM ".Track::table_name()."
                    WHERE vote_total IS NOT NULL
                    ORDER BY vote_total DESC LIMIT 100
                "
                , ARRAY_A
            );
        }

        $cond = array( 'v.deleted = 0' );
        $cond_parm = array();
        if ( $start_time ) {
            $cond[] = 'v.time_utc >= %s';
            $cond_parm[] = $start_time;
        }
        if ( $end_time ) {
            $cond[] = 'v.time_utc < %s';
            $cond_parm[] = $end_time;
        }

        return $wpdb->get_results( $wpdb->prepare (
            "
                SELECT t.stream_title, t.artist, t.title, SUM(v.value) vote_total
                FROM wp_musvote_track t
                LEFT JOIN wp_musvote_vote v on v.track_id=t.id
                WHERE " . implode( ' AND ', $cond ) . "
                GROUP BY t.id
                ORDER BY vote_total DESC, t.artist, t.title
            "
            , $cond_parm
        ), ARRAY_A );
    }

    /**
     * Get normalized 'track_key' for a given 'stream_title'
     * @param  string $text
     * @return string
     */
    private static function key_cleanup( $text ) {
        // remove accents
        $text = @iconv('UTF-8', 'us-ascii//TRANSLIT', $text);

        // remove extra characters
        $text = str_replace(
            array( '`','~','!','@','#','$','%','^','&','*','(',')','-','=','_','+',
            '[',']','{','}',';','\'',':','"',',','.','/','\\','<','>','?','|',' ' ),
            '', $text
        );

        // collapse runs of multiple spaces
        // $text = preg_replace( '!\s+!', ' ', $text );

        return strtolower( $text );
    }
}
