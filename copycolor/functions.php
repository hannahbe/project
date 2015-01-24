<?php

//copycolor's customizer
function copycolor_customize_register($wp_customize)
{
    //remove all the sections we don't need
    $wp_customize->remove_section('title_tagline');
    $wp_customize->remove_section('header_image');
    $wp_customize->remove_section('background_image');
    $wp_customize->remove_section('static_front_page');
    $wp_customize->remove_section( 'featured_content' ); //this doesn't work, why???

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

    //set the default value of background_color setting to white
    $wp_customize->get_setting('background_color')->default='#ffffff';

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

function copycolor_head()
{
    ?>
         <style type="text/css">
             #masthead.site-header { background-color:<?php echo get_theme_mod('header_color'); ?>};
             .primary-navigation ul ul { background-color:<?php echo get_theme_mod('header_color'); ?>};
         </style>
    <?php
}
add_action( 'wp_head', 'copycolor_head');


?>
