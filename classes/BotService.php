<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class BotService {
	public static $methods = array( 'get_info' );

    function __construct() {
        add_action("parse_request", function( $wp) {
	    	if ( $_POST[PLUGIN_SLUG . '_botcall'] == '1' ) {
	    		Util::fix_wp_slashes();
	    		$method = $_POST['method'];
	    		$args = json_decode( $_POST['args'], TRUE );

	    		if( $args['system_password'] != Options::get_instance()->password ) {
	    			$this->fail( 'Invalid system_password.' );
	    		}
	    		if ( in_array( $method, self::$methods ) ) {
	    			$result = call_user_func( array( &$this, $method ), $args );
	    		} else {
	    			$this->fail( 'Invalid method name.' );
	    		}

	    		echo json_encode( $result );
	    		exit;
	    	}
        });
    }

    private function get_info( $args ) {
    	return array(
    		'status' => 'ok',
    		'error_message' => ''
    	);
    }

    private function track_start( $args ) {

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
