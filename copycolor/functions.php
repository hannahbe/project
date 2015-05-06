<?php

// Add Customizer functionality.
require get_stylesheet_directory() . '/inc/customizer.php';

//Add Widgets functionality
require get_stylesheet_directory() . '/inc/widgets.php';

//Add Gallery functionality
require get_stylesheet_directory() . '/inc/gallery.php';

//Add Admin menu functionality
require get_stylesheet_directory() . '/inc/admin-menu.php';

//Add Cart functionality
require get_stylesheet_directory() . '/inc/cart.php';

//Add Database functionality
require get_stylesheet_directory() . '/inc/database.php';

//function to pass id as parameter in url
function add_custom_query_var( $vars ){
  $vars[] = 'id';
  return $vars;
}

add_filter( 'query_vars', 'add_custom_query_var');

//function used in view_item to turn url into path
function url_to_path($url){
    $url=str_replace(rtrim(get_site_url(),'/').'/', ABSPATH, $url);
    return $url;
}

//SESSION
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function myEndSession() {
    session_destroy ();
}

//upload the file n° $fileNumber of $files
function uploadToTemp($files, $fileNumber) {

    $uploadfolder =  url_to_path(WP_CONTENT_URL . '/uploads/temp');  //the temp folder url
    $fileName = uniqid();
    $extension = explode ('.', $files['name'][$fileNumber]);
    $n = count($extension) - 1;
    $extension = $extension[$n];

    $filePath = $uploadfolder . '/' . $fileName . '.' . $extension;

    //assure that we created a file name that doesn't already exist
    while (file_exists($filePath)) {
        $fileName = uniqid();
        $filePath = $uploadfolder . '/' . $fileName . '.' . $extension;
    }

    if (!is_dir($uploadfolder) && !mkdir($uploadfolder, 0777)) {
        echo 'Error creating folder, please try again';
        return NULL;
    }

    if (move_uploaded_file($files['tmp_name'][$fileNumber], $filePath)) {
        return $filePath;
    }

    return NULL;
}

?>
