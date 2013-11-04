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
        global $wpdb;

        $time_utc = $args['time_utc'];  // YYYY-MM-DD HH:MM:SS
        $stream_title = $args['stream_title'];

        $track_id = Track::create_or_get_id( $stream_title );
        $table_name = $wpdb->prefix . PLUGIN_TABLESLUG . '_play';

        $wpdb->insert(
            $table_name,
            array( 
                'time_utc' => $time_utc,
                'track_id' => $track_id,
                'stream_title' => $stream_title
            )
        );
        //TODO: error check

        return array(
            'status' => 'ok',
            'error_message' => ''
        );
    }

    private function web_post_vote( $args ) {
        global $wpdb;

        $time_utc = $args['time_utc']; // YYYY-MM-DD HH:MM:SS
        $stream_title = $args['stream_title'];
        $value = $args['value'];
        $nick = $args['nick'];
        $user_id = $args['user_id'];
        $is_authed = $args['is_authed'];
        $logged_in_since_utc = $args['logged_in_since_utc'];

        //TODO: sanity check input

        $track_id = Track::create_or_get_id( $stream_title );
        $table_name = $wpdb->prefix . PLUGIN_TABLESLUG . '_vote';

        $wpdb->insert(
            $table_name,
            array( 
                'time_utc' => $time_utc,
                'track_id' => $track_id,
                'stream_title' => $stream_title,
                'value' => $value,
                'nick' => $nick,
                'user_id' => $user_id,
                'is_authed' => $is_authed,
                'logged_in_since_utc' => $logged_in_since_utc
            ),
            array( '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s')
        );
        //TODO: error check

        return array(
            'status' => 'ok',
            'error_message' => ''
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
}
