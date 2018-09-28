<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists("save_cache")) {
	function save_cache($key, $data, $ttl = 0) {
		$key = md5($key);
		if(empty($ttl)) {
			$ttl = 86400 * 365;
		}
		$CI =& get_instance();
		return $CI->cache->file->save($key, $data, $ttl);
	}
}

if(!function_exists("get_cache")) {
	function get_cache($key) {
		$key = md5($key);
		if(empty($ttl)) {
			$ttl = 86400 * 365;
		}
		$CI =& get_instance();
		return $CI->cache->file->get($key);
	}
}

if(!function_exists("delete_cache")) {
	function delete_cache($key) {
		$key = md5($key);
		if(empty($ttl)) {
			$ttl = 86400 * 365;
		}
		$CI =& get_instance();
		return $CI->cache->file->delete($key);
	}
}
