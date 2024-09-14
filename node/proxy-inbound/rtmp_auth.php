<?php

#
# project:   zeus
# app:       rtmp auth
# author:    jamie whittingham
# created:   11.10.2023
# updated:   11.10.2023
# 
# (c) copyright by /dev/null.
#

# env
set_time_limit( 0 );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );

# functions
function search_array( $dataarray, $search_value, $key_to_search ) {
    $keys = array();
    foreach( $dataarray as $key => $cur_value ) {
        if( $cur_value[$key_to_search] == $search_value ) {
            $keys[] = $key;
        }
    }
    return $keys;
}

function is_cli() {
    if( defined( 'STDIN' ) ) {
        return 'cli';
    }

    if( php_sapi_name() === 'cli' ) {
        return 'cli';
    }

    if( array_key_exists( 'SHELL' , $_ENV) ) {
        return 'cli';
    }

    if( empty( $_SERVER['REMOTE_ADDR'] ) and !isset( $_SERVER['HTTP_USER_AGENT'] ) and count( $_SERVER['argv'] ) > 0 ) {
        return 'cli';
    } 

    if( !array_key_exists( 'REQUEST_METHOD' , $_SERVER) ) {
        return 'cli';
    }

    return 'web';
}

// session start
if( is_cli() != 'cli' ) {
    $stream_key 		= $_POST['name'];
	$client_ip 			= $_REQUEST['addr'];
} else {
	$stream_key 		= 'test1372';
	$client_ip 			= '1.2.3.4';
}

# vars
$cms 					= 'zeuscluster.com';

# logging
# error_log( "\n\n - stream_key = " . $stream_key . "\nclient_ip = " . $client_ip . "\n\n" );

# sanity check
if( !isset( $stream_key )  || empty( $stream_key ) ) {
	# return 404 "missing stream_key"
	http_response_code( 404 );

	# exit script
	die();
}

# ask cms is rtmp_guid is allowed to stream
# error_log( "https://".$cms."/api/?auth=1372&c=rtmp_guid_check&rtmp_guid=".$stream_key );
$api_call = file_get_contents( "https://".$cms."/api/?auth=1372&c=rtmp_guid_check&rtmp_guid=".$stream_key );

# convert json to array
$api_call = json_decode( $api_call, true );

# print debug on cli
if( is_cli() != 'web' ) {
	echo "<pre>";
	print_r( $api_call );
	echo "</pre>";
}

# is live_allowed = yes | no
if( isset( $api_call['data'] ) && $api_call['data']['live_allowed'] == 'yes' ) {
	# print debug on cli
	if( is_cli() != 'web' ) {
		echo "LIVE streaming is allowed for ".$stream_key."\n";
	}

	# get the lowest cpu_usage transcoding server
	# error_log( "https://".$cms."/api/?auth=1372&c=find_free_transcoding_server&rtmp_guid=".$stream_key );
	## $transcoding_server = file_get_contents( "https://".$cms."/api/?auth=1372&c=find_free_transcoding_server&rtmp_guid=".$stream_key );

	# convert json to array
	## $transcoding_server = json_decode( $transcoding_server, true );

	# map the data array to the main var
	## $transcoding_server = $transcoding_server['data'];

	# push to api 
	## error_log( "https://".$cms."/api/?auth=1372&c=add_stream&rtmp_guid=".$api_call['data']['rtmp_guid']."&hls_guid=".$api_call['data']['hls_guid']."&client_ip=".$client_ip );
	## @file_get_contents( "https://".$cms."/api/?auth=1372&c=add_stream&rtmp_guid=".$api_call['data']['rtmp_guid']."&hls_guid=".$api_call['data']['hls_guid']."&client_ip=".$client_ip );

	# forward stream to transcoding server
	# error_log( "Location:rtmp://".$transcoding_server['private_ip']."/convert_rtmp_to_hls/".$api_call['data']['rtmp_guid'] );
	# header( "Location:rtmp://".$transcoding_server['private_ip']."/convert_rtmp_to_hls/".$api_call['data']['rtmp_guid'], true, 302 );
	
	# return 200
	http_response_code( 200 );
} else {
	# return 403
	http_response_code( 403 );

	# exit script
	die();
}

# return 403
http_response_code( 403 );

# exit script
die();
?>
