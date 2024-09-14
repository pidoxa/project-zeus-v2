<?php

#
# project:   zeus
# app:       outbound load balancer
# author:    jamie whittingham
# created:   13.10.2023
# updated:   13.10.2023
# 
# (c) copyright by /dev/null.
#

# session handler
# session_start();

# error handling
error_reporting(E_ALL);
ini_set( 'display_errors', 1);
ini_set( 'error_reporting', E_ALL);

# vars
$data['name']           = @$_GET['name'];
$data['ext']            = @$_GET['ext'];
$data['filename']       = $data['name'].".".$data['ext'];

# get resolution
$name_bits              = explode( '_', $_GET['name'] );
if( isset( $name_bits[1] ) ) {
    $data['resolution']     = $name_bits[1];
}

# get hls_guid
if( isset( $name_bits[0] ) ) {
    $data['hls_guid']        = $name_bits[0];
}

# dev
$data['dev']            = @$_GET['dev'];

if( $data['dev'] == 'yes' ) {
    echo "<pre>";
    print_r( $data );
    echo "</pre>";
}

# get users real ip
if( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) { 
    $user_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
} else { 
    $user_ip = '0.0.0.0'; 
}

# get the current time in minutes and hours
$current_time = date( 'H:i' );

# extract the minutes from the current time
$current_minute = date( 'i', strtotime( $current_time ) );
$current_second = date( 's', time() );

# define an array of target minutes
$target_minutes = [0, 1, 5, 6, 10, 11, 15, 16, 20, 21, 25, 26, 30, 31, 35, 36, 40, 41, 45, 46, 50, 51, 55, 56];

# define an array of target seconds
$target_seconds_0 = range( 0, 9 );
$target_seconds_1 = range( 10, 19 );
$target_seconds_2 = range( 20, 29 );
$target_seconds_3 = range( 30, 39 );
$target_seconds_4 = range( 40, 49 );
$target_seconds_5 = range( 50, 59 );

# check if the current minute is in the array
$inject = in_array( $current_minute, $target_minutes );

# get the ad slug if needed
if( $inject == true ) {
    $ad = file_get_contents( "http://10.254.202.181/api/?auth=1372&c=get_ad" );

    # convert json to array
    $ad = json_decode( $ad, true );
}

# find minutes to run ad roles
# $inject = ( $current_minute % 5 >= 0 && $current_minute % 5 < 1 );

# get stream details
# error_log( "\n\nhttp://10.254.202.181/api/?auth=1372&c=get_stream&hls_guid=".$data['hls_guid']."\n\n" );
$stream = file_get_contents( "http://10.254.202.181/api/?auth=1372&c=get_stream&hls_guid=".$data['hls_guid']."&user_ip=".$user_ip );

# convert json to array
$stream = json_decode( $stream, true );

# sanity check
if( $stream['status'] != 'success' ) {
    # return 404
    http_response_code( 404 );

    die();
} else {
    # remap $stream['data'] to $stream
    $stream = $stream['data'];

    if( $data['dev'] == 'yes' ) {
        echo "<pre>";
        print_r( $stream );
        echo "</pre>";
    }

    # m3u8 files
    if( $data['ext'] == 'm3u8' ) {
        # build source url
        if( isset( $name_bits[1] ) ) {
            $source = "http://".$stream['transcoder_ip']."/hls/".$data['hls_guid']."_".$data['resolution'].".m3u8";
        } else {
            $source = "http://".$stream['transcoder_ip']."/hls/".$data['hls_guid'].".m3u8";
        }

        if( $data['dev'] == 'yes' ) {
            echo "<pre>";
            print_r( $source );
            echo "</pre>";
        }

        # process m3u8
        if( $data['dev'] == 'yes' ) {
            # get contents of m3u8
            $file = @file_get_contents( $source );

            echo "<pre>";
            echo $file;
            echo "</pre>";
        } else {
            # get contents of m3u8

            # check http code using curl
            $handle = curl_init( $source );
            curl_setopt( $handle,  CURLOPT_RETURNTRANSFER, TRUE );

            # get the HTML or whatever is linked in $url
            $response = curl_exec( $handle );

            # get httpcode
            $httpcode = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
            if( $httpcode == 200 || $httpcode == 301 || $httpcode == 302 ) {
                # set mime-type
                # header( "Content-Type: application/octet-stream" );
            } else {
                # set mime-type
                # header("Content-Type: video/mp4");

                # update source to stream_offline
                # $source = "http://localhost/stream_offline/stream_offline.mp4";
            }

            # open source
            $fp = fopen( $source, 'r' );

            # set headers
            header( "Content-Type: application/octet-stream" );
            header( "Content-Disposition: attachment; filename=".$data['filename'] );
            # header( "Content-Length: " . filesize( $fp ) );

            # pass the file
            fpassthru( $fp );
        }
    }

    # ts files
    if( $data['ext'] == 'ts' ) {
        # build source url
        $source = "http://".$stream['transcoder_ip']."/hls/".$data['filename'];

        # check if ad should be injected
        if( $inject == true ) {

            if( in_array( $current_second, $target_seconds_0 ) ) {
                $source = "/var/www/html/ads/".$ad['data']['slug']."/fileSequence000.ts";
            }
            if( in_array( $current_second, $target_seconds_1 ) ) {
                $source = "/var/www/html/ads/".$ad['data']['slug']."/fileSequence001.ts";
            }
            if( in_array( $current_second, $target_seconds_2 ) ) {
                $source = "/var/www/html/ads/".$ad['data']['slug']."/fileSequence002.ts";
            }
            if( in_array( $current_second, $target_seconds_3 ) ) {
                $source = "/var/www/html/ads/".$ad['data']['slug']."/fileSequence003.ts";
            }
            if( in_array( $current_second, $target_seconds_4 ) ) {
                $source = "/var/www/html/ads/".$ad['data']['slug']."/fileSequence004.ts";
            }
            if( in_array( $current_second, $target_seconds_5 ) ) {
                $source = "/var/www/html/ads/".$ad['data']['slug']."/fileSequence005.ts";
            }
        }

        ob_end_flush();

        if( $data['dev'] == 'yes' ) {
            echo "<pre>";
            print_r( $source );
            echo "</pre>";
        }

        # get contents of ts
        if( $data['dev'] == 'yes' ) {
            echo "ts file should stream here.";
        } else {
            # open source
            $fp = fopen( $source, 'r' );

            # set headers
            header( "Content-Type: video/mp2t" );
            header( "Content-Disposition: attachment; filename=".$data['filename'] );
            # header( "Content-Length: " . filesize( $fp ) );

            # pass the file
            fpassthru( $fp );
        }
    }
}
