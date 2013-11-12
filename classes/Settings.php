<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Music Stream Vote Settings in WordPress Admin screen
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Settings {
    /**
     * WordPress internal ID of our admin screen
     * @var string
     */
    private $screen_id;

    /**
     * Add display_settings() to admin screen.
     */
    public function __construct() {
        add_action('admin_menu', function() {
        	$this->screen_id = add_options_page(
        		PLUGIN_NAME, PLUGIN_NAME, 'manage_options',
        		PLUGIN_SLUG, array( &$this, 'display_settings' )
    		);
        });

        add_filter( 'contextual_help', array( &$this, 'help' ), 10, 3 );
        add_action( 'admin_enqueue_scripts', array( &$this, 'js' ) );
    }

    /**
     * Load JavaScript for admin screen
     * @return void
     */
    public function js() {
        if ( $_GET['page'] != PLUGIN_SLUG ) {
            return;
        }
        wp_enqueue_script( PLUGIN_SLUG . '_js', PLUGIN_URL . 'js/settings.js' );
    }

    /**
     * Display help box at the top of the Settings screen
     * @param  string $contextual_help Help text
     * @param  string $screen_id Screen ID of help being rendered now
     * @param  mixed $screen
     * @return string $contextual_help unchanged if not our screen; else help text
     */
    public function help( $contextual_help, $screen_id, $screen ) {
        if ( $screen_id != $this->screen_id ) {
            return $contextual_help;
        }

        $h = Help::get_instance();
        $pages = $h->get_contextual_pages();
        $tabs = [];
        $i = 0;
        foreach ( $pages as $page ) {
            $screen->add_help_tab( array(
                'id' => "help_tab_$i",
                'title' => $page[1],
                'content' => $h->render( 'contextual_help/' . $page[0], TRUE )
            ) );
            $i++;
        }

        $screen->set_help_sidebar( $h->render( 'contextual_help/sidebar', TRUE ) );
    }

    /**
     * Display Settings page.
     * @return void
     */
    function display_settings() {
        if ( isset( $_GET['help'] ) ) {
            Help::get_instance()->render();
            return;
        }

    	$opt = Options::get_instance();
        $state = State::get_instance();
    	$opt_saved = FALSE;
        $opt_restarted = FALSE;
        $out = array();

        $out['start_time'] = date_i18n(
            "Y-m-d H:i:s O", $state->last_checkin_utc + (get_option('gmt_offset') * 60 * 60), TRUE
        );

    	if ( $_POST[PLUGIN_SLUG . '_o'] == '1' ) {
            Util::fix_wp_slashes();
            foreach ( $opt->get_option_names() as $key ) {
                $opt->__set( $key, $_POST[PLUGIN_SLUG . '_' . $key] );
            }
            $opt->save();
            if ( $opt->need_restart ) {
                touch( BOT_DIR . 'modules/musicstreamvote/restart' );
                $opt_restarted = TRUE;
            }
            $opt_saved = TRUE;
    	}

        $defaults = $opt->get_defaults();
        $applied_defaults = FALSE;
        foreach ( $opt->get_option_names() as $key ) {
            if ( trim( $opt->__get($key) ) == '' ) {
                $opt->__set($key, $defaults[$key]);
                $applied_defaults = TRUE;
            }
        }
        if ( $applied_defaults ) { $opt->save(); }

    	include( PLUGIN_DIR . 'views/settings.php' );
    }
}
