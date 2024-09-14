<?php
# env
set_time_limit( 0 );
# error_reporting( E_ALL );
# ini_set( 'display_errors', 1 );
# ini_set( 'error_reporting', E_ALL );

# vars
$stream_key 		= $_POST['name'];

# check the source resolution
error_log( "\n\n ffprobe -v quiet -select_streams v:0 -show_entries stream=height -of csv=s=x:p=0 rtmp://localhost/convert_rtmp_to_hls/".$stream_key."\n\n" );
$resolution = shell_exec( "ffprobe -v quiet -select_streams v:0 -show_entries stream=height -of csv=s=x:p=0 rtmp://localhost/convert_rtmp_to_hls/".$stream_key );
$resolution = preg_replace( '/\s*\R\s*/', ' ', trim( $resolution ) );

error_log( "\n\nresolution = ".$resolution."\n\n" );

sleep( 5 );

# sanity check - issue a redirect to the correct transcoder
if( empty( $resolution ) ) {
	error_log( "\n\nRESOLUTION NOT FOUND - USING FALLBACK\n\n" );
	# record live stream check point
	if( $api_call['data']['record_stream'] == true ) {
		header( "Location:rtmp://127.0.0.1/1080_in/".$api_call['data']['hls_guid'], true, 302 );
	} else {
		header( "Location:rtmp://127.0.0.1/1080_in_no_recording/".$api_call['data']['hls_guid'], true, 302 );
	}
} else {
	error_log( "\n\nRESOLUTION FOUND - USING SOURCE RESOLUTION\n\n" );
	header( "Location:rtmp://127.0.0.1/".$resolution."_in/".$api_call['data']['hls_guid'], true, 302 );
}


?>