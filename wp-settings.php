<?php
/**
 * Used to set up and fix common variables and include
 * the WordPress procedural and class library.
 * 曾经用来设置变量，引入WordPress相关程序和类库
 *
 * Allows for some configuration in wp-config.php (see default-constants.php)
 * 考虑到 wp-config.php中的一些配置。
 *
 * @internal This file must be parsable by PHP4.
 *
 * @package WordPress
 */

/**
 * Stores the location of the WordPress directory of functions, classes, and core content.
 * 保存本地WordPress方法路径、类和核心常量
 * @since 1.0.0
 */
define( 'WPINC', 'wp-includes' );

// Include files required for initialization. 引入初始化所需文件
require( ABSPATH . WPINC . '/load.php' );
require( ABSPATH . WPINC . '/default-constants.php' );
require( ABSPATH . WPINC . '/plugin.php' );

/*
 * These can't be directly globalized in version.php. When updating,
 * we're including version.php from another install and don't want
 * these values to be overridden if already set.
 *
 * 这不能在version.php中直接全局化，当升级时，我们从其他安装文件中引入version.php,并且如果他已经设置，我们并不希望这些值被重写
 */
global $wp_version, $wp_db_version, $tinymce_version, $required_php_version, $required_mysql_version, $wp_local_package;
require( ABSPATH . WPINC . '/version.php' );

/**
 * If not already configured, `$blog_id` will default to 1 in a single site
 * configuration. In multisite, it will be overridden by default in ms-settings.php.
 * 如果没有被配置，在单个网站配置中$blog_id被默认为1；多网站时，它将被ms-settings.php中的默认值改写
 * @global int $blog_id
 * @since 2.0.0
 */
global $blog_id;

// Set initial default constants including WP_MEMORY_LIMIT, WP_MAX_MEMORY_LIMIT, WP_DEBUG, SCRIPT_DEBUG, WP_CONTENT_DIR and WP_CACHE.
// 设置初始化默认常量，包括WP_MEMORY_LIMIT, WP_MAX_MEMORY_LIMIT, WP_DEBUG, SCRIPT_DEBUG, WP_CONTENT_DIR and WP_CACHE
wp_initial_constants();

// Check for the required PHP version and for the MySQL extension or a database drop-in.
// 检查要求的PHP版本、MySQL拓展或者数据库防注入
wp_check_php_mysql_versions();

// Disable magic quotes at runtime. Magic quotes are added using wpdb later in wp-settings.php.
// 禁用运行时的魔法引用，魔法引用将在该文件后面以wpdb进行添加
@ini_set( 'magic_quotes_runtime', 0 );
@ini_set( 'magic_quotes_sybase',  0 );

// WordPress calculates offsets from UTC.计算时差
date_default_timezone_set( 'UTC' );

// Turn register_globals off. 关闭register_globals
wp_unregister_GLOBALS();

// Standardize $_SERVER variables across setups. 通过安装标准化$_SERVER变量
wp_fix_server_vars();

// Check if we have received a request due to missing favicon.ico 检查是否因为丢失favicon.ico而得到了一个请求
wp_favicon_request();

// Check if we're in maintenance mode. 检查是否开启了维护模式
wp_maintenance();

// Start loading timer. 开始加载定时器
timer_start();

// Check if we're in WP_DEBUG mode. 检查
wp_debug_mode();

/**
 * Filters whether to enable loading of the advanced-cache.php drop-in.
 * 是否选择性加载advanced-cache.php这个顺带文件
 *
 * This filter runs before it can be used by plugins. It is designed for non-web
 * run-times. If false is returned, advanced-cache.php will never be loaded.
 * 筛选器将在插件使用该文件前运行，他被设计用以非web的运行区间，如果返回的是false,advanced-cache.php将永远不被加载。
 *
 * @since 4.6.0
 *
 * @param bool $enable_advanced_cache Whether to enable loading advanced-cache.php (if present).
 *                                    Default true.
 */
if ( WP_CACHE && apply_filters( 'enable_loading_advanced_cache_dropin', true ) ) {
// For an advanced caching plugin to use. Uses a static drop-in because you would only want one.
	// 用以高级缓存插件，
	WP_DEBUG ? include( WP_CONTENT_DIR . '/advanced-cache.php' ) : @include( WP_CONTENT_DIR . '/advanced-cache.php' );
}

// Define WP_LANG_DIR if not set. 如果没有设置，则定义WP_LANG_DIR常量
wp_set_lang_dir();

// Load early WordPress files.提前加载的WordPress文件。
require( ABSPATH . WPINC . '/compat.php' );
require( ABSPATH . WPINC . '/functions.php' );
require( ABSPATH . WPINC . '/class-wp.php' );
require( ABSPATH . WPINC . '/class-wp-error.php' );
require( ABSPATH . WPINC . '/pomo/mo.php' );

// Include the wpdb class and, if present, a db.php database drop-in.
// 加载wpdb类，如果存在db.php，也同时加载他
global $wpdb;
require_wp_db();

// Set the database table prefix and the format specifiers for database table columns.
// 设置数据库表前缀，同时格式化数据库字段说明符（错误提示）。
$GLOBALS['table_prefix'] = $table_prefix;
wp_set_wpdb_vars();

// Start the WordPress object cache, or an external object cache if the drop-in is present.
// 开始WordPress对象缓存，如果有其他外在的缓存对象亦可以加载他。
wp_start_object_cache();

// Attach the default filters.
// 附带加载默认过滤器。
require( ABSPATH . WPINC . '/default-filters.php' );

// Initialize multisite if enabled.  如果支持多网站，初始化之
if ( is_multisite() ) {
	require( ABSPATH . WPINC . '/class-wp-site-query.php' );
	require( ABSPATH . WPINC . '/class-wp-network-query.php' );
	require( ABSPATH . WPINC . '/ms-blogs.php' );
	require( ABSPATH . WPINC . '/ms-settings.php' );
} elseif ( ! defined( 'MULTISITE' ) ) {
	define( 'MULTISITE', false );
}

register_shutdown_function( 'shutdown_action_hook' );

// Stop most of WordPress from being loaded if we just want the basics.
// 如果我们只想要基本功能，禁止WordPress中的大部分被加载。
if ( SHORTINIT )
	return false;

// Load the L10n library. 加载L10n库。
require_once( ABSPATH . WPINC . '/l10n.php' );

// Run the installer if WordPress is not installed. 如果WordPress没有安装，运行安装。
wp_not_installed();

// Load most of WordPress. 加载WordPress的大部分
require( ABSPATH . WPINC . '/class-wp-walker.php' );
require( ABSPATH . WPINC . '/class-wp-ajax-response.php' );
require( ABSPATH . WPINC . '/formatting.php' );
require( ABSPATH . WPINC . '/capabilities.php' );
require( ABSPATH . WPINC . '/class-wp-roles.php' );
require( ABSPATH . WPINC . '/class-wp-role.php' );
require( ABSPATH . WPINC . '/class-wp-user.php' );
require( ABSPATH . WPINC . '/query.php' );
require( ABSPATH . WPINC . '/date.php' );
require( ABSPATH . WPINC . '/theme.php' );
require( ABSPATH . WPINC . '/class-wp-theme.php' );
require( ABSPATH . WPINC . '/template.php' );
require( ABSPATH . WPINC . '/user.php' );
require( ABSPATH . WPINC . '/class-wp-user-query.php' );
require( ABSPATH . WPINC . '/session.php' );
require( ABSPATH . WPINC . '/meta.php' );
require( ABSPATH . WPINC . '/class-wp-meta-query.php' );
require( ABSPATH . WPINC . '/class-wp-metadata-lazyloader.php' );
require( ABSPATH . WPINC . '/general-template.php' );
require( ABSPATH . WPINC . '/link-template.php' );
require( ABSPATH . WPINC . '/author-template.php' );
require( ABSPATH . WPINC . '/post.php' );
require( ABSPATH . WPINC . '/class-walker-page.php' );
require( ABSPATH . WPINC . '/class-walker-page-dropdown.php' );
require( ABSPATH . WPINC . '/class-wp-post-type.php' );
require( ABSPATH . WPINC . '/class-wp-post.php' );
require( ABSPATH . WPINC . '/post-template.php' );
require( ABSPATH . WPINC . '/revision.php' );
require( ABSPATH . WPINC . '/post-formats.php' );
require( ABSPATH . WPINC . '/post-thumbnail-template.php' );
require( ABSPATH . WPINC . '/category.php' );
require( ABSPATH . WPINC . '/class-walker-category.php' );
require( ABSPATH . WPINC . '/class-walker-category-dropdown.php' );
require( ABSPATH . WPINC . '/category-template.php' );
require( ABSPATH . WPINC . '/comment.php' );
require( ABSPATH . WPINC . '/class-wp-comment.php' );
require( ABSPATH . WPINC . '/class-wp-comment-query.php' );
require( ABSPATH . WPINC . '/class-walker-comment.php' );
require( ABSPATH . WPINC . '/comment-template.php' );
require( ABSPATH . WPINC . '/rewrite.php' );
require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
require( ABSPATH . WPINC . '/feed.php' );
require( ABSPATH . WPINC . '/bookmark.php' );
require( ABSPATH . WPINC . '/bookmark-template.php' );
require( ABSPATH . WPINC . '/kses.php' );
require( ABSPATH . WPINC . '/cron.php' );
require( ABSPATH . WPINC . '/deprecated.php' );
require( ABSPATH . WPINC . '/script-loader.php' );
require( ABSPATH . WPINC . '/taxonomy.php' );
require( ABSPATH . WPINC . '/class-wp-term.php' );
require( ABSPATH . WPINC . '/class-wp-term-query.php' );
require( ABSPATH . WPINC . '/class-wp-tax-query.php' );
require( ABSPATH . WPINC . '/update.php' );
require( ABSPATH . WPINC . '/canonical.php' );
require( ABSPATH . WPINC . '/shortcodes.php' );
require( ABSPATH . WPINC . '/embed.php' );
require( ABSPATH . WPINC . '/class-wp-embed.php' );
require( ABSPATH . WPINC . '/class-wp-oembed-controller.php' );
require( ABSPATH . WPINC . '/media.php' );
require( ABSPATH . WPINC . '/http.php' );
require( ABSPATH . WPINC . '/class-http.php' );
require( ABSPATH . WPINC . '/class-wp-http-streams.php' );
require( ABSPATH . WPINC . '/class-wp-http-curl.php' );
require( ABSPATH . WPINC . '/class-wp-http-proxy.php' );
require( ABSPATH . WPINC . '/class-wp-http-cookie.php' );
require( ABSPATH . WPINC . '/class-wp-http-encoding.php' );
require( ABSPATH . WPINC . '/class-wp-http-response.php' );
require( ABSPATH . WPINC . '/class-wp-http-requests-response.php' );
require( ABSPATH . WPINC . '/widgets.php' );
require( ABSPATH . WPINC . '/class-wp-widget.php' );
require( ABSPATH . WPINC . '/class-wp-widget-factory.php' );
require( ABSPATH . WPINC . '/nav-menu.php' );
require( ABSPATH . WPINC . '/nav-menu-template.php' );
require( ABSPATH . WPINC . '/admin-bar.php' );
require( ABSPATH . WPINC . '/rest-api.php' );
require( ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php' );
require( ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php' );
require( ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php' );

// Load multisite-specific files. 记载 详细多站点文件。
if ( is_multisite() ) {
	require( ABSPATH . WPINC . '/ms-functions.php' );
	require( ABSPATH . WPINC . '/ms-default-filters.php' );
	require( ABSPATH . WPINC . '/ms-deprecated.php' );
}

// Define constants that rely on the API to obtain the default value.
// 定义依赖api获取默认值的常量。
// Define must-use plugin directory constants, which may be overridden in the sunrise.php drop-in.
// 定义必须使用的插件路径常量，他们可能在随带加载的sunrise.php给覆盖
wp_plugin_directory_constants();

$GLOBALS['wp_plugin_paths'] = array();

// Load must-use plugins. 加载必须要的插件
foreach ( wp_get_mu_plugins() as $mu_plugin ) {
	include_once( $mu_plugin );
}
unset( $mu_plugin );

// Load network activated plugins. 加载激活网络的插件。
if ( is_multisite() ) {
	foreach ( wp_get_active_network_plugins() as $network_plugin ) {
		wp_register_plugin_realpath( $network_plugin );
		include_once( $network_plugin );
	}
	unset( $network_plugin );
}

/**
 * Fires once all must-use and network-activated plugins have loaded.
 * 接触所有已经加载的必用和网络激活插件。
 *
 * @since 2.8.0
 */
do_action( 'muplugins_loaded' );

if ( is_multisite() )
	ms_cookie_constants(  );

// Define constants after multisite is loaded. 定义多站点被加载后的常量
wp_cookie_constants();

// Define and enforce our SSL constants 定义并生效SSL常量
wp_ssl_constants();

// Create common globals.常见全局常量
require( ABSPATH . WPINC . '/vars.php' );

// Make taxonomies and posts available to plugins and themes. 进行文件分类并发送变量给插件和主题
// @plugin authors: warning: these get registered again on the init hook.
create_initial_taxonomies();
create_initial_post_types();

// Register the default theme directory root 注册默认主题路径根目录
register_theme_directory( get_theme_root() );

// Load active plugins. 加载激活插件。
foreach ( wp_get_active_and_valid_plugins() as $plugin ) {
	wp_register_plugin_realpath( $plugin );
	include_once( $plugin );
}
unset( $plugin );

// Load luggable functions. 加载可插入的方法
require( ABSPATH . WPINC . '/pluggable.php' );
require( ABSPATH . WPINC . '/pluggable-deprecated.php' );

// Set internal encoding.  设置内部编码
wp_set_internal_encoding();

// Run wp_cache_postload() if object cache is enabled and the function exists.
// 如果缓存对象存在，并且方法存在，运行 wp_cache_postload()
if ( WP_CACHE && function_exists( 'wp_cache_postload' ) )
	wp_cache_postload();

/**
 * Fires once activated plugins have loaded.
 * 一次性运行激活了的插件。
 *
 * Pluggable functions are also available at this point in the loading order.
 *
 * @since 1.5.0
 */
do_action( 'plugins_loaded' );

// Define constants which affect functionality if not already defined.
// 定义影响功能的常量，如果没有被定义。
wp_functionality_constants();

// Add magic quotes and set up $_REQUEST ( $_GET + $_POST )
// 添加魔术引用并且设置$_REQUEST
wp_magic_quotes();

/**
 * Fires when comment cookies are sanitized.
 * 当评论被审查过后运行
 * @since 2.0.11
 */
do_action( 'sanitize_comment_cookies' );

/**
 * WordPress Query object
 * @global WP_Query $wp_the_query
 * @since 2.0.0
 */
$GLOBALS['wp_the_query'] = new WP_Query();

/**
 * Holds the reference to @see $wp_the_query 持有参考
 * Use this global for WordPress queries
 * @global WP_Query $wp_query
 * @since 1.5.0
 */
$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];

/**
 * Holds the WordPress Rewrite object for creating pretty URLs 持有重新对象来创建友好链接。
 * @global WP_Rewrite $wp_rewrite
 * @since 1.5.0
 */
$GLOBALS['wp_rewrite'] = new WP_Rewrite();

/**
 * WordPress Object
 * @global WP $wp
 * @since 2.0.0
 */
$GLOBALS['wp'] = new WP();

/**
 * WordPress Widget Factory Object 小器具工厂对象
 * @global WP_Widget_Factory $wp_widget_factory
 * @since 2.8.0
 */
$GLOBALS['wp_widget_factory'] = new WP_Widget_Factory();

/**
 * WordPress User Roles 用户角色
 * @global WP_Roles $wp_roles
 * @since 2.0.0
 */
$GLOBALS['wp_roles'] = new WP_Roles();

/**
 * Fires before the theme is loaded. 提前加载主题
 *
 * @since 2.6.0
 */
do_action( 'setup_theme' );

// Define the template related constants. 定义模板相关的常量
wp_templating_constants(  );

// Load the default text localization domain. 加载默认本地文本域
load_default_textdomain();

$locale = get_locale();
$locale_file = WP_LANG_DIR . "/$locale.php";
if ( ( 0 === validate_file( $locale ) ) && is_readable( $locale_file ) )
	require( $locale_file );
unset( $locale_file );

// Pull in locale data after loading text domain. 加载完默认本地文本域后，拉取本地数据
require_once( ABSPATH . WPINC . '/locale.php' );

/**
 * WordPress Locale object for loading locale domain date and various strings.
 * WordPress本地对象用以记载本地数据和变量。
 *
 * @global WP_Locale $wp_locale
 * @since 2.1.0
 */
$GLOBALS['wp_locale'] = new WP_Locale();

// Load the functions for the active theme, for both parent and child theme if applicable.
// 只要是能应用的，不管是父级还是子级主题，都将其主题功能进行加载。
if ( ! wp_installing() || 'wp-activate.php' === $pagenow ) {
	if ( TEMPLATEPATH !== STYLESHEETPATH && file_exists( STYLESHEETPATH . '/functions.php' ) )
		include( STYLESHEETPATH . '/functions.php' );
	if ( file_exists( TEMPLATEPATH . '/functions.php' ) )
		include( TEMPLATEPATH . '/functions.php' );
}

/**
 * Fires after the theme is loaded.
 * 运行加载了的主题
 * @since 3.0.0
 */
do_action( 'after_setup_theme' );

// Set up current user. 设置当前用户。
$GLOBALS['wp']->init();

/**
 * Fires after WordPress has finished loading but before any headers are sent.
 * WordPress完成加载后，headers设置之前运行。
 *
 * Most of WP is loaded at this stage, and the user is authenticated. WP continues
 * to load on the {@see 'init'} hook that follows (e.g. widgets), and many plugins instantiate
 * themselves on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
 *
 * If you wish to plug an action once WP is loaded, use the {@see 'wp_loaded'} hook below.
 *
 * @since 1.5.0
 */
do_action( 'init' );

// Check site status
if ( is_multisite() ) {
	if ( true !== ( $file = ms_site_check() ) ) {
		require( $file );
		die();
	}
	unset($file);
}

/**
 * This hook is fired once WP, all plugins, and the theme are fully loaded and instantiated.
 * 当WP，所有插件和主题全被加载并实例化后，这个钩子运行。
 *
 * Ajax requests should use wp-admin/admin-ajax.php. admin-ajax.php can handle requests for
 * users not logged in.
 *
 * @link https://codex.wordpress.org/AJAX_in_Plugins
 *
 * @since 3.0.0
 */
do_action( 'wp_loaded' );
