<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('UTC');

ini_set('default_socket_timeout', 180);

include('/root/slipstream/node/inc/global_vars.php');
include($app['basepath'].'inc/functions.php');
include($app['basepath'].'inc/php_colors.php');

// set vars
$task 				= $argv[1];
$new_line 			= " \n";
$allowed_files 		= array('mk4','mkv','mp4','flv','avi','mpeg','ts');

$parent_folders 	= array("/mnt/nfs_server/movies/");

function check_file_age($filename)
{
	if(file_exists($filename)) {
		return filemtime($filename);
	}else{
		return 0;
	}
}

function get_metadata($name)
{
	$name = trim($name);

	// try the open movie db for meta data
	$url = 'http://www.omdbapi.com/?apikey=19354e2e&t='.urlencode($name);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$metadata = curl_exec($curl);
	curl_close($curl);

	$metadata = json_decode($metadata, true);

	$data['name'] 				= $name;

	$data['year'] 				= '';
	$data['cover_photo']		= '';
	$data['description']		= '';
	$data['genre'] 				= '';
	$data['runtime'] 			= '';
	$data['language'] 			= '';

	if($metadata['Response'] == False || $metadata['Response'] == "False"){
		$data['status'] 		= 'no_match';
	}elseif($metadata['Response'] == True){
		$data['status'] 		= 'match';
		$data['name'] 			= addslashes($metadata['Title']);
		$data['year'] 			= addslashes($metadata['Year']);
		$data['cover_photo']	= addslashes($metadata['Poster']);
		$data['description']	= addslashes($metadata['Plot']);
		$data['genre'] 			= addslashes($metadata['Genre']);
		$data['runtime'] 		= addslashes($metadata['Runtime']);
		$data['language'] 		= addslashes($metadata['Language']);
	}

	return $data;
}

function search_for_folders($item)
{
	$data = array();

	$bits = glob("/*");
	foreach($bits as $bit){
		if(is_dir($bit)){
			$data[] = $bit;
		}
	}

	return $data;
}

$colors = new Colors();

$config = file_get_contents($app['basepath'].'config.json');
$config = json_decode($config, true);

if($task == 'scan_tv'){
	console_output("SlipStream CMS - TV Manager");

	// loop over all folders to scan
	foreach($parent_folders as $parent_folder){
		// santiy check for parent_folder
		$parent_folder_bits	= explode("/", $parent_folder);
		$parent_folder_bits = array_filter($parent_folder_bits);
		$parent_folder 		= "/".implode("/", $parent_folder_bits);
		echo "Parent Folder: ".$parent_folder.$new_line;

		// clean the folder
		// exec("sudo detox -r ".$parent_folder." > /dev/null");

		// list files and sub-folders
		$path = realpath($parent_folder);
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)) as $filename){
			// get folder for show name
			$folder_bits = explode("/", $filename);
			$folder_bits = array_reverse($folder_bits);
			$folder_bits = array_filter($folder_bits);

			// search folder_bits for tv show name
			foreach($folder_bits as $folder_bit){
				echo "checking ".$folder_bit.$new_line;

				if($folder_bit != 'home' || $folder_bit != 'tv' || $folder_bit != 'tv_shows' || $folder_bit != 'mnt' || $folder_bit != 'media' || $folder_bit != 'nfs' || $folder_bit != 'nfs_server'){
					$metadata = get_metadata($folder_bit);

					if($metadata['status'] == 'match'){
						$tv_show_name = $metadata['name'];
						
						// get just the file name
						$file 			= basename($filename);

						// get the file extension
						$file_ext 		= pathinfo($file, PATHINFO_EXTENSION);

						// file_ext sanity check
						if(in_array($file_ext, $allowed_files)){
							$file_new 		= str_replace(array(' ','_','-'), '.', $file);

							echo "Show: ".$tv_show_name.$new_line;
							echo "File: ".$file.$new_line;
						}

						break;
					}
				}
			}

			die();
		}

		echo $new_line;
	}
}

if($task == 'scan_movies'){
	console_output("SlipStream CMS - Movie Manager");
	// loop over all folders to scan
	foreach($parent_folders as $parent_folder){
		// santiy check for parent_folder
		$parent_folder_bits	= explode("/", $parent_folder);
		$parent_folder_bits = array_filter($parent_folder_bits);
		$parent_folder 		= "/".implode("/", $parent_folder_bits);
		echo "Parent Folder: ".$parent_folder.$new_line;

		// clean the folder
		exec("sudo detox -r ".$parent_folder." > /dev/null");

		// list files and sub-folders
		$path = realpath($parent_folder);
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)) as $filename){
			// get folder for show name
			$folder_bits = explode("/", $filename);
			$folder_bits = array_reverse($folder_bits);
			$folder_bits = array_filter($folder_bits);

			// get just the file name
			$file 			= basename($filename);

			// get the file extension
			$file_ext 		= pathinfo($file, PATHINFO_EXTENSION);

			// file_ext sanity check
			if(in_array($file_ext, $allowed_files)){
				$file_new 		= str_replace(array(' ','_','-'), '.', $file);

				echo "File: ".$file.$new_line;
				
				$pattern = '/[a-zA-Z0-9\.]+\.[0-9]{4}\./';
				preg_match($pattern, $file_new, $matches);
				if(isset($matches[0])){
					$item_name = substr(str_replace('.', ' ', $matches[0]), 0, -6);
					$metadata = get_metadata($item_name);

					// print_r($metadata);
					if($metadata['status'] == 'match'){
						echo "- METADATA Match Found".$new_line;
					}else{
						echo "- METADATA Failed to match".$new_line;
					}
				}
			}
		}

		echo $new_line;
	}
}