<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Options {
    private static $instance;
    public static $option_names = array('password');

    private function __construct() {
    }

    public function __get( $property ) {
        if ( !in_array( $property, self::$option_names ) ) {
            return FALSE;
        }
        return get_option( PLUGIN_SLUG . '_' . $property );
    }

    public function __set( $property, $value ) {
        if ( !in_array( $property, self::$option_names ) ) {
            return;
        }
        update_option( PLUGIN_SLUG . '_' . $property, $value );
    }

    public static function get_instance() {
        if ( !self::$instance ) { self::$instance = new Options(); }
        return self::$instance;
    }
}