<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Options {
    private static $instance;

    private $opt = NULL;
    private $opt_before = NULL;
    private $option_names = NULL;
    private $defaults = NULL;
    public $need_restart = FALSE;

    private function __construct() {
        $this->option_names = array();
        foreach ( OptionDefs::$option_defs as $group => $defs ) {
            foreach ( $defs as $opt_name => $attr ) {
                array_push( $this->option_names, $opt_name );
                $this->defaults[$opt_name] = $attr['d'];
            }
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
        $this->opt_before = $this->opt;
    }

    public function save() {
        foreach ( OptionDefs::$option_defs as $group => $defs ) {
            foreach ( $defs as $opt_name => $attr ) {
                if (
                    $attr['r'] &&
                    $this->opt[$opt_name] != $this->opt_before[$opt_name]
                ) {
                    $this->need_restart = TRUE;
                }
            }
        }

        update_option( PLUGIN_SLUG . '_options', serialize( $this->opt) );
        file_put_contents( BOT_DIR . 'modules/musicstreamvote/bootstrap.conf.php' ,
            "; <" . "?php exit(); ?" . ">\n" .
            "web_service_url = \"$this->web_service_url\"\n" .
            "web_service_password = \"$this->web_service_password\"\n"
        );
    }

    public function get_option_names() {
        return $this->option_names;
    }

    public function get_defaults() {
        $this->defaults['web_service_url'] = get_site_url();
        if ( substr( $this->defaults['web_service_url'], -1) != '/' ) {
            $this->defaults['web_service_url'] = $this->defaults['web_service_url'] . '/';
        }
        $this->defaults['web_service_password'] = $this->random_password();
        return $this->defaults;
    }

    private function random_password() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alphaLength = strlen( $alphabet ) - 1;
        for ($i = 0; $i < 16; $i++) {
            $n = rand( 0, $alphaLength );
            $pass[] = $alphabet[$n];
        }
        return implode( $pass );
    }

    public static function get_instance() {
        if ( !self::$instance ) { self::$instance = new Options(); }
        return self::$instance;
    }
}