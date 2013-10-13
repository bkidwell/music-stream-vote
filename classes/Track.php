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
