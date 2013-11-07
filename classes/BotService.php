<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class BotService {
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

    private function web_get_info( $args ) {
    	return array(
    		'status' => 'ok',
    		'error_message' => ''
    	);
    }

    private function web_checkin( $args ) {
        $state = State::get_instance();
        $state->last_checkin_utc = gmmktime();
        $state->save();

        return array(
            'status' => 'ok',
            'error_message' => ''
        );
    }

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

    private function web_track_start( $args ) {
        $time_utc = $args['time_utc'];  // YYYY-MM-DD HH:MM:SS
        $stream_title = $args['stream_title'];
        $track_id = Track::create_or_get_id( $stream_title );

        Play::new_play( $time_utc, $track_id, $stream_title );
        Track::update_count( $track_id );

        file_put_contents(
            PLUGIN_DIR . 'now_playing.txt',
            $stream_title
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

    private function web_help( $args ) {
        return array(
            'status' => 'ok',
            'output' => Options::get_instance()->txt_help
        );
    }

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

    private function web_stats( $args ) {
        $results = Track::top_ten_by_vote();

        $n = 1;
        $out = array();
        $out[] = "Top 10 tracks by vote average:   ";
        foreach ( $results as $result ) {
            $out[] = "<b>#$n</b> $result->stream_title (avg: $result->vote_average)   ";
            if ( $n % 3 == 1 ) { $out[] = "\n"; }
            $n++;
        }

        return array(
            'status' => 'ok',
            'error_message' => '',
            'output' => trim( implode( '', $out ) )
        );
    }

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
