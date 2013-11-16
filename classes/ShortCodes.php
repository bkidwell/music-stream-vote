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
        add_action( 'wp_enqueue_scripts', array( &$this, 'add_js' ) );
        add_action( 'wp_head', array( &$this, 'js_vars' ) );
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
            $out[] = "<b>#$n</b> " . esc_html($result['stream_title']) . " (score: " . $result['vote_total'] . ")<br />\n";
            $n++;
        }
        $out[] = "</p>\n";
        return implode( $out );
    }

    public function music_query() {
        $hist = new History();
        return $hist->render_form() . $hist->render_results();
    }

}
