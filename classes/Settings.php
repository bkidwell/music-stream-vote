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
     * Add display_settings() to admin screen.
     */
    function __construct() {
        add_action('admin_menu', function() {
        	add_options_page(
        		PLUGIN_NAME, PLUGIN_NAME, 'manage_options',
        		PLUGIN_SLUG, array( &$this, 'display_settings' )
    		);
        });
    }

    /**
     * Display Settings page.
     * @return void
     */
    function display_settings() {
        if ( isset( $_GET['help'] ) ) {
            (new Help)->render();
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
