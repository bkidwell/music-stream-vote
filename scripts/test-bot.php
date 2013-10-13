#!/usr/bin/env php
<?php

function botcall( $method, $args ) {
	$url = 'http://localhost:8300/votebot/';
	$fields = array(
	    'musicstreamvote_botcall' => '1',
	    'method' => $method,
	    'args' => json_encode( $args )
	);
	print_r( $fields );
	$data = http_build_query( $fields );

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	ob_start();
	$result = curl_exec( $ch );
	curl_close( $ch );
	$data = json_decode( ob_get_contents(), TRUE );
	ob_end_clean();

	return $data;
}

$response = botcall( 'get_info', array(
	'system_password' => 'password'
) );
print_r( $response );

$ts = gmdate('Y-m-d H:i:s');

$response = botcall( 'track_start', array(
    'system_password' => 'password',
    'time_utc' => $ts,
    'stream_title' => 'Björk - track1'
) );
print_r( $response );

$response = botcall( 'post_vote', array(
    'system_password' => 'password',
    'time_utc' => $ts,
    'stream_title' => 'Björk - track1',
    'value' => 3,
    'nick' => 'progo',
    'user_id' => 'progo@nowhere',
    'is_authed' => TRUE,
    'logged_in_since_utc' => $ts
) );
print_r( $response );
