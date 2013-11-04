<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class State {
    private static $instance;
    public static $state_names = array(
        'last_checkin_utc'
    );
    private $state = NULL;

    private function __construct() {
    }

    public function __get( $property ) {
        if ( !in_array( $property, self::$state_names  ) ) {
            return FALSE;
        }
        $this->load();
        return $this->state[$property];
    }

    public function __set( $property, $value ) {
        if ( !in_array( $property, self::$state_names ) ) {
            return;
        }
        $this->load();
        $this->state[$property] = $value;
    }

    private function load() {
        if ( $this->state !== NULL ) { return; }
        $this->state = array();
        $saved = unserialize( get_option( PLUGIN_SLUG . '_state' ) );
        foreach ( self::$state_names as $k ) {
            if ( is_array($saved) and array_key_exists( $k, $saved )) {
                $this->state[$k] = $saved[$k];
            } else {
                $this->state[$k] = '';
            }
        }
    }

    public function save() {
        update_option( PLUGIN_SLUG . '_state', serialize( $this->state) );
    }

    public function get_state_names() {
        return self::$state_names;
    }

    public static function get_instance() {
        if ( !self::$instance ) { self::$instance = new State(); }
        return self::$instance;
    }
}