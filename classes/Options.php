<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Singleton class to read and write options from memory to WordPress Options and a bootstrap file for the bot
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Options {
    /**
     * Singleton instance
     * @var Options
     */
    private static $instance;

    /**
     * Current Options values
     * @var string[]
     */
    private $opt = NULL;
    /**
     * Options at the point of the last load()
     * @var  string[]
     */
    private $opt_before = NULL;
    /**
     * List of option names from OptionDefs
     * @var string[]
     */
    private $option_names = NULL;
    /**
     * Key-Value list of defaults for each Option
     * @var string[]
     */
    private $defaults = NULL;
    /**
     * Bot needs to be restarted after last save().
     * @var boolean
     */
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

    /**
     * Class property getter
     * @param  string $property Valid option name
     * @return mixed
     */
    public function __get( $property ) {
        if ( !in_array( $property, $this->option_names ) ) {
            return FALSE;
        }
        $this->load();
        return $this->opt[$property];
    }

    /**
     * Class property setter
     * @param string $property Valid option name
     * @param mixed $value
     */
    public function __set( $property, $value ) {
        if ( !in_array( $property, $this->option_names ) ) {
            return;
        }
        $this->load();
        $this->opt[$property] = $value;
    }

    /**
     * Load Options from WordPress.
     * @return void
     */
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

    /**
     * Save Options to WordPress and 'modules/musicstream/bootstrap.conf.php'.
     * @return void
     */
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

    /**
     * Get option_names
     * @return string[]
     */
    public function get_option_names() {
        return $this->option_names;
    }

    /**
     * Get Key-Value list of defaults
     * @return string[]
     */
    public function get_defaults() {
        $this->defaults['web_service_url'] = get_site_url();
        if ( substr( $this->defaults['web_service_url'], -1) != '/' ) {
            $this->defaults['web_service_url'] = $this->defaults['web_service_url'] . '/';
        }
        $this->defaults['web_service_password'] = $this->random_password();
        return $this->defaults;
    }

    /**
     * Generate random default API password (alphanumeric, 16 characters)
     * @return string
     */
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

    /**
     * Get singleton instance
     * @return Options
     */
    public static function get_instance() {
        if ( !self::$instance ) { self::$instance = new Options(); }
        return self::$instance;
    }
}