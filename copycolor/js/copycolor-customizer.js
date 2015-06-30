/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */
(function ($) {

    /***** SITE TITLE *****/

    wp.customize('logo', function (value) {
        value.bind(function (newVal) {
            var logo_img = $('.header-main img');
            logo_img.attr('src', newVal);
        });
    });

    /***** COLORS *****/

    // Update the header color in real time...
    wp.customize('header_color', function (value) {
        value.bind(function (newval) {
            $('header').css('background-color', newval);
            $('#content2').css('background-color', newval);
        });
    });

    //Update site background color...
    wp.customize('background_color', function (value) {
        value.bind(function (newval) {
            $('body').css('background-color', newval);
        });
    });

    // Update the footer color in real time...
    wp.customize('footer_color', function (value) {
        value.bind(function (newval) {
            $('#colophon').css('background-color', newval);
        });
    });

    /***** COMPANY INFORMATIONS *****/

    wp.customize('company_address', function (value) {
        value.bind(function (newval) {
            $('.company_address').html(newval);
        });
    });

    wp.customize('company_phone', function (value) {
        value.bind(function (newval) {
            $('.company_phone').html(newval);
        });
    });

    wp.customize('company_email', function (value) {
        value.bind(function (newval) {
            $('.company_email').html(newval);
        });
    });

    wp.customize( 'display_facebook_page', function( value ) {
        value.bind( function( newval ) {
            if ( true === newval ) {
                $( '#fblink' ).removeClass( 'hidden' );
            } else {
                $( '#fblink' ).addClass( 'hidden' );
            }
        });
    }); 

})(jQuery);