<?php

function send_ntfy( $title, $content, $priority = 'default', $tags = 'core' ) {
	file_get_contents( 'http://notify.pidoxa.io/zeus_v2', false, stream_context_create( [
		'http' => [
			'method' => 'POST',
			'header' =>
				"Content-Type: text/plain\r\n" .
				"Title: ".$title."\r\n".
				"Priority: ".$priority."\r\n" .
				"Tags: ".$tags,
			'content' => $content
		]
	] ) );
}

function post( $key = null ) {
	if( is_null( $key ) ) {
		return $_POST;
	}
	$post = isset( $_POST[$key] ) ? $_POST[$key] : null;
	if( is_string( $post ) ) {
		$post = trim( $post );
	}
	return $post;
}

function get( $key = null ) {
	if( is_null( $key ) ) {
		return $_GET;
	}
	$get = isset( $_GET[$key] ) ? $_GET[$key] : null;
	if( is_string( $get ) ) {
		$get = trim( $get );
	}
	return $get;
}

function killlock() {
    global $lockfile;
	exec( "rm -rf $lockfile" );
}

function search_multi_array( $dataArray, $search_value, $key_to_search ) {
    $keys = array();
    foreach( $dataArray as $key => $cur_value ) {
        if( $cur_value[$key_to_search] == $search_value ) {
            $keys[] = $key;
        }
    }
    return $keys;
}

function check_file_age( $filename ) {
	if( file_exists( $filename ) ) {
		return filemtime ( $filename );
	}else{
		return 0;
	}
}

function channel_stats( $id ) {
	/*if( file_exists( "/var/www/html/logs/".$id.".log" ) ) {

		$handle = popen( "sudo tail -13l /var/www/html/logs/".$id.".log 2>&1", 'r' );
		while( !feof( $handle ) ) {
		    $line = fgets( $handle );

		    $line_bits = explode( "=", $line );

		    $key = $line_bits[0];
		    if( isset( $line_bits[1] ) ) {
		    	$value = $line_bits[1];

		    	// strip new lines from $value
			    $value = str_replace( '\n', '', $value );
			    $value = str_replace( '\r', '', $value );
			    $value = str_replace( '\r\n', '', $value );

			    $value = trim( preg_replace( '/\s\s+/', ' ', $value ) );

			    // stream uptime
			    if( isset( $key ) && $key == 'out_time') {
			    	$value_bits = explode( '.', $value );
			    	$data = $value_bits[0];
			    	// echo "Stream Runtime: '".$value_bits[0]."' \n";
			    }

			    // stream transcode speed
			    if( $key == 'speed' ) {
			    	$speed = $value;
			    	echo "Stream Speed: '".$speed."' \n";
			    }

			    // stream fps 
			    if( $key == 'fps' ) {
			    	$fps = $value;
			    	echo "Stream FPS: '".$fps."' \n";
			    }
		    }
		}

		pclose( $handle );

		$data['stream_speed'] 	= $speed;
		$data['stream_fps']		= $fps;
	}else{
		$data = array();
	}
	*/

	// open log file
	$log_data = shell_exec( "sudo tail -17l /var/www/html/logs/".$id.".log 2>&1" );

	// break the incoming data up
	$data_bits = array_filter( array_map( "trim", explode( "\n", $log_data ) ) );

	// create a blank array
	$bits = array();

	// sanitize the data
	foreach( $data_bits as $data ) {
	    list($key, $value) = explode( "=", $data );
	    $bits[trim( $key )] = trim( $value );
	}

	// json encode
	$data = json_encode( $bits );

	// json decode
	$data = json_decode( $data, true );

	return $data;
}

function ping( $ip ) {
    $pingresult = exec( "ping -c 2 $ip", $outcome, $status);
    if (0 == $status) {
        $status = "online";
    } else {
        $status = "offline";
    }
    
    return $status;
}

function get_metadata( $name) {
	$name = trim( $name);

	// try the open movie db for meta data
	$url = 'http://www.omdbapi.com/?apikey=19354e2e&t='.urlencode( $name );
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_URL, $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HEADER, false );
	$metadata = curl_exec( $curl );
	curl_close( $curl);

	$metadata = json_decode( $metadata, true );

	$data['name'] 				= $name;

	$data['year'] 				= '';
	$data['cover_photo']		= '';
	$data['description']		= '';
	$data['genre'] 				= '';
	$data['runtime'] 			= '';
	$data['language'] 			= '';

	if( $metadata['Response'] == False || $metadata['Response'] == "False" ){
		$data['status'] 		= 'no_match';
	}elseif( $metadata['Response'] == True){
		$data['status'] 		= 'match';
		$data['name'] 			= addslashes( $metadata['Title'] );
		$data['year'] 			= addslashes( $metadata['Year'] );
		$data['cover_photo']	= addslashes( $metadata['Poster'] );
		$data['description']	= addslashes( $metadata['Plot'] );
		$data['genre'] 			= addslashes( $metadata['Genre'] );
		$data['runtime'] 		= addslashes( $metadata['Runtime'] );
		$data['language'] 		= addslashes( $metadata['Language'] );
	}

	return $data;
}

function get_youtube_video_ID( $youtube_video_url) {
	$url_string = parse_url( $youtube_video_url, PHP_URL_QUERY );
  	parse_str( $url_string, $args );
  	return isset( $args['v'] ) ? $args['v'] : false;
}

function get_youtube_stream_url( $URL) {
	$QUALITY        = '';
	$USER_AGENT     = "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)";
	$REFERER_URL    = "http://facebook.com";
   
    $CONNECTION = curl_init();
    $TIMEOUT = 5;
    curl_setopt( $CONNECTION, CURLOPT_URL, $URL );
    curl_setopt( $CONNECTION, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $CONNECTION, CURLOPT_USERAGENT, $USER_AGENT);
    curl_setopt( $CONNECTION, CURLOPT_REFERER, $REFERER_URL );
    curl_setopt( $CONNECTION, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $CONNECTION, CURLOPT_CONNECTTIMEOUT, $TIMEOUT );
   
    $RESPONCE = curl_exec( $CONNECTION );
    curl_close( $CONNECTION );
   
    return $RESPONCE;
}

function is_dir_empty( $dir) {
	if ( !is_readable( $dir ) ) return NULL; 
	return ( count( scandir( $dir ) ) == 2);
}

// recursive scanning function
function getDirContents( $dir, &$results = array() ) {
    $files = scandir( $dir);

    foreach( $files as $key => $value ){
        $path = realpath( $dir.DIRECTORY_SEPARATOR.$value );
        if( !is_dir( $path ) ) {
            $results[] = $path;
        } elseif( $value != "." && $value != ".." ) {
            getDirContents( $path, $results );
            $results[] = $path;
        }
    }

    return $results;
}

function online_status() {
	/*
	$response = null;
	system( "ping -c 1 google.com", $response);
	if( $response == 0) {
	    $data = 'online';
	}else{
		$data = 'offline';
	}
	*/
	$connected = @fsockopen( "www.google.com", 80 ); 
        //website, port  (try 80 or 443)
       	if( $connected) {
        	$data = 'online'; //action when connected
          	fclose( $connected );
       	}else{
         	$data = 'offline'; //action in connection failure
       }

	return $data;
}

function console_output( $data ) {
	$timestamp = date( "Y-m-d H:i:s", time() );
	echo "[" . $timestamp . "] - " . $data . "\n";
}

function json_output( $data ) {
	// $data['timestamp']		= time();
	$data 					= json_encode( $data );
	echo $data;
	die();
}

function filesize_formatted( $path) {
    $size = filesize( $path );
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
    $power = $size > 0 ? floor( log( $size, 1024 ) ) : 0;
    return number_format( $size / pow( 1024, $power ), 2, '.', ',' ) . ' ' . $units[$power];
}

function percentage( $val1, $val2, $precision ) {
	$division = $val1 / $val2;
	$res = $division * 100;
	$res = round( $res, $precision );
	return $res;
}

function go( $link = '') {
	header( "Location: " . $link);
	die();
}

function url( $url = '') {
	$host = $_SERVER['HTTP_HOST'];
	$host = !preg_match( '/^http/', $host) ? 'http://' . $host : $host;
	$path = preg_replace( '/\w+\.php/', '', $_SERVER['REQUEST_URI'] );
	$path = preg_replace( '/\?.*$/', '', $path);
	$path = !preg_match( '/\/$/', $path) ? $path . '/' : $path;
	if ( preg_match( '/http:/', $host) && is_ssl() ) {
		$host = preg_replace( '/http:/', 'https:', $host);
	}
	if ( preg_match( '/https:/', $host) && !is_ssl() ) {
		$host = preg_replace( '/https:/', 'http:', $host);
	}
	return $host . $path . $url;
}

function debug( $input) {
	$output = '<pre>';
	if ( is_array( $input ) || is_object( $input ) ) {
		$output .= print_r( $input, true );
	} else {
		$output .= $input;
	}
	$output .= '</pre>';
	echo $output;
}

function call_remote_content( $url) {
	echo file_get_contents( $url );
}

function check_allowed_ip(){

	$remote_ip = $_SERVER['REMOTE_ADDR'];


	// GET FIRST ALL IPS FROM DATABASE OF USER
	$set_line_array = array (get_line_id_by_name( $line_user ) );
	$set_line = $db->query( 'SELECT line_allowed_ip FROM cms_lines WHERE line_id = ?', $set_line_array );

	// IF LINE ALLOWED IPS NOT EMPTY
	if( $set_line[0]['line_allowed_ip'] != '' ){

		// MAKE IPS AS ARRAY
		$allowed_ips = json_decode( $set_line[0]['line_allowed_ip'], true );
		foreach( $allowed_ips as $ip ){

			// IF IP IS REMOTE IP THEN ALLOW IT OTHERWISE DONT ALLOW IT
			if( $ip == $_SERVER['REMOTE_ADDR'] ) {
				$return = true;
			} else {
				$return = false;
			}
		}
	} else {
		$return = true;
	}

	return $return;
}