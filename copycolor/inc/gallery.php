<?php

//return the color we want the image to be tinted in when hover
function gallery_hover_color ($postID) {
    if (in_category('print', $postID)) {
        echo ('magenta');
        return;
    }
    if (in_category('design', $postID)) {
        echo ('cyan');
        return;
    }
    if (in_category('sublimation', $postID)) {
        echo ('yellow');
        return;
    }
    else
        echo ('no-color');
}

// Get URL of first image in a post
function get_first_image ($postID) {
    $post = get_post($postID);
    $first_img = '';
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];

    // no image found display default image instead
    if(empty($first_img)){
        $first_img = "/images/logo.png";
    }
    return $first_img;
}

?>
