<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class ShortCodes {
    public function __construct() {
        add_shortcode( 'now_playing', array( &$this, 'now_playing' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'add_js' ) );
        add_action('wp_head', array( &$this, 'js_vars' ) );
    }

    public function js_vars() {
        echo
        '<script type="text/javascript">' .
        'MUSIC_STREAM_VOTE_URL=\'' . PLUGIN_URL .
        "';</script>\n";
    }

    public function add_js() {
        wp_enqueue_script(
            'nowplaying',
            PLUGIN_URL . 'js/nowplaying.js',
            array( 'jquery' )
        );
    }

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
