<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Options {
    private static $instance;
    public static $option_descriptions = array(
        'IRC',                      # group
            'Nick',                 # title
            'irc_nick',             # key
            'Bot\'s name in chat.', # hint
            '',                     # HTML input class
        'IRC',
            'Password to authenticate with NickServ',
            'irc_nickserv_password',
            '',
            '',
        'IRC',
            'Real name',
            'irc_realname',
            'For whois queries.',
            'msv-input-wide',
        'IRC',
            'IRC server hostname',
            'irc_server',
            '',
            '',
        'IRC',
            'IRC server port',
            'irc_port',
            '',
            '',
        'IRC',
            'List of channels to join',
            'irc_channels',
            'Separated by spaces. Ex: "#music #chatter".',
            '',
        'IRC',
            'Ident string',
            'irc_ident',
            'Appears in long IRC username string after nick.',
            '',
        'WordPress Integration',
            'URL of WordPress site',
            'web_service_url', # set by WordPress plugin
            '',
            'msv-input-wide',
        'WordPress Integration',
            'WordPress vote service password',
            'web_service_password',
            'Used only by the IRC bot to talk to WordPress.',
            '',
        'Stream Info',
            'URL of stream status file',
            'stream_status_url',
            'An \'.xspf\' file',
            'msv-input-wide',
        'Stream Info',
            'Stream status polling interval (seconds)',
            'stream_status_poll_interval_sec',
            '',
            '',
        'Commands Names',
            'Help command',
            'cmd_help',
            'Example: !help',
            '',
        'Output Strings',
            'Now playing',
            'txt_now_playing',
            '',
            'msv-input-wide',
        'Miscellaneous',
            'Bot restart file polling interval',
            'restart_poll_interval_sec',
            'Only for when WordPress and the bot are on the same host.',
            '',
    );

    private $opt = NULL;
    private $option_names = NULL;

    private function __construct() {
        $this->option_names = array();
        for ( $i = 2; $i < count(self::$option_descriptions); $i += 5 ) {
            array_push( $this->option_names, self::$option_descriptions[$i] );
        }
    }

    public function __get( $property ) {
        if ( !in_array( $property, $this->option_names ) ) {
            return FALSE;
        }
        $this->load();
        return $this->opt[$property];
    }

    public function __set( $property, $value ) {
        if ( !in_array( $property, $this->option_names ) ) {
            return;
        }
        $this->load();
        $this->opt[$property] = $value;
    }

    private function load() {
    if ( $this->opt !== NULL ) { return; }
        $this->opt = array();
        $saved = unserialize( get_option( PLUGIN_SLUG . '_options' ) );
        foreach ( $this->option_names as $k ) {
            if ( is_array($saved) and array_key_exists( $k, $saved )) {
                $this->opt[$k] = $saved[$k];
            } else {
                $this->opt[$k] = '';
            }
        }
    }

    public function save() {
        update_option( PLUGIN_SLUG . '_options', serialize( $this->opt) );
        file_put_contents( BOT_DIR . 'modules/musicstreamvote/bootstrap.conf' ,
            "web_service_url = \"$this->web_service_url\"\n" .
            "web_service_password = \"$this->web_service_password\"\n"
        );
    }

    public function get_option_names() {
        return $this->option_names;
    }

    public static function get_instance() {
        if ( !self::$instance ) { self::$instance = new Options(); }
        return self::$instance;
    }
}