<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Singleton class to read and write application state from memory to WordPress Options
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class State {
    /**
     * Singleton instance
     * @var Options
     */
    private static $instance;
    /**
     * List of valid state properties
     * @var string[]
     */
    public static $state_names = array(
        'last_checkin_utc', 'db_version'
    );
    /**
     * Current State values
     * @var string[]
     */
    private $state = NULL;

    private function __construct() {
    }

    /**
     * Class property getter
     * @param  string $property Valid option name
     * @return mixed
     */
    public function __get( $property ) {
        if ( !in_array( $property, self::$state_names  ) ) {
            return FALSE;
        }
        $this->load();
        return $this->state[$property];
    }

    /**
     * Class property setter
     * @param string $property Valid option name
     * @param mixed $value
     */
    public function __set( $property, $value ) {
        if ( !in_array( $property, self::$state_names ) ) {
            return;
        }
        $this->load();
        $this->state[$property] = $value;
    }

    /**
     * Load State from WordPress.
     * @return void
     */
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

    /**
     * Save State to WordPress.
     * @return void
     */
    public function save() {
        update_option( PLUGIN_SLUG . '_state', serialize( $this->state) );
    }

    /**
     * Get state_names
     * @return string[]
     */
    public function get_state_names() {
        return self::$state_names;
    }

    /**
     * Get singleton instance
     * @return Options
     */
    public static function get_instance() {
        if ( !self::$instance ) { self::$instance = new State(); }
        return self::$instance;
    }
}