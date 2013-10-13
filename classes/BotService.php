<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class BotService {
	public static $methods = array( 'get_info' );
	private $password;

    function __construct() {
        add_action("parse_request", function( $wp) {
	    	if ( $_POST[PLUGIN_SLUG . '_botcall'] == '1' ) {
	    		Util::fix_wp_slashes();
	    		$method = $_POST['method'];
	    		$args = json_decode( $_POST['args'], TRUE );

	    		file_put_contents('/home/brendan/php.txt', $_POST );

	    		$this->password = Options::get_instance()->password;
	    		if( $this->password != $args['system_password'] ) {
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
