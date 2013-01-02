<?php
/**
 *	galliCache
 *	Author : Mahin Akbar | Team Webgalli
 *	Team Webgalli | Elgg developers and consultants
 *	Mail : info@webgalli.com
 *	Web	: http://webgalli.com
 *	Skype : 'team.webgalli'
 *	@package galliCache plugin
 *	Licence : GPLV2
 *	Copyright : Team Webgalli 2011-2015
 */
 
$validity = elgg_get_plugin_setting('validity', 'galliCache');
 
define('GALLI_CACHE_VERSION', '1.1');
define('GALLI_CACHE_PATH',elgg_get_data_path()."html_cache");
define('GALLI_CACHE_VALIDITY', $validity);
 
elgg_register_event_handler('init', 'system', 'galliCache_init');

function galliCache_init() {
	elgg_register_page_handler('galliCache_js', 'galliCache_js_page_handler');
	
	elgg_register_js('galliCache.js', elgg_get_site_url()."galliCache_js/");
	elgg_load_js('galliCache.js');	

	elgg_register_plugin_hook_handler('route', 'all', 'galliCache_route_hook');	
	elgg_register_plugin_hook_handler('index', 'system', 'galliCache_route_hook');
	
	elgg_register_plugin_hook_handler('cron', 'daily', 'galliCache_cron_jobs');	
}

function galliCache_filename($url){
	$last_cache = (int) elgg_get_config('lastcache'); 
	$filename = md5(elgg_get_friendly_title($url));
	$cache_filename = GALLI_CACHE_PATH . "/$filename" . "." . "$last_cache.php";
	return $cache_filename;
}	

function galliCache_cache_exists($filename = false){
	$context = elgg_get_context();
	if(elgg_is_logged_in() or in_array($context, galliCache_contexts_to_skip())){
		return false;
	}	
	if(!$filename){
		$filename = current_page_url();
	}	
    if(!is_dir(GALLI_CACHE_PATH)){
        mkdir(GALLI_CACHE_PATH, 0777);         
    } 	
	$cache_filename = galliCache_filename($filename);
	if (file_exists($cache_filename)){
		if ( galliCache_is_valid ($cache_filename)) {
			return $cache_filename;
		} else {
			unlink ($cache_filename);
		}	
	}
	return false;
}	

function galliCache_create_cache($filename = false){
	$context = elgg_get_context();
	if(elgg_is_logged_in() or in_array($context, galliCache_contexts_to_skip())){
		return true;
	}	
	if(!$filename){
		$filename = current_page_url();
	}	
	$cache_filename = galliCache_filename($filename);
	$buff = ob_get_contents(); 
	$file = fopen( $cache_filename, "w" );
	fwrite( $file, $buff );
	fclose( $file );
	ob_end_flush(); 
}	

function galliCache_read_cache($filename){
	if(!$filename){
		$filename = galliCache_filename(current_page_url());
	}	
	include $filename;
}

function galliCache_is_valid($cache_filename){
	$last_cache = (int) elgg_get_config('lastcache'); 
	$explode = explode("." , $cache_filename);
	$file_cache = $explode[1];
	if( ($last_cache == $file_cache) && ((time() - GALLI_CACHE_VALIDITY) < filemtime( $cache_filename )) && (filesize($cache_filename) > 0) ){
		return true;
	}
	return false;
}	

function galliCache_cron_jobs($hook, $entity_type, $returnvalue, $params){
	$directory = GALLI_CACHE_PATH;
	if( !$dirhandle = @opendir($directory) ){
		return;
	}	
	while( false !== ($filename = readdir($dirhandle)) ) {
		if( $filename != "." && $filename != ".." ) {
			$filename = $directory. "/". $filename;
			if( @filemtime($filename) < (time() - GALLI_CACHE_VALIDITY) ){
				@unlink($filename);
			}
		}
	}
	return $returnvalue;
}	

function galliCache_route_hook($hook, $entity_type, $returnvalue, $params){
	$version = GALLI_CACHE_VERSION;
	$cache = galliCache_cache_exists();
	if($cache){
		galliCache_read_cache($cache);
		echo "<!-- Static page served using Elgg-galliCache($version). Powered by Team Webgalli. -->";
		exit;
	} else {
		return $returnvalue;
	}	
}

function galliCache_js_page_handler($page) {
	Header("content-type: application/x-javascript");
	echo elgg_view('galliCache/initiate_elgg');
	return true;
}

function galliCache_contexts_to_skip(){
	$skipcontexts = elgg_get_plugin_setting('skipcontexts', 'galliCache');
	return explode(",",$skipcontexts);
}	