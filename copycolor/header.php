<?php

?>?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
	    <meta name="viewport" content="width=device-width">
	    <title><?php wp_title( '|', true, 'right' ); ?></title>
	    <link rel="profile" href="http://gmpg.org/xfn/11">
	    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	    <!--[if lt IE 9]>
	    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	    <![endif]-->
	    <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
    <div id="page">

        <header id="masthead" class="site-header" role="banner">
		<div class="header-main">
            <img src="<?php echo get_stylesheet_directory_uri() ?>/images/logo.png" alt="Copy Color Jerusalem"/>

			<nav id="primary-navigation" class="site-navigation primary-navigation" role="navigation">
				<button class="menu-toggle"><?php _e( 'Primary Menu', 'twentyfourteen' ); ?></button>
				<a class="screen-reader-text skip-link" href="#content"><?php _e( 'Skip to content', 'twentyfourteen' ); ?></a>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
			</nav>
		</div>

	</header><!-- #masthead -->

    <div id="main" class="site-main">
        
    <!-- </body> -->
<!-- </html> -->
