<?php
/*
Plugin Name: Music Stream Vote
Plugin URI: http://www.glump.net
Description: Collects and displays votes for the track currently playing on your IceCast music radio station. Votes are collected via a bot in your station's IRC channel. Current stats can be shown in WordPress or in IRC.
Version: 1.0
Author: Brendan Kidwell
Author URI: http://www.glump.net
License: GPL 3
*/

namespace GlumpNet\WordPress\MusicStreamVote;

define(__NAMESPACE__ . '\\PLUGIN_DIR', dirname(__FILE__) . '/');
define(__NAMESPACE__ . '\\PLUGIN_URL', plugins_url(basename(dirname(__FILE__))) . '/');

spl_autoload_register(__NAMESPACE__ . '\\autoload');
function autoload($cls) {
    $c = ltrim($cls, '\\'); $l = strlen(__NAMESPACE__);
    if(strncmp($c, __NAMESPACE__, $l) !== 0) { return; }
    $c = str_replace('\\', '/', substr($c, $l)); $f = PLUGIN_DIR . 'classes' . $c . '.php';
    if(!file_exists($f)) {
        ob_clean(); echo "<br><br><pre><b>Error loading class $cls</b>\n"; debug_print_backtrace(); die();
    }
    require_once($f);
}

// new Class1();
