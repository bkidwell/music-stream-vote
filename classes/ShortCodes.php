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
        if ( file_exists(PLUGIN_DIR . 'now_playing.txt') ) {
            return
                '<span class="now-playing">' .
                esc_html( file_get_contents( PLUGIN_DIR . 'now_playing.txt' ) ) .
                '</span>';
        }
        return '';
    }
}
