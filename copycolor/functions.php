<?php

// Add Customizer functionality.
require get_stylesheet_directory() . '/inc/customizer.php';

//Add Widgets functionality
require get_stylesheet_directory() . '/inc/widgets.php';

//Add Gallery functionality
require get_stylesheet_directory() . '/inc/gallery.php';

//Add Admin menu functionality
require get_stylesheet_directory() . '/inc/admin-menu.php';

function add_custom_query_var( $vars ){
  $vars[] = 'id';
  return $vars;
}
add_filter( 'query_vars', 'add_custom_query_var');

//function used in view_item to turn url into path
function url_to_path_test($url){
    $url=str_replace(rtrim(get_site_url(),'/').'/', ABSPATH, $url);
    return $url;
}

?>
