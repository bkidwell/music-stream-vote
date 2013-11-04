<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Track {

    public static function create_or_get_id( $stream_title ) {
        global $wpdb;

        $parts = explode( ' - ', $stream_title, 2 );
        if ( count( $parts ) < 2 ) { $parts[1] = ''; }
        $track_key = self::key_cleanup( $parts[0] ) . ' - ' . self::key_cleanup( $parts[1] );

        $table_name = $wpdb->prefix . PLUGIN_TABLESLUG . '_track';
        $id = $wpdb->get_var( $wpdb->prepare(
            "
                SELECT id
                FROM $table_name
                WHERE track_key = %s
            ", 
            $track_key
        ) );

        if ( $id === NULL ) {
            $wpdb->insert( 
                $table_name, 
                array( 
                    'stream_title' => $stream_title, 
                    'track_key' => $track_key,
                    'artist' => $parts[0],
                    'title' => $parts[1]
                ), 
                array( '%s', '%s', '%s', '%s')
            );
            $id = $wpdb->insert_id;
        }

        return $id;
    }

    public static function update_count( $track_id ) {
        global $wpdb;

        $t_track = $wpdb->prefix . PLUGIN_TABLESLUG . '_track';
        $t_play = $wpdb->prefix . PLUGIN_TABLESLUG . '_play';

        $wpdb->query( $wpdb->prepare(
            "
                UPDATE $t_track
                SET play_count=(
                  SELECT count(*)
                  FROM $t_play
                  WHERE track_id=%d
                )
                WHERE id=%d
            ",
            $track_id, $track_id
        ) );
    }

    public static function update_vote( $track_id ) {
        global $wpdb;

        $t_track = $wpdb->prefix . PLUGIN_TABLESLUG . '_track';
        $t_vote = $wpdb->prefix . PLUGIN_TABLESLUG . '_vote';

        $sql = $wpdb->prepare(
            "
                UPDATE $t_track t
                LEFT JOIN (
                    SELECT track_id, count(id) vote_count,
                    sum(value) vote_total, avg(value) vote_average
                    FROM $t_vote
                    WHERE track_id=%d
                ) v ON v.track_id = t.id
                SET t.vote_count = v.vote_count,
                t.vote_total = v.vote_total,
                t.vote_average = v.vote_average
                WHERE t.id=%d
            ",
            $track_id, $track_id
        );
        file_put_contents( PLUGIN_DIR . 'temp.txt', $sql );
        $wpdb->query( $sql );
    }    

    public static function is_recently_played( $track_id ) {
        global $wpdb;

        $t_play = $wpdb->prefix . PLUGIN_TABLESLUG . '_play';
        $count = $wpdb->query( $wpdb->prepare(
            "
                SELECT count(*) FROM $t_play
                WHERE track_id=%d
                AND timestampdiff(minute, time_utc, utc_timestamp()) < 60
            ",
            $track_id
        ) );
        return $count > 0;
    }

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
