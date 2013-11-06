<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Settings {
    function __construct() {
        add_action('admin_menu', function() {
        	add_options_page(
        		PLUGIN_NAME, PLUGIN_NAME, 'manage_options',
        		PLUGIN_SLUG, array( &$this, 'display_settings' )
    		);
        });
    }

    function display_settings() {
    	$opt = Options::get_instance();
        $state = State::get_instance();
    	$opt_saved = FALSE;
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
            $opt_saved = TRUE;
    	}

        $defaults = $opt->get_defaults();
        foreach ( $opt->get_option_names() as $key ) {
            if ( trim( $opt->__get($key) ) == '' ) {
                $opt->__set($key, $defaults[$key]);
            }
        }

    	include( PLUGIN_DIR . 'views/settings.php' );
    }
}
