<?php

function copycolor_customize_register($wp_customize)
{
    //add setting and control header_color to the section "Colors"
    $wp_customize->add_setting('header_color', array (
        'default' => '#30302f'));

    $wp_customize->add_control(new WP_Customize_Color_Control( 
	    $wp_customize, 'header_color', array(
		    'label'      => __( 'Header Color', 'copycolor' ),
		    'section'    => 'colors',
		    'settings'   => 'header_color',
            'priority'   => '1',
	    )
    ));

    //set the default value of background_color setting
    $wp_customize->get_setting('background_color')->default='#30302f';

    //remove the header_textcolor control because we won't be using it
    $wp_customize->remove_control('header_textcolor');

    //remove the title_tagline and header_image sections
    $wp_customize->remove_section('title_tagline');
    $wp_customize->remove_section('header_image');

    //add section to handle the logo, along with setting and control
    $wp_customize->add_section( 'title_logo' , array(
    'title'      => __('Site title','copycolor'),
    'priority'   => 1,
    ));

    $wp_customize->add_setting('logo', array (
         'default' => get_stylesheet_directory_uri().'/images/logo.png'));

    $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize, 'logo', array(
                'label'      => __( 'Upload a logo', 'copycolor' ),
                'section'    => 'title_logo',
                'settings'   => 'logo',
            )
     ));

}

add_action('customize_register', 'copycolor_customize_register' );

function copycolor_head()
{
    ?>
         <style type="text/css">
             #masthead.site-header { background-color:<?php echo get_theme_mod('header_color'); ?>};
         </style>
    <?php
}
add_action( 'wp_head', 'copycolor_head');

?>
