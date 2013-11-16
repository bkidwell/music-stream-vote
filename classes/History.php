<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Functions for query shortcodes
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class History {
    public $form_state;
    public $view = array();
    public static $field_names = [
        'music_query', 'nick', 'artist', 'title',
        'start_date', 'start_time', 'end_date', 'end_time'
    ];

    /**
     * Register shortcodes and their helper functions.
     */
    public function __construct() {
        $this->form_state = 'whichtype';

        if ( get_query_var( 'music_query' ) ) {
            // display search form
            Util::fix_wp_slashes();

            $this->form_state = get_query_var( 'music_query' );
            foreach ( self::$field_names as $f ) {
                $this->view[$f] = get_query_var( $f );
            }
        }
    }

    public function action_url() {
        return parse_url( $_SERVER['REQUEST_URI'] )['path'];
    }

    public function link_url( $parms ) {
        $p = parse_url( $_SERVER['REQUEST_URI'] );

        $out_parms = array();
        if ( $p['query'] ) {
            parse_str( $p['query'], $tmp );
            foreach ( $tmp as $key => $value ) {
                if ( ! in_array( $key, self::$field_names ) ) {
                    $out_parms[$key] = $value;
                }
            }
        }
        if ( $parms ) {
            foreach ( $parms as $key => $value ) {
                $out_parms[$key] = $value;
            }
        }

        if ( count( $out_parms ) ) {
            return $p['path'] . '?' . http_build_query( $out_parms );
        }
        return $p['path'];
    }

    public function wp_view_state() {
        foreach ( $_GET as $key => $value ) {
            if ( array_search ( $key, self::$field_names ) === FALSE ) {
                echo '<input type="hidden" name="' . esc_attr( $key ) .
                '" value="' . esc_attr( $value) . '" />' . "\n";
            }
        }
    }

    public function render_form() {
        wp_enqueue_script("jquery");
        wp_enqueue_script("jquery-ui-datepicker");
        wp_enqueue_style(
            'musicstreamvote-jquery-ui-css',
            '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css',
            false, PLUGIN_VERSION, false
        );
        wp_enqueue_script( PLUGIN_SLUG . '_js', PLUGIN_URL . 'js/query.js' );
        wp_enqueue_style(
            'musicstreamvote-query-css', PLUGIN_URL . 'css/query.css'
        );
        $v = $this->view;

        if ( $this->form_state == 'whichtype' ) {
            return
            '<p>Query type:&nbsp; ' .
            '<a href="' . $this->link_url( array( 'music_query' => 'track' ) ) . '">Track</a>&nbsp; ' .
            '<a href="' . $this->link_url( array( 'music_query' => 'nick' ) ) . '">Votes by Nick</a>&nbsp; ' .
            '<a href="' . $this->link_url( array( 'music_query' => 'top_hundred' ) ) . '">Top Hundred</a>&nbsp; ' .
            '<a href="' . $this->link_url( array( 'music_query' => 'playlist' ) ) . '">Playlist</a>&nbsp; ' .
            '</p>';
        } elseif ( in_array( $this->form_state, ['track', 'top_hundred', 'nick', 'playlist'] ) ) {
            ob_start();
            include( PLUGIN_DIR . 'views/query_' . $this->form_state . '.php' );
            return ob_get_clean();
        }
    }

    public function render_results() {
        if ( $this->form_state == 'which_type' ) { return ''; }

        wp_enqueue_script(
            PLUGIN_SLUG . '-tablesorter', PLUGIN_URL . 'lib/jquery.tablesorter/jquery.tablesorter.min.js'
        );
        wp_enqueue_style(
            'musicstreamvote-tablesorter-css', PLUGIN_URL . 'lib/jquery.tablesorter/style.css'
        );

        $v = $this->view;
        if ( $v['artist'] || $v['title'] ) {
            return $this->q_track();
        } elseif ( $v['nick'] ) {
            return $this->q_nick();
        } elseif ( $v['start_date'] . $v['start_time'] . $v['end_date'] . $v['end_time'] ) {
            if ( $this->form_state == 'top_hundred' ) {
                return $this->q_top_hundred();
            } elseif ( $this->form_state == 'playlist' ) {
            	return $this->q_playlist();
            }
        }
    }

    private function q_track() {
        $v = $this->view;
        $track_id = Track::get_id( $v['artist'] . ' -  ' . $v['title'] );
        if ( $track_id ) {
            $results = Vote::get_votes_by_track_id( $track_id );
            $votes = array();
            foreach ( $results as $result ) {
                $result['nick_link'] =
                    '<a href="' . $this->link_url( array(
                        'music_query' => 'nick',
                        'nick' => $result['nick']
                    ) ) . '">' . esc_html( $result['nick'] ) . '</a>';
                $votes[] = $result;
            }
            $cols = array(
                'time_utc' => 'Time (UTC)',
                'nick_link' => 'Nick',
                'value' => 'Vote Value'
            );
            $t = Track::get( $track_id );
            //$stream_title = (Track::get( $track_id ))->stream_title;
            $stream_title = $t->stream_title;
            ob_start();
            include( PLUGIN_DIR . 'views/results_votes_for_track.php' );
            return ob_get_clean();
        } else {
            $results = Track::search( $v['artist'], $v['title'] );
            $tracks = array();
            foreach ( $results as $result ) {
                $result['artist_link'] =
                    '<a href="' . $this->link_url( array(
                        'music_query' => 'track',
                        'artist' => $result['artist']
                    ) ) . '">' . esc_html( $result['artist'] ) . '</a>';
                $result['title_link'] =
                    '<a href="' . $this->link_url( array(
                        'music_query' => 'track',
                        'artist' => $result['artist'],
                        'title' => $result['title']
                    ) ) . '">' . esc_html( $result['title'] ) . '</a>';
                $tracks[] = $result;
            }
            $cols = array(
                'artist_link' => 'Artist',
                'title_link' => 'Title',
                'play_count' => 'Play Count',
                'vote_count' => 'Vote Count',
                'vote_total' => 'Vote Total'
            );
            $title = array();
            if ( $v['artist'] ) {
                $title[] = 'Artist "' . $v['artist'] . '"';
            }
            if ( $v['title'] ) {
                $title[] = 'Title "' . $v['title'] . '"';
            }
            $title = implode( ', ', $title );
            ob_start();
            include( PLUGIN_DIR . 'views/results_track_search.php' );
            return ob_get_clean();
        }
    }

    private function q_nick() {
        $v = $this->view;
        $nick = $v['nick'];
        $results = Vote::get_votes_by_nick( $nick );
        $votes = array();
        foreach ( $results as $result ) {
            $result['artist_link'] =
                '<a href="' . $this->link_url( array(
                    'music_query' => 'track',
                    'artist' => $result['artist']
                ) ) . '">' . esc_html( $result['artist'] ) . '</a>';
            $result['title_link'] =
                '<a href="' . $this->link_url( array(
                    'music_query' => 'track',
                    'artist' => $result['artist'],
                    'title' => $result['title']
                ) ) . '">' . esc_html( $result['title'] ) . '</a>';
            $votes[] = $result;
        }
        $cols = array(
            'time_utc' => 'Time (UTC)',
            'artist_link' => 'Artist',
            'title_link' => 'Title',
            'value' => 'Vote Value'
        );
        ob_start();
        include( PLUGIN_DIR . 'views/results_votes_for_nick.php' );
        return ob_get_clean();
    }

    private function q_top_hundred() {
        $v = $this->view;
        $start_time = trim( $v['start_date'] . ' ' . $v['start_time'] );
        $end_time = trim( $v['end_date'] . ' ' . $v['end_time'] );
        $title = trim( $start_time . ' – ' . $end_time );
        $results = Track::top_hundred_by_vote( $start_time, $end_time );
        $votes = array();
        foreach ( $results as $result ) {
            $result['artist_link'] =
                '<a href="' . $this->link_url( array(
                    'music_query' => 'track',
                    'artist' => $result['artist']
                ) ) . '">' . esc_html( $result['artist'] ) . '</a>';
            $result['title_link'] =
                '<a href="' . $this->link_url( array(
                    'music_query' => 'track',
                    'artist' => $result['artist'],
                    'title' => $result['title']
                ) ) . '">' . esc_html( $result['title'] ) . '</a>';
            $votes[] = $result;
        }
        $cols = array(
            'artist_link' => 'Artist',
            'title_link' => 'Title',
            'vote_total' => 'Vote Total'
        );
        ob_start();
        include( PLUGIN_DIR . 'views/results_top_hundred.php' );
        return ob_get_clean();
    }

    private function q_playlist() {
        $v = $this->view;
        $start_time = trim( $v['start_date'] . ' ' . $v['start_time'] );
        $end_time = trim( $v['end_date'] . ' ' . $v['end_time'] );
        $title = trim( $start_time . ' – ' . $end_time );
        $results = Play::playlist( $start_time, $end_time );
        $plays = array();
        foreach ( $results as $result ) {
            $result['artist_link'] =
                '<a href="' . $this->link_url( array(
                    'music_query' => 'track',
                    'artist' => $result['artist']
                ) ) . '">' . esc_html( $result['artist'] ) . '</a>';
            $result['title_link'] =
                '<a href="' . $this->link_url( array(
                    'music_query' => 'track',
                    'artist' => $result['artist'],
                    'title' => $result['title']
                ) ) . '">' . esc_html( $result['title'] ) . '</a>';
            $plays[] = $result;
        }
        $cols = array(
        	'time_utc' => 'Time (UTC)',
            'artist_link' => 'Artist',
            'title_link' => 'Title',
            'vote_total' => 'Vote Total'
        );
        ob_start();
        include( PLUGIN_DIR . 'views/results_playlist.php' );
        return ob_get_clean();
    }

}
