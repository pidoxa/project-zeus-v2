<?php

#
# project:   zeus
# app:       dupe rtmp stream checker
# author:    jamie whittingham
# created:   13.11.2023
# updated:   13.11.2023
# 
# (c) copyright by /dev/null.
#

# vars
$stream_key 		= $_POST['name'];
$client_ip 			= $_REQUEST['addr'];

# get the lowest cpu_usage transcoding server
$data = file_get_contents( "http://10.254.202.181/api/?auth=1372&c=dupe_rtmp_stream_checker&rtmp_guid=".$stream['rtmp_guid'] );

# convert json to array
$data = json_decode( $data, true );

# sanity check
if( $data['status'] == 'error' ) {
    # return 404 "stream_key already in use"
    http_response_code( 403 );

    # exit script
    die();
}