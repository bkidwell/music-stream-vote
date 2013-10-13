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
    	$opt_saved = FALSE;

    	if ( $_POST[PLUGIN_SLUG . '_o'] == '1' ) {
    		$opt->password = $_POST[PLUGIN_SLUG . '_password'];
    		$opt_saved = TRUE;
    	}

    	include( PLUGIN_DIR . 'views/settings.php' );
    }
}
