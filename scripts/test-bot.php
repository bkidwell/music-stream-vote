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
	print_r( $data );

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
