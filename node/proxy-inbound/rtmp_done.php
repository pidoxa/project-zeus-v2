<?php

#
# project:   zeus
# app:       rtmp done
# author:    jamie whittingham
# created:   11.10.2023
# updated:   11.10.2023
# 
# (c) copyright by /dev/null.
#

# env
set_time_limit( 0 );
# error_reporting( E_ALL );
# ini_set( 'display_errors', 1 );
# ini_set( 'error_reporting', E_ALL );

# vars
$stream_key 		= $_POST['name'];
$cms_ip 			= '10.200.3.230';

# ask cms is rtmp_guid is allowed to stream
$api_call = file_get_contents( "http://".$cms_ip."/api/?auth=1372&c=rtmp_guid_check&rtmp_guid=".$stream_key );

# convert json to array
$api_call = json_decode( $api_call, true );

# push to api 
error_log( "http://".$cms_ip."/api/?auth=1372&c=remove_stream&rtmp_guid=".$stream_key."&hls_guid=".$api_call['data']['hls_guid']  );
@file_get_contents( "http://".$cms_ip."/api/?auth=1372&c=remove_stream&rtmp_guid=".$stream_key."&hls_guid=".$api_call['data']['hls_guid'] );

?>