<?php
define( 'MUSICSTREAMVOTE_DEBUG', TRUE );

class musicstreamvote extends module {

    public $title = 'Music Stream Vote';
    public $author = 'Brendan Kidwell';
    public $version = '1.0';

    private $mod_dir = '';
    private $options = array();
    private $in_channels = array();
    private $timers = array();
    private $curl = array();
    private $now_playing = '';

    public function init() {
        $this->dbg( 'init()' );

        $this->mod_dir = dirname( __FILE__ ) . '/modules/musicstreamvote/';
        foreach ( explode( ',', 'bootstrap.conf,musicstreamvote.conf,options.json' ) as $f ) {
            if ( ! file_exists( $this->mod_dir . $f ) ) {
                die( 'Fatal error: ' . $this->mod_dir . $f . "is missing.\n" );
            }
        }

        $this->options = json_decode( file_get_contents( $this->mod_dir . 'options.json' ), TRUE );
        $this->curl['wordpress'] = FALSE;
        $this->curl['streaminfo'] = FALSE;
    }

    public function destroy() {
        $this>dbg( 'destroy()' );
        foreach ( $this->timers as $key => $value ) {
            $this->timerClass->removeTimer( $key );
        }
        foreach ( $this->curl as $key => $value ) {
            curl_close( $value );
        }
    }

    private function evt_logged_in() {
        if ( count($this->in_channels) > 1 ) { return; }

        $this->dbg( 'checking in' );
        $this->webservice( 'checkin', array() );
        $this->dbg( 'done checking in ' );

        $this->add_timer( 'evt_stream_poll', $this->options['stream_status_poll_interval_sec'] );
        $this->evt_stream_poll();
    }

    public function evt_stream_poll( ) {
        $this->dbg( 'entering evt_stream_poll()' );
        $data = $this->streaminfo( $this->options['stream_status_url'] );
        $this->dbg( 'current stream_title: ' . $data['stream_title'] );
        if ( $data['stream_title'] != $this->now_playing ) {
            $this->now_playing = $data['stream_title'];
            $this->announce( "\02Now playing:\017 " . $this->now_playing );
        }
        $this->dbg( 'exiting evt_stream_poll()' );
        return TRUE;
    }

    public function cmd_help( $line, $args ) {
        $text = 'Hello world.';
        if ( $args['query'] ) {
            $text .= ' ' . $args['query'];
        }
        $this->reply( $line, $text );
    }

    public function evt_join( $line ) {
        if ( $this->options['irc_nick'] == $line['fromNick'] ) {
            $this->dbg( 'evt_join(): ' . $line['text'] );
            $this->in_channels[$line['text']] = 1;
            $this->evt_logged_in();
        }
    }

    private function reply( $line, $text ) {
        if ( $line['to'] == $this->options['irc_nick'] ) {
            $to = $line['fromNick'];
        } else {
            $to = $line['to'];
        }
        $this->ircClass->privMsg($to, $text, $queue = 1);
    }

    private function announce( $text ) {
        foreach ( $this->in_channels as $key => $value ) {
            $this->ircClass->privMsg(
                $key, $text, $queue = 1
            );
        }
    }

    private function dbg( $text ) {
        $this->ircClass->log( "[MSV] $text" );
    }

    private function webservice( $method, $args ) {
        $args['web_service_password'] = $this->options['web_service_password'];

        $fields = array(
            'musicstreamvote_botcall' => '1',
            'method' => $method,
            'args' => json_encode( $args )
        );
        $data = http_build_query( $fields );

        if ( $this->curl['wordpress'] === FALSE ) {
            $this->curl['wordpress'] = curl_init();
        }
        $ch = &$this->curl['wordpress'];

        curl_setopt( $ch, CURLOPT_URL, $this->options['web_service_url'] );
        curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Connection: Keep-Alive',
            'Keep-Alive: 300'
        ) );
        ob_start();
        $result = curl_exec( $ch );
        $error = curl_error( $ch );
        $data = json_decode( ob_get_contents(), TRUE );
        ob_end_clean();

        if ( $result === FALSE ) {
            $data = array();
            $data['status'] = 'error';
            $data['error_message'] = $error;
        }

        if ( $data['status'] == 'error' ) {
            $this->announce( "\02Error:\017 " . $data['error_message'] );
        }
        // print_r($data);

        return $data;
    }

    private function streaminfo( $url ) {
        if ( $this->curl['streaminfo'] === FALSE ) {
            $this->curl['streaminfo'] = curl_init();
        }
        $ch = &$this->curl['streaminfo'];

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Connection: Keep-Alive',
            'Keep-Alive: 300'
        ) );
        ob_start();
        $result = curl_exec( $ch );
        $error = curl_error( $ch );
        $data = array();
        $data['xml'] = ob_get_contents();
        $data['stream_title'] = '';
        ob_end_clean();

        if ( $result === FALSE ) {
            $data = array();
            $data['status'] = 'error';
            $data['error_message'] = $error;
            $data['xml'] = '';
        } else {
            $data['status'] = 'ok';
            $data['error_message'] = '';
            $info = new SimpleXMLElement($data['xml']);
            $data['stream_title'] = (string) $info->trackList[0]->track[0]->title[0];
        }

        if ( $data['status'] == 'error' ) {
            foreach ( $this->in_channels as $key => $value ) {
                $this->ircClass->privMsg(
                    $key, "\02Error:\017 " . $data['error_message'], $queue = 1
                );
            }
        }
  
        return $data;
    }

    private function add_timer( $function_name, $interval_sec ) {
        $timer_name = 'msv_' . $function_name;
        $this->dbg( "add_timer($timer_name, $function_name, $interval_sec)" );

        $this->timerClass->addTimer(
            $timer_name, $this, $function_name, '', $interval_sec, false
        );
        $this->timers[$timer_name] = 1;
    }
}

?>