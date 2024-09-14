<?php

# 
# project:   zeus
# app:       outbound load balancer
# author:    jamie whittingham
# created:   13.10.2023
# updated:   31.07.2024
# 
# (c) copyright by /dev/null.
#

# session handler
# session_start();

# error handling
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );

# vars
$cms_server             = 'zeuscluster.com';
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
    $data['hls_guid']     = $name_bits[0];
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
} elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
    $user_ip = explode( ',', $_SERVER["HTTP_X_FORWARDED_FOR"] )[0];
} else {
    $user_ip = $_SERVER["REMOTE_ADDR"];
}

# HTTP_CF_IPCOUNTRY
if( isset( $_SERVER["HTTP_CF_IPCOUNTRY"] ) ) { 
    $user_country = $_SERVER["HTTP_CF_IPCOUNTRY"];
} else { 
    $user_country = 'unknown'; 
}

# HTTP_USER_AGENT
if( isset( $_SERVER["HTTP_USER_AGENT"] ) ) { 
    $user_agent = $_SERVER["HTTP_USER_AGENT"];
} else { 
    $user_agent = 'unknown'; 
}

# get stream details
# error_log( "\n\nhttps://".$cms_server."/api/?auth=1372&c=get_stream&hls_guid=".$data['hls_guid']."\n\n" );
$stream = file_get_contents( "https://".$cms_server."/api/?auth=1372&c=get_stream&hls_guid=".$data['hls_guid'] );

# convert json to array
$stream = json_decode( $stream, true );

# sanity check
if( $stream['status'] != 'success' ) {
    # stream is offline, find a placeholder
    $source = 'https://zeuscluster.com/media/no_signal/m3u8/index.m3u8';

    # dev output
    if( $data['dev'] == 'yes' ) {
        echo "<pre>";
        print_r( $source );
        echo "</pre>";
    }
    
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

        # action upon http 200 
        if( $httpcode == 200 ) {
            # open source
            $fp = fopen( $source, 'r' );

            # set headers
            header( "Content-Type: application/octet-stream" );
            header( "Content-Disposition: attachment; filename=".$data['filename'] );
            # header( "Content-Length: " . filesize( $fp ) );

            # pass the file
            fpassthru( $fp );
        } else{
            http_response_code( 404 );
        }
    }
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

            # action upon http 200 
            if( $httpcode == 200 ) {
                # record the viewer
                $requested_url = $_SERVER['REQUEST_URI'];
                $search_term = "m3u8";
                if( preg_match( "/\b$search_term\b/i", $requested_url ) ) {
                    $track_view = @file_get_contents( "https://".$cms_server."/api/?auth=1372&c=track_view&hls_guid=".$data['hls_guid']."&user_ip=".$user_ip."&user_agent=".$user_agent );
                }

                error_log( "https://".$cms_server."/api/?auth=1372&c=track_view&hls_guid=".$data['hls_guid']."&user_ip=".$user_ip."&user_agent=".$user_agent );

                # open source
                $fp = fopen( $source, 'r' );

                # set headers
                header( "Content-Type: application/octet-stream" );
                header( "Content-Disposition: attachment; filename=".$data['filename'] );
                # header( "Content-Length: " . filesize( $fp ) );

                # pass the file
                fpassthru( $fp );
            } else{
                http_response_code( 404 );
            }
        }
    }

    # ts files
    if( $data['ext'] == 'ts' ) {
        # build source url
        $source = "http://".$stream['transcoder_ip']."/hls/".$data['filename'];

        # check http code using curl
        $handle = curl_init( $source );
        curl_setopt( $handle,  CURLOPT_RETURNTRANSFER, TRUE );

        # get the HTML or whatever is linked in $url
        $response = curl_exec( $handle );

        # get httpcode
        $httpcode = curl_getinfo( $handle, CURLINFO_HTTP_CODE );

        # action upon http 200 
        if( $httpcode == 200 ) {
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
        } else {
            http_response_code( 404 );
        }
    }
}
