<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Shortcodes for displaying data in the web site front-end
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class ShortCodes {
    /**
     * Register shortcodes and their helper functions.
     */
    public function __construct() {
        add_shortcode( 'now_playing', array( &$this, 'now_playing' ) );
        add_shortcode( 'recent_tracks', array( &$this, 'recent_tracks' ) );
        add_shortcode( 'last_day', array( &$this, 'last_day' ) );
        add_shortcode( 'top_hundred', array( &$this, 'top_hundred' ) );
        add_shortcode( 'music_query', array( &$this, 'music_query' ) );
        add_shortcode( 'music_query_results', array( &$this, 'music_query_results' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'add_js' ) );
        add_action( 'wp_head', array( &$this, 'js_vars' ) );

        $this->q_fields = ['music_query', 'start_date', 'end_date', 'nick', 'artist', 'title', 'action'];
        if ( $_GET['music_query'] == '1' ) {
            Util::fix_wp_slashes();
            $this->start_date = $_GET['start_date'];
            $this->end_date = $_GET['end_date'];
            $this->nick = $_GET['nick'];
            $this->artist = $_GET['artist'];
            $this->title = $_GET['title'];
            $this->action = $_GET['action'];
            $this->track_id = $_GET['track_id'];
        }
    }

    /**
     * Add MUSIC_STREAM_VOTE_URL JavaScript varaible to the page header.
     * @return void
     */
    public function js_vars() {
        echo
        '<script type="text/javascript">' .
        'MUSIC_STREAM_VOTE_URL=\'' . PLUGIN_URL .
        "';</script>\n";
    }

    /**
     * Add link to js/nowplaying.js to the page header.
     */
    public function add_js() {
        wp_enqueue_script(
            'nowplaying',
            PLUGIN_URL . 'js/nowplaying.js',
            array( 'jquery' )
        );
    }

    /**
     * Expand 'now_playing' shortcode.
     * @param  string[] $atts
     * @return string Expanded result
     */
    public function now_playing( $atts ) {
        if ( file_exists( PLUGIN_DIR . 'now_playing.txt' ) ) {
            return
                '<span class="now-playing">' .
                esc_html( file_get_contents( PLUGIN_DIR . 'now_playing.txt' ) ) .
                '</span>';
        }
        return '';
    }

    /**
     * Expand 'recent_tracks' shortcode.
     * @param  string[] $atts
     * @return string
     */
    public function recent_tracks( $atts ) {
        if ( file_exists( PLUGIN_DIR . 'recent_tracks.html') ) {
            return
                '<div class="recent-tracks">' .
                file_get_contents( PLUGIN_DIR . 'recent_tracks.html' ) .
                '</div>';
        }
        return '';
    }

    /**
     * List last 24 hours
     * @param  string[] $atts
     * @return string
     */
    public function last_day( $atts ) {
        $day = Play::last_day();
        $out = array();
        $out[] = "<p>";
        foreach ( $day as $play ) {
            //$out[] = date( 'H:i:s', $play->time_utc ) . ' UTC: ' .
            $out[] = $play->time_utc . ' UTC: ' .
            esc_html( $play->stream_title ) . "<br />\n";
        }
        $out[] = "</p>\n";
        return implode( $out );
    }

    /**
     * Top 100 tracks by vote
     * @param  string[] $atts
     * @return string
     */
    public function top_hundred( $atts ) {
        $top = Track::top_hundred_by_vote();
        $out = array();
        $out[] = "<p>";
        $n = 1;
        $out = array();
        foreach ( $top as $result ) {
            $out[] = "<b>#$n</b> " . esc_html($result->stream_title) . " (score: $result->vote_total)<br />\n";
            $n++;
        }
        $out[] = "</p>\n";
        return implode( $out );
    }

    /**
     * Open-ended database query
     * @param  string[] $atts
     * @return string
     */
    public function music_query( $atts ) {
        if ( ! $this->track_id ) {
            ob_start();
            include( PLUGIN_DIR . 'views/query.php' );
            return ob_get_clean();
        }
        return '';
    }

    /**
     * Open-ended database query results
     * @param  string[] $atts
     * @return string
     */
    public function music_query_results( $atts ) {
        $out = array();
        $out[] = parse_url( $_SERVER['REQUEST_URI'] )['path'] . "?";
        $count = 0;
        foreach ( $_GET as $key => $value ) { if ( strlen( $value ) ) {
            if ( $count > 0 ) { $out[] = "&"; }
            $out[] = $key . "=" . urlencode( $value );
            $count++;
        } }
        $return_url = implode( $out );

        if ( $this->action == 'Votes By Nick' ) {
            $rows = Vote::get_votes_by_nick ( $this->nick );
            $cols = array(
                'time_utc' => 'Time (UTC)',
                'track_link' => 'Track',
                'value' => 'Vote Value'
            );
            $results = array();
            $parms = '&action=Search+Tracks';
            if ( $this->start_date ) {
                $parms .= '&start_date=' . urlencode( $this->start_date );
            }
            if ( $this->end_date ) {
                $parms .= '&end_date=' . urlencode( $this->end_date );
            }
            foreach ( $rows as $result ) {
                $result['track_link'] =
                    '<a href="' .
                    $this->query_url(
                        $parms . '&artist=' . $result['artist'] . '&title=' . $result['title']
                    ) .
                    '">' . esc_html( $result['stream_title'] ) . '</a>';
                $results[] = $result;
            }
        }
        if ( $this->action == 'Search Tracks' ) {
            $track_id = Track::get_id ( "$this->artist - $this->title" );
            if ( $track_id ) {
                $rows = Vote::get_votes_by_track_id ( $track_id );
                $cols = array(
                    'time_utc' => 'Time (UTC)',
                    'nick_link' => 'Nick',
                    'value' => 'Vote Value'
                );
                $results = array();
                $parms = '&action=Votes+By+Nick';
                if ( $this->start_date ) {
                    $parms .= '&start_date=' . urlencode( $this->start_date );
                }
                if ( $this->end_date ) {
                    $parms .= '&end_date=' . urlencode( $this->end_date );
                }
                foreach ( $rows as $result ) {
                    $result['track_link'] =
                        '<a href="' .
                        $this->query_url(
                            $parms . '&nick=' . $result['nick']
                        ) .
                        '">' . esc_html( $result['nick'] ) . '</a>';
                    $results[] = $result;
                }
            }
        }

        ob_start();
        include( PLUGIN_DIR . 'views/query_results.php' );
        return ob_get_clean();
    }

    public function query_url( $parms ) {
        $out = array();
        $out[] = parse_url( $_SERVER['REQUEST_URI'] )['path'] . "?music_query=1";
        foreach ( $_GET as $key => $value ) {
            if ( array_search( $key, $this->q_fields ) === FALSE ) {
                $out[] = '&' . $key . '=' . urlencode( $value );
            }
        }
        if ( $parms ) { $out[] = '&' . $parms; }
        return implode( $out );
    }

}
