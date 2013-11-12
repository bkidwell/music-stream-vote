#!/usr/bin/env php
<?php /*

This script reads the WordPress URL and web service password from bootstrap.conf
and calls the WordPress music-stream-vote plugin to get the rest of the settings
for the IRC bot. It then writes the settings to the appropriate places.

*/

define( 'MOD_DIR', dirname( __FILE__ ) . '/' );
define( 'BOT_DIR', realpath( dirname( MOD_DIR . '../../../' ) ) . '/' );
define( 'REQUIRE_PHP_VER', '5.4.0' );

if (version_compare(phpversion(), REQUIRE_PHP_VER, "<")) {
    echo(
        'Error: this app requires PHP ' . REQUIRE_PHP_VER .
        '. Found ' . phpversion() . ". Aborting.\n"
    );
    exit( 1 );
}

$conf = parse_ini_file( MOD_DIR . 'bootstrap.conf.php' );
$web_service_url = $conf['web_service_url'];
$web_service_password = $conf['web_service_password'];

function botcall( $method, $args ) {
    global $web_service_url;

    $fields = array(
        'musicstreamvote_botcall' => '1',
        'method' => $method,
        'args' => json_encode( $args )
    );
    $data = http_build_query( $fields );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $web_service_url );
    curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    ob_start();
    $result = curl_exec( $ch );
    $error = curl_error( $ch );
    curl_close( $ch );
    $data = json_decode( ob_get_contents(), TRUE );
    ob_end_clean();

    if ( $result === FALSE ) {
        die('Error calling WordPress service: ' . $error . "\n" );
    }

    return $data;
}

$response = botcall( 'get_options', array(
    'web_service_password' => $web_service_password
) );

$options = $response['options'];

// BOT_DIR/bot.conf.php file

function format_opt( $opt_name) {
    global $options;

    switch ( $opt_name ) {
        case 'nick':
            return 'nick ' . $options['irc_nick'] . "\n";
        case 'password':
            return 'password ' . $options['irc_nickserv_password'] . "\n";
        case 'realname':
            return 'realname ' . $options['irc_realname'] . "\n";
        case 'server':
            return 'server ' . $options['irc_server'] . "\n";
        case 'port':
            return 'port ' . $options['irc_port'] . "\n";
        case "channel":
            $text = '';
            foreach ( explode( ' ', trim( $options['irc_channels'] ) ) as $channel ) {
                $text .= 'channel ' . $channel . "\n";
            }
            return $text;
        case 'ident':
            return 'ident ' . $options['irc_ident'] . "\n";
    }
}

$out = array();
$opt_found = array(
    'nick' => 0,
    'password' => 0,
    'realname' => 0,
    'server' => 0,
    'port' => 0,
    'channel' => 0,
    'ident' => 0
);
foreach ( file( BOT_DIR . 'bot.conf.php.template', FILE_IGNORE_NEW_LINES ) as $l => $text ) {
    $opt_name = trim( explode( ' ', $text )[0] );
    if ( array_key_exists( $opt_name, $opt_found ) ) {
        if ( $opt_found[$opt_name] == 0 ) {
            $opt_found[$opt_name] = 1;
            $text = format_opt( $opt_name );
        } else {
            $text = "";
        }
    } else {
        $text .= "\n";
    }
    $out[] = $text;
}
foreach ( $opt_found as $key => $value ) {
    if ( $value == 0 ) {
        $text = format_opt( $key );
        $out[] = $text;
    }
}

file_put_contents( BOT_DIR . 'bot.conf.php', $out );

// MOD_DIR/musicstreamvote.conf file

foreach ( $options as $k => $v ) {
    if ( substr( $k, 0, 4 ) == 'cmd_' && substr( $k, -7 ) != '_switch' ) {
        // skip disabled commands
        if ( $options[$k . '_switch'] == '1' ) {
            $commands[] = substr( $k, 4 );
        }
    }
}
$out = array();
$out[] = "file\tmusicstreamvote\tmodules/musicstreamvote/musicstreamvote.php\n";
$out[] = "join\tmusicstreamvote\tevt_join\n";
$out[] = "raw\tmusicstreamvote\tevt_raw\n";
foreach ( $commands as $command ) {
    $aliases = explode( ' ', $options['cmd_' . $command] );
    foreach ( $aliases as $alias ) {
        $out[] = "priv\t$alias \ttrue\ttrue\tfalse\t0\tmusicstreamvote\tcmd_$command\n";
        if ( $command == 'vote' ) {
            for ( $i = -5; $i < 6; $i++ ) {
                $out[] = "priv\t$alias$i \ttrue\ttrue\tfalse\t0\tmusicstreamvote\tcmd_shortvote\n";
            }
        }
    }
}
file_put_contents( MOD_DIR . 'musicstreamvote.conf', $out );

// MOD_DIR/options.json

file_put_contents(
    MOD_DIR . 'options.json.php',
    "/* <" . "?php exit(); ?" . "> */\n" .
    json_encode( $options, JSON_PRETTY_PRINT )
);
