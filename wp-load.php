<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the wp-config.php file. The wp-config.php
 * file will then load the wp-settings.php file, which
 * will then set up the WordPress environment.
 * Bootstrap 文件设置了绝对路径常量，加载了wp-config.php配置文件。
 * wp-config.php接着加载wp-settings.php，该文件将建立WordPress环境
 *
 * If the wp-config.php file is not found then an error
 * will be displayed asking the visitor to set up the
 * wp-config.php file.
 *如果wp-config.php该文件没有找到，一个错误信息将会抛出，请求用户设置该文件
 *
 * Will also search for wp-config.php in WordPress' parent
 * directory to allow the WordPress directory to remain
 * untouched.
 * 也将会在WordPress的父级目录搜索wp-config.php来让父目录保持独立
 *
 * @internal This file must be parsable by PHP4.
 *
 * @package WordPress
 */

/** Define ABSPATH as this file's directory  定义根目录的绝对路径*/
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );

/*
 * If wp-config.php exists in the WordPress root, or if it exists in the root and wp-settings.php
 * doesn't, load wp-config.php. The secondary check for wp-settings.php has the added benefit
 * of avoiding cases where the current directory is a nested installation, e.g. / is WordPress(a)
 * and /blog/ is WordPress(b).
 *
 * 不管wp-settings.php有没有在根目录，只要wp-config.php在根目录，也就加载 wp-config.php
 * 第二个检查项是wp-settings.php是否已经添加，以利于避免当前目录被嵌套安装。
 *
 * If neither set of conditions is true, initiate loading the setup process.
 * 如果上述条件都不满足，进行初始化安装。
 */
if ( file_exists( ABSPATH . 'wp-config.php') ) {

	/** The config file resides in ABSPATH   config文件位于根目录*/
	require_once( ABSPATH . 'wp-config.php' );

} elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {

	/**
	 * The config file resides one level above ABSPATH but is not part of another install
	 *  config文件位于根目录上一级，但它不是另外一个安装程序的一部分
	 * */
	require_once( dirname( ABSPATH ) . '/wp-config.php' );

} else {

	// A config file doesn't exist  config文件不存在

	define( 'WPINC', 'wp-includes' );
	require_once( ABSPATH . WPINC . '/load.php' );

	// Standardize $_SERVER variables across setups. 通过安装标准化$_SERVER变量
	wp_fix_server_vars();

	require_once( ABSPATH . WPINC . '/functions.php' );

	$path = wp_guess_url() . '/wp-admin/setup-config.php';

	/*
	 * We're going to redirect to setup-config.php. While this shouldn't result
	 * in an infinite loop, that's a silly thing to assume, don't you think? If
	 * we're traveling in circles, our last-ditch effort is "Need more help?"
	 */
	if ( false === strpos( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
		header( 'Location: ' . $path );
		exit;
	}

	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	require_once( ABSPATH . WPINC . '/version.php' );

	wp_check_php_mysql_versions();
	wp_load_translations_early();

	// Die with an error message
	$die  = sprintf(
		/* translators: %s: wp-config.php */
		__( "There doesn't seem to be a %s file. I need this before we can get started." ),
		'<code>wp-config.php</code>'
	) . '</p>';
	$die .= '<p>' . sprintf(
		/* translators: %s: Codex URL */
		__( "Need more help? <a href='%s'>We got it</a>." ),
		__( 'https://codex.wordpress.org/Editing_wp-config.php' )
	) . '</p>';
	$die .= '<p>' . sprintf(
		/* translators: %s: wp-config.php */
		__( "You can create a %s file through a web interface, but this doesn't work for all server setups. The safest way is to manually create the file." ),
		'<code>wp-config.php</code>'
	) . '</p>';
	$die .= '<p><a href="' . $path . '" class="button button-large">' . __( "Create a Configuration File" ) . '</a>';

	wp_die( $die, __( 'WordPress &rsaquo; Error' ) );
}
