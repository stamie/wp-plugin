<?php
/* Plugin Name:      Debug permalinks
* Plugin URI:        http://maciejbis.net/
* Description:       Plugin that allows to fix the broken slugs.
* Version:           1.0.0
* Author:            Maciej Bis
* Author URI:        http://maciejbis.net/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Domain Path:       /languages
*/

function debug_permalinks_init() {
	global $wp;

	if(isset($_GET['debug_init'])) {
		print_r($wp);
		die();
	}
}
add_action('init', 'debug_permalinks_init', 9999);

function debug_permalinks_wp_loaded() {
	global $wp_filter;

	if(isset($_GET['debug_wp_loaded'])) {
		$hook = $_GET['debug_wp_loaded'];
		print_r($wp_filter[$hook]);
		die();
	}
}
add_action('wp_loaded', 'debug_permalinks_wp_loaded', 9999);

function debug_parse_query($wp_query) {

	if(isset($_GET['debug_parse_query'])) {
		$hook = $_GET['debug_parse_query'];
		print_r($wp_query);
		die();
	}
}
add_action('parse_query', 'debug_parse_query', 999);

function debug_permalinks_wp() {
	global $wp;

	if(isset($_GET['debug_wp'])) {
		print_r($wp);
		die();
	}
}
add_action('wp', 'debug_permalinks_wp', 9999);

function debug_pre_get_posts($query) {
	global $wp_filter;
	
	if(isset($_GET['debug_pre_get_posts'])) {
		print_r($query);
		die();
	}
}
add_action('pre_get_posts', 'debug_pre_get_posts', 9);

function debug_posts_pre_query($query) {
	if(isset($_GET['debug_posts_pre_query'])) {
		print_r($query);
		die();
	}

	return $query;
}
add_filter('posts_pre_query', 'debug_posts_pre_query', 9999);

function debug_posts_request($query) {
	if(isset($_GET['debug_posts_request'])) {
		print_r($query);
		die();
	}

	return $query;
}
add_filter('posts_request', 'debug_posts_request', 9999);

function debug_posts_request_ids($ids, $query) {
	if(isset($_GET['debug_posts_request_ids'])) {
		print_r($ids);
		print_r($query);
		die();
	}

	return $ids;
}
//add_filter('posts_request_ids', 'debug_posts_request_ids', 9999);

function debug_posts_results($query) {
	if(isset($_GET['debug_posts_results'])) {
		print_r($query);
		die();
	}

	return $query;
}
add_filter('posts_results', 'debug_posts_results', 9999);

function debug_loop_start($query) {
	if(isset($_GET['debug_loop_start'])) {
		print_r($query);
		die();
	}
}
add_action('loop_start', 'debug_loop_start', 9999);

function debug_assembled_query($query) {
	if(isset($_GET['debug_query'])) {
  	print_r($query);
		die();
	}
}
add_action('posts_selection', 'debug_assembled_query', 999);

function debug_current_post($post) {
	if(isset($_GET['debug_post'])) {
  	print_r($post);
		die();
	}
}
add_action('the_post', 'debug_current_post', 999);

function debug_wp_safe_redirect($url, $status) {
	if(isset($_GET['debug_template_redirect'])) {
		print_r($url);
		print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10));
		die();
	}

	return $url;
}
add_filter('wp_redirect', 'debug_wp_safe_redirect', 11, 2);
