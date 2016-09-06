<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */
/**
 * Tells WordPress to load the WordPress theme and output it.
 * 告知WordPress加?主?并?出。
 * @var bool
 */
define('WP_USE_THEMES', true);

/**
 * Loads the WordPress Environment and Template
 * 加载WordPress环境和模板
 */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
