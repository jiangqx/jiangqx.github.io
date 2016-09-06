<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Sydney
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!--======================seo优化内容开始===============================-->
	<?php
	$description = '';
	$keywords = '';

	if (is_home() || is_page()) {
		// 将以下引号中的内容改成你的主页description
		$description = "一个生活家的技术博客";

		// 将以下引号中的内容改成你的主页keywords
		$keywords = "it,博客,全栈,blog,技术,php,python,linux,centos,运维,javascript,angular,angularjs,laravel,框架,前端,frontend,backend,CSS3,bootstrap,HTML5,H5,生活,音乐,吉他";
	} elseif (is_single()) {
		$description1 = get_post_meta($post->ID, "description", true);
		$description2 = str_replace("\n","",mb_strimwidth(strip_tags($post->post_content), 0, 200, "…", 'utf-8'));

		// 填写自定义字段description时显示自定义字段的内容，否则使用文章内容前200字作为描述
		$description = $description1 ? $description1 : $description2;

		// 填写自定义字段keywords时显示自定义字段的内容，否则使用文章tags作为关键词
		$keywords = get_post_meta($post->ID, "keywords", true);
		if($keywords == '') {
			$tags = wp_get_post_tags($post->ID);
			foreach ($tags as $tag ) {
				$keywords = $keywords . $tag->name . ", ";
			}
			$keywords = rtrim($keywords, ', ');
		}
	} elseif (is_category()) {
		// 分类的description可以到后台 - 文章 -分类目录，修改分类的描述
		$description = category_description();
		$keywords = single_cat_title('', false);
	} elseif (is_tag()){
		// 标签的description可以到后台 - 文章 - 标签，修改标签的描述
		$description = tag_description();
		$keywords = single_tag_title('', false);
	}
	$description = trim(strip_tags($description));
	$keywords = trim(strip_tags($keywords));
	?>
	<meta name="description" content="<?php echo $description; ?>" />
	<meta name="keywords" content="<?php echo $keywords; ?>" />
<!--==========================seo优化结束===============================-->
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) : ?>
	<?php if ( get_theme_mod('site_favicon') ) : ?>
		<link rel="shortcut icon" href="<?php echo esc_url(get_theme_mod('site_favicon')); ?>" />
	<?php endif; ?>
<?php endif; ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="preloader">
    <div class="spinner">
        <div class="pre-bounce1"></div>
        <div class="pre-bounce2"></div>
    </div>
</div>	
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'sydney' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="header-wrap">
            <div class="container">
                <div class="row">
				<div class="col-md-4 col-sm-8 col-xs-12">
		        <?php if ( get_theme_mod('site_logo') ) : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>"><img class="site-logo" src="<?php echo esc_url(get_theme_mod('site_logo')); ?>" alt="<?php bloginfo('name'); ?>" /></a>
				<?php elseif ( wp_is_mobile() ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		        <?php endif; ?>
				</div>
				<div class="col-md-8 col-sm-4 col-xs-12">
					<div class="btn-menu"></div>
					<nav id="mainnav" class="mainnav" role="navigation">
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb' => 'sydney_menu_fallback' ) ); ?>
					</nav><!-- #site-navigation -->
				</div>
				</div>
			</div>
		</div>
	</header><!-- #masthead -->
	<?php sydney_slider_template(); ?>

	<div class="header-image">
		<?php sydney_header_overlay(); ?>
		<img class="header-inner" src="<?php header_image(); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" alt="<?php bloginfo('name'); ?>">
	</div>

	<div id="content" class="page-wrap">
		<div class="container content-wrapper">
			<div class="row">	