<?php

// Add Customizer functionality.
require get_stylesheet_directory() . '/inc/customizer.php';

//Add Widgets functionality
require get_stylesheet_directory() . '/inc/widgets.php';

//Add Catalog and Gallery functionality
require get_stylesheet_directory() . '/inc/catalog-gallery.php';

//Add Admin menu functionality
require get_stylesheet_directory() . '/inc/admin-menu.php';

//Add Cart functionality
require get_stylesheet_directory() . '/inc/cart.php';

//Add Database functionality
require get_stylesheet_directory() . '/inc/database.php';

//Add Files-updload functionality
require get_stylesheet_directory() . '/inc/files-upload.php';

//Add Service functionality
require get_stylesheet_directory() . '/inc/service.php';

//Add PDF Invoice functionality
require get_stylesheet_directory() . '/inc/invoice.php';

// echo the 3 images (print design sublimation) displayed in home page and services pages
function echoBeforeFooter() {

    echo '<div id="content2" style="background-color:' . get_theme_mod("header_color") . '">'; // the color of this div is the same as the header
        
        echo '<div id="print" class="pds">';
            echo '<img src="' . get_stylesheet_directory_uri() . '/images/print.png" alt="Print" width="60%">';
        echo '</div>'; // #print

        echo '<div id="design" class="pds">';
            echo '<img src="' . get_stylesheet_directory_uri() . '/images/design.png" alt="Design" width="60%">';
        echo '</div>'; // #design

        echo '<div id="sublimation" class="pds">';
            echo '<img src="' . get_stylesheet_directory_uri() . '/images/sublimation.png" alt="Sublimation" width="60%">';
        echo '</div>'; // #sublimation

    echo '</div>'; // #content2
}

?>
