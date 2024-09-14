<?php

#
# project:   zeus cluster
# app:       rtmp c & c
# author:    jamie whittingham
# created:   23.07.2024
# updated:   23.07.2024
# 
# (c) copyright by /dev/null.
#

// timeout
ini_set( 'default_socket_timeout', 30 );
ini_set( 'max_execution_time', 0 );

// includes
$app['basepath'] = '/opt/zeus/';
include( $app['basepath'].'inc/functions.php' );
include( $app['basepath'].'inc/php_colors.php' );

// get script options
$shortopts  = "";
$shortopts .= "f:"; 	// Required value
$shortopts .= "v::";	// Optional value
$shortopts .= "abc";	// These options do not accept values

$longopts  = array(
    "required:",    	// Required value
    "optional::",   	// Optional value
    "option",       	// No value
    "opt",          	// No value
    "verbose::",    	// Optional 0 = now | 1 = yes
    "action::",    		// Optional value
    "channel_id::",    	// Optional value
    "server_id::",    	// Optional value
    "server_guid::",    // Optional value
    "rtmp_guid::",    	// Optional value
    "hls_guid::",    	// Optional value
);
$script_options = getopt( $shortopts, $longopts );

// set dev mode
if( isset( $script_options['verbose'] ) && $script_options['verbose'] == 1 ) {
	$verbose = true;
} else {
	$verbose = false;
}

// sanity check
if( !isset( $script_options['action'] ) || empty( $script_options['action'] ) ) {
	console_output( 'example usage: php -q console.php --action=task' );
	die();
}

// set vars
$new_line = "\n";

// check age of all lock files
$lockfiles = glob( $app['basepath'].'console/*.loc' );

/*
// parse lock file results
	if( is_array( $lockfiles ) ) {
		foreach( $lockfiles as $lockfile ) {
			console_output( "Checking ".$lockfile );

			// check if file is there
			if (time()-filemtime( $lockfile ) > 120 ) {
				exec( "rm -rf ".$lockfile );
				if( $verbose == true ) {
					console_output( $lockfile . " is stale, removing it" );
				}
			}
		}
	}
*/

// enable php colors
$colors = new Colors();

// get the config file
$config = array();
$config['cms']['server'] = 'zeuscluster.com';
$config['server_guid'] = @file_get_contents( $app['basepath'].'server_guid' );
$config['server_guid'] = trim( $config['server_guid'] );
// $config = json_decode( $config, true );

// script actions
if( $script_options['action'] == 'cron_manager' ) {
	if( $verbose == true ) {
		console_output( "Zeus Cluster - RTMP Node Cron Manager" );
	}

	if( $verbose == true ) {
		console_output( "Loading Module: checkin" );
	}

	shell_exec( 'php -q /opt/zeus/console/console.php --action=checkin > /tmp/cron.checkin.log' );

	$check = shell_exec( "ps aux | grep 'action=jobs' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: jobs" );
		}
		shell_exec( 'php -q /opt/zeus/console/console.php --action=jobs > /tmp/cron.jobs.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: jobs" );
		}
	}

	$check = shell_exec( "ps aux | grep 'action=stream_zombie_check' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: stream_zombie_check" );
		}
		shell_exec( 'php -q /opt/zeus/console/console.php --action=stream_zombie_check > /tmp/cron.stream_zombie_check.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: stream_zombie_check" );
		}
	}

	/*
	$check = shell_exec( "ps aux | grep 'action=vod_monitored_folder_scan' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: vod_monitored_folder_scan" );
		}
		shell_exec( 'php -q /opt/zeus/console/console.php --action=vod_monitored_folder_scan > /tmp/cron.vod_monitored_folder_scan.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: vod_monitored_folder_scan" );
		}
	}

	$check = shell_exec( "ps aux | grep 'action=vod_tv_monitored_folder_scan' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: vod_tv_monitored_folder_scan" );
		}
		shell_exec( 'php -q /opt/zeus/console/console.php --action=vod_tv_monitored_folder_scan > /tmp/cron.vod_tv_monitored_folder_scan.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: vod_tv_monitored_folder_scan" );
		}
	}
	*/

	/*
	$check = shell_exec( "ps aux | grep 'action=channels_247_monitored_folder_scan' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: channels_247_monitored_folder_scan" );
		}
		shell_exec( 'php -q /opt/zeus/console/console.php --action=channels_247_monitored_folder_scan > /tmp/cron.channels_247_monitored_folder_scan.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: channels_247_monitored_folder_scan" );
		}
	}
	*/

	$check = shell_exec( "ps aux | grep 'action=channels_247_manager' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: channels_247_manager" );
		}
		shell_exec( 'php -q /opt/zeus/console/console.php --action=channels_247_manager > /tmp/cron.channels_247_manager.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: channels_247_manager" );
		}
	}

	$check = shell_exec( "ps aux | grep 'action=channels_manager' | grep -v 'grep' | grep -v '/bin/sh' | wc -l" );
	if( $check == 0) {
		if( $verbose == true ) {
			console_output( "Loading Module: channels_manager" );
		}

		shell_exec( 'php -q /opt/zeus/console/console.php --action=channels_manager > /tmp/cron.channels_manager.log' );
	} else {
		if( $verbose == true ) {
			console_output( "Skipping Module: channels_manager" );
		}
	}
	// checking for zombies
	$pids = shell_exec( "ps aux | grep 'action=channels_manager' | grep -v 'grep' | grep -v 'sudo' | awk '{print $2}' " );
	if( isset( $pids) && !empty( $pids ) ) {
		$pids = explode( PHP_EOL, $pids );

		foreach( $pids as $pid ) {
			if( !empty( $pids ) ) {
				$run_time = shell_exec( 'ps -o etimes= -p '.$pid );
				if( $run_time > 300 ) {
			    	shell_exec( 'sudo kill -9 '.$pid );
			    }
			}
		}
	}

	$pids = shell_exec( "ps aux | grep 'action=channel_slave' | grep -v 'grep' | grep -v 'sudo' | awk '{print $2}' " );
	if( isset( $pids) && !empty( $pids ) ) {
		$pids = explode( PHP_EOL, $pids );

		foreach( $pids as $pid ) {
			if( !empty( $pids ) ) {
				$run_time = shell_exec( 'ps -o etimes= -p '.$pid );
				if( $run_time > 300 ) {
			    	shell_exec( 'sudo kill -9 '.$pid );
			    }
			}
		}
	}
	
	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}

	killlock();
}

if( $script_options['action'] == 'updates' ) {
	console_output( "Running updates and patches" );

	console_output( "Finished" );

	echo "\n\n";

	killlock();
}

if( $script_options['action'] == 'checkin' ) {
	// cli_set_process_title( "Stiliam Cron - checkin" );
	// get basic system stats
	$data 				= exec( 'sudo sh /opt/stiliam-server-monitor/system_stats.sh' );
	$data 				= json_decode( $data, true);
	
	$data['uuid']		= $config['server_guid'];
	$data['cpu_usage'] 	= str_replace( "%", "", $data['cpu_usage'] );
	$data['cpu_usage'] 	= number_format( $data['cpu_usage'], 2);

	$data['ram_usage'] 	= str_replace( "%", "", $data['ram_usage'] );
	$data['ram_usage'] 	= number_format( $data['ram_usage'], 2);

	$data['disk_usage'] = str_replace( "%", "", $data['disk_usage'] );
	$data['disk_usage'] = number_format( $data['disk_usage'], 2);

	$data['os_version'] = exec( 'cat /etc/os-release | grep PRETTY | sed "s/PRETTY_NAME=//g" | sed "s/\"//g"' );

	if( $verbose == true ) {
		console_output( "Node Checkin" );
		console_output( "Remote Server: " . $config['cms']['server'] );
		console_output( "Local IP: " . $data['ip_address'] );
		console_output( "Local UUID: " . $config['server_guid'] );
	}

	// get GPU data if available
	$has_gpu	= exec( "sudo lspci | grep NVIDIA | wc -l" );
	if( $has_gpu > 0) {
		$xml 		= simplexml_load_string(shell_exec( 'nvidia-smi -q -x' ) );
		
		$json 		= json_encode( $xml);

		$gpu_stats 	= json_decode( $json, true);

		// print_r( $gpu_stats);

		$stats['driver_version'] 	= $gpu_stats['driver_version'];
		$stats['cuda_version'] 		= $gpu_stats['cuda_version'];
		
		$count = 0;

		if(isset( $gpu_stats['gpu'][0] ) ) {
			foreach( $gpu_stats['gpu'] as $gpu_stat) {
				$stats['gpu'][$count]['id'] 				= $count;
				$stats['gpu'][$count]['uuid'] 				= $gpu_stat['uuid'];
				$stats['gpu'][$count]['gpu_name'] 			= $gpu_stat['product_name'];
				
				if( $verbose == true ) {
					console_output( "GPU Found: ".$gpu_stat['product_name'] );
				}

				$stats['gpu'][$count]['fan_speed'] 			= $gpu_stat['fan_speed'];

				$stats['gpu'][$count]['gpu_temp'] 			= $gpu_stat['temperature']['gpu_temp'];

				$stats['gpu'][$count]['graphics_clock'] 	= $gpu_stat['clocks']['graphics_clock'];
				$stats['gpu'][$count]['sm_clock']		 	= $gpu_stat['clocks']['sm_clock'];
				$stats['gpu'][$count]['mem_clock'] 			= $gpu_stat['clocks']['mem_clock'];
				$stats['gpu'][$count]['video_clock'] 		= $gpu_stat['clocks']['video_clock'];

				$stats['gpu'][$count]['total_ram'] 			= $gpu_stat['fb_memory_usage']['total'];
				$stats['gpu'][$count]['used_ram'] 			= $gpu_stat['fb_memory_usage']['used'];
				$stats['gpu'][$count]['free_ram'] 			= $gpu_stat['fb_memory_usage']['free'];

				$stats['gpu'][$count]['gpu_util'] 			= $gpu_stat['utilization']['gpu_util'];

				/*
				$stats['gpu'][$count]['processes']			= $gpu_stat['processes']['process_info'];
				if( empty( $stats['gpu'][$count]['processes'] ) ) {
					$stats['gpu'][$count]['processes'] = 0;
				}
				*/

				if(isset( $gpu_stat['processes']['process_info'] ) ) {
					$stats['gpu'][$count]['processes']			= $gpu_stat['processes']['process_info'];
				} else {
					$stats['gpu'][$count]['processes']			= '0';
				}

				$count++;
			}
		} else {
			$stats['gpu'][$count]['id'] 				= $count;
			$stats['gpu'][$count]['uuid'] 				= $gpu_stats['gpu']['uuid'];
			$stats['gpu'][$count]['gpu_name'] 			= $gpu_stats['gpu']['product_name'];
			
			if( $verbose == true ) {
				console_output( "GPU Found: ".$gpu_stats['gpu']['product_name'] );
			}
			$stats['gpu'][$count]['fan_speed'] 			= $gpu_stats['gpu']['fan_speed'];

			$stats['gpu'][$count]['gpu_temp'] 			= $gpu_stats['gpu']['temperature']['gpu_temp'];

			$stats['gpu'][$count]['graphics_clock'] 	= $gpu_stats['gpu']['clocks']['graphics_clock'];
			$stats['gpu'][$count]['sm_clock']		 	= $gpu_stats['gpu']['clocks']['sm_clock'];
			$stats['gpu'][$count]['mem_clock'] 			= $gpu_stats['gpu']['clocks']['mem_clock'];
			$stats['gpu'][$count]['video_clock'] 		= $gpu_stats['gpu']['clocks']['video_clock'];

			$stats['gpu'][$count]['total_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['total'];
			$stats['gpu'][$count]['used_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['used'];
			$stats['gpu'][$count]['free_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['free'];

			$stats['gpu'][$count]['gpu_util'] 			= $gpu_stats['gpu']['utilization']['gpu_util'];

			if(isset( $gpu_stats['gpu']['processes']['process_info'] ) ) {
				$stats['gpu'][$count]['processes']			= $gpu_stats['gpu']['processes']['process_info'];
			} else {
				$stats['gpu'][$count]['processes']			= '0';
			}
		}

		$data['gpu_stats'] = $stats;
	}

	$data['version'] = file_get_contents( '/opt/zeus/version.txt' );

	// post data
	if( $verbose == true ) {
		console_output( "Posting to CMS Server" );
	}

	$url = "http://".$config['cms']['server']."/api/?c=checkin";
	
	if( $verbose == true ) {
		console_output( "URL: ".$url );
	}

	$ch = curl_init( $url );
	$postString = http_build_query( $data, '', '&' );
	// curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-length:' . strlen( $postString) ));
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $postString );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	
	$response = curl_exec( $ch );
	
	curl_close( $ch );

	// console_output( "Server Reply: " . $response);
	
	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}

	killlock();
}

if( $script_options['action'] == 'jobs' ) {
	if( $verbose == true ) {
		console_output( "Jobs: checking for work" );
		console_output( "API ENDPOINT: http://".$config['cms']['server']."/api/?c=jobs&uuid=".$config['server_guid'] );
	}

	$jobs 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=jobs&uuid=".$config['server_guid'] );
	$jobs		= json_decode( $jobs, true);

	if(is_array( $jobs) && isset( $jobs[0] ) ) {
		// print_r( $jobs);

		foreach( $jobs as $job) {
			if( $job['job']['action'] == 'reboot' ) {
				if( $verbose == true ) {
					console_output( "Reboot job found, node will reboot in 10 seconds" );
				}

				@file_get_contents( "http://".$config['cms']['server']."/api/?c=job_complete&id=".$job['id'] );
				
				sleep(10);
				exec( "sudo /sbin/shutdown -r now" );
			}

			if( $job['job']['action'] == 'kill_pid' ) {
				if( $verbose == true ) {
					console_output( "Kill PID job found" );
				}

				@file_get_contents( "http://".$config['cms']['server']."/api/?c=job_complete&id=".$job['id'] );
				
				sleep(1);
				exec( $job['job']['command'] );
			}

			if( $job['job']['action'] == 'streams_restart_all' ) {
				if( $verbose == true ) {
					console_output( "Restart All Streams job found" );
				}

				// stop ffmpeg from running
				exec( "sudo killall ffmpeg" );
				exec( "sudo killall ffmpeg" );
				exec( "sudo killall ffmpeg" );
				exec( "sudo killall ffmpeg" );
				exec( "sudo killall ffmpeg" );

				exec( "sudo pkill ffmpeg" );
				exec( "sudo pkill ffmpeg" );
				exec( "sudo pkill ffmpeg" );
				exec( "sudo pkill ffmpeg" );
				exec( "sudo pkill ffmpeg" );

				// remove all hls stream files.
				exec( "sudo rm -rf /mnt/streaming/hls/*" );

				// report job complete
				@file_get_contents( "http://".$config['cms']['server']."/api/?c=job_complete&id=".$job['id'] );
			}
		}
	} else {
		if( $verbose == true ) {
			console_output( "No pending jobs" );
		}
	}

	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}

	killlock();
}

if( $script_options['action'] == 'speedtest' ) {
	console_output( "Running speedtest" );
	$raw = shell_exec( '/usr/bin/speedtest --simple' );

	$speedtest_bits = explode( "\n", $raw);

	$speedtest_bits = array_filter( $speedtest_bits);

	$ping = explode( ": ", $speedtest_bits[0] );
	$data['ping'] = $ping[1];

	$download = explode( ": ", $speedtest_bits[1] );
	$data['download'] = $download[1];

	$upload = explode( ": ", $speedtest_bits[2] );
	$data['upload'] = $upload[1];

	$json = json_encode( $data);

	file_put_contents( '/opt/zeus/speedtest.json', $json);

	console_output( "Finished" );

	echo "\n\n";

	killlock();
}

if( $script_options['action'] == 'gpu_stats' ) {
	$xml 		= simplexml_load_string(shell_exec( 'nvidia-smi -q -x' ) );
	
	$json 		= json_encode( $xml);

	$gpu_stats 	= json_decode( $json, true);

	// print_r( $gpu_stats);

	$stats['driver_version'] 	= $gpu_stats['driver_version'];
	$stats['cuda_version'] 		= $gpu_stats['cuda_version'];
	
	$count = 0;

	if(isset( $gpu_stats['gpu'][0] ) ) {
		foreach( $gpu_stats['gpu'] as $gpu_stat) {
			$stats['gpu'][$count]['uuid'] 				= $gpu_stat['uuid'];
			$stats['gpu'][$count]['gpu_name'] 			= $gpu_stat['product_name'];

			$stats['gpu'][$count]['fan_speed'] 			= $gpu_stat['fan_speed'];

			$stats['gpu'][$count]['gpu_temp'] 			= $gpu_stat['temperature']['gpu_temp'];

			$stats['gpu'][$count]['graphics_clock'] 	= $gpu_stat['clocks']['graphics_clock'];
			$stats['gpu'][$count]['sm_clock']		 	= $gpu_stat['clocks']['sm_clock'];
			$stats['gpu'][$count]['mem_clock'] 			= $gpu_stat['clocks']['mem_clock'];
			$stats['gpu'][$count]['video_clock'] 		= $gpu_stat['clocks']['video_clock'];

			$stats['gpu'][$count]['total_ram'] 			= $gpu_stat['fb_memory_usage']['total'];
			$stats['gpu'][$count]['used_ram'] 			= $gpu_stat['fb_memory_usage']['used'];
			$stats['gpu'][$count]['free_ram'] 			= $gpu_stat['fb_memory_usage']['free'];

			$stats['gpu'][$count]['gpu_util'] 			= $gpu_stat['utilization']['gpu_util'];

			if(isset( $gpu_stat['processes']['process_info'] ) ) {
				$stats['gpu'][$count]['processes']			= $gpu_stat['processes']['process_info'];
			} else {
				$stats['gpu'][$count]['processes']		= '';
			}

			$count++;
		}
	} else {
		$stats['gpu'][$count]['uuid'] 				= $gpu_stats['gpu']['uuid'];
		$stats['gpu'][$count]['gpu_name'] 			= $gpu_stats['gpu']['product_name'];

		$stats['gpu'][$count]['fan_speed'] 			= $gpu_stats['gpu']['fan_speed'];

		$stats['gpu'][$count]['gpu_temp'] 			= $gpu_stats['gpu']['temperature']['gpu_temp'];

		$stats['gpu'][$count]['graphics_clock'] 	= $gpu_stats['gpu']['clocks']['graphics_clock'];
		$stats['gpu'][$count]['sm_clock']		 	= $gpu_stats['gpu']['clocks']['sm_clock'];
		$stats['gpu'][$count]['mem_clock'] 			= $gpu_stats['gpu']['clocks']['mem_clock'];
		$stats['gpu'][$count]['video_clock'] 		= $gpu_stats['gpu']['clocks']['video_clock'];

		$stats['gpu'][$count]['total_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['total'];
		$stats['gpu'][$count]['used_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['used'];
		$stats['gpu'][$count]['free_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['free'];

		$stats['gpu'][$count]['gpu_util'] 			= $gpu_stats['gpu']['utilization']['gpu_util'];

		if(isset( $gpu_stats['gpu']['processes']['process_info'] ) ) {
			$stats['gpu'][$count]['processes']		= $gpu_stats['gpu']['processes']['process_info'];
		} else {
			$stats['gpu'][$count]['processes']		= '';
		}
	}
	
	print_r( $stats);
}

if( $script_options['action'] == 'gpu_stats_raw' ) {
	$xml 		= simplexml_load_string(shell_exec( 'nvidia-smi -q -x' ) );
	
	$json 		= json_encode( $xml);

	$gpu_stats 	= json_decode( $json, true);

	print_r( $gpu_stats);

	$stats['driver_version'] 	= $gpu_stats['driver_version'];
	$stats['cuda_version'] 		= $gpu_stats['cuda_version'];
	
	$count = 0;

	if(isset( $gpu_stats['gpu'][0] ) ) {
		foreach( $gpu_stats['gpu'] as $gpu_stat) {
			$stats['gpu'][$count]['uuid'] 				= $gpu_stat['uuid'];
			$stats['gpu'][$count]['gpu_name'] 			= $gpu_stat['product_name'];

			$stats['gpu'][$count]['fan_speed'] 			= $gpu_stat['fan_speed'];

			$stats['gpu'][$count]['gpu_temp'] 			= $gpu_stat['temperature']['gpu_temp'];

			$stats['gpu'][$count]['graphics_clock'] 	= $gpu_stat['clocks']['graphics_clock'];
			$stats['gpu'][$count]['sm_clock']		 	= $gpu_stat['clocks']['sm_clock'];
			$stats['gpu'][$count]['mem_clock'] 			= $gpu_stat['clocks']['mem_clock'];
			$stats['gpu'][$count]['video_clock'] 		= $gpu_stat['clocks']['video_clock'];

			$stats['gpu'][$count]['total_ram'] 			= $gpu_stat['fb_memory_usage']['total'];
			$stats['gpu'][$count]['used_ram'] 			= $gpu_stat['fb_memory_usage']['used'];
			$stats['gpu'][$count]['free_ram'] 			= $gpu_stat['fb_memory_usage']['free'];

			$stats['gpu'][$count]['gpu_util'] 			= $gpu_stat['utilization']['gpu_util'];

			if(isset( $gpu_stat['processes']['process_info'] ) ) {
				$stats['gpu'][$count]['processes']			= $gpu_stat['processes']['process_info'];
			} else {
				$stats['gpu'][$count]['processes']		= '';
			}

			$count++;
		}
	} else {
		$stats['gpu'][$count]['uuid'] 				= $gpu_stats['gpu']['uuid'];
		$stats['gpu'][$count]['gpu_name'] 			= $gpu_stats['gpu']['product_name'];

		$stats['gpu'][$count]['fan_speed'] 			= $gpu_stats['gpu']['fan_speed'];

		$stats['gpu'][$count]['gpu_temp'] 			= $gpu_stats['gpu']['temperature']['gpu_temp'];

		$stats['gpu'][$count]['graphics_clock'] 	= $gpu_stats['gpu']['clocks']['graphics_clock'];
		$stats['gpu'][$count]['sm_clock']		 	= $gpu_stats['gpu']['clocks']['sm_clock'];
		$stats['gpu'][$count]['mem_clock'] 			= $gpu_stats['gpu']['clocks']['mem_clock'];
		$stats['gpu'][$count]['video_clock'] 		= $gpu_stats['gpu']['clocks']['video_clock'];

		$stats['gpu'][$count]['total_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['total'];
		$stats['gpu'][$count]['used_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['used'];
		$stats['gpu'][$count]['free_ram'] 			= $gpu_stats['gpu']['fb_memory_usage']['free'];

		$stats['gpu'][$count]['gpu_util'] 			= $gpu_stats['gpu']['utilization']['gpu_util'];

		if(isset( $gpu_stats['gpu']['processes']['process_info'] ) ) {
			$stats['gpu'][$count]['processes']		= $gpu_stats['gpu']['processes']['process_info'];
		} else {
			$stats['gpu'][$count]['processes']		= '';
		}
	}
	
	print_r( $stats);
}

if( $script_options['action'] == 'get_youtube_data' ) {
	$channel_id = $argv[2];

	console_output( "YouTube Data Parse" );

	ini_set( "user_agent","facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)" );

	function get_data( $url) {
	    $ch = curl_init();
	    $timeout = 5;
	    curl_setopt( $ch, CURLOPT_URL, $url);
	    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt( $ch, CURLOPT_USERAGENT, "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)" );
	    curl_setopt( $ch, CURLOPT_REFERER, "http://facebook.com" );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	    $data = curl_exec( $ch);
	    curl_close( $ch);
	    return $data;
	}

	$urlVideoDetails        = "https://www.youtube.com/get_video_info?video_id=".$channel_id."&el=detailpage";
	
	// $string = get_data( $urlVideoDetails);
	// $returnedData           = get_data( $urlVideoDetails);

	// restart tor to get a new IP
	shell_exec( "sudo /etc/init.d/tor restart" );

	sleep(5);

	$returnedData           	= shell_exec( "torify curl '".$urlVideoDetails."' " );

	// $parts = parse_url( $returnedData);
	parse_str( $returnedData, $query);
	
	print_r( $query);

	// parse json data
	$data = json_decode( $query['player_response'], true);
	print_r( $data);

	console_output( "Stream URL: " . $data['streamingData']['hlsManifestUrl'] );
}

if( $script_options['action'] == 'stream_loop_check' ) {
	console_output( "FFMPEG Unlink Bug Fix" );

	$hls_folder = '/opt/zeus/play/hls/';

	foreach( glob( $hls_folder."*.ts" ) as $file ) {
		if( time() - filectime( $file) > 180) {
			console_output( "Zombie Found: ".$file );
	    	unlink( $file );
	    }
	}

	console_output( "Finished" );
}

if( $script_options['action'] == 'hard_retart' ) {
	console_output( "Stiliam Hard Restart" );

	shell_exec( "service cron stop" );

	shell_exec( "killall ffmpeg" );
	sleep(1);
	shell_exec( "killall ffmpeg" );
	sleep(1);
	shell_exec( "killall ffmpeg" );
	sleep(1);
	shell_exec( "killall ffmpeg" );
	sleep(1);
	shell_exec( "killall ffmpeg" );

	shell_exec( "rm -rf /opt/zeus/play/hls/*" );

	shell_exec( "service cron restart" );

	console_output( "Finished" );

	echo "\n\n";
}

if( $script_options['action'] == 'nginx_sanity_check' ) {
	console_output( "NGINX Santiy Check" );

	// read nginx error log file
	$file = file( "/var/log/nginx/error.log" );

	// loop over the last 10 lines
	for( $i = max( 0, count( $file )-10 ); $i < count( $file ); $i++ ) {

		// check if the line contains a php-fpm error
		if (strpos( $file[$i], '111: Connection refused' ) !== false || strpos( $file[$i], '11: Resource temporarily unavailable' ) !== false) {
		    echo 'PHP-FPM ERROR - Restart needed.';

		    $nginx_restart = shell_exec( "sh /opt/zeus/scripts/nginx_update.sh" );

		    echo $nginx_restart;

		    die();
		}
	}

	console_output( "Finished" );

	echo "\n\n";
}

if( $script_options['action'] == 'vod_monitored_folder_scan' ) {
	if( $verbose == true ) {
		console_output( "Movies VoD: Library Folders" );
		console_output( "API ENDPOINT: http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	}

	$count 			= 0;
	$data_files		= array();

	$allowed_files 	= array( 'mk4','mkv','mp4','flv','avi','mpeg','ts','mov','wmv' );

	// get data about streams for this server
	$server 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	$server			= json_decode( $server, true);

	$existing_vods	= array();
	foreach( $server['vod_files'] as $existing_vod ) {
		$existing_vods[] = $existing_vod['file_location'];
	}

	if( $server['total_vod_monitored_folders'] > 0 ) {
		foreach( $server['vod_monitored_folders'] as $data ) {
			$parent_folder 		= $data['folder'];

			// santiy check for parent_folder
			$parent_folder_bits	= explode( "/", $parent_folder );
			$parent_folder_bits = array_filter( $parent_folder_bits );
			$parent_folder 		= "/".implode( "/", $parent_folder_bits );
			
			if( $verbose == true ) {
				console_output( "Parent Folder: ".$parent_folder );
			}

			// check if folder is there
			if( file_exists( $parent_folder ) ) {
				// get files including sub folder contents
				// check if folder is empty
				if( !is_dir_empty( $parent_folder ) ) {
					$files = getDirContents( $parent_folder );

					// loop over the results to get only what we need
					foreach( $files as $file ) {
					    // check if $file is a file or a folder
					    if( is_file( $file ) ) {
					        // check if the file is a desired format
					        $file_bits = pathinfo( $file );

					        if( isset( $file_bits['extension'] ) ) {
						        if( $file_bits['extension'] == 'avi' || $file_bits['extension'] == 'AVI' || $file_bits['extension'] == 'mkv' || $file_bits['extension'] == 'MKV' || $file_bits['extension'] == 'mp4' || $file_bits['extension'] == 'MP4' ) {
						            $data_files[$count] = $file;
						            $count++;
						        }
						    } else {
						    	if( $verbose == true ) {
									console_output( " - ERROR: file extension was not available. " );
									console_output( " - DEBUG OUTPUT: ".print_r( $file_bits ) );
								}
						    }
					    }
					}

					// print_r( $data_files);

					foreach( $data_files as $data_file ) {
						if( $verbose == true ) {
							console_output( "File: ".basename( $data_file ) );
						}

						if( !in_array( $data_file, $existing_vods ) ) {
							$encoded_file	= base64_encode( $data_file );
							
							if( $verbose == true ) {
								console_output( "- New VoD File" );
							}
							
							$submit 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=vod_add&server_uuid=".$config['server_guid']."&full_path=".$encoded_file );

							if( $verbose == true ) {
								console_output( "POST: http://".$config['cms']['server']."/api/?c=vod_add&server_uuid=".$config['server_guid']."&full_path=".$encoded_file );
							}
						} else {
							if( $verbose == true ) {
								console_output( "- Existing VoD File" );
							}
						}
					}
				} else {
					if( $verbose == true ) {
						console_output( "- Folder is empty" );
					}
				}
			} else {
				if( $verbose == true ) {
					console_output( "- Folder does not exist" );
				}
			}
		}
	}

	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}
}

if( $script_options['action'] == 'vod_tv_monitored_folder_scan' ) {
	if( $verbose == true ) {
		console_output( "TV VoD: Library Folders" );
		console_output( "API ENDPOINT: http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	}

	$count 			= 0;
	$data_files		= array();

	$allowed_files 	= array( 'mk4','mkv','mp4','flv','avi','mpeg','ts','mov','wmv' );

	// get data about streams for this server
	$server 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	$server			= json_decode( $server, true);

	$existing_vods	= array();
	foreach( $server['vod_tv_files'] as $existing_vod ) {
		$existing_vods[] = $existing_vod['file_location'];
	}

	if( $server['total_vod_tv_monitored_folders'] > 0 ) {
		foreach( $server['vod_tv_monitored_folders'] as $data ) {
			$parent_folder 		= $data['folder'];

			// get last character of $last_path
			$last_character = substr( $parent_folder, -1 );

			// sanity check
			if( $last_character == '/' ) {
				$parent_folder = rtrim( $parent_folder, "/" );
			}

			// santiy check for parent_folder
			$parent_folder_bits	= explode( "/", $parent_folder );
			$parent_folder_bits = array_filter( $parent_folder_bits );
			$parent_folder 		= "/".implode( "/", $parent_folder_bits );
			
			if( $verbose == true ) {
				console_output( "Parent Folder: ".$parent_folder );
			}

			// check if folder is there
			if( file_exists( $parent_folder ) ) {
				// get files including sub folder contents
				// check if folder is empty
				if( !is_dir_empty( $parent_folder ) ) {
					// get the shows inside this monitored folder
					$tv_shows = glob( $parent_folder.'/*', GLOB_ONLYDIR);

					// cycle each tv show
					foreach( $tv_shows as $tv_show ) {
						// create blank array
						$tv_show_data = array();
						
						// set the show full path
						$tv_show_data['path'] = $tv_show;

						// get the show folder name
						$tv_show_data['folder'] = str_replace( $parent_folder.'/', '', $tv_show );
						$tv_show_data['encoded_folder'] = base64_encode( $tv_show_data['folder'] );

						if( $verbose == true ) {
							console_output( "- TV Show Path: ".$tv_show_data['path'] );
							console_output( "- TV Show Folder: ".$tv_show_data['folder'] );
						}

						// submit the show to cms for master asset creation
						$vod_tv_show = @file_get_contents( "http://".$config['cms']['server']."/api/?c=vod_tv_add&server_uuid=".$config['server_guid']."&show=".$tv_show_data['encoded_folder'] );
						$vod_tv_show = json_decode( $vod_tv_show, true );

						if( $verbose == true ) {
							console_output( "- SHOW POST: http://".$config['cms']['server']."/api/?c=vod_tv_add&server_uuid=".$config['server_guid']."&show=".$tv_show_data['encoded_folder'] );
						}

						// scan this tv_show folder for containing files
						$tv_show_data['files'] = getDirContents( $tv_show_data['path'] );

						// remove dirs
						foreach( $tv_show_data['files'] as $key => $value ) {
							// ignore subfolders
							if( !is_dir( $value ) ) {
								// check to see if we already know about this episode
								if( !in_array( $value, $existing_vods ) ) {

									if( $verbose == true ) {
										console_output( "- EPISODE NAME: ".$value );
									}

									// encode for sending data
									$excoded_episode = base64_encode( $value );

									// submit the filename with show id
									$submit_episode = @file_get_contents( "http://".$config['cms']['server']."/api/?c=vod_tv_episode_add&server_uuid=".$config['server_guid']."&vod_tv_id=".$vod_tv_show['vod_tv_id']."&episode=".$excoded_episode );

									if( $verbose == true ) {
										console_output( "- EPISODE POST: http://".$config['cms']['server']."/api/?c=vod_tv_episode_add&server_uuid=".$config['server_guid']."&vod_tv_id=".$vod_tv_show['vod_tv_id']."&episode=".$excoded_episode );
									}

									if( $verbose == true ) {
										console_output( ' ' );
									}
								} else {
									if( $verbose == true ) {
										console_output( "- Existing Episode File" );
									}
								}
							}
						}
					}
				} else {
					if( $verbose == true ) {
						console_output( "- Folder is empty" );
					}
				}
			} else {
				if( $verbose == true ) {
					console_output( "- Folder does not exist" );
				}
			}
		}
	}

	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}
}

if( $script_options['action'] == 'channels_247_monitored_folder_scan' ) {
	if( $verbose == true ) {
		console_output( "24/7 Channels: Library Folders" );
		console_output( "API ENDPOINT: http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	}

	$count 			= 0;
	$data_files		= array();

	$allowed_files 	= array( 'mk4','mkv','mp4','flv','avi','mpeg','ts','mov','wmv' );

	// get data about streams for this server
	$server 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	$server			= json_decode( $server, true);

	$existing_vods	= array();
	foreach( $server['channels_247_files'] as $existing_vod ) {
		$existing_vods[] = $existing_vod['file_location'];
	}

	if( $server['total_channels_247_monitored_folders'] > 0 ) {
		foreach( $server['channels_247_monitored_folders'] as $data ) {
			$parent_folder 		= $data['folder'];

			// get last character of $last_path
			$last_character = substr( $parent_folder, -1 );

			// sanity check
			if( $last_character == '/' ) {
				$parent_folder = rtrim( $parent_folder, "/" );
			}

			// santiy check for parent_folder
			$parent_folder_bits	= explode( "/", $parent_folder );
			$parent_folder_bits = array_filter( $parent_folder_bits );
			$parent_folder 		= "/".implode( "/", $parent_folder_bits );
			
			if( $verbose == true ) {
				console_output( "Parent Folder: ".$parent_folder );
			}

			// check if folder is there
			if( file_exists( $parent_folder ) ) {
				// get files including sub folder contents
				// check if folder is empty
				if( !is_dir_empty( $parent_folder ) ) {
					// get the shows inside this monitored folder
					$tv_shows = glob( $parent_folder.'/*', GLOB_ONLYDIR);

					// cycle each tv show
					foreach( $tv_shows as $tv_show ) {
						// create blank array
						$tv_show_data = array();
						
						// set the show full path
						$tv_show_data['path'] = $tv_show;

						// get the show folder name
						$tv_show_data['folder'] = str_replace( $parent_folder.'/', '', $tv_show );
						$tv_show_data['encoded_folder'] = base64_encode( $tv_show_data['folder'] );

						if( $verbose == true ) {
							console_output( "- TV Show Path: ".$tv_show_data['path'] );
							console_output( "- TV Show Folder: ".$tv_show_data['folder'] );
						}

						// submit the show to cms for master asset creation
						$vod_tv_show = @file_get_contents( "http://".$config['cms']['server']."/api/?c=channels_247_add&server_uuid=".$config['server_guid']."&show=".$tv_show_data['encoded_folder'] );
						$vod_tv_show = json_decode( $vod_tv_show, true );

						if( $verbose == true ) {
							console_output( "- SHOW POST: http://".$config['cms']['server']."/api/?c=channels_247_add&server_uuid=".$config['server_guid']."&show=".$tv_show_data['encoded_folder'] );
						}

						// scan this tv_show folder for containing files
						$tv_show_data['files'] = getDirContents( $tv_show_data['path'] );

						// remove dirs
						foreach( $tv_show_data['files'] as $key => $value ) {
							// ignore subfolders
							if( !is_dir( $value ) ) {
								// check to see if we already know about this episode
								if( !in_array( $value, $existing_vods ) ) {

									if( $verbose == true ) {
										console_output( "- EPISODE NAME: ".$value );
									}

									// encode for sending data
									$excoded_episode = base64_encode( $value );

									// submit the filename with show id
									$submit_episode = @file_get_contents( "http://".$config['cms']['server']."/api/?c=channels_247_episode_add&server_uuid=".$config['server_guid']."&vod_tv_id=".$vod_tv_show['vod_tv_id']."&episode=".$excoded_episode );

									if( $verbose == true ) {
										console_output( "- EPISODE POST: http://".$config['cms']['server']."/api/?c=channels_247_episode_add&server_uuid=".$config['server_guid']."&vod_tv_id=".$vod_tv_show['vod_tv_id']."&episode=".$excoded_episode );
									}

									if( $verbose == true ) {
										console_output( ' ' );
									}
								} else {
									if( $verbose == true ) {
										console_output( "- Existing Episode File" );
									}
								}
							}
						}
					}
				} else {
					if( $verbose == true ) {
						console_output( "- Folder is empty" );
					}
				}
			} else {
				if( $verbose == true ) {
					console_output( "- Folder does not exist" );
				}
			}
		}
	}

	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}
}

if( $script_options['action'] == 'channels_247_manager' ) {
	if( $verbose == true ) {
		console_output( '24/7 Channels: Manager' );
		console_output( 'API ENDPOINT: http://'.$config['cms']['server'].'/api/?c=server&server_uuid='.$config['server_guid'] );
	}

	$source_file_type = 'txt';

	// get data about channel_247 for this server
	$server 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	$server			= json_decode( $server, true );

	// do we have any channels_247 configured
	if( isset( $server['channels_247'] ) && is_array( $server['channels_247'] ) ) {
		if( $verbose == true ) {
			console_output( ' ' );
		}
		// we do, loop over them
		foreach( $server['channels_247'] as $data ) {
			if( $verbose == true ) {
				console_output( '24/7 Channel:  '.stripslashes( $data['title'] ) );
			}

			// build the playlist
			$playlist = 'ffconcat version 1.0'.$new_line;

			// check stream yes or not
			if( $data['stream'] == 'yes' ) {
				// sanity check
				if( isset( $data['files'] ) && is_array( $data['files'] ) ) {
					// check the files if stream = yes
					if( $data['stream'] == 'yes' ) {
						// loop over the files for this channel
						foreach( $data['files'] as $file ) {
							// is this a local or remote file and check if either exists
							if( filter_var( $file['file'], FILTER_VALIDATE_URL ) ) { 
								// its a url, check if its a valid url to use
								$ch = curl_init( $file['file'] );
							    curl_setopt( $ch, CURLOPT_NOBODY, true );
							    curl_exec( $ch );
							    $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
							    curl_close( $ch );

							    // url is valid and available, add it
							    if ( $code == 200) {
							        // remote url found
							        // console_output( "Remote file found: '".$file['file']."'" );
							        $playlist .= "file '".$file['file']."'".$new_line;
							    } else {
							        // remote url not found
							        if( $verbose == true ) {
										console_output( '- ERROR - Remote file NOT found: "'.$file['file'].'" ' );
									}
							    }
							} elseif ( file_exists( $file['file'] ) ) {
								// file found
								// console_output( "Local file found: '".$file['file']."'" );
								$playlist .= "file '".$file['file']."'".$new_line;
							} else {
								// file not found
								if( $verbose == true ) {
									console_output( '- ERROR - Local file NOT found: "'.$file['file'].'" ' );
								}
							}
						}
					}

					// make the stream folder
					// shell_exec( "mkdir -p /var/www/html/play/hls/channel_".$data['id'] );

					// write the m3u file
					@file_put_contents( '/var/www/html/play/hls/channel_247_'.$data['id'].'_playlist.txt', $playlist );

					// ffmpeg -re -y -hide_banner -loglevel info -fflags +genpts -safe 0 -f concat -i '/media/slipstream/uploads/the.simpsons.txt' -map 0:a? -map 0:v? -map 0:s? -strict -2 -dn -c copy -hls_flags delete_segments -hls_time 10 -hls_list_size 6 /var/www/html/play/hls/1372/index.m3u8

					// build ffmpeg command
					$cmd =  "ffmpeg ";
					$cmd .= "-y ";
					$cmd .= "-nostdin ";
					$cmd .= "-hide_banner ";
					$cmd .= "-loglevel warning ";
					$cmd .= "-err_detect ignore_err ";
					// $cmd .= "-nofix_dts ";
					$cmd .= "-start_at_zero ";
					$cmd .= "-copyts ";
					$cmd .= "-vsync 0 ";
					$cmd .= "-correct_ts_overflow 0 ";
					$cmd .= "-avoid_negative_ts disabled ";
					$cmd .= "-max_interleave_delta 0 ";
					$cmd .= "-re ";
					$cmd .= "-probesize 5000000 ";
					$cmd .= "-analyzeduration 5000000 ";
					$cmd .= "-safe 0 ";
					$cmd .= "-protocol_whitelist 'file,http,https,tcp,tls,rtmp,hls,rtsp,rtp' ";
					$cmd .= "-i '/var/www/html/play/hls/channel_247_".$data['id']."_playlist.txt' ";
					$cmd .= "-threads 1 ";
					$cmd .= "-strict -2 ";
					$cmd .= "-dn ";
					$cmd .= "-sn ";
					$cmd .= "-c:v copy ";
					$cmd .= "-c:a copy ";
					$cmd .= "-copy_unknown ";

					// hls options
					$cmd .= "-sc_threshold 0 ";
					$cmd .= "-flags -global_header ";
					$cmd .= "-hls_flags delete_segments ";
					$cmd .= "-hls_time 4 ";
					$cmd .= "-hls_list_size 3 ";
					$cmd .= "-hls_flags delete_segments ";
					$cmd .= "-f hls /var/www/html/play/hls/channel_247_".$data['id']."_.m3u8 ";
					
					// get the running PID
					$pid = exec( "ps aux | grep '/var/www/html/play/hls/channel_247_".$data['id']."_.m3u8' | grep -v 'grep' | awk '{print $2}'" );

					// stream is enabled and no pid
					if( $data['stream'] == 'yes' && empty( $pid ) ) {
						if( $verbose == true ) {
							console_output( '- Starting stream' );
						}

						// run ffmpeg
						shell_exec( $cmd . ' > /opt/zeus/logs/channel_247_'.$data['id'].'.log 2>&1 & echo $!' );

						if( $verbose == true ) {
							$cmd_nice = str_replace( " -", "\n-", $cmd);

							echo "\n";
							echo "====================================================================================================\n";
							echo $cmd . "\n";
							echo "\n";
							echo $cmd_nice . "\n";
							echo "====================================================================================================\n";
							echo "\n";
						}

						// post channel data
						$post_update =@file_get_contents( "http://".$config['cms']['server']."/api/?c=channel_247_status_update&id=".$data['id']."&status=starting" );

						if( $verbose == true ) {
							console_output( '- UPDATE API ENDPOINT - http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=starting' );
						}
					} 

					// stream is set to yet and it looks like its running
					if( $data['stream'] == 'yes' && !empty( $pid ) ) {
						// stream appears to be running
						$pid_runtime = shell_exec( 'ps -p '.$pid.' -o etimes=' );
						$pid_runtime = trim( $pid_runtime );

						// post data
						$post_update =@file_get_contents( 'http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=online&uptime='.$pid_runtime );

						if( $verbose == true ) {
							console_output( '- Checking stream' );
							console_output( '- PID: '.$pid );
							console_output( '- Status: '.$colors->getColoredString( "online.", "green", "black" ) );
							console_output( '- UPDATE API ENDPOINT - http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=online&uptime='.$pid_runtime );
						}
					}

					// stream set to no, lets find and kill it
					if( $data['stream'] == 'no' ) {
						// kill the running_pid
						if( !empty( $pid ) ) {
							exec( 'sudo kill -9 ' . $pid);
						}

						// clean up old files
						exec( 'rm -rf /var/www/html/play/hls/channel_247_'.$data['id'].'_*' );

						// post channel data
						$post_update =@file_get_contents( 'http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=offline' );

						if( $verbose == true ) {
							console_output( '- Stopping stream' );
							console_output( '- UPDATE API ENDPOINT - http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=offline' );
						}
					}

					if( $verbose == true ) {
						console_output( "--------------------------------------------------" );
					}
				} else {
					if( $verbose == true ) {
						console_output( "--------------------------------------------------" );
					}
				}
			} else {
				// clean up old files
				exec( 'rm -rf /var/www/html/play/hls/channel_247_'.$data['id'].'_*' );

				// check if its still running
				$pid = exec( "ps aux | grep '/var/www/html/play/hls/channel_247_".$data['id']."_.m3u8' | grep -v 'grep' | awk '{print $2}'" );
				if( !empty( $pid ) ) {
					if( $verbose == true ) {
						console_output( '- Killing PID: '.$pid );
					}

					shell_exec( "sudo kill -9 ".$pid );
				}

				// post channel data
				$post_update = @file_get_contents( 'http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=offline' );

				if( $verbose == true ) {
					console_output( '- Marking offline' );
					console_output( '- UPDATE API ENDPOINT - http://'.$config['cms']['server'].'/api/?c=channel_247_status_update&id='.$data['id'].'&status=offline' );
					console_output( "--------------------------------------------------" );
				}
			}
		}
	} else {
		if( $verbose == true ) {
			console_output( "No 24/7 Channels" );
		}
	}

	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}

	killlock();
}

if( $script_options['action'] == 'rtmp_gatekeeper' ) {
	// sent ntfy
	send_ntfy( 'Zues Core', 'RTMP Gatekeeper has started.', 'default', 'core');

	console_output( 'RTMP Gatekeeper' );

	# loop like a deamon
	while (true) {
		// Fetch the RTMP status XML from Nginx
		$statusUrl = 'http://localhost/stat';
		$statusXml = @file_get_contents( $statusUrl );

		// Check if the request was successful
		if( $statusXml === FALSE ) {
			die( 'Error fetching RTMP status.' );
		}

		// Parse the XML
		$xml = simplexml_load_string( $statusXml );

		// Convert the XML into a PHP array
		$json = json_encode( $xml );
		$statusArray = json_decode( $json, true );

		// Get the list of active streams
		$streams = array();

		// Check if the 'server' and 'application' keys exist
		if( isset( $statusArray['server']['application'] ) ) {
			$application = $statusArray['server']['application'];
			// Check if the application name is 'live' and it has 'live' key with 'stream'
			if( $application['name'] === 'live' && isset( $application['live']['stream'] ) ) {
				// Check if stream is an array (multiple streams) or a single stream
				if( isset( $application['live']['stream'][0] ) ) {
					// Multiple streams
					$streams[] = $application['live']['stream'];
				} else {
					// Single stream
					$streams[] = $application['live']['stream'];
				}
			}
		}

		// dirty hack
		if( isset( $streams[0][0] ) ) {
			$streams = $streams[0];
		}

		// Print array
		// print_r( $streams );

		// sanity check
		if( isset( $streams[0] ) ) {
			if( $verbose == true ) {
				console_output( '  ' );
				console_output( 'Total RTMP Streams: '.count( $streams ) );
			} else {
				console_output( '  ' );
				console_output( 'Total RTMP Streams: '.count( $streams ) );
			}

			// loop of each stream
			foreach( $streams as $stream ) {
				// sanity check for $stream['meta']['video']['height'] presence 
				if( isset( $stream['meta']['video']['height'] ) ) {
					if( $verbose == true ) {
						console_output( ' -- RTMP GUID: '.$stream['name'] );
						console_output( ' --- Screen Resolution: '.$stream['meta']['video']['height'].'x'.$stream['meta']['video']['width'] );
					}

					// rtmp key sanity check
					$rtmp_security_check = false;
					$allowed_rtmp_keys = @file_get_contents( 'https://'.$config['cms']['server'].'/api/?c=allowed_rtmp_streams&auth=c5282c67-d9a8-492f-9d16-d5aea4190726' );
					$allowed_rtmp_keys = json_decode( $allowed_rtmp_keys, true );

					// sanity check
					if( !isset( $allowed_rtmp_keys[0] ) ) {
						if( $verbose == true ) {
							console_output(  '!!! $allowed_rtmp_keys did not populate correctly, waiting 10 seconds and trying again' );
						}

						// sent urgent ntfy warning
						send_ntfy( 'Zues RTMP Error', 'FS RTMP Keys could not be pulled from the API.');

						// sleep for 10 seconds
						sleep( 1 );
					} else {
						foreach( $allowed_rtmp_keys as $allowed_rtmp_key ) {
							if( $allowed_rtmp_key['rtmp_guid'] == $stream['name'] ) {
								$stream['fs_stream_data'] = $allowed_rtmp_key;
								$rtmp_security_check = true;
								break;
							}
						}

						if( $rtmp_security_check == false ) {
							if( $verbose == true ) {
								console_output( ' --- RTMP KEY NOT ALLOWED - TERMINATING STREAM' );
							}

							shell_exec( "curl -X GET 'http://localhost/control/drop/publisher?app=live&name=".$stream['name']."'" );
						} else {
							// is the stream already being relayed
							$existing_stream = shell_exec( 'ps aux | grep '.$stream['name'].' | grep -v grep | grep -v tail | wc -l' );
							$existing_stream = trim( $existing_stream );
							if( $verbose == true ) {
								console_output( ' --- Existing Stream: '.$existing_stream );
							}

							// is $existing_stream == 0 then restream to transcoding server
							if( $existing_stream == 0 ) { 
								// check for LAN connections
								if( empty( $stream['client']['address'] ) ) {
									$stream['client']['address'] = '192.168.1.1';
								}

								// lets find which transcoding server to restream to
								if( $verbose == true ) {
									console_output( ' --- API Get Transcoding Server: https://'.$config['cms']['server'].'/api/?c=add_stream&server_guid='.$config['server_guid'].'&rtmp_guid='.$stream['name'].'&hls_guid='.$stream['fs_stream_data']['hls_guid'].'&publisher_ip='.$stream['client']['address'] );
								}
								$transcoding_server = @file_get_contents( 'https://'.$config['cms']['server'].'/api/?c=add_stream&server_guid='.$config['server_guid'].'&rtmp_guid='.$stream['name'].'&hls_guid='.$stream['fs_stream_data']['hls_guid'].'&publisher_ip='.$stream['client']['address'] );
								$transcoding_server = json_decode( $transcoding_server, true );

								if( $verbose == true ) {
									console_output( ' --- Transcode Server: '.$transcoding_server['result']['streaming_ip_address'] );
								}

								// clean up old files
								exec('rm -rf /mnt/streaming/hls/'.$stream['name']."*" );

								// build restream command
								$restream_cmd = "ffmpeg -y -threads 1 -i rtmp://localhost/live/".$stream['name']." -c copy -f flv rtmp://".$transcoding_server['result']['streaming_ip_address']."/1080_in_no_recording/".$stream['fs_stream_data']['hls_guid'];
								if( $verbose == true ) {
									console_output( ' --- Restream Command: '.$restream_cmd );
								}

								// run restream command
								$restream_cmd_pid = shell_exec( "nohup ".$restream_cmd." > /opt/zeus/logs/".$stream['name'].".log 2>&1 & " );
								if( $verbose == true ) {
									console_output( ' --- Process ID: ' . $restream_cmd_pid );
								}

								// post data to the cms
								// @file_get_contents( 'https://'.$config['cms']['server'].'/api/?c=update_stream&rtmp_guid='.$stream['name'].'&transcoder_ip='.$transcoding_server['result']['ip_address'].'&pid='.$restream_cmd_pid );

								if( $verbose == true ) {
									console_output( '' );
								}
							} else {
								if( $verbose == true ) {
									console_output( ' --- Restream is already active.' );
								}
							}
						}
					}
				} else {
					if( $verbose == true ) {
						console_output( ' -- RTMP GUID: '.$stream['name'] );
						console_output( ' --- Stream is not ready for restreaming yet, skipping for now.' );
					}
				}
			}
		} else {
			if( $verbose == true ) {
				console_output( ' - No RTMP streams.' );
			}

			// get list of streams
			$get_streams = @file_get_contents( 'https://'.$config['cms']['server'].'/api/?c=get_streams' );
			$get_streams = json_decode( $get_streams, true );

			if( $verbose == true ) {
				console_output( 'https://'.$config['cms']['server'].'/api/?c=get_streams' );
				print_r( $get_streams );
			}

			// check streams to see if they are still running
			foreach( $get_streams['data'] as $stream ) {
				// ps aux | grep ab72853c-060b-4938-9adf-2a53274ec642 | grep -v grep | awk '{print $2}'
				$old_stream = shell_exec( 'ps aux | grep '.$stream['rtmp_guid'].' | grep -v grep | grep -v tail | wc -l' );
				$old_stream = trim( $old_stream );

				// clean up old stream
				if( $old_stream != 0 ) {
					// get ffmpeg pid to kill
					$running_pid = exec( "ps aux | grep ".$stream['rtmp_guid']." | grep -v grep | awk '{print $2}'" );

					// kill ffmpeg pid
					exec( "kill -9 ".$running_pid );

					// push details to the cms
					$clean_up = @file_get_contents( 'https://'.$config['cms']['server'].'/api/?c=remove_stream&rtmp_guid='.$stream['rtmp_guid'] );
					$clean_up = json_decode( $clean_up, true );

					if( $verbose == true ) {
						console_output( 'https://'.$config['cms']['server'].'/api/?c=remove_stream&rtmp_guid='.$stream['rtmp_guid'] );
						print_r( $clean_up );
					}
				}
			}
		}

		// sleep
		sleep( 10 ); // prevent high CPU usage
	}
}

if( $script_options['action'] == 'channels_manager' ) {
	if( $verbose == true ) {
		console_output( 'Live Channels: Manager' );
		console_output( 'API ENDPOINT: http://'.$config['cms']['server'].'/api/?c=server&server_uuid='.$config['server_guid'] );
	}

	// exec( "mkdir -p " );

	// how many times to process this job
	$runs = 1;

	// get data about channel for this server
	$server 		= @file_get_contents( "http://".$config['cms']['server']."/api/?c=server&server_uuid=".$config['server_guid'] );
	$server			= json_decode( $server, true );

	if( is_array( $server['channels'] ) ) {
		foreach( $server['channels'] as $channel ) {
			// primary vs secondary
			$channel['topology'] 		= unserialize( $channel['topology'] );
			$channel_type = 'none';
			if( is_array( $channel['topology'] ) ) {
				foreach( $channel['topology'] as $topology_server ) {
					if( $topology_server['server_id'] == $server['id'] ) {
						if( $topology_server['type'] == 'primary' ) {
							$channel_type = 'primary';
						} elseif ( $topology_server['type'] == 'secondary' ) {
							$channel_type = 'secondary';
						}
						break;
					}
				}
			}

			// is this channel assigned to this server?
			if( $channel_type != 'none' ) {
				// check if channel is already running, report online and dont spawn child is running
				if( $channel['stream'] == 'yes' && $channel['status'] == 'online' || $channel['stream'] == 'yes' && $channel['status'] == 'offline' ) {
					// ffprobe the output
					$channel['output_probe'] = shell_exec( 'timeout 1 ffprobe -v quiet -print_format json -show_format -show_streams /var/www/html/play/hls/'.$channel['id'].'_.m3u8' );
					$channel['output_probe'] = json_decode( $channel['output_probe'], true );

					// get running pid
					$pid = exec( "ps aux | grep '/var/www/html/play/hls/".$channel['id']."_.m3u8' | grep -v 'grep' | grep -v 'sudo' | awk '{print $2}' " );

					// work with results
					if( isset( $channel['output_probe']['streams'] ) ) {
						// check for dupes
						$instances = exec( "ps aux | grep 'ffmpeg' | grep '/var/www/html/play/hls/".$channel['id']."_.m3u8' | grep -v 'grep' | grep -v 'sudo' | wc -l" );
						if( $instances > 1) {
							if( $verbose == true ) {
								console_output( 'Channel: "'.stripslashes( $channel['title'] ).'" -> ' . $colors->getColoredString( "duplicates, restarting", "red", "black" ) );
							}

							// get the pids for all instances
							$dupe_pids = shell_exec( "ps aux | grep '/var/www/html/play/hls/".$channel['id'].".log' | grep -v 'grep' | awk '{print $2}'" );
							
							$dupe_pids = explode( PHP_EOL, $dupe_pids );
							
							foreach( $dupe_pids as $dupe_pid ) {
								if( !empty( $dupe_pid ) ) {
									if( $verbose == true ) {
										console_output( " - >>> Killing PID: ".$dupe_pid );
									}
									exec( "sudo kill -9 ".$dupe_pid );
								}
							}
						} else {
							if( $verbose == true ) {
								console_output( 'Channel: "'.stripslashes( $channel['title'] ).'" -> ' . $colors->getColoredString( "online", "green", "black" ) );
							}

							// get screen resolution
							if( isset( $channel['output_probe']['streams'] ) ) {
								foreach( $channel['output_probe']['streams'] as $bits ) {
									if( $bits['codec_type'] == 'video' ) {
										$channel_stats_extras					= channel_stats( $channel['id'] );
										$channel_stats['speed']					= $channel_stats_extras['speed'];
										$channel_stats['fps']					= $channel_stats_extras['fps'];
										$channel_stats['width'] 				= $bits['width'];
										$channel_stats['height'] 				= $bits['height'];
										if( isset( $bits['pix_fmt'] ) ) {
											$channel_stats['pix_fmt'] 			= $bits['pix_fmt'];
										} else {
											$channel_stats['pix_fmt'] 			= '';
										}
										if( isset( $bits['display_aspect_ratio'] ) ) {
											$channel_stats['aspect_ratio'] 		= $bits['display_aspect_ratio'];
										} else {
											$channel_stats['aspect_ratio']		= '';
										}

										if( isset( $bits['codec_name'] ) ) {
											$channel_stats['video_codec']		= $bits['codec_name'];
										} else {
											$channel_stats['video_codec']		= 'unknown';
										}
									}

									if( $bits['codec_type'] == 'audio' ) {
										$channel_stats['audio_codec'] 			= $bits['codec_name'];
									} else {
										$channel_stats['audio_codec'] 			= 'unknown';
									}
								}
							} else {
								$channel_stats = array();
								$channel_stats['audio_codec'] 					= '';
								$channel_stats['video_codec']					= '';
								$channel_stats['width']							= '';
								$channel_stats['height']						= '';
								$channel_stats['pix_fmt']						= '';
								$channel_stats['aspect_ratio']					= '';
								$channel_stats['fps']							= '';
								$channel_stats['speed']							= '';
							}

							// get uptime
							$channel_stats['uptime'] = exec( "ps -p ".$pid." -o etimes=" );
							$channel_stats['uptime'] = trim( $channel_stats['uptime'] );

							// get stream bitrate
							$hls_bits = glob( '/var/www/html/play/hls/'.$channel['id'].'_*.ts' );

							// parse stream bitrate results
							if( is_array( $hls_bits ) ) {
								$hls_bits = array_reverse( $hls_bits );
								if( $verbose == true ) {
									// console_output( "FFPROBE command: timeout 30 ffprobe -v quiet -print_format json -show_format -show_streams '".$hls_bits[2]."' " );
								}
								$hls_segment_test = shell_exec( "timeout 1 ffprobe -v quiet -print_format json -show_format -show_streams '".$hls_bits[0]."' " );
								$hls_segment_test = json_decode( $hls_segment_test, true );
								$channel_stats['bitrate'] = ( isset( $hls_segment_test['format']['bit_rate'] ) ? $hls_segment_test['format']['bit_rate'] : '0' );
							}

							// post channel data
							$channel_stats_json = json_encode( $channel_stats );
							$channel_stats_base64 = base64_encode( $channel_stats_json );
							if( $verbose == true ) {
								console_output( "API Channel Update API: http://".$config['cms']['server']."/api/?c=channel_status_update&id=".$channel['id']."&server_id=".$server['id']."&server_type=".$channel_type."&status=online&pid=".$pid."&uptime=".$channel_stats['uptime']."&speed=".$channel_stats['speed']."&fps=".$channel_stats['fps']."&resolution_w=".$channel_stats['width']."&resolution_h=".$channel_stats['height']."&aspect_ratio=".$channel_stats['aspect_ratio']."&bitrate=".$channel_stats['bitrate']."&video_codec=".$channel_stats['video_codec']."&audio_codec=".$channel_stats['audio_codec']."&channel_stats=".$channel_stats_base64 );
							}
							
							// $channel_update = @file_get_contents( "http://".$config['cms']['server']."/api/?c=channel_status_update&id=".$channel['id']."&server_id=".$server['id']."&server_type=".$channel_type."&status=online&pid=".$pid."&uptime=".$channel_stats['uptime']."&speed=".$channel_stats['speed']."&fps=".$channel_stats['fps']."&resolution_w=".$channel_stats['width']."&resolution_h=".$channel_stats['height']."&aspect_ratio=".$channel_stats['aspect_ratio']."&bitrate=".$channel_stats['bitrate']."&video_codec=".$channel_stats['video_codec']."&audio_codec=".$channel_stats['audio_codec']."&channel_stats=".$channel_stats_base64 );

							exec( "nohup curl -s 'http://".$config['cms']['server']."/api/?c=channel_status_update&id=".$channel['id']."&server_id=".$server['id']."&server_type=".$channel_type."&status=online&pid=".$pid."&uptime=".$channel_stats['uptime']."&speed=".$channel_stats['speed']."&fps=".$channel_stats['fps']."&resolution_w=".$channel_stats['width']."&resolution_h=".$channel_stats['height']."&aspect_ratio=".$channel_stats['aspect_ratio']."&bitrate=".$channel_stats['bitrate']."&video_codec=".$channel_stats['video_codec']."&audio_codec=".$channel_stats['audio_codec']."&channel_stats=".$channel_stats_base64 . "' 2>/dev/null & ");
						}
					} else {
						$channels[] = $channel['id'];

						if( $verbose == true ) {
							console_output('Channel: "'.stripslashes( $channel['title'] ).'" -> ' . $colors->getColoredString( "Local M3U failed, Auto Restarting Channel", "yellow", "black" ) );
						}
					}
				} elseif ( $channel['stream'] == 'no' && $channel['status'] != 'offline' ) { // stop any needed channels
					// marked as do not stream
					if( $verbose == true ) {
						console_output('Channel: "'.stripslashes( $channel['title'] ).'" -> ' . $colors->getColoredString( "Stopping", "red", "black" ) );
					}
					
					// see if anything is running and needs to be terminated
					$pid = exec( "ps aux | grep '/var/www/html/play/hls/".$channel['id']."_.m3u8' | grep -v 'grep' | grep -v 'sudo' | awk '{print $2}' " );

					// kill the running_pid
					if( !empty( $pid ) ) {
						exec('sudo kill -9 ' . $pid);
					}

					// clean up old files
					exec('rm -rf /var/www/html/play/hls/'.$channel['id']."_*" );
					exec('rm -rf /var/www/html/logs/'.$channel['id'].".log" );

					// post channel data
					if( $verbose == true ) {
						console_output( "API Channel Update API: http://".$config['cms']['server']."/api/?c=channel_status_update&id=".$channel['id']."&server_id=".$server['id']."&server_type=".$channel_type."&status=offline" );
					}
					// $channel_update = @file_get_contents( "http://".$config['cms']['server']."/api/?c=channel_status_update&id=".$channel['id']."&server_id=".$server['id']."&server_type=".$channel_type."&status=offline" );

					exec( "nohup curl -s 'http://".$config['cms']['server']."/api/?c=channel_status_update&id=".$channel['id']."&server_id=".$server['id']."&server_type=".$channel_type."&status=offline' & " );
				} elseif ( $channel['stream'] == 'no' && $channel['status'] == 'offline' ) { // channel already stopped
					// marked as do not stream
					if( $verbose == true ) {
						console_output('Channel: "'.stripslashes( $channel['title'] ).'" -> ' . $colors->getColoredString( "Already Stopped", "red", "black" ) );
					}
					
					// see if anything is running and needs to be terminated
					$pid = exec( "ps aux | grep '/var/www/html/play/hls/".$channel['id']."_.m3u8' | grep -v 'grep' | grep -v 'sudo' | awk '{print $2}' " );

					// kill the running_pid
					if( !empty( $pid ) ) {
						exec('sudo kill -9 ' . $pid);
					}

					// clean up old files
					exec('rm -rf /var/www/html/play/hls/'.$channel['id']."_*" );
					exec('rm -rf /var/www/html/logs/'.$channel['id'].".log" );
				} else { // build $channels
					$channels[] = $channel['id'];
				}
			}
		}

		if( isset( $channels ) ) {
			if( $verbose == true ) {
				console_output( 'Channel IDs: '.implode( ',', $channels ) );
			}

			// count number of streams to process
			$count 				= count( $channels );

			if( $verbose == true ) {
				console_output( "Processing ".$count." channels out of ".count( $server['channels'] )." total channels." );
			}

			// process $streams with popen
			for ( $i=0; $i<$runs; $i++) {
		        for ( $j=0; $j<$count; $j++) {
		        	if( $verbose == true ) {
		        		console_output( "Spawning: php -q /opt/zeus/console/console.php --action=channel_slave --server_id=".$server['id']." --channel_id=".$channels[$j] );
		        	}

		        	if ( $j % 25 != 0) {
		            	$pipe[$j] = popen( "php -q /opt/zeus/console/console.php --action=channel_slave --server_id=".$server['id']." --channel_id=".$channels[$j]." 2>/dev/null & ", 'w' );
		        	} else {
		        		if( $verbose == true ) {
		        			console_output( number_format( $j)." | Flood Protection Enabled." );
		        		}
		        		sleep(1);
		        		$pipe[$j] = popen( "php -q /opt/zeus/console/console.php --action=channel_slave --server_id=".$server['id']." --channel_id=".$channels[$j]." 2>/dev/null & ", 'w' );
		        	}
		        }
		        
		        // wait for them to finish
		        for ( $j=0; $j<$count; ++$j) {
		            pclose( $pipe[$j] );
		        }
		    }
		} else {
			if( $verbose == true ) {
				console_output( "No channels for this server to process." );
			}
		}
	} else {
		if( $verbose == true ) {
			console_output( "No channels on cms." );
		}
	}

	if( $verbose == true ) {
		console_output( "Finished" );
		echo "\n\n";
	}

	killlock();
}

exit;