<?php

//copycolor's customizer
function copycolor_customize_register($wp_customize)
{
    //remove all the sections we don't need
    $wp_customize->remove_section('title_tagline');
    $wp_customize->remove_section('header_image');
    $wp_customize->remove_section('background_image');
    $wp_customize->remove_section('static_front_page');
    $wp_customize->remove_section('featured_content'); //this doesn't work, why???

    /***** SITE TITLE *****/

    //add section to handle the logo, along with setting and control
    $wp_customize->add_section( 'title_logo' , array(
    'title'      => __('Site title','copycolor'),
    'priority'   => 1,
    ));

    $wp_customize->add_setting('logo', array (
         'default'      => get_stylesheet_directory_uri().'/images/logo.png',
         'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize, 'logo', array(
                'label'      => __( 'Upload a logo', 'copycolor' ),
                'section'    => 'title_logo',
                'settings'   => 'logo',
            )
     ));

    /***** NAVIGATION *****/

    //add section navigation with same arguments except description => changes description
    $wp_customize->add_section( 'nav' , array(
    'title'      => __('Navigation','copycolor'),
    'priority'   => 15,
    'description'=> __('Your theme supports one menu. Select which menu appears on the top. You can edit your menu content on the Menus screen in the Appearance section.', 'copycolor'),
    ));

    //remove the nav_menu_locations[secondary] control because we won't be using it
    $wp_customize->remove_control('nav_menu_locations[secondary]');

    /***** COLORS *****/

    //remove the header_textcolor control because we won't be using it
    $wp_customize->remove_control('header_textcolor');

    //add setting and control header_color to the section "Colors"
    $wp_customize->add_setting('header_color', array (
        'default'      => '#30302f',
        'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Color_Control( 
	    $wp_customize, 'header_color', array(
		    'label'      => __( 'Header Color', 'copycolor' ),
		    'section'    => 'colors',
		    'settings'   => 'header_color',
            'priority'   => '1',
	    )
    ));

    //add setting and control footer_color to the section "Colors"
    $wp_customize->add_setting('footer_color', array (
        'default'      => '#ffffff',
        'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Color_Control( 
	    $wp_customize, 'footer_color', array(
		    'label'      => __( 'Footer Color', 'copycolor' ),
		    'section'    => 'colors',
		    'settings'   => 'footer_color',
            'priority'   => '2',
	    )
    ));

    //set the default value of background_color setting to white
    $wp_customize->get_setting('background_color')->default='#ffffff';

    /***** COMPANY INFORMATIONS *****/

     //add section to handle copycolor's informations: address, phone number, email, facebook page
     $wp_customize->add_section( 'company_informations' , array(
    'title'      => __('Company informations','copycolor'),
    'priority'   => 150,
    ));

    //address
    $wp_customize->add_setting('company_address', array (
         'default'      => '',
         'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize, 'company_address', array(
                'label'      => __( 'Address', 'copycolor' ),
                'section'    => 'company_informations',
                'settings'   => 'company_address',
            )
     ));

    //phone number
    $wp_customize->add_setting('company_phone', array (
         'default'      => '',
         'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize, 'company_phone', array(
                'label'      => __( 'Phone number', 'copycolor' ),
                'section'    => 'company_informations',
                'settings'   => 'company_phone',
            )
     ));

    //email
    $wp_customize->add_setting('company_email', array (
         'default'      => '',
         'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize, 'company_email', array(
                'label'      => __( 'Email', 'copycolor' ),
                'section'    => 'company_informations',
                'settings'   => 'company_email',
            )
     ));

    //facebook page
    $wp_customize->add_setting('url_facebook_page', array (
         'default'      => '',
         'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize, 'url_facebook_page', array(
                'label'      => __( 'Facebook Page', 'copycolor' ),
                'section'    => 'company_informations',
                'settings'   => 'url_facebook_page',
            )
     ));

    $wp_customize->add_setting('display_facebook_page', array (
         'default'      => FALSE,
         'transport'    => 'postMessage'));

    $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize, 'display_facebook_page', array(
                'label'      => __( 'Display link to Facebook Page', 'copycolor' ),
                'section'    => 'company_informations',
                'settings'   => 'display_facebook_page',
                'type'       => 'checkbox',
            )
     ));
}

add_action('customize_register', 'copycolor_customize_register' );

// changes the color of the header according to the user's choice
function copycolor_head()
{
    /* change color of header, subpages's titles*/
    ?>
        <style type="text/css">
            #masthead.site-header { background-color:<?php echo get_theme_mod('header_color'); ?>};
            .primary-navigation ul ul { background-color:<?php echo get_theme_mod('header_color'); ?>};
         </style>
    <?php
}
add_action( 'wp_head', 'copycolor_head');

// changes the color of the footer according to the user's choice
function copycolor_footer()
{
    ?>
         <style type="text/css">
             #colophon { background-color:<?php echo get_theme_mod('footer_color'); ?>};
         </style>
    <?php
}
add_action( 'wp_footer', 'copycolor_footer');

   /**
    * This outputs the javascript needed to automate the live settings preview.
    * Also keep in mind that this function isn't necessary unless your settings 
    * are using 'transport'=>'postMessage' instead of the default 'transport'
    * => 'refresh'
    * 
    * Used by hook: 'customize_preview_init'
    * 
    * @see add_action('customize_preview_init',$func)
    * @since MyTheme 1.0
    */
function copycolor_customize_preview_js() {
      wp_enqueue_script( 
           'copycolor_customizer', // Give the script a unique ID
           get_template_directory_uri() . '/js/copycolor-customizer.js', // Define the path to the JS file
           array(  'jquery', 'customize-preview' ), // Define dependencies
           '', // Define a version (optional) 
           true // Specify whether to put in footer (leave this true)
      );
   }
   // Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , 'copycolor_customize_preview_js' );

?>
