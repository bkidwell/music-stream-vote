<?php
define( 'MUSICSTREAMVOTE_DEBUG', TRUE );

/**
 * musicstreamvote module for PHP-IRC bot
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class musicstreamvote extends module {

    /**
     * Module title
     * @var string
     */
    public $title = 'Music Stream Vote';
    /**
     * Module author
     * @var string
     */
    public $author = 'Brendan Kidwell';
    /**
     * Module version
     * @var string
     */
    public $version = '1.0';

    /**
     * Absolute filesystem path to module, with trailing slash
     * @var string
     */
    private $mod_dir = '';
    /**
     * Key-Value list of application Options from WordPress
     * @var string[]
     */
    private $options = array();
    /**
     * List of channels bot is currently joined to
     * @var string[]
     */
    private $in_channels = array();
    /**
     * Key-Value list of active timers
     * @var mixed[]
     */
    private $timers = array();
    /**
     * Key-Value list of active CURL objects (for querying IceCast, WordPress)
     * @var mixed[]
     */
    private $curl = array();
    /**
     * Last 'stream_title'
     * @var string
     */
    private $now_playing = '';
    /**
     * Last IRC announcement for 'stream_title'
     * @var string
     */
    private $now_playing_response = '';
    /**
     * Votes that are waiting for WHOIS responses from IRC server
     * @var mixed[]
     */
    private $pending_votes = array();
    /**
     * Remember last error message so it isn't repeated.
     * @var string
     */
    private $last_error = '';

    /**
     * Start bot.
     *
     * Invoked by the framework.
     * 
     * @return [type]
     */
    public function init() {
        $this->dbg( 'init()' );

        $this->mod_dir = dirname( __FILE__ ) . '/modules/musicstreamvote/';
        foreach ( explode( ',', 'bootstrap.conf.php,musicstreamvote.conf,options.json.php' ) as $f ) {
            if ( ! file_exists( $this->mod_dir . $f ) ) {
                die( 'Fatal error: ' . $this->mod_dir . $f . "is missing.\n" );
            }
        }

        if ( file_exists( $this->mod_dir . 'restart' ) ) {
            unlink( $this->mod_dir . 'restart' );
        }

        $json = file_get_contents( $this->mod_dir . 'options.json.php' );
        $json = str_ireplace( "/* <" . "?php exit(); ?" . "> */", '', $json );
        $this->options = json_decode( $json, TRUE );
        if ( json_last_error() !=  JSON_ERROR_NONE ) {
            echo "Can't decode options.json.php: " . $this->json_last_error_msg();
            exit(1);
        }

        $this->curl['wordpress'] = FALSE;
        $this->curl['streaminfo'] = FALSE;
    }

    /**
     * Shut down bot.
     * @return [type]
     */
    public function destroy() {
        $this>dbg( 'destroy()' );
        foreach ( $this->timers as $key => $value ) {
            $this->timerClass->removeTimer( $key );
        }
        foreach ( $this->curl as $key => $value ) {
            curl_close( $value );
        }
    }

    /**
     * Bot is logged into a channel and ready to start doing work.
     *
     * Invoked by the framework.
     * 
     * @return void
     */
    private function evt_logged_in() {
        if ( count($this->in_channels) > 1 ) { return; }

        $this->dbg( 'checking in' );
        $this->webservice( 'checkin', array() );
        $this->dbg( 'done checking in ' );

        $this->add_timer( 'evt_stream_poll', $this->options['stream_status_poll_interval_sec'] );
        $this->add_timer( 'evt_restart_poll', $this->options['restart_poll_interval_sec'] );
        $this->evt_stream_poll();
    }

    /**
     * Poll IceCast server to get current stream_title.
     *
     * Invoked by the framework.
     * 
     * @return boolean
     */
    public function evt_stream_poll() {
        $data = $this->streaminfo( $this->options['stream_status_url'] );

        if ( $data['stream_title'] != $this->now_playing ) {
            $this->now_playing = $data['stream_title'];
            $this->now_playing_response = '';
            $this->dbg( 'Now playing: ' . $this->now_playing );
        }

        // Try to post 'now playing' to web service on each polling cycle until success
        if ( $this->now_playing_response == '' ) {
            $response = $this->webservice( 'track_start', array(
                'time_utc' => date('Y-m-d H:i:s', time() ),
                'stream_title' => $data['stream_title']
            ));
            if ( $response['output'] ) {
                $this->now_playing_response = $response['output'];
                $this->announce( $response['output'] );
            }
        }

        return TRUE;
    }

    /**
     * Poll for existence of './restart' file to see if we need to abort and reload IRC bot.
     *
     * Invoked by the framework.
     * 
     * @return boolean
     */
    public function evt_restart_poll() {
        if ( file_exists( $this->mod_dir . 'restart' ) ) {
            $this->announce ( 'is being restarted.', TRUE );
            //exit();
            $this->add_timer( 'evt_abort', 2 );
        }
        return TRUE;
    }

    /**
     * Abort IRC bot in order to restart.
     * @return void
     */
    public function evt_abort() {
        die();
    }

    /**
     * Process raw private/channel/notice messages.
     *
     * Invoked by the framework.
     *
     * Look for: 1) WHOIS responses for votes; 2) Newbie who hasn't found the Help command.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function evt_raw( $line, $args ) {
        $cmd = $line['cmd'];
        if ( $cmd == 307 || $cmd == 318 ) {
            $this->evt_whois( $line, $args );
        }
        if ( $cmd == 'PRIVMSG' ) {
            if (
                preg_match(
                    "/^[\\W]*" . $this->options['irc_nick'] . "\\W.*/i", $line['text']
                ) ||
                (
                    $line['to'] == $this->options['irc_nick'] &&
                    substr( $line['text'], 0, 1 ) != '!'
                )
            ) {
                $this->evt_sayhi( $line, $args );
            }
        }
    }

    /**
     * Process WHOIS reply.
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    private function evt_whois( $line, $args ) {
        $cmd = $line['cmd'];
        $subject = $line['params'];
        if ( array_key_exists( $subject, $this->pending_votes ) ) {
            if ( $cmd == 307 ) {
                $this->pending_votes[$subject]['is_authed'] = 1;
            } elseif ( $cmd == 318 ) {
                $this->cmd_vote_finish( $subject );
            }
        }
    }

    /**
     * Process newbie query and suggest saying '!$help_cmd'.
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    private function evt_sayhi( $line, $args ) {
        $response = $this->webservice( 'sayhi', array(
            'nick' => $line['fromNick'],
        ), $line );
        if ( $response['output'] ) {
            $this->reply( $line, $response['output'] );
        }
    }

    /**
     * Display Help from WordPress.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_help( $line, $args ) {
        $response = $this->webservice( 'help', array(), $line );
        if ( $response['output'] ) {
            $this->reply( $line, $response['output'] );
        }
    }

    /**
     * Display last Now Playing announcement.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_nowplaying( $line, $args ) {
        if ( $this->now_playing_response ) {
            $this->reply( $line, $this->now_playing_response );
        }
    }

    /**
     * Submit vote to WordPress. (Part 1.)
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_vote( $line, $args ) {
        $fromNick = $line['fromNick'];
        $vote = array(
            'line' => $line,
            'time_utc' => date('Y-m-d H:i:s', time() ),
            'stream_title' => $this->now_playing,
            'value' => $args['query'],
            'nick' => $fromNick,
            'user_id' => $line['from'],
            'is_authed' => 0
        );
        $this->pending_votes[$fromNick] = $vote;
        $this->ircClass->sendRaw( "WHOIS $fromNick" );
    }

    /**
     * Submit vote to WordPress. (Part 2.)
     *
     * Invoked by the evt_whois().
     * 
     * @param  string $subject nick who owns the pending vote
     * @return void
     */
    public function cmd_vote_finish( $subject ) {
        $vote = $this->pending_votes[$subject];
        $line = $vote['line'];
        $response = $this->webservice( 'post_vote', array(
            'time_utc' => $vote['time_utc'],
            'stream_title' => $vote['stream_title'],
            'value' => $vote['value'],
            'nick' => $vote['nick'],
            'user_id' => $vote['user_id'],
            'is_authed' => $vote['is_authed'],
        ), $line );
        if ( $response['output'] ) {
            $this->reply( $line, $response['output'] );
        }
        unset($this->pending_votes[$subject]);
    }

    /**
     * Undo last vote by this nick.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_unvote( $line, $args ) {
        $response = $this->webservice( 'undo_vote', array(
            'nick' => $line['fromNick'],
        ), $line );
        if ( $response['output'] ) {
            $this->reply( $line, $response['output'] );
        }
    }

    /**
     * Vote +3.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_like( $line, $args ) {
        $args['query'] = '+3';
        $this->cmd_vote( $line, $args );
    }

    /**
     * Vote -3.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_hate( $line, $args ) {
        $args['query'] = '-3';
        $this->cmd_vote( $line, $args );
    }

    /**
     * Vote shortcut -- allow missing space after vote command
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_shortvote( $line, $args ) {
        $value = preg_replace( '/[^\d\.\-]/', '', $args['cmd'] );
        $args['query'] = trim( $value . ' ' . $args['query'] );
        $this->cmd_vote( $line, $args );
    }

    /**
     * Get top ten tracks by vote sum from WordPress.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @param  string[] $args
     * @return void
     */
    public function cmd_stats( $line, $args ) {
        $response = $this->webservice( 'stats', array(), $line );
        if ( $response['output'] ) {
            $this->reply( $line, $response['output'] );
        }
    }

    /**
     * The bot has joined a channel.
     *
     * Invoked by the framework.
     * 
     * @param  string[] $line
     * @return void
     */
    public function evt_join( $line ) {
        if ( $this->options['irc_nick'] == $line['fromNick'] ) {
            $this->dbg( 'evt_join(): ' . $line['text'] );
            $this->in_channels[$line['text']] = 1;
            $this->evt_logged_in();
        }
    }

    /**
     * Reply to nick (PM) or channel (not PM)
     * @param  string[] $line the data received from the framework for this event
     * @param  string $text what to say
     * @return void
     */
    private function reply( $line, $text ) {
        if ( $line['to'] == $this->options['irc_nick'] ) {
            $to = $line['fromNick'];
        } else {
            $to = $line['to'];
        }
        $text = str_ireplace(
            array('<b>', '</b>'),
            array("\02", "\017"),
            $text
        );
        foreach ( explode( "\n", $text ) as $output_line ) {
            $this->ircClass->notice($to, $output_line, $queue = 1);
        }
    }

    /**
     * Announce to all channels bot is currently in.
     * @param  string  $text
     * @param  boolean $action Use "/me" form?
     * @return void
     */
    private function announce( $text, $action = FALSE ) {
        $text = str_ireplace(
            array('<b>', '</b>'),
            array("\02", "\017"),
            $text
        );
        foreach ( explode( "\n", $text ) as $output_line ) {
            foreach ( $this->in_channels as $key => $value ) {
                if ( $action ) {
                    $this->ircClass->action(
                        $key, $output_line, $queue = 1
                    );
                } else {
                    $this->ircClass->notice(
                        $key, $output_line, $queue = 1
                    );
                }
            }
        }
    }

    /**
     * Write to debug log.
     * @param  string $text
     * @return void
     */
    private function dbg( $text ) {
        $this->ircClass->log( "[MSV] $text" );
    }

    /**
     * Invoke a method in the WordPress web service.
     * @param  string $method
     * @param  mixed[] $args Key-Value list of arguments
     * @param  string[] $line the data received from the framework for this event
     * @return mixed[]
     */
    private function webservice( $method, $args, $line = NULL ) {
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
            if ( $line ) {
                $this->reply( $line, "\02Error:\017 " . $data['error_message'] );
            } else {
                if ( $data['error_message'] != $this->last_error ) {
                    $this->announce( "\02Error:\017 " . $data['error_message'] );
                    $this->last_error = $data['error_message'];
                }
            }
        }
        // print_r($data);

        return $data;
    }

    /**
     * Get current 'stream_title' from IceCast.
     * @param  string $url
     * @return string
     */
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
            $data['stream_title'] = $this->decode_bad_html_entities(
                (string) $info->trackList[0]->track[0]->title[0]
            );
        }

        if ( $data['status'] == 'error' ) {
            foreach ( $this->in_channels as $key => $value ) {
                $this->ircClass->notice(
                    $key, "\02Error:\017 " . $data['error_message'], $queue = 1
                );
            }
        }
  
        return $data;
    }

    /**
     * Create a repeating timer event.
     * @param string $function_name callback function name in this module
     * @param int $interval_sec
     */
    private function add_timer( $function_name, $interval_sec ) {
        $timer_name = 'msv_' . $function_name;
        $this->dbg( "add_timer($timer_name, $function_name, $interval_sec)" );

        $this->timerClass->addTimer(
            $timer_name, $this, $function_name, '', $interval_sec, false
        );
        $this->timers[$timer_name] = 1;
    }

    /**
     * Convert last json parse error to string
     * @return string
     */
    private static function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    }

    /**
     * Change HTML entities in string to unicode characters
     *
     * Finds HTMl entities with decimal numbers and hexidecimal numbers in
     * input string and turns them into unicode characters. Handles malformed
     * entity references where "&"" is written as "& amp ;".
     * 
     * @param  [type] $text [description]
     * @return [type]       [description]
     */
    private function decode_bad_html_entities( $text ) {
        // example input: Pajama Parties (&amp;#12497;&amp;#12472;
        // get it back to HTML entities: Pajama Parties (&#12497;&#12472;
        $text = preg_replace_callback(
            '/&amp;(#x{0,1})([\dabcdef]+);/i',
            function ( $matches) {
                print_r($matches);
                return "&$matches[1]$matches[2];";
            },
            $text
        );
        // decode HTML entities
        return html_entity_decode($text, ENT_HTML401);
    }

}

?>