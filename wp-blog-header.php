<?php
/**
 * Loads the WordPress environment and template.
 * 加载WordPress环境和模板
 *
 * @package WordPress
 */

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	// Load the WordPress library. 记载WordPress库
	require_once( dirname(__FILE__) . '/wp-load.php' );

	// Set up the WordPress query. 建立WordPress查询
	wp();

	// Load the theme template. 加载主题模板
	require_once( ABSPATH . WPINC . '/template-loader.php' );

}
