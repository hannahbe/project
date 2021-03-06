<?php
/**
 * The template for displaying the header
 * Contains header content and the opening of the #main and #page div elements.
 */
?>

<!DOCTYPE html>
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
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	    <?php wp_head(); ?>
    </head>

    <?php const CART_ID = 208; ?>

    <body <?php body_class(); ?>>
    <div id="page">

        <header id="masthead" class="site-header" role="banner">

		    <div class="header-main">
                <!-- Get the logo that user chose in customizer: -->
                <a href="index.php" title="Home"><img src="<?php echo get_theme_mod('logo'); ?>" alt="Copy Color Jerusalem"></a>
                <!-- The primary menu: -->
			    <nav id="primary-navigation" class="site-navigation primary-navigation" role="navigation">
				    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
                    
			    </nav>
                <a href="<?php echo get_page_link(CART_ID); ?>" title="My cart"><img id="cart-logo" src="<?php echo get_stylesheet_directory_uri() ?>/images/cart.png" alt="Cart"></a>
		    </div><!-- .header-main -->

	    </header><!-- #masthead -->

        <div id="main" class="site-main">
        
    <!-- </body> -->
<!-- </html> -->
