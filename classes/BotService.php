<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Web service to connect the bot to WordPress
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class BotService {
    /**
     * Patch the 'parse_request' action in WordPress to handle calls to this web service.
     */
    function __construct() {
        add_action("parse_request", function( $wp) {
	    	if ( $_POST[PLUGIN_SLUG . '_botcall'] == '1' ) {
	    		Util::fix_wp_slashes();
	    		$method = $_POST['method'];
	    		$args = json_decode( $_POST['args'], TRUE );

	    		if ( $args['web_service_password'] != Options::get_instance()->web_service_password ) {
	    			$this->fail( 'Invalid system_password.' );
	    		}
                if ( ! method_exists( $this, 'web_' . $method ) ) {
                    $this->fail( 'Invalid method name.' );
                }
    			$result = call_user_func( array( &$this, 'web_' . $method ), $args );

	    		echo json_encode( $result );
	    		exit;
	    	}
        });
    }

    /**
     * (Web service method) Notify WordPress that the bot is online.
     * @param  mixed[] $args an empty array
     * @return mixed[] status; error_message
     */
    private function web_checkin( $args ) {
        $state = State::get_instance();
        $state->last_checkin_utc = gmmktime();
        $state->save();

        return array(
            'status' => 'ok',
            'error_message' => ''
        );
    }

    /**
     * (Web service method) Fetch all application options.
     * @param  mixed[] $args an empty array
     * @return mixed[] status; error_message; options (key => value)
     */
    private function web_get_options ( $args ) {
        $opt = Options::get_instance();
        $result = array();
        foreach ( $opt->get_option_names() as $key ) {
            $result[$key] = $opt->__get( $key );
        }
        return array(
            'status' => 'ok',
            'error_message' => '',
            'options' => $result
        );
    }

    /**
     * (Web service method) Submit track start event
     * @param  mixed[] $args time_utc (YYYY-MM-DD); stream_title
     * @return mixed[] status; error_message; output (what to announce in IRC chat room)
     */
    private function web_track_start( $args ) {
        $time_utc = $args['time_utc'];  // YYYY-MM-DD HH:MM:SS
        $stream_title = $args['stream_title'];
        $track_id = Track::create_or_get_id( $stream_title );

        Play::new_play( $time_utc, $track_id, $stream_title );
        Track::update_play_count( $track_id );

        file_put_contents(
            PLUGIN_DIR . 'now_playing.txt',
            $stream_title
        );
        $six = Play::recent_six();
        $out = array();
        $out[] = "<p><em>Now Playing:</em> " . esc_html($stream_title) . "</p>\n";
        $out[] = "<p>Previously...<br />";
        $i = 0;
        foreach ( $six as $play ) {
            if ( $i > 0 ) {
                //$out[] = date( 'H:i:s', $play->time_utc ) . ' UTC: ' .
                $out[] = $play->time_utc . ' UTC: ' .
                esc_html( $play->stream_title ) . "<br />\n";
            }
            $i++;
        }
        $out[] = "</p>";
        file_put_contents(
            PLUGIN_DIR . 'recent_tracks.html',
            implode( $out )
        );

        return array(
            'status' => 'ok',
            'error_message' => '',
            'output' => str_ireplace(
                '${stream_title}', $args['stream_title'],
                Options::get_instance()->txt_now_playing
            )
        );
    }

    /**
     * (Web service method) Get Help string
     * @param  mixed[] $args an empty array
     * @return mixed[] status; error_message; output (what to announce in IRC chat room)
     */
    private function web_help( $args ) {
        return array(
            'status' => 'ok',
            'output' => Options::get_instance()->txt_help
        );
    }

    /**
     * (Web service method) Get one-line greeting string
     * @param  mixed[] $args an empty array
     * @return mixed[] status; error_message; output (what to announce in IRC chat room)
     */
    private function web_sayhi( $args ) {
        $options = Options::get_instance();
        $out = str_ireplace( '${nick}', $args['nick'], $options->txt_sayhi );
        $out = str_ireplace( '${cmd_help}', $options->cmd_help, $out );
        $out = str_ireplace( '${value}', $num_txt, $out );

        return array(
            'status' => 'ok',
            'output' => $out
        );
    }

    /**
     * (Web service method) Submit vote for current track
     * @param  mixed[] $args time_utc (YYYY-MM-DD); stream_title; value (-5 .. 5); nick; user_id; is_authed (0|1)
     * @return mixed[] status; error_message; output (what to announce in IRC chat room)
     */
    private function web_post_vote( $args ) {
        $time_utc = $args['time_utc']; // YYYY-MM-DD HH:MM:SS
        $stream_title = $args['stream_title'];
        $value = $args['value'];
        $nick = $args['nick'];
        $user_id = $args['user_id'];
        $is_authed = $args['is_authed'];

        $opt = Options::get_instance();

        if ( is_numeric($value) ) {
            $num = (int) $value;
            if ( $num < -5 || $num > 5 ) {
                $num = FALSE;
            }
        } else {
            $num = FALSE;
        }
        if ( $num === FALSE ) {
            $this->fail(
                $nick . ': Invalid vote value. Say "' .
                $opt->cmd_help . '" for help.'
            );
        }

        $track_id = Track::create_or_get_id( $stream_title );
        if ( ! Track::is_recently_played( $track_id ) ) {
            $this->fail( $nick . ': "' . $stream_title . '" wasn\'t played recently!' );
        }
        $vote_id = Vote::get_recent_id( $track_id, $nick );

        if ( $vote_id ) {
            Vote::delete( $vote_id );
            $txt_vote_response = $opt->txt_revote_response;
        } else {
            $txt_vote_response = $opt->txt_vote_response;
        }
        Vote::new_vote(
            $time_utc, $track_id, $stream_title, $num, $nick, $user_id, $is_authed
        );

        if ( $num > 0 ) {
            $num_txt = '+' . $num;
        } else {
            $num_txt = (string) $num;
        }

        $out = str_ireplace( '${stream_title}', $args['stream_title'], $txt_vote_response );
        $out = str_ireplace( '${nick}', $nick, $out );
        $out = str_ireplace( '${value}', $num_txt, $out );

        Track::update_vote( $track_id );

        return array(
            'status' => 'ok',
            'error_message' => '',
            'output' => $out
        );
    }

    /**
     * (Web service method) Undo vote for current track (for this nick)
     * @param  mixed[] $args nick
     * @return mixed[] status; error_message; output (what to announce in IRC chat room)
     */
    private function web_undo_vote( $args ) {
        $nick = $args['nick'];
        $vote = Vote::get_undoable_vote( $nick );

        if ( $vote == NULL ) {
            $this->fail( $nick . ': Can\'t delete last vote if it is over 10 minutes old.' );
        }
        if ( $vote->deleted == 1 ) {
            $this->fail( $nick . ': Your most recent vote has already been deleted.' );
        }

        Vote::delete( $vote->id );
        Track::update_vote( $vote->track_id );

        $out = str_ireplace( '${stream_title}', $vote->stream_title, Options::get_instance()->txt_unvote_response );
        $out = str_ireplace( '${nick}', $nick, $out );
        $out = str_ireplace( '${value}', $num_txt, $out );

        return array(
            'status' => 'ok',
            'error_message' => '',
            'output' => $out
        );
    }

    /**
     * (Web service method) Get current top 10 by vote sum
     * @param  mixed[] $args an empty array
     * @return mixed[] status; error_message; output (what to announce in IRC chat room)
     */
    private function web_stats( $args ) {
        $results = Track::top_ten_by_vote();

        $n = 1;
        $out = array();
        $out[] = "Top 10 tracks by vote average:   ";
        foreach ( $results as $result ) {
            $out[] = "<b>#$n</b> $result->stream_title (score: $result->vote_total)   ";
            if ( $n % 3 == 1 ) { $out[] = "\n"; }
            $n++;
        }

        return array(
            'status' => 'ok',
            'error_message' => '',
            'output' => trim( implode( '', $out ) )
        );
    }

    /**
     * Return a failure result from a web service call
     * @param  string $message
     * @return void
     */
    private function fail( $message ) {
    	$result = array(
    		'status' => 'error',
    		'error_message' => $message
    	);
    	echo json_encode( $result );
    	exit;
    }

/*

update play counts:

update wp_musvote_track t
left join (
  select track_id, count(*) total from wp_musvote_play group by track_id
) p on p.track_id = t.id
set t.play_count = p.total

update votes:

update wp_musvote_track t
left join (
  select track_id, count(id) vote_count, sum(value) vote_total, avg(value) vote_average from wp_musvote_vote where deleted = 0 group by track_id
) v on v.track_id = t.id
set t.vote_count = v.vote_count,
t.vote_total = v.vote_total,
t.vote_average = v.vote_average

*/

}
